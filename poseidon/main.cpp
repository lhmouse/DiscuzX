// This file is part of Poseidon.
// Copyleft 2022 - 2023, LH_Mouse. All wrongs reserved.

#include "precompiled.ipp"
#include "base/config_file.hpp"
#include "static/main_config.hpp"
#include "static/fiber_scheduler.hpp"
#include "static/async_logger.hpp"
#include "static/timer_driver.hpp"
#include "static/async_task_executor.hpp"
#include "static/network_driver.hpp"
#include "utils.hpp"
#include <locale.h>
#include <signal.h>
#include <stdlib.h>
#include <stdio.h>
#include <stdarg.h>
#include <dlfcn.h>
#include <pthread.h>
#include <sys/wait.h>
#include <sys/file.h>
#include <sys/resource.h>
namespace {
using namespace poseidon;

template<typename ResultT, typename... ParamsT, typename... ArgsT>
ROCKET_ALWAYS_INLINE
ResultT
do_syscall(ResultT func(ParamsT...), ArgsT&&... args)
  {
    ResultT r;
    while(((r = func(args...)) < 0) && (errno == EINTR));
    return r;
  }

[[noreturn]]
int
do_print_help_and_exit(const char* self)
  {
    ::printf(
//        1         2         3         4         5         6         7     |
// 3456789012345678901234567890123456789012345678901234567890123456789012345|
"""""""""""""""""""""""""""""""""""""""""""""""""""""""""" R"'''''''''''''''(
Usage: %s [OPTIONS] [[--] DIRECTORY]

  -d      daemonize; detach from terminal and run in background
  -h      show help message then exit
  -V      show version information then exit
  -v      enable verbose mode

If DIRECTORY is specified, the working directory is switched there before
doing everything else.

Visit the homepage at <%s>.
Report bugs to <%s>.
)'''''''''''''''" """"""""""""""""""""""""""""""""""""""""""""""""""""""""+1,
// 3456789012345678901234567890123456789012345678901234567890123456789012345|
//        1         2         3         4         5         6         7     |
      self,
      PACKAGE_URL,
      PACKAGE_BUGREPORT);

    ::fflush(nullptr);
    ::quick_exit(0);
  }

[[noreturn]]
int
do_print_version_and_exit()
  {
    ::printf(
//        1         2         3         4         5         6         7     |
// 3456789012345678901234567890123456789012345678901234567890123456789012345|
"""""""""""""""""""""""""""""""""""""""""""""""""""""""""" R"'''''''''''''''(
%s (internal %s)

Visit the homepage at <%s>.
Report bugs to <%s>.
)'''''''''''''''" """"""""""""""""""""""""""""""""""""""""""""""""""""""""+1,
// 3456789012345678901234567890123456789012345678901234567890123456789012345|
//        1         2         3         4         5         6         7     |
      PACKAGE_STRING, POSEIDON_ABI_VERSION_STRING,
      PACKAGE_URL,
      PACKAGE_BUGREPORT);

    ::fflush(nullptr);
    ::quick_exit(0);
  }

// Define command-line options here.
struct Command_Line_Options
  {
    // options
    bool daemonize = false;
    bool verbose = false;

    // non-options
    string cd_here;
  };

// They are declared here for convenience.
Command_Line_Options cmdline;
unique_posix_fd daemon_pipe_wfd;

// These are process exit status codes.
enum Exit_Code : uint8_t
  {
    exit_success            = 0,
    exit_system_error       = 1,
    exit_invalid_argument   = 2,
  };

[[noreturn]] ROCKET_NEVER_INLINE
int
do_exit_printf(Exit_Code code, const char* fmt, ...) noexcept
  {
    // Wait for pending logs to be flushed.
    async_logger.synchronize();

    // Output the string to standard error.
    ::va_list ap;
    va_start(ap, fmt);
    ::vfprintf(stderr, fmt, ap);
    va_end(ap);

    // Perform fast exit.
    ::fflush(nullptr);
    ::quick_exit((int)code);
  }

ROCKET_NEVER_INLINE
void
do_parse_command_line(int argc, char** argv)
  {
    bool help = false;
    bool version = false;

    optional<bool> daemonize;
    optional<bool> verbose;
    optional<string> cd_here;

    if(argc > 1) {
      // Check for common long options before calling `getopt()`.
      if(::strcmp(argv[1], "--help") == 0)
        do_print_help_and_exit(argv[0]);

      if(::strcmp(argv[1], "--version") == 0)
        do_print_version_and_exit();
    }

    // Parse command-line options.
    int ch;
    while((ch = ::getopt(argc, argv, "dhVv")) != -1) {
      switch(ch) {
        case 'd':
          daemonize = true;
          break;

        case 'h':
          help = true;
          break;

        case 'V':
          version = true;
          break;

        case 'v':
          verbose = true;
          break;

        default:
          do_exit_printf(exit_invalid_argument,
              "%s: invalid argument -- '%c'\nTry `%s -h` for help.\n",
              argv[0], optopt, argv[0]);
      }
    }

    // Check for early exit conditions.
    if(help)
      do_print_help_and_exit(argv[0]);

    if(version)
      do_print_version_and_exit();

    // If more arguments follow, they denote the working directory.
    if(argc - optind > 1)
      do_exit_printf(exit_invalid_argument,
          "%s: too many arguments -- '%s'\nTry `%s -h` for help.\n",
          argv[0], argv[optind+1], argv[0]);

    if(argc - optind > 0)
      cd_here = string(argv[optind]);

    // Daemonization is off by default.
    if(daemonize)
      cmdline.daemonize = *daemonize;

    // Verbose mode is off by default.
    if(verbose)
      cmdline.verbose = *verbose;

    // The default working directory is empty which means 'do not switch'.
    if(cd_here)
      cmdline.cd_here = ::std::move(*cd_here);
  }

ROCKET_NEVER_INLINE
void
do_set_working_directory()
  {
    if(cmdline.cd_here.empty())
      return;

    if(::chdir(cmdline.cd_here.safe_c_str()) != 0)
      do_exit_printf(exit_system_error,
          "Could not set working directory to '%s': %s",
          cmdline.cd_here.c_str(), ::strerror(errno));
  }

ROCKET_NEVER_INLINE
void
do_daemonize_start()
  {
    if(!cmdline.daemonize)
      return;

    // Create a pipe so the parent process can check for startup errors.
    unique_posix_fd rfd, wfd;
    int pipe_fds[2];
    if(::pipe(pipe_fds) != 0)
      POSEIDON_THROW((
          "Could not create pipe",
          "[`pipe()` failed: $1]"),
          format_errno());

    rfd.reset(pipe_fds[0]);
    wfd.reset(pipe_fds[1]);

    // Create the child process that will run in background.
    ::fprintf(stderr,
        "Daemonizing process %d...\n",
        (int) ::getpid());

    int child_pid = ::fork();
    if(child_pid == -1)
      POSEIDON_THROW((
          "Could not create child process",
          "[`fork()` failed: $1]"),
          format_errno());

    if(child_pid == 0) {
      // The child process shall continue execution.
      ::setsid();
      daemon_pipe_wfd = ::std::move(wfd);
      return;
    }

    ::fprintf(stderr,
        "Awaiting child process %d...\n",
        child_pid);

    // This is the parent process. Wait for notification.
    char discard[4];
    wfd.reset();
    if(do_syscall(::read, rfd, discard, sizeof(discard)) > 0)
      do_exit_printf(exit_success,
          "Detached child process %d successfully.\n",
          child_pid);

    // Something went wrong. Now wait for the child and forward its exit
    // status. Note `waitpid()` may also return if the child has been stopped
    // or continued.
    int wstat = 0;
    if(do_syscall(::waitpid, child_pid, &wstat, 0) < 0)
      do_exit_printf(exit_system_error,
          "Failed to await child process %d: %m\n",
          child_pid);

    if(WIFEXITED(wstat))
      do_exit_printf(static_cast<Exit_Code>(WEXITSTATUS(wstat)),
          "Child process %d exited with %d\n",
          child_pid, WEXITSTATUS(wstat));

    if(WIFSIGNALED(wstat))
      do_exit_printf(static_cast<Exit_Code>(128 + WTERMSIG(wstat)),
          "Child process %d terminated by signal %d: %s\n",
          child_pid, WTERMSIG(wstat), ::strsignal(WTERMSIG(wstat)));

    ::quick_exit(-1);
  }

ROCKET_NEVER_INLINE
void
do_daemonize_finish()
  {
    if(!daemon_pipe_wfd)
      return;

    // Notify the parent process. Errors are ignored.
    do_syscall(::write, daemon_pipe_wfd, "OK", 2U);
    daemon_pipe_wfd.reset();
  }

template<class ObjectT>
void
do_create_resident_thread(ObjectT& obj, const char* name)
  {
    auto thrd_function = +[](void* ptr) noexcept
      {
        // Set thread information. Errors are ignored.
        int oldst;
        ::pthread_setcancelstate(PTHREAD_CANCEL_DISABLE, &oldst);

        ::sigset_t sigset;
        ::sigemptyset(&sigset);
        ::sigaddset(&sigset, SIGINT);
        ::sigaddset(&sigset, SIGTERM);
        ::sigaddset(&sigset, SIGHUP);
        ::sigaddset(&sigset, SIGALRM);
        ::pthread_sigmask(SIG_BLOCK, &sigset, nullptr);

        // Enter an infinite loop.
        for(;;)
          try {
            ((ObjectT*) ptr)->thread_loop();
          }
          catch(exception& stdex) {
            ::fprintf(stderr,
                "WARNING: Caught an exception from thread loop: %s\n"
                "[static class `%s`]\n"
                "[exception class `%s`]\n",
                stdex.what(), typeid(ObjectT).name(), typeid(stdex).name());
          }

        // Make the return type deducible.
        return (void*) nullptr;
      };

    ::pthread_t thrd;
    int err = ::pthread_create(&thrd, nullptr, thrd_function, ::std::addressof(obj));
    if(err != 0)
      do_exit_printf(exit_system_error,
          "Could not create thread '%s': %s\n",
          name, ::strerror(err));

    // Name the thread and detach it. Errors are ignored.
    ::pthread_setname_np(thrd, name);
    ::pthread_detach(thrd);
  }

ROCKET_NEVER_INLINE
void
do_create_threads()
  {
    do_create_resident_thread(async_logger, "logger");
    do_create_resident_thread(timer_driver, "timer");
    do_create_resident_thread(async_task_executor, "task1");
    do_create_resident_thread(async_task_executor, "task2");
    do_create_resident_thread(async_task_executor, "task3");
    do_create_resident_thread(async_task_executor, "task4");
    do_create_resident_thread(network_driver, "network");
  }

ROCKET_NEVER_INLINE
void
do_check_euid()
  {
    bool permit_root_startup = false;
    const auto conf = main_config.copy();

    auto value = conf.query("general", "permit_root_startup");
    if(value.is_boolean())
      permit_root_startup = value.as_boolean();
    else if(!value.is_null())
      POSEIDON_LOG_WARN((
          "Ignoring `general.permit_root_startup`: expecting a `boolean`, got `$1`",
          "[in configuration file '$2']"),
          value, conf.path());

    if(!permit_root_startup && (::geteuid() == 0))
      do_exit_printf(exit_invalid_argument,
          "Please do not start this program as root. If you insist, you may "
          "set `general.permit_root_startup` in '%s' to `true` to bypass this "
          "check. Note that starting as root should be considered insecure. An "
          "unprivileged user should have been created for this service. You "
          "have been warned.",
          conf.path().c_str());
  }

ROCKET_NEVER_INLINE
void
do_init_signal_handlers()
  {
    // Ignore some signals for good.
    struct ::sigaction sigact;
    ::sigemptyset(&(sigact.sa_mask));
    sigact.sa_flags = 0;
    sigact.sa_handler = SIG_IGN;
    ::sigaction(SIGPIPE, &sigact, nullptr);

    if(cmdline.daemonize)
      ::sigaction(SIGHUP, &sigact, nullptr);

    // Trap signals. Errors are ignored.
    sigact.sa_handler = +[](int n) { exit_signal.store(n);  };
    ::sigaction(SIGINT, &sigact, nullptr);
    ::sigaction(SIGTERM, &sigact, nullptr);
    ::sigaction(SIGALRM, &sigact, nullptr);
  }

ROCKET_NEVER_INLINE
void
do_write_pid_file()
  {
    string pid_file_path;
    const auto conf = main_config.copy();

    auto value = conf.query("general", "pid_file_path");
    if(value.is_string())
      pid_file_path = value.as_string();
    else if(!value.is_null())
      POSEIDON_LOG_WARN((
          "Ignoring `general.permit_root_startup`: expecting a `string`, got `$1`",
          "[in configuration file '$2']"),
          value, conf.path());

    if(pid_file_path.empty())
      return;

    // Create the lock file and lock it in exclusive mode before overwriting.
    unique_posix_fd pid_file(::creat(pid_file_path.safe_c_str(), 0644));
    if(!pid_file)
      do_exit_printf(exit_system_error,
          "Could not create PID file '%s': %s",
          pid_file_path.c_str(), ::strerror(errno));

    if(::flock(pid_file, LOCK_EX | LOCK_NB) != 0)
      do_exit_printf(exit_system_error,
          "Could not lock PID file '%s': %s",
          pid_file_path.c_str(), ::strerror(errno));

    // Write the PID of myself.
    POSEIDON_LOG_DEBUG(("Writing current process ID to '$1'"), pid_file_path.c_str());
    ::dprintf(pid_file, "%d\n", (int) ::getpid());

    // Downgrade the lock so the PID may be read by others.
    ::flock(pid_file, LOCK_SH);
  }

ROCKET_NEVER_INLINE
void
do_check_ulimits()
  {
    ::rlimit rlim;
    if((::getrlimit(RLIMIT_CORE, &rlim) == 0) && (rlim.rlim_cur <= 0))
      POSEIDON_LOG_WARN((
          "Core dumps have been disabled. We highly suggest you enable them in case "
          "of crashes. See `/etc/security/limits.conf` for details."));

    if((::getrlimit(RLIMIT_NOFILE, &rlim) == 0) && (rlim.rlim_cur <= 10'000))
      POSEIDON_LOG_WARN((
          "The limit of number of open files (which is `$1`) is too low. This might "
          "result in denial of service when there are too many simultaneous network "
          "connections. We suggest you set it to least `10000` for production use. "
          "See `/etc/security/limits.conf` for details."),
          rlim.rlim_cur);
  }

ROCKET_NEVER_INLINE
void
do_load_addons()
  {
    cow_vector<::asteria::Value> addons;
    size_t count = 0;
    const auto conf = main_config.copy();

    auto value = conf.query("addons");
    if(value.is_array())
      addons = value.as_array();
    else if(!value.is_null())
      POSEIDON_LOG_WARN((
          "Ignoring `addons`: expecting an `array`, got `$1`",
          "[in configuration file '$2']"),
          value, conf.path());

    for(const auto& addon : addons) {
      string path;

      if(addon.is_string())
        path = addon.as_string();
      else if(!addon.is_null())
        POSEIDON_LOG_WARN((
            "Ignoring invalid path to add-on: $1",
            "[in configuration file '$2']"),
            addon, conf.path());

      if(path.empty())
        continue;

      POSEIDON_LOG_INFO(("Loading add-on: $1"), path);

      if(::dlopen(path.safe_c_str(), RTLD_NOW | RTLD_NODELETE))
        count ++;
      else
        POSEIDON_LOG_ERROR((
            "Failed to load add-on: $1",
            "[`dlopen()` failed: $2]"),
            path, ::dlerror());

      POSEIDON_LOG_INFO(("Finished loading add-on: $1"), path);
    }

    if(count == 0)
      POSEIDON_LOG_FATAL(("No add-on has been loaded. What's the job now?"));
  }

}  // namespace

int
main(int argc, char** argv)
  try {
    // Select the C locale.
    // UTF-8 is required for wide-oriented standard streams.
    ::setlocale(LC_ALL, "C.UTF-8");
    ::tzset();
    ::pthread_setname_np(::pthread_self(), PACKAGE);
    ::pthread_setcancelstate(PTHREAD_CANCEL_DISABLE, &::opterr);
    ::srandom((uint32_t) ::clock());

    // Note that this function shall not return in case of errors.
    do_parse_command_line(argc, argv);
    do_set_working_directory();
    do_daemonize_start();
    main_config.reload();
    POSEIDON_LOG_INFO(("Starting up: $1"), PACKAGE_STRING);

    async_logger.reload(main_config.copy());
    fiber_scheduler.reload(main_config.copy());
    network_driver.reload(main_config.copy());
    do_init_signal_handlers();
    do_write_pid_file();
    do_create_threads();
    do_check_euid();
    do_check_ulimits();
    do_load_addons();

    POSEIDON_LOG_INFO(("Startup complete: $1"), PACKAGE_STRING);
    do_daemonize_finish();

    // Schedule fibers if there is something to do, or no stop signal has
    // been received.
    while((fiber_scheduler.size() != 0) || (exit_signal.load() == 0))
      fiber_scheduler.thread_loop();

    int sig = exit_signal.load();
    POSEIDON_LOG_INFO(("Shutting down (signal $1: $2)"), sig, ::strsignal(sig));

    do_exit_printf(exit_success, "");
  }
  catch(exception& stdex) {
    // Print the message in `stdex`. There isn't much we can do.
    do_exit_printf(exit_system_error,
        "%s\n[exception class `%s`]\n",
        stdex.what(), typeid(stdex).name());
  }
