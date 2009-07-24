/**
 * $Id: editor_plugin_src.js 264 2007-04-26 20:53:09Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
if (!tinyMCE.settings['contextmenu_skip_plugin_css']) {
	tinyMCE.loadCSS(tinyMCE.baseURL + "/plugins/contextmenu/css/contextmenu.css");
}

var TinyMCE_ContextMenuPlugin = {
	// Private fields
	_contextMenu : null,

	getInfo : function() {
		return {
			longname : 'Context menus',
			author : 'Moxiecode Systems AB',
			authorurl : 'http://tinymce.moxiecode.com',
			infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/contextmenu',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},

	initInstance : function(inst) {
		// Is not working on MSIE 5.0 or Opera no contextmenu event
		if (tinyMCE.isMSIE5_0 && tinyMCE.isOpera)
			return;

		TinyMCE_ContextMenuPlugin._contextMenu = new TinyMCE_ContextMenu({
			commandhandler : "TinyMCE_ContextMenuPlugin._commandHandler",
			spacer_image : tinyMCE.baseURL + "/plugins/contextmenu/images/spacer.gif"
		});

		// Add hide event handles
		tinyMCE.addEvent(inst.getDoc(), "click", TinyMCE_ContextMenuPlugin._hideContextMenu);
		tinyMCE.addEvent(inst.getDoc(), "keypress", TinyMCE_ContextMenuPlugin._hideContextMenu);
		tinyMCE.addEvent(inst.getDoc(), "keydown", TinyMCE_ContextMenuPlugin._hideContextMenu);
		tinyMCE.addEvent(document, "click", TinyMCE_ContextMenuPlugin._hideContextMenu);
		tinyMCE.addEvent(document, "keypress", TinyMCE_ContextMenuPlugin._hideContextMenu);
		tinyMCE.addEvent(document, "keydown", TinyMCE_ContextMenuPlugin._hideContextMenu);

		// Attach contextmenu event
		if (tinyMCE.isGecko) {
			tinyMCE.addEvent(inst.getDoc(), "contextmenu", function(e) {TinyMCE_ContextMenuPlugin._showContextMenu(tinyMCE.isMSIE ? inst.contentWindow.event : e, inst);});
		} else
			tinyMCE.addEvent(inst.getDoc(), "contextmenu", TinyMCE_ContextMenuPlugin._onContextMenu);
	},

	// Private plugin internal methods

	_onContextMenu : function(e) {
		var elm = tinyMCE.isMSIE ? e.srcElement : e.target;
		var targetInst, body;

		// Find instance
		if ((body = tinyMCE.getParentElement(elm, "body")) != null) {
			for (var n in tinyMCE.instances) {
				var inst = tinyMCE.instances[n];
				if (!tinyMCE.isInstance(inst))
					continue;

				if (body == inst.getBody()) {
					targetInst = inst;
					break;
				}
			}

			return TinyMCE_ContextMenuPlugin._showContextMenu(tinyMCE.isMSIE ? targetInst.contentWindow.event : e, targetInst);
		}
	},

	_showContextMenu : function(e, inst) {
		if (e.ctrlKey)
			return true;

		function getAttrib(elm, name) {
			return elm.getAttribute(name) ? elm.getAttribute(name) : "";
		}

		var x, y, elm, contextMenu;
		var pos = tinyMCE.getAbsPosition(inst.iframeElement);

		x = tinyMCE.isMSIE ? e.screenX : pos.absLeft + (e.pageX - inst.getBody().scrollLeft);
		y = tinyMCE.isMSIE ? e.screenY : pos.absTop + (e.pageY - inst.getBody().scrollTop);
		elm = tinyMCE.isMSIE ? e.srcElement : e.target;

		contextMenu = this._contextMenu;
		contextMenu.inst = inst;

		// Mozilla needs some time
		window.setTimeout(function () {
			var theme = tinyMCE.getParam("theme");

			contextMenu.clearAll();
			var sel = inst.selection.getSelectedText().length != 0 || elm.nodeName == "IMG";

			// Default items
			contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/cut.gif", "$lang_cut_desc", "Cut", "", !sel);
			contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/copy.gif", "$lang_copy_desc", "Copy", "", !sel);
			contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/paste.gif", "$lang_paste_desc", "Paste", "", false);

			if (sel || (elm ? (elm.nodeName == 'A' && tinyMCE.getAttrib(elm, 'name') == '') || (elm.nodeName == 'IMG') : false)) {
				contextMenu.addSeparator();
				contextMenu.addItem(tinyMCE.baseURL + "/themes/advanced/images/link.gif", "$lang_link_desc", inst.hasPlugin("advlink") ? "mceAdvLink" : "mceLink");
				contextMenu.addItem(tinyMCE.baseURL + "/themes/advanced/images/unlink.gif", "$lang_unlink_desc", "unlink", "", (elm ? (elm.nodeName != 'A') && (elm.nodeName != 'IMG') : true));
			}

			// Get element
			elm = tinyMCE.getParentElement(elm, "img,table,td" + (inst.hasPlugin("advhr") ? ',hr' : ''));
			if (elm) {
				switch (elm.nodeName) {
					case "IMG":
						contextMenu.addSeparator();

						// If flash
						if (tinyMCE.hasPlugin('flash') && tinyMCE.getAttrib(elm, 'class').indexOf('mceItemFlash') != -1)
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/flash/images/flash.gif", "$lang_flash_props", "mceFlash");
						else if (tinyMCE.hasPlugin('media') && /mceItem(Flash|ShockWave|WindowsMedia|QuickTime|RealMedia)/.test(tinyMCE.getAttrib(elm, 'class')))
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/flash/images/flash.gif", "$lang_media_title", "mceMedia");
						else
							contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/image.gif", "$lang_image_props_desc", inst.hasPlugin("advimage") ? "mceAdvImage" : "mceImage");
						break;

					case "HR":
						contextMenu.addSeparator();
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/advhr/images/advhr.gif", "$lang_insert_advhr_desc", "mceAdvancedHr");
						break;

					case "TABLE":
					case "TD":
						// Is table plugin loaded
						if (inst.hasPlugin("table")) {
							var colspan = (elm.nodeName == "TABLE") ? "" : getAttrib(elm, "colspan");
							var rowspan = (elm.nodeName == "TABLE") ? "" : getAttrib(elm, "rowspan");

							colspan = colspan == "" ? "1" : colspan;
							rowspan = rowspan == "" ? "1" : rowspan;

							contextMenu.addSeparator();
							contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/cut.gif", "$lang_table_cut_row_desc", "mceTableCutRow");
							contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/copy.gif", "$lang_table_copy_row_desc", "mceTableCopyRow");
							contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/paste.gif", "$lang_table_paste_row_before_desc", "mceTablePasteRowBefore", "", inst.tableRowClipboard == null);
							contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/paste.gif", "$lang_table_paste_row_after_desc", "mceTablePasteRowAfter", "", inst.tableRowClipboard == null);

	/*						contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/justifyleft.gif", "$lang_justifyleft_desc", "JustifyLeft", "", false);
							contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/justifycenter.gif", "$lang_justifycenter_desc", "JustifyCenter", "", false);
							contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/justifyright.gif", "$lang_justifyright_desc", "JustifyRight", "", false);
							contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/justifyfull.gif", "$lang_justifyfull_desc", "JustifyFull", "", false);*/
							contextMenu.addSeparator();
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table.gif", "$lang_table_desc", "mceInsertTable", "insert");
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table.gif", "$lang_table_props_desc", "mceInsertTable");
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_cell_props.gif", "$lang_table_cell_desc", "mceTableCellProps");
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_delete.gif", "$lang_table_del", "mceTableDelete");
							contextMenu.addSeparator();
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_row_props.gif", "$lang_table_row_desc", "mceTableRowProps");
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_insert_row_before.gif", "$lang_table_row_before_desc", "mceTableInsertRowBefore");
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_insert_row_after.gif", "$lang_table_row_after_desc", "mceTableInsertRowAfter");
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_delete_row.gif", "$lang_table_delete_row_desc", "mceTableDeleteRow");
							contextMenu.addSeparator();
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_insert_col_before.gif", "$lang_table_col_before_desc", "mceTableInsertColBefore");
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_insert_col_after.gif", "$lang_table_col_after_desc", "mceTableInsertColAfter");
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_delete_col.gif", "$lang_table_delete_col_desc", "mceTableDeleteCol");
							contextMenu.addSeparator();
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_split_cells.gif", "$lang_table_split_cells_desc", "mceTableSplitCells", "", (colspan == "1" && rowspan == "1"));
							contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_merge_cells.gif", "$lang_table_merge_cells_desc", "mceTableMergeCells", "", false);
						}
						break;
				}
			}  else {
				// Add table specific
				if (inst.hasPlugin("table")) {
					contextMenu.addSeparator();
					contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table.gif", "$lang_table_desc", "mceInsertTable", "insert");
				}
			}

			contextMenu.show(x, y);
		}, 10);

		// Cancel default handeling
		tinyMCE.cancelEvent(e);
		return false;
	},

	_hideContextMenu : function() {
		if (TinyMCE_ContextMenuPlugin._contextMenu)
			TinyMCE_ContextMenuPlugin._contextMenu.hide();
	},

	_commandHandler : function(command, value) {
		var cm = TinyMCE_ContextMenuPlugin._contextMenu;

		cm.hide();

		// UI must be true on these
		var ui = false;
		if (command == "mceInsertTable" || command == "mceTableCellProps" || command == "mceTableRowProps" || command == "mceTableMergeCells")
			ui = true;

		if (command == "Paste")
			value = null;

		if (tinyMCE.getParam("dialog_type") == "modal" && tinyMCE.isMSIE) {
			// Cell properties will generate access denied error is this isn't done?!
			window.setTimeout(function() {
				cm.inst.execCommand(command, ui, value);
			}, 100);
		} else
			cm.inst.execCommand(command, ui, value);
	}
};

tinyMCE.addPlugin("contextmenu", TinyMCE_ContextMenuPlugin);

// Context menu class

function TinyMCE_ContextMenu(settings) {
	var doc, self = this;

	// Default value function
	function defParam(key, def_val) {
		settings[key] = typeof(settings[key]) != "undefined" ? settings[key] : def_val;
	}

	this.isMSIE = (navigator.appName == "Microsoft Internet Explorer");

	// Setup contextmenu div
	this.contextMenuDiv = document.createElement("div");
	this.contextMenuDiv.className = "contextMenu";
	this.contextMenuDiv.setAttribute("class", "contextMenu");
	this.contextMenuDiv.style.display = "none";
	this.contextMenuDiv.style.position = 'absolute';
	this.contextMenuDiv.style.zindex = 1000;
	this.contextMenuDiv.style.left = '0';
	this.contextMenuDiv.style.top = '0';
	this.contextMenuDiv.unselectable = "on";

	document.body.appendChild(this.contextMenuDiv);

	// Setup default values
	defParam("commandhandler", "");
	defParam("spacer_image", "images/spacer.gif");

	this.items = new Array();
	this.settings = settings;
	this.html = "";

	// IE Popup
	if (tinyMCE.isMSIE && !tinyMCE.isMSIE5_0 && !tinyMCE.isOpera) {
		this.pop = window.createPopup();
		doc = this.pop.document;
		doc.open();
		doc.write('<html><head><link href="' + tinyMCE.baseURL + '/plugins/contextmenu/css/contextmenu.css" rel="stylesheet" type="text/css" /></head><body unselectable="yes" class="contextMenuIEPopup"></body></html>');
		doc.close();
	}
};

TinyMCE_ContextMenu.prototype = {
	clearAll : function() {
		this.html = "";
		this.contextMenuDiv.innerHTML = "";
	},

	addSeparator : function() {
		this.html += '<tr class="contextMenuItem"><td class="contextMenuIcon"><img src="' + this.settings['spacer_image'] + '" width="20" height="1" class="contextMenuImage" /></td><td><img class="contextMenuSeparator" width="1" height="1" src="' + this.settings['spacer_image'] + '" /></td></tr>';
	},

	addItem : function(icon, title, command, value, disabled) {
		if (title.charAt(0) == '$')
			title = tinyMCE.getLang(title.substring(1));

		var onMouseDown = '';
		var html = '';

		if (tinyMCE.isMSIE && !tinyMCE.isMSIE5_0)
			onMouseDown = 'contextMenu.execCommand(\'' + command + '\', \'' + value + '\');return false;';
		else
			onMouseDown = this.settings['commandhandler'] + '(\'' + command + '\', \'' + value + '\');return false;';

		if (icon == "")
			icon = this.settings['spacer_image'];

		if (!disabled)
			html += '<tr class="contextMenuItem">';
		else
			html += '<tr class="contextMenuItemDisabled">';

		html += '<td class="contextMenuIcon"><img src="' + icon + '" width="20" height="20" class="contextMenuImage" /></td>';
		html += '<td><div class="contextMenuText">';
		html += '<a href="javascript:void(0);" onclick="' + onMouseDown + '" onmousedown="return false;">&#160;';

		// Add text
		html += title;

		html += '&#160;</a>';
		html += '</div></td>';
		html += '</tr>';

		// Add to main
		this.html += html;
	},

	show : function(x, y) {
		var vp, width, height, yo;

		if (this.html == "")
			return;

		var html = '';

		html += '<a href="#"></a><table border="0" cellpadding="0" cellspacing="0">';
		html += this.html;
		html += '</table>';

		this.contextMenuDiv.innerHTML = html;

		// Get dimensions
		this.contextMenuDiv.style.display = "block";
		width = this.contextMenuDiv.offsetWidth;
		height = this.contextMenuDiv.offsetHeight;
		this.contextMenuDiv.style.display = "none";

		if (tinyMCE.isMSIE && !tinyMCE.isMSIE5_0 && !tinyMCE.isOpera) {
			// Setup popup and show
			this.pop.document.body.innerHTML = '<div class="contextMenu">' + html + "</div>";
			this.pop.document.tinyMCE = tinyMCE;
			this.pop.document.contextMenu = this;
			this.pop.show(x, y, width, height);
		} else {
			vp = this.getViewPort();
			yo = tinyMCE.isMSIE5_0 ? document.body.scrollTop : self.pageYOffset;
			this.contextMenuDiv.style.left = (x > vp.left + vp.width - width ? vp.left + vp.width - width : x) + 'px';
			this.contextMenuDiv.style.top = (y > vp.top + vp.height - height ? vp.top + vp.height - height : y) + 'px';
			this.contextMenuDiv.style.display = "block";
		}
	},

	getViewPort : function() {
		return {
			left : self.pageXOffset || self.document.documentElement.scrollLeft || self.document.body.scrollLeft,
			top: self.pageYOffset || self.document.documentElement.scrollTop || self.document.body.scrollTop,
			width : document.documentElement.offsetWidth || document.body.offsetWidth,
			height : self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight
		};
	},

	hide : function() {
		if (tinyMCE.isMSIE && !tinyMCE.isMSIE5_0 && !tinyMCE.isOpera)
			this.pop.hide();
		else
			this.contextMenuDiv.style.display = "none";
	},

	execCommand : function(command, value) {
		eval(this.settings['commandhandler'] + "(command, value);");
	}
};
