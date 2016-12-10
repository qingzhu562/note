/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = 'zh-cn';
	// config.uiColor = '#AADC6E';
	// 
	config.toolbarGroups = [
		{ name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'others' },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'styles', groups: ['undo']},
		{ name: 'colors' }
	];
	config.filebrowserUploadUrl = '/admin/upload/editor';
	config.filebrowserImageUploadUrl =  '/admin/upload/editor?type=Images' ;  
	config.filebrowserFlashUploadUrl =  '/admin/upload/editor?type=Flash' ;

	//所需要添加的CSS文件 在此添加 可使用相对路径和网站的绝对路径
	//config.contentsCss = "ckeditor/contents.css"
	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript,Iframe,Print,Save';

	// Set the most common block elements.
	//config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';
};
