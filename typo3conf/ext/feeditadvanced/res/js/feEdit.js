/*
	feEdit.js -- frontend editing javascript support
		- Toolbar
			- ToolbarWidget
		- FrontendNotificationMessage
		- AJAXJavascriptHandler
		- EditPanel
		- DropZone
		- EditPanelAction
		- ClipboardObj
		- Lightbox
 */

Ext.namespace('TYPO3.FeEdit');

// TODO: go through every part (also CSS and PHP) and use this base class for every class and ID
TYPO3.FeEdit.baseCls = 'feeditadvanced';

TYPO3.FeEdit.Base = function() {};

/*
 * Class for Toolbars and Draggable Widgets within the toolbars.
 */
TYPO3.FeEdit.Toolbar = function(toolbarElementId) {
	this.el = Ext.get(toolbarElementId);
	this.widgets = [];

	// initialize the toolbar element and finds all draggable buttons
	if (this.el) {
		// This does not work in Ext JS, thus it's a bug in the contrib library
		// @todo: send this issue to Ext JS
		// Problem: selecting items with multiple classes while having a different root node
		// than the original document results in nothing
		// var allWidgets = Ext.DomQuery.select('.draggable', this.el);
		var allWidgets = Ext.select('#' + this.el.id + ' .feEditAdvanced-draggable');
		// Create all the draggable buttons in the toolbar
		allWidgets.each(function(draggableElement) {
			this.widgets.push(new TYPO3.FeEdit.ToolbarWidget(draggableElement));
		}, this);
	}

	/**
	 * adds a draggable object and registers the toolbar widget
	 **/
	this.addDraggable = function(toolbarElement) {
		// get draggable item
		// var draggableElements = Ext.DomQuery.select('.draggable', toolbarElement);
		var draggableElements = Ext.select('#' + toolbarElement.id + ' .feEditAdvanced-draggable');
		draggableElements.each(function(draggableElement) {
			this.widgets.push(new TYPO3.FeEdit.ToolbarWidget(draggableElement));
		}, this);
	};
};



/** 
 * Class for the toolbar item that is on top of the page
 * needs 
 */
TYPO3.FeEdit.ToolbarWidget = function(draggableEl) {
	this.el = Ext.get(draggableEl);

		// Override clicks on any elements that are also draggable. 
		// This may eventually trigger an add in the main content area instead.
	this.el.on('click', function(evt) {
		evt.stopEvent();
	});

	this.dd = new Ext.dd.DragSource(Ext.id(this.el), {
//		dropAllowed: 'feEditAdvanced-dropzone',
		ddGroup: 'feeditadvanced-toolbar'
	});

	this.dd.startDrag = function(x, y) {
		// Show drag proxy at the same point as mouse.
		this.setDelta(0, 0);

		var dragEl = Ext.get(this.getDragEl());
		var el = Ext.get(this.getEl());

		dragEl.applyStyles({'z-index': 20000, 'width': el.getWidth() + 'px' });
		dragEl.update(el.dom.innerHTML);
		dragEl.addClass(el.dom.className + ' feeditadvanced-dd-proxy');
		FrontendEditing.activateDropZones();
	};

	// is called over and over again, until you leave or drop the 
		// id is the ID of the drop zone
/*	this.dd.onDragOver = function(evt, id) {
		console.log('Toolbarwidget is currently over ' + id);
	};*/

	this.dd.afterInvalidDrop = function(evt, id) {
		FrontendEditing.deactivateDropZones();
	};

		// Returning false prevents the reset() method from removing classes added in startDrag()
		// notifyOut must be called to mimic what ExtJS does if beforeDragOut returns true
	this.dd.beforeDragOut = function(target, e, id) {
		if(target.isNotifyTarget){
			target.notifyOut(this, e, this.dragData);
		}
		return false;
	}

	Ext.dd.Registry.register(this.dd);
};


	// Object for Javascript handling as part of an AJAX request.
TYPO3.FeEdit.AJAXJavascriptHandler = Ext.extend(TYPO3.FeEdit.Base, {
	regexpScriptTags: '<script[^>]*>([\\S\\s]*?)<\/script>',
	
	constructor: function() {
		this.loadedElements = new Ext.util.MixedCollection();
		this.unloadedElements = [];

		this.registerLoadedElements();
	},

	registerLoadedElements: function() {
		Ext.select('head script[type="text/javascript"]').each(function(script) {
			script = Ext.get(script);
			if (src = script.getAttribute('src')) {
				this.loadedElements.add(src, 1);
			}
		}, this);
		
		Ext.select('head link[type="text/css"]').each(function(css) {
			css = Ext.get(css);
			if (src = css.getAttribute('href')) {
				this.loadedElements.add(src, 1);
			}
		}, this);
	},

	evaluate: function(textContent) {
		var matchScript = new RegExp(this.regexpScriptTags, 'img');
		(textContent.match(matchScript) || []).map(function(scriptTag) {
			this.addJavascript(scriptTag);
		}.bind(this));
		
		
		linkFragment = "<link[^<>]*href=\"([\\S\\s]*?)\\S*>";
		var matchLink = new RegExp(linkFragment, 'img');
		(textContent.match(matchLink) || []).map(function(linkTag) {
			this.addCSS(linkTag);
		}.bind(this));
		
		this.processQueue();
	},

	addInlineJavascript: function(scriptContent) {
		var scriptElement = this.createScriptElement();
		scriptElement.textContent = scriptContent;

		if ("text" in scriptElement) {
			scriptElement.text = scriptContent;
		} else if ("textContent" in scriptElement) {
			scriptElement.textContent = scriptContent;
		} else if ("innerHTML" in scriptElement) {
			scriptElement.innerHTML = scriptContent;
		} else {
			scriptElement.appendChild(document.createTextNode(scriptContent));
		}
			// Add the element to the queue for processing later on
		this.addElementToQueue(scriptElement);
	},

	addExternalJavascript: function(src) {
		if (!this.loadedElements.get(src)) {
			var scriptElement = this.createScriptElement();
			scriptElement.set({'src': src});

				// Add the element to the queue for processing later on
			this.addElementToQueue(scriptElement);
			this.loadedElements.add(src, 1);
		}
	},
	
	addInlineCSS: function(cssContent) {
		var styleElement = this.createStyleElement();
		styleElement.set({'type': 'text/css'});

		if (styleElement.styleSheet) {   // IE
			styleElement.styleSheet.cssText = cssContent;
		} else {                // the world
			var tt1 = document.createTextNode(def);
			styleElement.appendChild(cssContent);
		}
		
		this.addElementToQueue(styleElement);
	},
	
	addExternalCSS: function(src) {
		var linkElement = this.createLinkElement();
		linkElement.set({'href': src});
		this.addElementToQueue(linkElement);
	},

	addJavascript: function(scriptTag) {
		var matchOne = new RegExp(this.regexpScriptTags, 'im');
		var srcFragment = 'src=(?:\"|\')([\\S\\s]*?)(?:\"|\')(?:\\S\\s)*?\>';
		var srcRegExp = new RegExp(srcFragment , 'im');
		if (result = srcRegExp.exec(scriptTag)) {
			var srcAttribute = result[1];
			this.addExternalJavascript(srcAttribute);
		} else {
			inlineJS = (scriptTag.match(matchOne) || ['', ''])[1];
			this.addInlineJavascript(inlineJS);
		}
	},
	
	addCSS: function(linkTag) {
		var hrefFragment = 'href=(?:\"|\')([\\S\\s]*?)(?:\"|\')(?:\\S\\s)*?\>';
		var hrefFragment = 'href=(?:\"|\')([\\S\\s]*?)(?:\"|\')';
		var hrefRegExp = new RegExp(hrefFragment , 'im');
		if (result = hrefRegExp.exec(linkTag)) {
			var hrefAttribute = result[1];
			this.addExternalCSS(hrefAttribute);
		}
	},

	createScriptElement: function() {
		var scriptID = new Date().getTime() + "_onDemandLoadedScript";
		var scriptElement = Ext.DomHelper.createDom({
			'tag': 'script',
			'id': styleId,
			'type': 'text/javascript'
		});

		this.addCallBacksToElementWhenLoaded(scriptElement);
		return scriptElement;
	},
	
	createStyleElement: function() {
		var styleID = new Date().getTime() + '_onDemandLoadedStyle';
		var styleElement = Ext.DomHelper.createDom({
			'tag': 'style',
			'id': styleId,
			'type': 'text/css'
		});
		this.addCallBacksToElementWhenLoaded(styleElement);
		return styleElement;
	},
	
	createLinkElement: function() {
		var styleID = new Date().getTime() + '_onDemandLoadedStyle';
		var linkElement = Ext.DomHelper.createDom({
			'tag': 'link',
			'id': styleId,
			'rel': 'stylesheet',
			'type': 'text/css'
		});
		this.addCallBacksToElementWhenLoaded(linkElement);
		return linkElement;
	},
	
	// class that is used internally to apply certain "onstatechange" and "onload" events when the element is loaded
	// so that the process queue is run
	addCallBacksToElementWhenLoaded: function(element) {
		element.on('readystatechange', function() {
			if ((element.readyState == 'complete') || (element.readyState == 'loaded')) {
				this.processQueue();
			}
		}, this);

		element.on('load', function() {
			this.processQueue();
		}, this);
		return element;
	},
	
	addElementToHead: function(element) {
		Ext.DomQuery.select('head').first().appendChild(element);
	},

	addElementToQueue: function(element) {
		this.unloadedElements.push(element);
	},

	processQueue: function() {
		if (this.unloadedElements.length) {
				// Grab the first element in the queue and add it to the DOM.
			firstElement = this.unloadedElements.shift();
			if (typeof firstElement == 'object') {
				this.addElementToHead(firstElement);

					// @todo	In Webkit, first element is null sometimes.  Not sure why but it throws an error here.
					// @todo: check if this still exists
				try {
					src = firstElement.readAttribute('src');
				} catch (e) {}

					// If there's no source attribute, immediately process the next item.
					// Otherwise, wait for it to fire an onload event.
				if (!src) {
					this.processQueue();
				} else {
					this.loadedElements.add(src, 1);
				}
			}
		}
	}
});

	// Object for an entire content element and its EditPanel.
TYPO3.FeEdit.EditPanel = Ext.extend(TYPO3.FeEdit.Base, {
		// the DOM element (actually it's a Ext.get) of the wrapper Element of the content element
	el: null,
		// the DOM element of the editPanel or the hover menu of this content element
	menuEl: null,
		// the DOM element of the form object of the editPanel of this content element
	formEl: null,
	params: null,

	pid: null,
	record: null,
	isPagePanel: false,	// whether this panel edits the page (thus it's in the menupanel), or a content panel
	
	sortable: false,
	hoverMenuEnabled: false,
	alwaysVisible: false,
	clickContentToEdit: false,

	constructor: function(wrapperElement) {
		this.el = Ext.get(wrapperElement);
		this.el.setVisibilityMode(Ext.Element.DISPLAY);
		this.menuEl = Ext.get(this.el.select('div.feEditAdvanced-editPanelDiv').first());
		this.formEl = Ext.get(this.el.select('form').first());	// todo: we should use a class here
		this.hoverMenuEnabled = true;
		this.isPagePanel = (this.el.up('.feEditAdvanced-menuToolbar') ? true : false);
		this.getFormParameters();
		this.setupEventListeners();

		if (this.el.hasClass('feEditAdvanced-draggable') && !this.isPagePanel) {
			this.sortable = true;
			this._makeDraggable();
		}
		
		if (this.el.hasClass('alwaysVisible') || this.isPagePanel) {
			this.alwaysVisible = true;
		}
		
		if (this.isPagePanel) {
			this.el.show();
		}
		this.updateUpDownButtons();
	},

	enable: function() {
		this.enableHoverMenu();
		this.el.removeClass('feEditAdvanced-noBorder');
	},

	disable: function() {
		this.disableHoverMenu();
		this.el.addClass('feEditAdvanced-noBorder');
	},
	
	elementIsHidden: function() {
		return (this.el.hasClass('feEditAdvanced-hiddenElement') && !FrontendEditing.showHiddenContentElements);
	},

	addDropZone: function() {
		if (this.sortable && !this.elementIsHidden()) {
			this.dropZone = new TYPO3.FeEdit.DropZone(this);
		}
	},

	removeDropZone: function() {
		if (this.sortable && this.dropZone) {
			this.dropZone.remove();
			this.dropZone = null;
		}
	},

	/*
	 * writes all form parameters needed to identify the element to a
	 * a parameter string
	 */
	getFormParameters: function() {
			// Extract values from hidden form fields
		this.formEl.select('input').each(function(formElement) {
			formElement = Ext.get(formElement);
			// @todo getAttribute call is not working properly in IE.
			switch (formElement.getAttribute('name')) {
				case 'TSFE_EDIT[cmd]':
					// do nothing
					break;
				case 'TSFE_EDIT[record]':
					this.record = formElement.getValue();
					break;
				case 'TSFE_EDIT[pid]':
					this.pid = formElement.getValue();
					break;
			}
		}, this);
			// make the additional formElement values as "&name=value"
		this.params = Ext.Ajax.serializeForm(this.formEl);
	},

	_makeDraggable: function() {
		this.dd = new Ext.dd.DragSource(this.el, {
			// TODO: different group please
			ddGroup: 'feeditadvanced-toolbar'
			,maintainOffset: true
//			,dropAllowed: 'feEditAdvanced-dropzone'
		});
		
		// find the handle and give the handle an ID
		var dragHandle = Ext.get(this.el.select('.feEditAdvanced-dragHandle').first());
		var dragHandleId = Ext.id(dragHandle, 'feEditAdvanced-dragHandle-');
		dragHandle.set({'id': dragHandleId});
		this.dd.setOuterHandleElId(dragHandleId);
		//this.dd.setDragElId(this.el.id);

		this.dd.startDrag = function(x, y) {
			var dragEl = Ext.get(this.getDragEl());
			var el = Ext.get(this.getEl());

			dragEl.applyStyles({'z-index': 2000, 'width': el.getWidth() + 'px'});

			// Show drag proxy at the same point as mouse.
			this.setDelta(el.getWidth() - 15, 0);

			FrontendEditing.activateDropZones(FrontendEditing.editPanels.get(el.id));
			el.setVisibilityMode(Ext.Element.DISPLAY);
			el.hide();

			dragEl.update(el.dom.innerHTML);
			dragEl.addClass(el.dom.className + ' feeditadvanced-dd-proxy');
		};

			// id is the ID of the drop zone
		this.dd.afterInvalidDrop = function(evt, id) {
			var el = Ext.get(this.getEl());
			el.show();
			FrontendEditing.deactivateDropZones();
		};
		this.dd.afterDragDrop = function(target, evt, id) {
			var el = Ext.get(this.getEl());
			el.show();
			FrontendEditing.deactivateDropZones();
		};

			// Returning false prevents the reset() method from removing classes added in startDrag()
			// notifyOut must be called to mimic what ExtJS does if beforeDragOut returns true
		this.dd.beforeDragOut = function(target, e, id) {
			if(target.isNotifyTarget){
				target.notifyOut(this, e, this.dragData);
			}
			return false;
		}


		Ext.dd.Registry.register(this.dd);
	},

	_handleButtonClick: function(evt) {
		var targetEl = evt.getTarget();
		targetEl = Ext.get(targetEl);
		if (targetEl && 
		    !targetEl.hasClass('feEditAdvanced-editButton') &&
		    targetEl.id != 'feEditAdvanced-closeButton') {
			targetEl = Ext.get(targetEl.up('.feEditAdvanced-editButton'));
		}

		if (targetEl) {
			if (targetEl.hasClass('editAction')) {
				this.edit();
			} else if (targetEl.hasClass('upAction')) {
				this.up();
			} else if (targetEl.hasClass('downAction')) {
				this.down();
			} else if (targetEl.hasClass('newRecordAction') || targetEl.hasClass('newPageAction')) {
				this.create();
			} else if (targetEl.hasClass('hideAction')) {
				this.hide();
			} else if (targetEl.hasClass('unhideAction')) {
				this.unhide();
			} else if (targetEl.hasClass('deleteAction')) {
				this.remove();
			} else if (targetEl.hasClass('saveAction')) {
				this.save();
			} else if (targetEl.hasClass('saveCloseAction')) {
				this.saveAndClose();
			} else if (targetEl.hasClass('closeAction')) {
				this.close();
			} else if (targetEl.hasClass('cutAction')) {
				this.cut();
			} else if (targetEl.hasClass('copyAction')) {
				this.copy();
			}
		}
		
		evt.stopEvent();
		return false;
	},

	showMenu: function(evt) {
		if (!this.alwaysVisible && FrontendEditing.editPanelsEnabled && this.hoverMenuEnabled) {
			this.menuEl.show();
			this.el.addClass('feEditAdvanced-allWrapperHover');

			if (this.clickContentToEdit) {
				this.el.addClass('feEditAdvanced-clickContentToEdit');
			}
		}
		if (evt != undefined) {
			evt.stopEvent();
		}
	},

	hideMenu: function(evt) {
		if (!this.alwaysVisible && !this.isPagePanel) {
			this.menuEl.hide();
			this.el.removeClass('feEditAdvanced-allWrapperHover');

			if (this.clickContentToEdit) {
				this.el.removeClass('feEditAdvanced-clickContentToEdit');
			}
		}
		if (evt != undefined) {
			evt.stopEvent();
		}
	},

	editOnClick: function(evt) {
			// if in middle of dragging, exit
		if (!FrontendEditing.editPanelsEnabled || !this.hoverMenuEnabled) {
			return;
		}
			// make sure on valid element
		var targetEl = evt.getTarget('.editableOnClick', 20);
		if (targetEl) {
			this.edit();
		}

		if (evt != undefined) {
			evt.stopEvent();
		}
	},

	enableHoverMenu: function() {
		this.hoverMenuEnabled = true;
	},

	disableHoverMenu: function() {
		this.hoverMenuEnabled = false;
		if (this.isHoverMenuVisible()) {
			this.hideMenu();
		}
	},

	isHoverMenuVisible: function() {
		return this.menuEl.isVisible();
	},

	createFormObservers: function() {
		this.el.select('form').each(function(formEl) {
			formEl = Ext.get(formEl);
			// @todo Find a better way to remove the attribute completely.
			formEl.set({'onsubmit':''});
			formEl.on('submit', function(evt) { evt.stopEvent(); });
		}, this);

			// Buttons at the bottom of the edit window
		Ext.DomQuery.select('#feEditAdvanced-editControls button').each(function(button) {
			Ext.get(button).on('click', this._handleButtonClick, this);
		}, this);

			// Close button in the top right corner of the edit window
		Ext.get('feEditAdvanced-closeButton').on('click', this._handleButtonClick, this);
	},

	setupEventListeners: function() {
			// Show and hide the menu based on mouseovers
		this.el.on('mouseover', this.showMenu, this);
		this.el.on('mouseout',  this.hideMenu, this);
		
		var editPanelToolbar = this.el.first();

			// Set up event handlers for the hover menu buttons
		editPanelToolbar.select('.feEditAdvanced-editButton').each(function(button) {
			button = Ext.get(button);
			button.setVisibilityMode(Ext.Element.DISPLAY);
			button.on('click', this._handleButtonClick, this);
		}, this);

			// Setup event handler for edit on click
		if (editPanelToolbar.next('.editableOnClick')) {
			var editableOnClick = editPanelToolbar.next('.editableOnClick');
			Ext.get(editableOnClick).on('click', this.editOnClick, this);
			this.clickContentToEdit = true;
		}

			// If the content element is empty, always show the hover menu as there's no other way to activate it.
		if (editPanelToolbar.next('.feEditAdvanced-emptyContentElement')) {
			this.alwaysVisible = true;
			this.menuEl.show();
		}
	},
	
	// @todo Is this beter suited as an action?
	// used when getting content back from the iframe editing
	pushContentUpdate: function(json) {
		if (json.content) {
			var content = json.content;
			json.content = Ext.util.Format.stripScripts(content);
		}
		id = this.el.id;

		// @todo	This is where we'd normally call this._process for an action.
		
		// @todo	Get the table from the json response.
		table = 'tt_content';

		if ((table + ':' + json.uid) == id) {
			// overwrite the content of this ID
			this.replaceContent(json.content);
			// @todo Pull this re-registration into a standalone method.
			this.el = Ext.get(id);
			this.menuEl = Ext.get(this.el.select('div.feEditAdvanced-editPanelDiv').first());
			this.formEl = Ext.get(this.el.select('form').first());	// todo: we should use a class here
			this.setupEventListeners();
		} else {
			// Insert the HTML and register the new edit panel
			Ext.DomHelper.insertHtml('afterEnd', this.el.dom, json.content);
			FrontendEditing.scanForEditPanels();
		}

		/**
		 * Reenable when JSHandler is ported to ExtJS
		if (json.content) {
			FrontendEditing.JSHandler.evaluate(content);
		}
		
		if (json.header) {
			FrontendEditing.JSHandler.evaluate(json.header)
		}
		*/
	},

	replaceContent: function(newContent) {
		elId = this.el.id;
		Ext.DomHelper.insertHtml('afterEnd', this.el.dom, newContent);
		this.el.remove();
		this.el = Ext.get(elId);
	},
	
	removeContent: function() {
		this.el.remove();
		this.el = null;
	},
	
	getPreviousContentElement: function() {
		var prevEl = this.el;
		while (prevEl = prevEl.prev('.feEditAdvanced-allWrapper')) {
			if (!prevEl.hasClass('feEditAdvanced-hiddenElement') || FrontendEditing.showHiddenContentElements) {
				return prevEl;
			}
		}
		return false;
	},
	
	getNextContentElement: function() {
		var nextEl = this.el;
		while (nextEl = nextEl.next('.feEditAdvanced-allWrapper')) {
			if (!nextEl.hasClass('feEditAdvanced-hiddenElement') || FrontendEditing.showHiddenContentElements) {
				return nextEl;
			}
		}
		return false;
	},
	
	hideUpButton: function() {
		Ext.get(this.el.select('input.upAction')).hide();
	},

	showUpButton: function() {
		Ext.get(this.el.select('input.upAction')).show();
	},

	hideDownButton: function() {
		Ext.get(this.el.select('input.downAction')).hide();
	},

	showDownButton: function() {
		Ext.get(this.el.select('input.downAction')).show();
	},
	
	updateUpDownButtons: function() {
		if (!this.getPreviousContentElement()) {
			this.hideUpButton();
		} else {
			this.showUpButton();
		}
		if (!this.getNextContentElement()) {
			this.hideDownButton();
		} else {
			this.showDownButton();
		}
	},
	
	getTableName: function() {
		recordInfo =  this.record.split(':');
		return recordInfo[0];
	}
});


TYPO3.FeEdit.DropZone = Ext.extend(TYPO3.FeEdit.Base, {
	hasEditPanelAttached: false,
	baseCls: 'feEditAdvanced-dropzone',
	// the ad-hoc created element
	el: null,
	dz: null,

	constructor: function(editPanelObj, hasNoEditPanelAttached) {
		this.hasEditPanelAttached = (hasNoEditPanelAttached != true);
		if (this.hasEditPanelAttached) {
				//  Use an ID that relate the dropzone element back to the edit panel.
				// Insert the drop zone after the edit panel.
			var previousElement = editPanelObj.el;
			var elId = this.baseCls + '-' + previousElement.id;
		} else {
				// the editpanelobj in this case is the first div
			var previousElement = editPanelObj;
			var elId = Ext.id(null, this.baseCls + '-top-');
		}
		this.el = Ext.DomHelper.insertAfter(previousElement, {
			'tag': 'div',
			'id':  elId,
			'cls': this.baseCls,
			'html': '<div class="' + this.baseCls + 'Text">' + TYPO3.LLL.feeditadvanced.dropMessage + '</div>'
		}, true).fadeIn();
		this.el.setVisibilityMode(Ext.Element.DISPLAY);

		// create the drop zone
		this.dz = new Ext.dd.DropZone(this.el, {
			ddGroup: 'feeditadvanced-toolbar',
			overClass: this.baseCls + 'Active'
		});
		this.dz.notifyEnter = this.onHover;
		this.dz.notifyOut   = this.onHoverOut;
		this.dz.notifyDrop  = this.onDrop;
		Ext.dd.Registry.register(this.dz);
	},

	onDrop: function(dragSource, evt, data) {
		var linkedDragEl = Ext.get(dragSource.getEl());
		var dropZoneEl = Ext.get(this.getEl());

		if (linkedDragEl.hasClass('feEditAdvanced-contentTypeItem')) {
			// create a new record
			var previousContentElement = dropZoneEl.prev('.feEditAdvanced-allWrapper');
			if (!previousContentElement) {
					// it is the first element in this list, was dropped onto feEditAdvanced-firstWrapper
					// so TCEmain needs a "moveAfter" with the correct colPos and the page (needs to be negative)
				var contentElementContainerId = dropZoneEl.prev('.feEditAdvanced-firstWrapper').id;
				// the ID looks like this: feEditAdvanced-firstWrapper-colPos-0-pages-13
				var colPos = contentElementContainerId.substr(35, 1);
				var pageId = contentElementContainerId.substr(contentElementContainerId.indexOf('-pages-') + 7);
				
				var additionalParams = linkedDragEl.getAttribute('rel') + '&defVals[tt_content][colPos]=' + colPos;
				additionalParams += '&TSFE_EDIT[record]=tt_content:NEW';
				additionalParams += '&TSFE_EDIT[pid]=' + pageId;
				additionalParams += '&pid=' + pageId;

				var action = new TYPO3.FeEdit.NewRecordAction();
				action.trigger(additionalParams, contentElementContainerId);
			} else {
				var editPanel = FrontendEditing.editPanels.get(previousContentElement.id);
				editPanel.create(linkedDragEl.getAttribute('rel'));
			}
		} else if (linkedDragEl.hasClass('feEditAdvanced-allWrapper')) {
			// Move a record
			linkedDragEl.insertBefore(dropZoneEl);
			linkedDragEl.highlight('ffff9c', {duration: 1});

			var sourceEditPanel = FrontendEditing.editPanels.get(linkedDragEl.id);
			var previousContentElement = linkedDragEl.prev('.feEditAdvanced-allWrapper');
			if (!previousContentElement) {
					// it is the first element in this list, was dropped onto feEditAdvanced-firstWrapper
					// so TCEmain needs a "moveAfter" with the correct colPos and the page (needs to be negative)
				var contentElementContainerId = linkedDragEl.prev('.feEditAdvanced-firstWrapper').id;
				// the ID looks like this: feEditAdvanced-firstWrapper-colPos-0-pages-13
				var colPos = contentElementContainerId.substr(35, 1);
				var pageId = contentElementContainerId.substr(contentElementContainerId.indexOf('-pages-') + 7);
					// "-" and the page ID tells TCEmain to move it to the first position
				var moveAfter = '-' + pageId;
					// also if the item was dragged to a different colPos, this needs to be updated in the DB as well
				moveAfter += '&TSFE_EDIT[colPos]=' + colPos;

			} else {
				// just a basic: move one after the other, the colPos will be adapted automatically
				var destinationEditPanel = FrontendEditing.editPanels.get(previousContentElement.id);
				var recordFields = destinationEditPanel.record.split(':');
				var moveAfter = recordFields[1];
			}
			sourceEditPanel.moveAfter(moveAfter);
		} else if (linkedDragEl.hasClass('clipObj')) {
			srcElement = linkedDragEl.select('form input.feEditAdvanced-tsfeedit-input-record').first().getValue();
			cmd = linkedDragEl.select('form input.feEditAdvanced-tsfeedit-input-cmd').first().getValue();

				// do a clear of element on clipboard
			FrontendEditing.clipboard.clearClipboard(linkedDragEl);

				// if source is on this page, then move it
			if (srcElement) {
					// set source and destination
				source = FrontendEditing.editPanels.get(srcElement.id);
				destination = FrontendEditing.editPanels.get(dropZoneEl.prev().id);

				srcElement.setAttribute('style', '');
					// do the actual cut/copy
				if (cmd == 'cut') {
						// move the element to where it is dropped
					source.paste(destination.getDestinationPointer());
					srcElement.removeClass('doCut');
					dropZoneEl.insertAfter(srcElement);
					//TODO: Ext? linkedDragEl.highlight({duration: 5});

					// now trigger the cut action
				} else if (cmd == 'copy') {
						// display the element where it is dropped
					srcElement.removeClass('doCopy');

					clonedElement = srcElement.cloneNode(true);
					dropZoneEl.insertAfter(clonedElement);
					newSource = FrontendEditing.editPanels.get(clonedElement.id);
					newSource.paste(destination.getDestinationPointer());
				}
			}
			// if source is NOT on this page, then need to:
			// 		do everything except use "blank" source
			//
		} else {
			alert("hmm, doesn't look like we can handle this drag.");
		}
		FrontendEditing.deactivateDropZones();
	},
	
	onHover: function(dragSource, evt, data) {
		var dragEl = Ext.get(dragSource.getDragEl());
		this.el.addClass('feEditAdvanced-dropzoneActive');
		this.el.frame('e0e0e0');
	},
	
	onHoverOut: function(source, evt, data) {
		this.el.removeClass('feEditAdvanced-dropzoneActive');
	},
	
	remove: function() {
		this.dz.unreg();
		if (this.el) {
			this.el.fadeOut({remove: true});
		}
		this.dz = this.el = null;
	}
});


// ==== Define classes for each edit action ====

/**
 * default action that every action inherits from
 */
TYPO3.FeEdit.EditPanelAction = Ext.extend(TYPO3.FeEdit.Base, {
	ajaxRequestUrl: 'index.php',
		// there are "ajax" actions and "iframe" actions
		// iframe actions only need the URL and don't trigger the AJAX call when triggering the action
	requestType: 'ajax',
	_isModalAction: true,
	// the edit panel to work on
	parent: null,
	// cmd, the command that the action is done
	cmd: null,

	// init function, sets the "parent" which is the edit panel (I believe so at least)
	// and the command from the subclass
	constructor: function(parent) {
		this.parent = parent;
		this.cmd = this._getCmd();
	},

	// is called when an icon is pressed, something is dropped or edited
	trigger: function(additionalParams) {
		FrontendEditing.actionRunning = true;

		// instantiate a edit window if this doesn't exist yet
		if (!FrontendEditing.editWindow) {
			FrontendEditing.editWindow = new TYPO3.FeEdit.EditWindow(this.parent);
		}
		
		// if the "isModelAction" flag is set, then there is a notification message
		if (this._isModalAction) {
			FrontendEditing.editWindow.displayLoadingMessage(this._getNotificationMessage());
		}

		// make a request to the server
		if (this.requestType == 'ajax') {
				// now do the AJAX request
			Ext.Ajax.request({
				url:    this.ajaxRequestUrl,
				params: this._getAjaxRequestParameters(additionalParams),
				method: 'POST',
				headers: { Accept: 'application/json' },
				success: this._handleSuccessResponse,
				failure: this._handleFailureResponse,
				scope: this
			});
		}
	},
	
	// function to return a full URL (good for the iframe variant)
	getRequestUrl: function(additionalParams) {
		return this.ajaxRequestUrl + (this.ajaxRequestUrl.indexOf('?') == -1 ? '?' : '&') + this._getAjaxRequestParameters(additionalParams);
	},

	// function to return additional parameters that will be sent to the server through the AJAX call or iframe GET parameters
	_getAjaxRequestParameters: function(additionalParams) {
		var requestParams = 'eID=feeditadvanced';
		if (this.parent) {
			this.parent.getFormParameters();
			if (this.parent.params) {
				requestParams += '&' + this.parent.params;
			}
		
			pid = this.parent.pid;
			pidRequestParam = '&pid=' + pid;
		} else {
			// @todo	Grab the PID from some global location.
			pidRequestParam = '';
		}

		if (additionalParams != undefined) {
			requestParams += '&' + additionalParams;
		}
		// remove the doubled TSFE_EDIT[cmd] (because it's empty) before we add the real cmd value
		requestParams  = requestParams.replace(/&TSFE_EDIT%5Bcmd%5D=&/, '&');
		requestParams += '&TSFE_EDIT[cmd]=' + this.cmd + pidRequestParam;
		return requestParams;
	},

	// callback function to handle if the AJAX response was faulty
	_handleFailureResponse: function(response, options) {
		FrontendEditing.actionRunning = false;
		alert(TYPO3.LLL.feeditadvanced.ajaxError + ': ' + response.responseText);
	},

	// callback function to extract the JSON response from the server 
	_handleSuccessResponse: function(response, options) {
		FrontendEditing.actionRunning = false;
		if (response.getResponseHeader('X-JSON')) {
			var json = Ext.decode(response.responseText);
			if (json.error) {
				FrontendEditing.editWindow.displayStaticMessage(json.error);
			} else if (json.url) {
				window.location = json.url;
			} else if (json.uid == 'NEW') {
				// New element was not saved so do nothing and discard it.
			} else {
				if (json.content) {
					content = json.content;
					json.content = content.stripScripts();
				}

				if (json.newContent) {
					newContent = json.newContent;
					json.newContent = newContent.stripScripts();
				}
				
				id = this.parent.el.id;
				this._process(json);
				this.parent.el = Ext.get(id);

				if (json.content) {
					FrontendEditing.JSHandler.evaluate(content);
				}

				if (json.header) {
					FrontendEditing.JSHandler.evaluate(json.header);
				}

				if (json.newContent) {
					FrontendEditing.JSHandler.evaluate(newContent);
				}
			}
		} else {
			FrontendEditing.editWindow.displayStaticMessage(TYPO3.LLL.feeditadvanced.generalError);
		}

			// Always scan for new edit panels to update after response.
		FrontendEditing.scanForEditPanels();
	},

	_process: function() {
		// Implemented by concrete classes
	},

	_getCmd: function() {
		// Implemented by concrete classes
	},

	_getNotificationMessage: function() {
		return '';
	},

	_getAlreadyProcessingMsg: function() {
		return TYPO3.LLL.feeditadvanced.alreadyProcessingAction;
	}
});



TYPO3.FeEdit.NewRecordAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	requestType: 'iframe',

	trigger: function(additionalParams, targetID) {
		TYPO3.FeEdit.EditAction.superclass.trigger.apply(this, arguments);
		if (this.parent && this.parent.getTableName() == 'pages') {
			label = TYPO3.LLL.feeditadvanced.newPage;
		} else {
			label = TYPO3.LLL.feeditadvanced.newContentElement;
		}
		var url = this.getRequestUrl(additionalParams);
		FrontendEditing.editWindow.displayIframe(label, url);
		if (targetID) {
			FrontendEditing.editWindow.setTargetID(targetID);
		}
	},

	_process: function () {},

	_getCmd: function() {
		return 'new';
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.loadingMessage;
	}
});

TYPO3.FeEdit.EditAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	requestType: 'iframe',

	trigger: function() {
		TYPO3.FeEdit.EditAction.superclass.trigger.apply(this, arguments);
		if (this.parent && this.parent.getTableName() == 'pages') {
			label = TYPO3.LLL.feeditadvanced.editPageProperties;
		} else {
			label = TYPO3.LLL.feeditadvanced.editContentElement;
		}
		var url = this.getRequestUrl();
		FrontendEditing.editWindow.displayIframe(label, url);
	},

	_process: function() {},

	_getCmd: function() {
		return 'edit';
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.loadingMessage;
	}
});

TYPO3.FeEdit.DeleteAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	_process: function(json) {
		if (this.parent && this.parent.getTableName() != 'pages') {
			this.parent.removeContent();
		}
	},

	trigger: function() {
		if (confirm(TYPO3.LLL.feeditadvanced.confirmDelete)) {
			TYPO3.FeEdit.DeleteAction.superclass.trigger.apply(this);
		}
	},

	_getCmd: function() {
		return 'delete';
	},

	_getNotificationMessage: function() {
		return "Deleting content.";
	},
	
	_isModalAction: false
});

TYPO3.FeEdit.HideAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	_process: function(json) {
		if (this.parent && this.parent.getTableName() != 'pages') {
			this.parent.el.addClass('feEditAdvanced-hiddenElement');
			Ext.get(this.parent.el.select('input.unhideAction').first()).setDisplayed('block');
			Ext.get(this.parent.el.select('input.hideAction').first()).setDisplayed('none');
		}
	},

	_getCmd: function() {
		return 'hide';
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.hideNotification;
	},
	
	_isModalAction: false
});

TYPO3.FeEdit.UnhideAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	_process: function(json) {
		if (this.parent && this.parent.getTableName() != 'pages') {
			this.parent.el.removeClass('feEditAdvanced-hiddenElement');
			Ext.get(this.parent.el.select('input.unhideAction').first()).setDisplayed('none');
			Ext.get(this.parent.el.select('input.hideAction').first()).setDisplayed('block');
		}
	},

	_getCmd: function() {
		return 'unhide';
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.unhideNotification;
	},

	_isModalAction: false
});

TYPO3.FeEdit.UpAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	trigger: function() {
		var previousEditPanel = this.parent.getPreviousContentElement();
		if (previousEditPanel) {
			this.parent.el.insertBefore(previousEditPanel);
			this.parent.updateUpDownButtons();
			FrontendEditing.editPanels.get(previousEditPanel.id).updateUpDownButtons();
			this.parent.hideMenu();
			TYPO3.FeEdit.UpAction.superclass.trigger.apply(this, arguments);
		}
	},
	
	_process: function(json) {
		FrontendEditing.editPanelsEnabled = true;
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.moveNotification;
	},

	_getCmd: function() {
		return 'up';
	},

	_isModalAction: false
});

TYPO3.FeEdit.DownAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	trigger: function() {
		var nextEditPanel = this.parent.getNextContentElement();
		if (nextEditPanel) {
			this.parent.el.insertAfter(nextEditPanel);
			this.parent.updateUpDownButtons();
			FrontendEditing.editPanels.get(nextEditPanel.id).updateUpDownButtons();
			this.parent.hideMenu();
			TYPO3.FeEdit.DownAction.superclass.trigger.apply(this, arguments);
		}
	},
	
	_process: function(json) {
		FrontendEditing.editPanelsEnabled = true;
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.moveNotification;
	},

	_getCmd: function() {
		return 'down';
	},

	_isModalAction: false
});

TYPO3.FeEdit.MoveAfterAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	_process: function(json) {
			// allow to edit again
		FrontendEditing.editPanelsEnabled = true;
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.moveNotification;
	},

	_getCmd: function() {
		return 'moveAfter';
	},

	_isModalAction: false
});

TYPO3.FeEdit.SaveAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	trigger: function() {
			// Set the doSave element.
		this.parent.el.select('input.feEditAdvanced-tsfeedit-input-doSave').each(function(el) {
			Ext.get(el).set({'value': 1});
		});

		if (TBE_EDITOR.checkSubmit(1)) {
			formParams = Ext.Ajax.serializeForm(Ext.get('feEditAdvanced-editWindow').select('form').first());
			TYPO3.FeEdit.SaveAction.superclass.trigger.apply(this, formParams);
		}
	},

	_process: function(json) {
		// @todo	Alert if the save was not successful.
		if (FrontendEditing.editWindow) {
			if (this.parent && this.parent.getTableName() == 'pages') {
				label = TYPO3.LLL.feeditadvanced.editPage;
			} else {
				label = TYPO3.LLL.feeditadvanced.editContentElement;
			}
			FrontendEditing.editWindow.displayEditingForm(label, json.content);
		}
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.saveNotification;
	},

	_getCmd: function() {
			// @todo	Temporary hack to return edit form again on save().
		return 'edit';
	}
});


TYPO3.FeEdit.CloseAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	trigger: function() {
			// If this EditPanel is nested inside another, find the ID of the parent EditPanel
		if (this.parent.el.up('.feEditAdvanced-allWrapper')) {
			parentID = this.parent.el.up('.feEditAdvanced-allWrapper').id;
			formParams = '&TSFE_EDIT[parentEditPanel]=' + parentID;
		} else {
			formParams = '';
		}
		TYPO3.FeEdit.CloseAction.superclass.trigger.apply(this, formParams);
	},

	_process: function(json) {
		FrontendEditing.editWindow.close();
		// @todo	Get the table from the json response.
		table = 'tt_content'; 
		
		if (json.uid) {
			ep = FrontendEditing.editPanels.get([table + ':' + json.uid]);
			ep.replaceContent(json.content);
		} else {
			this.parent.replaceContent(json.content);
			this.parent.setupEventListeners();
		}
		
		if (json.newUID) {
				// Insert the HTML and register the new edit panel.
			this.parent.el.insertAfter(json.newContent);
			nextEditPanel = this.parent.getNextContentElement();
			FrontendEditing.editPanels.add(nextEditPanel.id, new TYPO3.FeEdit.EditPanel(nextEditPanel));
		}
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.closeNotification;
	},

	_getCmd: function() {
		return 'close';
	}
});

TYPO3.FeEdit.SaveAndCloseAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	trigger: function() {
			// Set the doSave element.
		var content = $(this.parent.el.dom);
		content.select('input[name=TSFE_EDIT[doSave]]').each(function(el) {
			Ext.get(el).setAttribute("value", 1);
		});

		if (TBE_EDITOR.checkSubmit(1)) {
			formObj = $$('#feEditAdvanced-editWindow form')[0];
			formParams = $$('#feEditAdvanced-editWindow form')[0].serialize();

				// If this EditPanel is nested inside another, find the ID of the parent EditPanel
			if (this.parent.el.up('.feEditAdvanced-allWrapper')) {
				parentID = this.parent.el.up('.feEditAdvanced-allWrapper').id;
				formParams += '&TSFE_EDIT[parentEditPanel]=' + parentID;
			}

			TYPO3.FeEdit.SaveAndCloseAction.superclass.trigger.apply(this, formParams);
		}
	},

	_process: function(json) {
		FrontendEditing.editWindow.close();
		// @todo	Get the table from the json response.
		table = 'tt_content';

		if (json.uid) {
			ep = FrontendEditing.editPanels.get(table + ':' + json.uid);
			ep.replaceContent(json.content);
		} else {
			this.parent.replaceContent(json.content);
			this.parent.setupEventListeners();
		}

		if (json.newUID) {
				// Insert the HTML and register the new edit panel.
			this.parent.el.insertAfter(json.newContent);
			nextEditPanel = this.parent.getNextContentElement();
			FrontendEditing.editPanels.add(nextEditPanel.id, new TYPO3.FeEdit.EditPanel(nextEditPanel));
		}
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.saveNotification;
	},

	_getCmd: function() {
		return 'saveAndClose';
	}
});

TYPO3.FeEdit.CopyAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	_process: function(json) {
			// put "copy" selector around
		this.parent.el.addClass('doCopy');

			// create new "copy" object in menubar clipboard
		clipboardObj = Ext.fly('feEditAdvanced-clipboardToolbar');
		if (clipboardObj) {
			FrontendEditing.clipboard.addToClipboard(this);
		}
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.copyNotification;
	},

	_getCmd: function() {
		return 'copy';
	}
});

TYPO3.FeEdit.CutAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	_process: function(json) {
			// put "cut" selector around
		this.parent.el.addClass('doCut');

			// create new "cut" object in menubar clipboard
		clipboardObj = Ext.fly('feEditAdvanced-clipboardToolbar');
		if (clipboardObj) {
			FrontendEditing.clipboard.addToClipboard(this);
		}
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.cutNotification;
	},

	_getCmd: function() {
		return 'cut';
	}
});

TYPO3.FeEdit.PasteAction = Ext.extend(TYPO3.FeEdit.EditPanelAction, {
	trigger: function() {

		formParams = Ext.Ajax.serializeForm(this.parent.el.select('form').first());
			// add setCopyMode
			// add sourcePointer
		TYPO3.FeEdit.PasteAction.superclass.trigger.apply(this, formParams);
	},

	_process: function(json) {
	},

	_getNotificationMessage: function() {
		return TYPO3.LLL.feeditadvanced.pasteNotification;
	},

	_getCmd: function() {
		return 'paste';
	}
});

	// Add all the actions directly to the EditPanel objects.
Ext.override(TYPO3.FeEdit.EditPanel, {
	create: function(additionalParams) {
		action = new TYPO3.FeEdit.NewRecordAction(this);
		action.trigger(additionalParams);
	},

	edit: function() {
		action = new TYPO3.FeEdit.EditAction(this);
		action.trigger();
	},

	hide: function() {
		action = new TYPO3.FeEdit.HideAction(this);
		action.trigger();
	},
	unhide: function() {
		action = new TYPO3.FeEdit.UnhideAction(this);
		action.trigger();
	},
	remove: function() {
		action = new TYPO3.FeEdit.DeleteAction(this);
		action.trigger();
	},

	moveAfter: function(afterUID) {
		extraParam = 'TSFE_EDIT[moveAfter]=' + afterUID;
		action = new TYPO3.FeEdit.MoveAfterAction(this);
		action.trigger(extraParam);
	},

	save: function() {
		action = new TYPO3.FeEdit.SaveAction(this);
		action.trigger();
	},

	close: function() {
		action = new TYPO3.FeEdit.CloseAction(this);
		action.trigger();
	},

	saveAndClose: function() {
		action = new TYPO3.FeEdit.SaveAndCloseAction(this);
		action.trigger();
	},

	cut: function() {
		action = new TYPO3.FeEdit.CutAction(this);
		action.trigger();
	},

	copy: function() {
		action = new TYPO3.FeEdit.CopyAction(this);
		action.trigger();
	},

	paste: function(additionalParams) {
		action = new TYPO3.FeEdit.PasteAction(this);
		action.trigger();
	},

	up: function(additionalParams) {
		// @todo Need possibly a MoveUpDownAction...
		action = new TYPO3.FeEdit.UpAction(this);
		action.trigger();
	},

	down: function(additionalParams) {
		action = new TYPO3.FeEdit.DownAction(this);
		action.trigger();
	}	
});


TYPO3.FeEdit.ClipboardObj = Ext.extend(TYPO3.FeEdit.Base, {
	showClipboard: function(onOff) {
		if (onOff) {
			Ext.get('feEditAdvanced-clipboardToolbar').show();
		} else {
			Ext.get('feEditAdvanced-clibpoardToolbar').hide();
		}
	},

	addToClipboard: function(editPanelAction) {
		this.showClipboard(true);
			// create & cleanup "display" string
		strVal = editPanelAction.parent.el.select('.feEditAdvanced-contentWrapper').first().innerHTML;
			 // strip tags
		strVal = strVal.replace(/(<([^>]+)>)/ig,"");
			 // trim spaces
		strVal = strVal.replace(/^\s+|\s+$/g, '');
			// first 12 chars
		strVal = strVal.substr(0,12);

			// determine which clip object to add to
		if (!clipboardObj.select('#clip1').first()) {
			clipID = 'clip1';
		} else if (!clipboardObj.select('#clip2').first()) {
			clipID = 'clip2';
		} else if (!clipboardObj.select('#clip3').first()) {
			clipID = 'clip3';
		} else {
				 // if all are used, overwrite first one
			clipID = 'clip1';
		}
			// grab the UID (so can easily search based on this)
		if (rec = editPanelAction.parent.record) {
			splt = rec.indexOf(':');
			thisUID = rec.substr(splt+1);
		}
			// build a clipboard object that has values from content element
		pasteValues = '<input type="hidden" name="TSFE_EDIT[cmd]" value="' + editPanelAction._getCmd() + '"><input type="hidden" name="TSFE_EDIT[record]" value="' + rec + '"><input type="hidden" name="TSFE_EDIT[pid]" value="' + editPanelAction.parent.pid + '"><input type="hidden" name="TSFE_EDIT[flexformPtr]" value="' + editPanelAction.parent.flexformPtr + '"><input type="hidden" name="TSFE_EDIT[uid]" value="' + thisUID + '">';
		clearBtn = '<div class="clearBtn" id="clearBtn' + thisUID + '"> </div>';
		pasteEl = '<div class="clipContainer" id="' + clipID + '"><div class="feEditAdvanced-draggable clipObj"><form name="TSFE_EDIT_FORM_' + thisUID + '">' +
					strVal + pasteValues +
					'</form></div>' + clearBtn + '</div>';
		newEl = clipboardObj.append(pasteEl);

			// allow to click on clear button
		thisBtn = Ext.get('clearBtn' + thisUID);
		thisBtn.on('click', this.clickClearClipboard, this);

			// make the element draggable
		toolbar.addDraggable(Ext.get(clipObj));

	},

		// find the element that clicked on
	clickClearClipboard: function(evt) {
		var element = evt.getTarget('div');
		this.clearClipboard(element);
	},

	clearClipboard: function(element) {
			// hide (or delete) the clipboard object
		clipObj = element.up();
		clipObj = Ext.get(clipObj);
		clipObj.hide();

			// clear out the marked content element
		var editPanelId = clipObj.select('form input[name="TSFE_EDIT[record]"]').first().getValue();
		if (editPanelId) {
			Ext.get(editPanelId).removeClass('doCopy');
			Ext.get(editPanelId).removeClass('doCut');
		}

		// @TODO check to see if anything on clipboard...if empty, then hide
		// this.showClipboard(false);

	}

});


TYPO3.FeEdit.EditWindow = Ext.extend(TYPO3.FeEdit.Base, {
	editPanel: null,
	targetID: null,
	
	constructor: function(editPanel) {
		this.editPanel = editPanel;
		if (!Ext.ux.Lightbox.hasListener('close')) {
			Ext.ux.Lightbox.addListener('close', this.close, this);
		}
	},
	
	displayLoadingMessage: function(message) {
		Ext.ux.Lightbox.openMessage(message, 200, 120, true);
	},
	
	displayStaticMessage: function(message) {
		Ext.ux.Lightbox.openMessage(message, 200, 100, false);
	},

	displayIframe: function(headerText, url) {
		height = TYPO3.configuration.feeditadvanced.editWindow.height ? parseInt(TYPO3.configuration.feeditadvanced.editWindow.height) : 600;
		width = TYPO3.configuration.feeditadvanced.editWindow.width ? parseInt(TYPO3.configuration.feeditadvanced.editWindow.width) : 800;

		Ext.ux.Lightbox.openUrl({'href': url, 'title': headerText}, width, height);
	},

	close: function() {
		name = 'ux-lightbox-shim';
		if (window.frames[name].response) {
			json = window.frames[name].response;

			if (json.error) {
				this.displayStaticMessage(json.error);
			} else if (json.url) {
				window.location = json.url;
			} else if (json.uid == 'NEW') {
				// New element was not saved so do nothing and discard it.
			} else if (this.editPanel) {
				this.editPanel.pushContentUpdate(json);
			} else if (this.targetID) {
				Ext.DomHelper.insertAfter(Ext.get(this.targetID), json.content);
				FrontendEditing.scanForEditPanels();
			} else {
				alert(TYPO3.LLL.generalError);
			}
		}
		FrontendEditing.editPanelsEnabled = true;

			// Reset elements to be validated by TBE_EDITOR.
		if (typeof(TBE_EDITOR) != 'undefined') {
			TBE_EDITOR.elements = {};
			TBE_EDITOR.nested = {'field':{}, 'level':{}};
		}
	},

	setTargetID: function(id) {
		this.targetID = id;
	}
});

/*
 * Main class for storing all current values
 * relevant for frontend editing
 */
var FrontendEditing = {
	clipboard: new TYPO3.FeEdit.ClipboardObj(),
	editPanels: new Ext.util.MixedCollection(),
	editPanelsEnabled: true,
	JSHandler: new TYPO3.FeEdit.AJAXJavascriptHandler(),
	toolbar: null,
	actionRunning: false,
	editWindow: null,
	showHiddenContentElements: true,
	dropZones: [],	//stores all dropzones that are 

	init: function() {
		Ext.getBody().addClass('feEditAdvanced');
		this.scanForEditPanels();
		this.initializeMenuBar();
		this.updatePageStyling();
	},

		// @todo	We eventually want to encapsulate this in a class or something, but it
		//		gives us a quick way to re-register all EditPanels when new content is added.
	scanForEditPanels: function() {
		// Create all the EditPanels and stick them in an array
		Ext.each(Ext.query('div.feEditAdvanced-allWrapper'), function (el) {
			if (el.id) {
				if (!this.editPanels.get(el.id)) {
					this.editPanels.add(el.id, new TYPO3.FeEdit.EditPanel(el));
				} else {
						panel = this.editPanels.get(el.id);
						// @todo We need to do some kind of re-initialization here so that drop zones are correct.
						if (panel.el.hasClass('feEditAdvanced-draggable') && !panel.isPagePanel) {
							panel._makeDraggable();
						}
				}
			}
		}, this);
		this.checkContentElements();

		// @todo This requires TV! Move it to feEditTV.js
		if (typeof FrontendEditing.updatePointerElements == 'function') {
			FrontendEditing.updatePointerElements();
		}
		if (typeof FrontendEditing.addFlexformPointers == 'function') {
			FrontendEditing.addFlexformPointers();
		}
	},
	
	initializeMenuBar: function() {
		this.toolbar = new TYPO3.FeEdit.Toolbar('feEditAdvanced-menuBar');
		this.showHiddenContentElements = parseInt(Ext.get('TSFE_ADMIN_PANEL-preview_showHiddenRecords').getValue());

		var cb = Ext.get('feEditAdvanced-showHiddenContent-input');
		if (cb) {
			cb.on('click', this.toggleHiddenContentElements, this);
			if (this.showHiddenContentElements) {
				cb.set({'checked': 'checked'});
			} else {
				this.toggleHiddenContentElements();
			}
		}
	},

		// Update page styling to account for the menu bar at the top. Currently, background-position is adjusted.
	updatePageStyling: function() {
		var body = Ext.getBody();
		backgroundPosition = body.getStyle('background-position');
		if (backgroundPosition) {
			backgroundPosition = backgroundPosition.split(' ');
			xPosition = backgroundPosition[0];
			yPosition = backgroundPosition[1];
		} else {
			xPosition = 0;
			yPosition = 0;
		}
		var menuBarHeight = Ext.get('feEditAdvanced-menuBar').getHeight();
		if (yPosition == '0' || yPosition == '0px' || yPosition == '0pt' || yPosition == '0%') {
			body.setStyle('background-position', xPosition + ' ' + menuBarHeight + 'px');
		} else if (yPosition.indexOf('px')) {
			body.setStyle('background-position', xPosition + ' ' + (parseInt(yPosition.substr(0, yPosition.length-2)) + menuBarHeight) + 'px');
		}

		// If the firstWrapper is behind the menu, shift it down so that it and all edit panels are visible.
		var menuBarBottom = Ext.get('feEditAdvanced-menuBar').getBottom();
		Ext.select('div.feEditAdvanced-firstWrapper').each(function(wrapper) {
			if (wrapper.getTop() <= (menuBarBottom + 5)) {
				wrapper.setStyle('margin-top', (menuBarHeight + 25) + 'px');
			}
		});
	},

		// Enable drop indicators when a drag is started.
	activateDropZones: function(draggedPanel) {
		FrontendEditing.editPanelsEnabled = false;
		
		var prevPanel;
		if (draggedPanel) {
			prevPanel = FrontendEditing.editPanels.get(draggedPanel.getPreviousContentElement().id);
		}

		FrontendEditing.editPanels.each(function(panel) {
			panel.disable();
				// Add a dropzone for all edit panels except for two
				// 1) The panel being dragged
				// 2) The panel before the one being dragged (prevPanel)
			if (!draggedPanel || ((draggedPanel !== panel) && (prevPanel !== panel))) {
				panel.addDropZone();
			}
		});

		// go through each Content element container and add a dropZone
		Ext.select('div.feEditAdvanced-firstWrapper').each(function(containerElement) {
			FrontendEditing.dropZones.push(new TYPO3.FeEdit.DropZone(containerElement, true));
		});
		Ext.getBody().setStyle('cursor', 'move');
	},
	
		// Disable drop indicators when a drag is done
	deactivateDropZones: function() {
		FrontendEditing.editPanelsEnabled = true;
		FrontendEditing.editPanels.each(function(panel) {
			panel.removeDropZone();
			panel.enable();
		});
		Ext.getBody().setStyle('cursor', 'pointer');

		// go through each Content element container and remove the dropZone
		Ext.each(this.dropZones, function(dropZone) {
			dropZone.remove();
		});
		this.dropZones = [];
		this.checkContentElements();
	},
	
	/**
	 * checks if one of the CEs is too small, thus adds another class, so it is modifiable
	 * via CSS
	 * could later be used for some other functionalities that need to be done after certain things
	 */
	checkContentElements: function() {
		this.editPanels.each(function(panel) {
			if (panel.el.getWidth() < 300) {
				panel.el.addClass('feEditAdvanced-contentWrapperSmall');
			} else {
				panel.el.removeClass('feEditAdvanced-contentWrapperSmall');
			}
			panel.updateUpDownButtons();
		});
	},

	/** 
	 * used when the checkbox is used
	 */
	toggleHiddenContentElements: function() {
		var isChecked = Ext.get('feEditAdvanced-showHiddenContent-input').is(':checked');
		var admPanelEl = Ext.get('TSFE_ADMIN_PANEL-preview_showHiddenRecords');
		this.editPanels.each(function(panel) {
			if (panel.el.hasClass('feEditAdvanced-hiddenElement')) {
				panel.el.setStyle('display', (isChecked ? 'block' : 'none'));
			}
		});
		this.showHiddenContentElements = isChecked;
		
		// save the new value in the backend (currently not needed as it's overriden by TSconfig anyway)
		var newAdmPanelElValue = (admPanelEl.getValue() == '1' ? '0' : '1');
		admPanelEl.set({'value': newAdmPanelElValue});
		var frm = Ext.get('TSFE_ADMIN_PANEL_Form');
		Ext.Ajax.request({
			'url': frm.getAttribute('action'),
			'form': frm,
			'disableCaching': true
		});
	}
};


/**
 * class for rendering the horizontal slider
 */
TYPO3.FeEdit.ContentTypeToolbar = Ext.extend(TYPO3.FeEdit.Base, {
	toolbarWidth: 0,
	totalElementWidth: 0,
	el: null,
	innerEl: null,
	totalWidth: null,

	constructor: function() {
		this.el = Ext.get('feEditAdvanced-contentTypeToolbar');

		// create two divs. the first will hold the scrolling div, th
		// latter will contain all contentTypeItems
		this.innerEl = this.el.insertFirst({
			'id':  'feEditAdvanced-contentTypeToolbar-inner',
			'tag': 'div',
			'cn': [{
				'tag': 'div',
				'id': 'feEditAdvanced-contentTypeToolbar-scroller'
			}]
		});


		// move all draggables in the scrolling container
		Ext.select('.feEditAdvanced-contentTypeItem', false, this.el.dom).each(function (el) { 
			el = Ext.get(el);
			this.totalElementWidth += el.getWidth();
			el.appendTo(this.innerEl.first());
		}, this);

		// create the arrows
		Ext.DomHelper.insertBefore(this.innerEl, {
			tag: 'div',
			id: 'feEditAdvanced-contentTypeToolbar-arrLeft',
			html: '&laquo;'
		}, true).on('click', function() { this.scroll('left'); }, this);
		Ext.DomHelper.insertAfter(this.innerEl, {
			tag: 'div',
			id: 'feEditAdvanced-contentTypeToolbar-arrRight',
			html: '&raquo;'
		}, true).on('click', function() { this.scroll('right'); }, this);

		// reset the left margin and set the innerWidth of the scroller to the maximum size
		this.el.setStyle('marginLeft', '0px');
		Ext.get('feEditAdvanced-contentTypeToolbar-scroller').setWidth(this.totalElementWidth + 20);

		// calculate the width for the scrolling bar and set up an event for that
		this.recalculateAvailableWidth();
		Ext.EventManager.on(window, 'resize', this.recalculateAvailableWidth, this);
	},

	/**
	 * this method calculates the width in the secondRow that is available
	 * for the new Content Elements
	 */
	recalculateAvailableWidth: function() {
		var availableWidth = Ext.getBody().getWidth();

		// get all divs that are in the second row and subtract the available width
		Ext.select('div.feEditAdvanced-secondRow > div').each(function(el) {
			var el = Ext.get(el);
			var width = el.getWidth();
			if (el.dom.id != this.el.dom.id && width > 0) {
				availableWidth -= width;
			}
		}, this);
		this.toolbarWidth = availableWidth;
		this.el.setWidth(this.toolbarWidth);
		
		// check if scrolling is needed
		if (this.toolbarWidth <= this.totalElementWidth) {
			this.innerEl.setWidth(this.toolbarWidth - 40);
			Ext.get('feEditAdvanced-contentTypeToolbar-arrLeft').show();
			Ext.get('feEditAdvanced-contentTypeToolbar-arrRight').show();
		} else {
			// all icons have space without scrolling
			this.innerEl.setWidth('auto');
			this.el.setWidth(this.innerEl.getWidth() + 40);
			Ext.get('feEditAdvanced-contentTypeToolbar-arrLeft').hide();
			Ext.get('feEditAdvanced-contentTypeToolbar-arrRight').hide();
		}
	},

	/*
	 * this method actually does the scrolling, which is currently done by a certain fix
	 * amount of pixels (100), but could be on a per item basis later on
	 */
	scroll: function(direction) {
		var by = -100;
		var currentPos = parseInt(this.innerEl.first().getStyle('marginLeft'));
		var anim = {by: by, unit: 'px' };
		if (direction == 'left') {
			anim.by = Math.abs(by);
			if (currentPos >= 0) {
				anim = { to: 0, unit: 'px' };
			}
		} else {
			if (Math.abs(currentPos) >= (this.totalElementWidth-this.toolbarWidth)) {
				anim = { by: 0, unit: 'px' };
			}
		}
		this.innerEl.first().animate({ marginLeft: anim }, 0.3, null, 'easeOut', 'run');
	}
});


// Set the edit panels and menu bar on window load 
Ext.onReady(function() { 
	FrontendEditing.init();  
	new TYPO3.FeEdit.ContentTypeToolbar();
});
