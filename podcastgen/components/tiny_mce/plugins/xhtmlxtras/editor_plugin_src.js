 /**
 * $Id: editor_plugin_src.js 42 2006-08-08 14:32:24Z spocke $
 *
 * @author Moxiecode - based on work by Andrew Tetlaw
 * @copyright Copyright � 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('xhtmlxtras');

var TinyMCE_XHTMLXtrasPlugin = {
	getInfo : function() {
		return {
			longname : 'XHTML Xtras Plugin',
			author : 'Moxiecode Systems AB',
			authorurl : 'http://tinymce.moxiecode.com',
			infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/xhtmlxtras',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},

	initInstance : function(inst) {
		tinyMCE.importCSS(inst.getDoc(), tinyMCE.baseURL + "/plugins/xhtmlxtras/css/xhtmlxtras.css");
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "cite":
				return tinyMCE.getButtonHTML(cn, 'lang_xhtmlxtras_cite_desc', '{$pluginurl}/images/cite.gif', 'mceCite', true);

			case "acronym":
				return tinyMCE.getButtonHTML(cn, 'lang_xhtmlxtras_acronym_desc', '{$pluginurl}/images/acronym.gif', 'mceAcronym', true);

			case "abbr":
				return tinyMCE.getButtonHTML(cn, 'lang_xhtmlxtras_abbr_desc', '{$pluginurl}/images/abbr.gif', 'mceAbbr', true);

			case "del":
				return tinyMCE.getButtonHTML(cn, 'lang_xhtmlxtras_del_desc', '{$pluginurl}/images/del.gif', 'mceDel', true);

			case "ins":
				return tinyMCE.getButtonHTML(cn, 'lang_xhtmlxtras_ins_desc', '{$pluginurl}/images/ins.gif', 'mceIns', true);

			case "attribs":
				return tinyMCE.getButtonHTML(cn, 'lang_xhtmlxtras_attribs_desc', '{$pluginurl}/images/attribs.gif', 'mceAttributes', true);
		}

		return "";
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		var template, inst, elm;

		switch (command) {
			case "mceCite":
				if (!this._anySel(editor_id))
					return true;

				template = new Array();
				template['file'] = '../../plugins/xhtmlxtras/cite.htm';
				template['width'] = 350;
				template['height'] = 250;
				tinyMCE.openWindow(template, {editor_id : editor_id});
				return true;

			case "mceAcronym":
				if (!this._anySel(editor_id))
					return true;

				template = new Array();
				template['file'] = '../../plugins/xhtmlxtras/acronym.htm';
				template['width'] = 350;
				template['height'] = 250;
				tinyMCE.openWindow(template, {editor_id : editor_id});
				return true;

			case "mceAbbr":
				if (!this._anySel(editor_id))
					return true;

				template = new Array();
				template['file'] = '../../plugins/xhtmlxtras/abbr.htm';
				template['width'] = 350;
				template['height'] = 250;
				tinyMCE.openWindow(template, {editor_id : editor_id});
				return true;

			case "mceIns":
				if (!this._anySel(editor_id))
					return true;

				template = new Array();
				template['file'] = '../../plugins/xhtmlxtras/ins.htm';
				template['width'] = 350;
				template['height'] = 310;
				tinyMCE.openWindow(template, {editor_id : editor_id});
				return true;

			case "mceDel":
				if (!this._anySel(editor_id))
					return true;

				template = new Array();
				template['file'] = '../../plugins/xhtmlxtras/del.htm';
				template['width'] = 350;
				template['height'] = 310;
				tinyMCE.openWindow(template, {editor_id : editor_id});
				return true;

			case "mceAttributes":
				inst = tinyMCE.getInstanceById(editor_id);
				elm = inst.getFocusElement();

				if (elm && elm.nodeName !== 'BODY' && elm.className.indexOf('mceItem') == -1) {
					tinyMCE.openWindow({
						file : '../../plugins/xhtmlxtras/attributes.htm',
						width : 380,
						height : 370
					}, {editor_id : editor_id});
				}

				return true;
		}

		return false;
	},

	cleanup : function(type, content, inst) {
		if (type == 'insert_to_editor' && tinyMCE.isIE && !tinyMCE.isOpera) {
			content = content.replace(/<abbr([^>]+)>/gi, '<html:ABBR $1>');
			content = content.replace(/<\/abbr>/gi, '</html:ABBR>');
		}

		return content;
	},

	handleNodeChange : function(editor_id, node, undo_index,undo_levels, visual_aid, any_selection) {
		var elm = tinyMCE.getParentElement(node);

		if (node == null)
			return;

		tinyMCE.switchClass(editor_id + '_attribs', 'mceButtonDisabled');

		if (!any_selection) {
			// Disable the buttons
			tinyMCE.switchClass(editor_id + '_cite', 'mceButtonDisabled');
			tinyMCE.switchClass(editor_id + '_acronym', 'mceButtonDisabled');
			tinyMCE.switchClass(editor_id + '_abbr', 'mceButtonDisabled');
			tinyMCE.switchClass(editor_id + '_del', 'mceButtonDisabled');
			tinyMCE.switchClass(editor_id + '_ins', 'mceButtonDisabled');
		} else {
			// A selection means the buttons should be active.
			tinyMCE.switchClass(editor_id + '_cite', 'mceButtonNormal');
			tinyMCE.switchClass(editor_id + '_acronym', 'mceButtonNormal');
			tinyMCE.switchClass(editor_id + '_abbr', 'mceButtonNormal');
			tinyMCE.switchClass(editor_id + '_del', 'mceButtonNormal');
			tinyMCE.switchClass(editor_id + '_ins', 'mceButtonNormal');
		}

		if (elm && elm.nodeName != 'BODY' && elm.className.indexOf('mceItem') == -1)
			tinyMCE.switchClass(editor_id + '_attribs', 'mceButtonNormal');

		switch (node.nodeName) {
			case "CITE":
				tinyMCE.switchClass(editor_id + '_cite', 'mceButtonSelected');
				return true;

			case "ACRONYM":
				tinyMCE.switchClass(editor_id + '_acronym', 'mceButtonSelected');
				return true;

			case "abbr": // IE
			case "HTML:ABBR": // FF
			case "ABBR":
				tinyMCE.switchClass(editor_id + '_abbr', 'mceButtonSelected');
				return true;

			case "DEL":
				tinyMCE.switchClass(editor_id + '_del', 'mceButtonSelected');
				return true;

			case "INS":
				tinyMCE.switchClass(editor_id + '_ins', 'mceButtonSelected');
				return true;
		}

		return true;
	},

	_anySel : function(editor_id) {
		var inst = tinyMCE.getInstanceById(editor_id), pe;

		pe = tinyMCE.getParentElement(inst.getFocusElement(), 'CITE,ACRONYM,ABBR,HTML:ABBR,DEL,INS');

		return pe || !inst.selection.isCollapsed();
	}
};

tinyMCE.addPlugin("xhtmlxtras", TinyMCE_XHTMLXtrasPlugin);
