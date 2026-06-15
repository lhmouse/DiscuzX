/**
 * Admin Index Widget Drag & Drop
 * Drag from boxheader, hide/show toggle, cross-column, AJAX save
 */
(function() {
	'use strict';

	var userBar = document.getElementById('user_bar');
	if(userBar) {
		userBar.addEventListener('mouseover', function(e) {
			showMenu('user_bar');
		});
	}
	var userBarMenu = document.getElementById('user_bar_menu');
	if(!userBarMenu) {
		var allToolbars = document.querySelectorAll('.widget-toolbar');
		allToolbars.forEach(function(toolbar) {
			toolbar.style.display = 'none';
		});
		return;
	}

	var leftCol = document.getElementById('show_widgets_left');
	var rightCol = document.getElementById('show_widgets_right');
	if (!leftCol || !rightCol) return;

	var saveTimer = null;

	// ── Init: inject toggle buttons into boxheader, set draggable ──
	var allWrappers = document.querySelectorAll('.widget-wrapper');
	allWrappers.forEach(function(wrapper) {
		var toolbar = wrapper.querySelector('.widget-toolbar');
		var boxheader = wrapper.querySelector('.boxheader');

		if (boxheader) {
			wrapper.classList.add('has-boxheader');
			boxheader.setAttribute('draggable', 'true');

			if (toolbar && !boxheader.querySelector('.widget-toggle')) {
				var toggle = toolbar.querySelector('.widget-toggle');
				if (toggle) {
					boxheader.appendChild(toggle);
				}
			}

			// Group badge + toggle together for flex-layout boxheaders (e.g. todobox)
			var badge = boxheader.querySelector('.todo-badge');
			var toggleBtn = boxheader.querySelector('.widget-toggle');
		} else if (toolbar) {
			toolbar.setAttribute('draggable', 'true');
		} else {
			wrapper.setAttribute('draggable', 'true');
		}
	});

	// ── Drag & Drop ──

	document.addEventListener('dragstart', function(e) {
		var boxheader = e.target.closest('.boxheader');
		var toolbar = e.target.closest('.widget-toolbar');
		var handle = boxheader || toolbar;

		if (!handle) return;

		// Don't initiate drag from toggle button
		if (e.target.closest('.widget-toggle')) {
			e.preventDefault();
			return;
		}

		var wrapper = handle.closest('.widget-wrapper');
		if (!wrapper) return;

		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData('text/plain', '');
		wrapper.classList.add('dragging');
	});

	document.addEventListener('dragend', function(e) {
		var wrapper = document.querySelector('.widget-wrapper.dragging');
		if (wrapper) {
			wrapper.classList.remove('dragging');
		}
		clearDropIndicators();
	});

	[leftCol, rightCol].forEach(function(col) {
		col.addEventListener('dragover', function(e) {
			e.preventDefault();
			e.dataTransfer.dropEffect = 'move';

			var dragging = document.querySelector('.widget-wrapper.dragging');
			if (!dragging) return;

			var target = getDropTarget(col, e.clientY);
			clearDropIndicators();

			if (target && target !== dragging) {
				var rect = target.getBoundingClientRect();
				var midY = rect.top + rect.height / 2;
				if (e.clientY < midY) {
					target.classList.add('drop-before');
				} else {
					target.classList.add('drop-after');
				}
			} else if (!target || target === dragging) {
				col.classList.add('drop-target');
			}
		});

		col.addEventListener('dragleave', function(e) {
			if (!col.contains(e.relatedTarget)) {
				col.classList.remove('drop-target');
			}
		});

		col.addEventListener('drop', function(e) {
			e.preventDefault();
			var dragging = document.querySelector('.widget-wrapper.dragging');
			if (!dragging) return;

			var target = getDropTarget(col, e.clientY);

			if (target && target !== dragging) {
				var rect = target.getBoundingClientRect();
				var midY = rect.top + rect.height / 2;
				if (e.clientY < midY) {
					target.parentNode.insertBefore(dragging, target);
				} else {
					target.parentNode.insertBefore(dragging, target.nextSibling);
				}
			} else if (!target) {
				col.appendChild(dragging);
			}

			dragging.classList.remove('dragging');
			clearDropIndicators();

			var newType = col === leftCol ? 'left' : 'right';
			dragging.setAttribute('data-type', newType);

			debounceSave();
		});
	});

	// ── Hide/Show Toggle ──

	// Prevent mousedown on toggle from triggering drag on boxheader
	document.addEventListener('mousedown', function(e) {
		if (e.target.closest('.widget-toggle')) {
			e.stopPropagation();
		}
	});

	document.addEventListener('click', function(e) {
		var toggleBtn = e.target.closest('.widget-toggle');
		if (!toggleBtn) return;

		var wrapper = toggleBtn.closest('.widget-wrapper');
		if (!wrapper) return;

		// Find data attributes from toolbar (which has data-title-hide/show)
		var toolbar = wrapper.querySelector('.widget-toolbar');
		var titleHide = (toolbar ? toolbar.getAttribute('data-title-hide') : null) || 'Hide';
		var titleShow = (toolbar ? toolbar.getAttribute('data-title-show') : null) || 'Show';

		var isHidden = wrapper.hasAttribute('data-hidden');
		if (isHidden) {
			wrapper.removeAttribute('data-hidden');
			wrapper.classList.remove('widget-hidden');
			toggleBtn.innerHTML = '&#x25CE;';
			toggleBtn.title = titleHide;
		} else {
			wrapper.setAttribute('data-hidden', '1');
			wrapper.classList.add('widget-hidden');
			toggleBtn.innerHTML = '&#x25C9;';
			toggleBtn.title = titleShow;
		}

		debounceSave();
	});

	// ── Reset Button ──

	var resetBtn = document.getElementById('widget_reset');
	if (resetBtn) {
		resetBtn.addEventListener('click', function() {
			if (!confirm(WIDGET_RESET_CONFIRM || 'Reset to default layout?')) return;

			var url = WIDGET_AJAX_URL + '&ajax_action=reset&formhash=' + WIDGET_FORMHASH;
			fetch(url, {
				method: 'POST',
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			})
			.then(function(r) { return r.json(); })
			.then(function(data) {
				if (data.error === 0) {
					location.reload();
				}
			})
			.catch(function() {
				alert('Reset failed');
			});
		});
	}

	// ── Show Hidden Button ──
	var showHiddenBtn = document.getElementById('widget_showhidden');
	if (showHiddenBtn) {
		showHiddenBtn.addEventListener('click', function() {
			leftCol.querySelectorAll('.widget-hidden').forEach(function(w) {
				w.classList.toggle('widget-invisible');
			});
			rightCol.querySelectorAll('.widget-hidden').forEach(function(w) {
				w.classList.toggle('widget-invisible');
			});
		});
	}

	// ── Helper Functions ──
	function getDropTarget(col, y) {
		var wrappers = col.querySelectorAll('.widget-wrapper:not(.dragging)');
		var closest = null;
		var closestDist = Infinity;

		for (var i = 0; i < wrappers.length; i++) {
			var rect = wrappers[i].getBoundingClientRect();
			var midY = rect.top + rect.height / 2;
			var dist = Math.abs(y - midY);
			if (dist < closestDist) {
				closestDist = dist;
				closest = wrappers[i];
			}
		}

		return closestDist < 200 ? closest : null;
	}

	function clearDropIndicators() {
		document.querySelectorAll('.drop-before, .drop-after').forEach(function(el) {
			el.classList.remove('drop-before', 'drop-after');
		});
		document.querySelectorAll('.drop-target').forEach(function(el) {
			el.classList.remove('drop-target');
		});
	}

	function collectState() {
		var order = { left: [], right: [] };
		var hidden = { left: [], right: [] };

		leftCol.querySelectorAll('.widget-wrapper').forEach(function(w) {
			var name = w.getAttribute('data-widget');
			order.left.push(name);
			if (w.hasAttribute('data-hidden')) {
				hidden.left.push(name);
			}
		});

		rightCol.querySelectorAll('.widget-wrapper').forEach(function(w) {
			var name = w.getAttribute('data-widget');
			order.right.push(name);
			if (w.hasAttribute('data-hidden')) {
				hidden.right.push(name);
			}
		});

		return { order: order, hidden: hidden };
	}

	function debounceSave() {
		clearTimeout(saveTimer);
		saveTimer = setTimeout(saveState, 500);
	}

	function saveState() {
		var state = collectState();
		var url = WIDGET_AJAX_URL + '&ajax_action=save&formhash=' + WIDGET_FORMHASH;

		fetch(url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: JSON.stringify(state)
		})
		.then(function(r) { return r.json(); })
		.then(function(data) {
			// Save complete
		})
		.catch(function() {
			// Silently fail
		});
	}

})();
