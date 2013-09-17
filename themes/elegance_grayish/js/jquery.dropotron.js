/* dropotron v1.3.2 | (c) n33 | n33.co @n33co | MIT + GPLv2 */

(function(jQuery) {

	jQuery.fn.disableSelection_dropotron = function() { return jQuery(this).css('user-select', 'none').css('-khtml-user-select', 'none').css('-moz-user-select', 'none').css('-o-user-select', 'none').css('-webkit-user-select', 'none'); }

	jQuery.fn.dropotron = function(options) {
		var settings = jQuery.extend({
			selectorParent:		jQuery(this)
		}, options);
		return jQuery.dropotron(settings);
	}

	jQuery.dropotron = function(options) {

		// Settings
			var settings = jQuery.extend({
				selectorParent:			null,				// The parent jQuery object
				baseZIndex:				1000,				// Base Z-Index
				menuClass:				'dropotron',		// Menu class (assigned to every UL)
				expandMode:				'hover',			// Expansion mode ("hover" or "click")
				hoverDelay:				150,				// Hover delay (in ms)
				hideDelay:				250,				// Hide delay (in ms; 0 disables)
				openerClass:			'opener',			// Opener class
				openerActiveClass:		'active',			// Active opener class
				submenuClassPrefix:		'dropotron-level-',	// Submenu class prefix
				mode:					'slide',		    // Menu mode ("instant", "fade", "slide", "zoom")
				speed:					'fast',				// Menu speed ("fast", "slow", or ms)
				easing:					'swing',			// Easing mode ("swing", ???)
				alignment:				'left',				// Alignment ("left", "center", "right")
				offsetX:				0,					// Submenu offset X
				offsetY:				0,					// Submenu offset Y
				globalOffsetY:			0,					// Global offset Y
				IEOffsetX:				0,					// IE Offset X
				IEOffsetY:				0,					// IE Offset Y
				noOpenerFade:			false,				// If true, when in "fade" mode the top-level opener will not fade with the menu
				detach:					true,				// Detach second level menus (to prevent parent styling from bleeding through)
				cloneOnDetach:			true				// If true, when detaching second level menus, leave a copy behind
			}, options);

		// Variables
			var _top = settings.selectorParent, _menus = _top.find('ul');
			var _window = jQuery('html');
			var isLocked = false, hoverTimeoutId = null, hideTimeoutId = null;
			var _isTouch = !!('ontouchstart' in window), _eventType = 'click';

		// Main
			_top
				.bind('doCollapseAll', function() {
					_menus
						.trigger('doCollapse');
				});

			_menus.each(function() {
				var menu = jQuery(this), opener = menu.parent();

				if (settings.hideDelay > 0)
					menu.add(opener)
						.mouseleave(function(e) {
							window.clearTimeout(hideTimeoutId);
							hideTimeoutId = window.setTimeout(function() {
								menu.trigger('doCollapse');
							}, settings.hideDelay);
						});

				menu
					.disableSelection_dropotron()
					.hide()
					.addClass(settings.menuClass)
					.css('position', 'absolute')
					.mouseenter(function(e) {
						window.clearTimeout(hideTimeoutId);
					})
					.bind('doExpand', function() {
						
						if (menu.is(':visible'))
							return false;

						window.clearTimeout(hideTimeoutId);
						
						_menus.each(function() {
							var t = jQuery(this);
							if (!jQuery.contains(t.get(0), opener.get(0)))
								t.trigger('doCollapse');
						});
						
						var x, c, left, top, isTL = (menu.css('z-index') == settings.baseZIndex), oo = opener.offset(), op = opener.position(), opp = opener.parent().position(), ow = opener.outerWidth(), mw = menu.outerWidth();
						
						if (isTL)
						{
							if (!settings.detach)
								x = op;
							else
								x = oo;
						
							top = x.top + opener.outerHeight() + settings.globalOffsetY;
							c = settings.alignment;
							
							menu
								.removeClass('left')
								.removeClass('right')
								.removeClass('center');

							switch (settings.alignment)
							{
								case 'right':
									left = x.left - mw + ow;
									
									if (left < 0)
									{
										left = x.left;
										c = 'left';
									}
										
									break;
									
								case 'center':
									left = x.left - Math.floor((mw - ow) / 2);

									if (left < 0)
									{
										left = x.left;
										c = 'left';
									}
									else if (left + mw > _window.width())
									{
										left = x.left - mw + ow;
										c = 'right';
									}
										
									break;

								case 'left':
								default:
									left = x.left;
									
									if (left + mw > _window.width())
									{
										left = x.left - mw + ow;
										c = 'right';
									}

									break;
							}
							
							menu.addClass(c);
						}
						else
						{
							// Non-static position fix
								if (opener.css('position') == 'relative'
								||	opener.css('position') == 'absolute')
								{
									top = settings.offsetY;
									left = (-1 * op.left);
								}
								else
								{
									top = op.top + settings.offsetY;
									left = 0;
								}

							switch (settings.alignment)
							{
								case 'right':
									left += (-1 * opener.parent().outerWidth()) + settings.offsetX;
									
									break;
								
								case 'center':
								case 'left':
								default:
									left += opener.parent().outerWidth() + settings.offsetX;

									break;
							}
						}

						if (navigator.userAgent.match(/MSIE ([0-9]+)\./) && RegExp.$1 < 8)
						{
							left += settings.IEOffsetX;
							top += settings.IEOffsetY;
						}

						menu
							.css('left', left + 'px')
							.css('top', top + 'px');

						menu.css('opacity', '0.01').show();
						
						// Kludge!
							var tmp = false;
							
							// Non-static position fix
								if (opener.css('position') == 'relative'
								||	opener.css('position') == 'absolute')
									left = (-1 * op.left);
								else
									left = 0;
							
							if (menu.offset().left < 0)
							{
								left += opener.parent().outerWidth() - settings.offsetX;
								tmp = true;
							}
							else if (menu.offset().left + mw > _window.width())
							{
								left += (-1 * opener.parent().outerWidth()) - settings.offsetX;
								tmp = true;
							}

							if (tmp)
								menu.css('left', left + 'px');

							menu.hide().css('opacity', '1');

						switch (settings.mode)
						{
							case 'zoom':

								isLocked = true;

								opener.addClass(settings.openerActiveClass);
								menu.animate({
									width: 'toggle',
									height: 'toggle'
								}, settings.speed, settings.easing, function() {
									isLocked = false;
								});

								break;
						
							case 'slide':

								isLocked = true;

								opener.addClass(settings.openerActiveClass);
								menu.animate({ height: 'toggle' }, settings.speed, settings.easing, function() {
									isLocked = false;
								});

								break;
						
							case 'fade':

								isLocked = true;
								
								if (isTL && !settings.noOpenerFade)
								{
									var tmp;

									if (settings.speed == 'slow')
										tmp = 80;
									else if (settings.speed == 'fast')
										tmp = 40;
									else
										tmp = Math.floor(settings.speed / 2);
									
									opener.fadeTo(tmp, 0.01, function() {
										opener.addClass(settings.openerActiveClass);
										opener.fadeTo(settings.speed, 1);
										menu.fadeIn(settings.speed, function() {
											isLocked = false;
										});
									});
								}
								else
								{
									opener.addClass(settings.openerActiveClass);
									opener.fadeTo(settings.speed, 1);
									menu.fadeIn(settings.speed, function() {
										isLocked = false;
									});
								}

								break;
								
							case 'instant':
							default:

								opener.addClass(settings.openerActiveClass);
								menu.show();

								break;
						}

						return false;
					})
					.bind('doCollapse', function() {
						
						if (!menu.is(':visible'))
							return false;

						menu.hide();
						opener.removeClass(settings.openerActiveClass);
						menu.find('.' + settings.openerActiveClass).removeClass(settings.openerActiveClass);
						menu.find('ul').hide();
						
						return false;

					})
					.bind('doToggle', function(e) {
					
						if (menu.is(':visible'))
							menu.trigger('doCollapse');
						else
							menu.trigger('doExpand');
					
						return false;

					});
					
				opener
					.disableSelection_dropotron()
					.addClass('opener')
					.css('cursor', 'pointer')
					.bind(_eventType, function(e) {
					
						if (isLocked)
							return;
						
						e.preventDefault();
						e.stopPropagation();
						menu.trigger('doToggle');
					
					});

				if (settings.expandMode == 'hover')
					opener.hover(function(e) {
							if (isLocked)	
								return;
							hoverTimeoutId = window.setTimeout(function() {
								menu.trigger('doExpand');
							}, settings.hoverDelay);
						},
						function (e) {
							window.clearTimeout(hoverTimeoutId);
						}
					);
			});

			_menus.find('a')
				.css('display', 'block')
				.click(function(e) {

					if (isLocked)
						return;
					
					_top.trigger('doCollapseAll');

					e.stopPropagation();

					if (jQuery(this).attr('href').length < 1)
						e.preventDefault();

				});
				
			_top.find('li')
				.css('white-space', 'nowrap')
				.each(function() {
					var t = jQuery(this), a = t.children('a'), ul = t.children('ul');
					
					a.click(function(e) {
						if (jQuery(this).attr('href').length < 1)
							e.preventDefault();
						else
							e.stopPropagation();
					});
					
					if (a.length > 0 && ul.length == 0)
						t.click(function(e) {

							if (isLocked)
								return;
								
							_top.trigger('doCollapseAll');

							e.stopPropagation();

						});
				});

			_top.children('li').each(function() {

				var opener = jQuery(this), menu = opener.children('ul'), c;

				if (menu.length > 0)
				{
					if (settings.detach)
					{
						if (settings.cloneOnDetach)
						{
							c = menu.clone();
							
							c
								.attr('class', '')
								.hide()
								.appendTo(menu.parent());
						}
					
						menu
							.detach()
							.appendTo('body');
					}

					for(var z = settings.baseZIndex, i = 1, y = menu; y.length > 0; i++)
					{
						y.css('z-index', z++);
						
						if (settings.submenuClassPrefix)
							y.addClass(settings.submenuClassPrefix + (z - 1 - settings.baseZIndex));
						
						y = y.find('> li > ul');
					}
				}

			});
			
			_window
				.click(function() {
					if (!isLocked)
						_top.trigger('doCollapseAll');
				})
				.keypress(function(e) {
					if (!isLocked
					&&	e.keyCode == 27) {
						e.preventDefault();
						_top.trigger('doCollapseAll');
					}
				});
	};

})(jQuery);