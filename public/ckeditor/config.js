/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.config.allowedContent = true;﻿
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
    config.defaultLanguage = 'en';
    config.language = userLang;
    config.enterMode = CKEDITOR.ENTER_BR;
    config.entities_greek = true;
    config.entities_latin = true;
    config.resize_enabled = false;
    config.forcePasteAsPlainText = true;
//    config.width = "610px";
    config.height = "150px";
    config.toolbar = [
        ["Source", "-", "NewPage", "Preview", "Print"],
        ["Cut", "Copy", "Paste", "PasteText", "PasteFromWord", "-", "Undo", "Redo"],
        ["Find", "Replace", "-", "SelectAll", "-", "Scayt"],
        ["ShowBlocks", "Maximize"],
        "/",
        ["Font", "FontSize"],
        ["Bold", "Italic", "Underline", "Strike", "-", "Subscript", "Superscript"],
        ["TextColor", "BGColor"],
        ["RemoveFormat"],
        ["Zoom"],
        "/",
        ["JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock", "-", "NumberedList", "BulletedList", "-", "Outdent", "Indent", "-", "BidiLtr", "BidiRtl"],
        ["Link", "Unlink"],
        ["Image", "EqnEditor", "Flash", "SpecialChar", "Table", "HorizontalRule", "Smiley"],["textInput","Video"]
    ];
    config.extraPlugins ='htmlbuttons,video';
};

