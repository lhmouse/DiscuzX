<?php

class app_home_switch {
	public static function getModules() {
		return ['follow', 'feed', 'blog', 'friend', 'album', 'share', 'doing', 'wall', 'task', 'medal', 'magic', 'favorite'];
	}

}

class app_home_switch_follow {

	const Icon = STATICURL.'image/feed/follow_b.png';
	const Name = 'setting_functions_curscript_follow';
	const Desc = 'setting_functions_curscript_follow_intro';

	public static function getStatus() {
		return getglobal('setting/followstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=follow" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_feed {

	const Icon = STATICURL.'image/feed/feed_b.png';
	const Name = 'setting_functions_curscript_feed';
	const Desc = 'setting_functions_curscript_feed_intro';

	public static function getStatus() {
		return getglobal('setting/feedstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=feed" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_blog {

	const Icon = STATICURL.'image/feed/blog_b.png';
	const Name = 'setting_functions_curscript_blog';
	const Desc = 'setting_functions_curscript_blog_intro';

	public static function getStatus() {
		return getglobal('setting/blogstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=blog" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_friend {

	const Icon = STATICURL.'image/feed/friend_b.png';
	const Name = 'setting_functions_curscript_friend';
	const Desc = 'setting_functions_curscript_friend_intro';

	public static function getStatus() {
		return getglobal('setting/friendstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=friend" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_album {

	const Icon = STATICURL.'image/feed/album_b.png';
	const Name = 'setting_functions_curscript_album';
	const Desc = 'setting_functions_curscript_album_intro';

	public static function getStatus() {
		return getglobal('setting/albumstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=album" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_share {

	const Icon = STATICURL.'image/feed/share_b.png';
	const Name = 'setting_functions_curscript_share';
	const Desc = 'setting_functions_curscript_share_intro';

	public static function getStatus() {
		return getglobal('setting/sharestatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=share" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_doing {

	const Icon = STATICURL.'image/feed/doing_b.png';
	const Name = 'setting_functions_curscript_doing';
	const Desc = 'setting_functions_curscript_doing_intro';

	public static function getStatus() {
		return getglobal('setting/doingstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=doing" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_wall {

	const Icon = STATICURL.'image/feed/wall_b.png';
	const Name = 'setting_functions_curscript_message';
	const Desc = 'setting_functions_curscript_message_intro';

	public static function getStatus() {
		return getglobal('setting/wallstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=wall" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_task {

	const Icon = STATICURL.'image/feed/task_b.png';
	const Name = 'setting_functions_curscript_task';
	const Desc = 'setting_functions_curscript_task_intro';

	public static function getStatus() {
		return getglobal('setting/taskstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=task" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_medal {

	const Icon = STATICURL.'image/feed/medal_b.png';
	const Name = 'setting_functions_curscript_medal';
	const Desc = 'setting_functions_curscript_medal_intro';

	public static function getStatus() {
		return getglobal('setting/medalstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=medal" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_magic {

	const Icon = STATICURL.'image/feed/magic_b.png';
	const Name = 'setting_functions_curscript_magic';
	const Desc = 'setting_functions_curscript_magic_intro';

	public static function getStatus() {
		return getglobal('setting/magicstatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=magic" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}

class app_home_switch_favorite {

	const Icon = STATICURL.'image/feed/favorite_b.png';
	const Name = 'setting_functions_curscript_favorite';
	const Desc = 'setting_functions_curscript_favorite_intro';

	public static function getStatus() {
		return getglobal('setting/favoritestatus');
	}

	public static function getOptions() {
		return '<a href="'.ADMINSCRIPT.'?action=misc&operation=setnav&do='.(self::getStatus() ? 'close' : 'open').'&type=favorite" onclick="showWindow(\'setnav\', this.href, \'get\', 0);return false;">'.(self::getStatus() ? cplang('setting_functions_curscript_close') : cplang('setting_functions_curscript_open')).'</a>';
	}

}
