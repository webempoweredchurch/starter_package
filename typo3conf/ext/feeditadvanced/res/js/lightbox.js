Ext.ns('Ext.ux');

Ext.ux.Lightbox = (function(){
	var els = {},
		urls = [],
		activeUrl,
		urlIsSet,
		initialized = false,
		selectors = [],
		width = 400,
		height = 300,
		closeOnSubmit = false;

	return {
		overlayOpacity: 0.5,
		animate: true,
		resizeSpeed: 8,
		borderSize: 10,

		init: function() {
			this.resizeDuration = this.animate ? ((11 - this.resizeSpeed) * 0.15) : 0;
			this.overlayDuration = this.animate ? 0.2 : 0;

			if(!initialized) {
				Ext.apply(this, Ext.util.Observable.prototype);
				Ext.util.Observable.constructor.call(this);
				this.addEvents('open', 'close');
				this.initMarkup();
				
				els.shim.on('load', this.shimLoaded, this);
				initialized = true;
			}
		},

		initMarkup: function() {
			els.overlay = Ext.DomHelper.append(document.body, {
				id: 'ux-lightbox-overlay'
			}, true);

			var lightboxTpl = new Ext.Template(this.getTemplate());
			els.lightbox = lightboxTpl.append(document.body, {}, true);

			els.shim = Ext.DomHelper.append(Ext.fly('ux-lightbox-content'), {
				tag: 'iframe',
				id: 'ux-lightbox-shim',
				name: 'ux-lightbox-shim'
			}, true);
			
			els.loading = Ext.DomHelper.append(Ext.fly('ux-lightbox-content'), {
				tag: 'div',
				id: 'ux-lightbox-loading'
			}, true);
			
			els.msg = Ext.DomHelper.append(Ext.fly('ux-lightbox-content'), {
				tag: 'div',
				id: 'ux-lightbox-msg'
			}, true);

			var ids = ['wrapper', 'content', 'loading', 'header'];

			Ext.each(ids, function(id){
				els[id] = Ext.get('ux-lightbox-' + id);
			});

			Ext.each([els.overlay, els.lightbox, els.shim, els.header, els.loading], function(el){
				el.setVisibilityMode(Ext.Element.DISPLAY);
				el.hide();
			});

			var size = (this.animate ? 250 : 1) + 'px';
			els.wrapper.setStyle({
				width: size,
				height: size
			});
		},

		getTemplate : function() {
			return [
				'<div id="ux-lightbox">',
					'<div id="ux-lightbox-wrapper">',
						'<div id="ux-lightbox-header" style="display:none;">',
						'</div>',
						'<div id="ux-lightbox-content">',
						'</div>',
					'</div>',
				'</div>'
			];
		},

		register: function(sel, group) {
			if(selectors.indexOf(sel) === -1) {
				selectors.push(sel);

				Ext.fly(document).on('click', function(ev){
					var target = ev.getTarget(sel);

					if (target) {
						ev.preventDefault();
						this.open(target, sel, group);
					}
				}, this);
			}
		},

		registerUrl: function(sel, width, height) {
			if(selectors.indexOf(sel) === -1) {
				selectors.push(sel);

				Ext.fly(document).on('click', function(ev){
					var target = ev.getTarget(sel);

					if (target) {
						ev.preventDefault();
						this.openUrl(target, width, height);
					}
				}, this);
			}
		},

		openUrl: function(options, fWidth, fHeight) {
			els.shim.dom.src = '';
			this.setViewSize();
			els.overlay.fadeIn({
				duration: this.overlayDuration,
				endOpacity: this.overlayOpacity,
				callback: function() {
					urls = [];

					var index = 0;
					urls.push([options.href, options.title]);


					// calculate top and left offset for the lightbox
					var pageScroll = Ext.fly(document).getScroll();

					var lightboxTop = pageScroll.top + (Ext.lib.Dom.getViewportHeight() / 10);
					var lightboxLeft = pageScroll.left;

					maxHeight = Ext.lib.Dom.getViewportHeight() * .80;
					maxWidth = Ext.lib.Dom.getViewportWidth() * .80;

					if (fHeight > maxHeight) {
						shimHeight = maxHeight;
					} else {
						shimHeight = fHeight;
					}
					// 30 pixels accounts for the content header above the edit window.
					shimHeight -= 30;

					if (fWidth > maxWidth) {
						shimWidth = maxWidth;
					} else {
						shimWidth = fWidth;
					}

					els.lightbox.setStyle({
						top: lightboxTop + 'px',
						left: lightboxLeft + 'px'
					}).show();
					els.shim.setStyle({
						width: shimWidth + 'px',
						height: shimHeight + 'px',
						alpha:	'(opacity=100)'
					});
					this.setUrl(index, shimWidth, shimHeight);
					els.header.update('<h3>' + options.title + '</h3>');

					this.fireEvent('open', urls[index]);
				},
				scope: this
			});
		},
		
		openMessage: function(mText, fWidth, fHeight, showLoadingIndicator) {
			fWidth = fWidth || width;
			fHeight = fHeight || height;
			 
			this.setViewSize();
			els.overlay.fadeIn({
				duration: this.overlayDuration,
				endOpacity: this.overlayOpacity,
				callback: function() {
					

					// calculate top and left offset for the lightbox
					var pageScroll = Ext.fly(document).getScroll();

					var lightboxTop = pageScroll.top + (Ext.lib.Dom.getViewportHeight() / 10);
					var lightboxLeft = pageScroll.left;
					els.lightbox.setStyle({
						top: lightboxTop + 'px',
						left: lightboxLeft + 'px'
					}).show();
					this.setMessage(mText, fWidth, fHeight, showLoadingIndicator);

					this.fireEvent('open', mText);
				},
				scope: this
			});
		},

		setViewSize: function(){
			var viewSize = this.getViewSize();
			els.overlay.setStyle({
				width: viewSize[0] + 'px',
				height: viewSize[1] + 'px'
			});
			els.shim.setStyle({
				width: viewSize[0] + 'px',
				height: viewSize[1] + 'px'
			}).show();
		},

		setMessage: function(mText, fWidth, fHeight, showLoadingIndicator){
			els.msg.update('');
			if (showLoadingIndicator) {
				els.loading.show();
			} else {
				els.loading.hide();
			}

			els.shim.hide();
			els.msg.hide();
			els.header.hide();

			els.msg.update(mText);
			els.msg.show();

			this.resizeBox(fWidth, fHeight);
		},

		setUrl: function(index, shimWidth, shimHeight) {
			activeUrl = index;
			els.shim.dom.src = urls[activeUrl][0];
			this.urlIsSet = true;
			this.shimWidth = shimWidth;
			this.shimHeight = shimHeight;
		},
		
		shimLoaded : function() {
			// iframe load fires on element creation and actual load. Check variable to ensure that we're dealing with the second.
			if (this.urlIsSet) {

				response = window.frames['ux-lightbox-shim'].response;
				if(response) {
					if (response.error) {
						this.setMessage(response.error, 200, 100, false);
					} else {
						this.close();
					}
				} else {
					els.msg.hide();
					els.loading.hide();
					// For some reason, ExtJS's Fx.shift() comes up 20 pixels short when resizing the lighbox.  This corrects for it.
					shiftFxCorrection = 20;
					// 30 pixels accounts for the content header at the top of the edit window.
					this.resizeBox(this.shimWidth + shiftFxCorrection, this.shimHeight + 30 + shiftFxCorrection);
					els.shim.fadeIn();
					els.header.fadeIn();
				
					els.shim.setStyle({
						alpha:	'(opacity=100)'
					});

					// @todo	Should this code live in feEdit.js?
					wrapper = window.frames['ux-lightbox-shim'].document.getElementsByClassName('formsOnPageWrapper')[0];
					if (Ext.get(wrapper)) {
						// 75 pixels accounts for the height of the bottoms at the bottom of the edit window.
						Ext.get(wrapper).setHeight(this.shimHeight - 45);
					}

					// @todo Move this code out of the lightbox and into feEdit.js
					forms = window.frames['ux-lightbox-shim'].document.forms;
					editForm = Ext.get(forms[0]);
					if (editForm) {
						editForm.on('submit', function(evt, el) {
							this.displayContentUpdateMessage();
						}, this);
					}
				}
			}
		},
		displayContentUpdateMessage: function() {
			if (this.closeOnSubmit) {
				this.setMessage(TYPO3.LLL.feeditadvanced.updatingContent, 200, 120, true);
			}
		},

		setCloseOnSubmit: function(value) {
			this.closeOnSubmit = value;
		},

		resizeBox: function(w,h) {
			var wCur = els.wrapper.getWidth();
			var hCur = els.wrapper.getHeight();

			var wNew = w;
			var hNew = h;

			var wDiff = wCur - wNew;
			var hDiff = hCur - hNew;

			var queueLength = 0;

			if (hDiff != 0 || wDiff != 0) {
				els.wrapper.syncFx()
					.shift({
						height: hNew,
						duration: this.resizeDuration
					})
					.shift({
						width: wNew,
						duration: this.resizeDuration
					});
				queueLength++;
			}

			var timeout = 0;
			if ((hDiff == 0) && (wDiff == 0)) {
				timeout = (Ext.isIE) ? 250 : 100;
			}
		},

		close: function(){
			response = window.frames['ux-lightbox-shim'].response;
			if(!response.url) {
				els.lightbox.hide();
				els.overlay.fadeOut({
					duration: this.overlayDuration
				});
				els.shim.hide();
			}
			this.fireEvent('close', this);
		},

		getViewSize: function() {
			return [Ext.lib.Dom.getViewWidth(true), Ext.lib.Dom.getViewHeight(true)];
		}
	}
})();

Ext.onReady(Ext.ux.Lightbox.init, Ext.ux.Lightbox);