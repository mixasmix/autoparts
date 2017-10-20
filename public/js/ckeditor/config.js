/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
        CKEDITOR.config.indentClasses = ["ul-grey", "ul-red", "text-red", "ul-content-red", "circle", "style-none", "decimal", "paragraph-portfolio-top", "ul-portfolio-top", "url-portfolio-top", "text-grey"];
CKEDITOR.config.protectedSource.push(/<(style)[^>]*>.*<\/style>/ig);
CKEDITOR.config.protectedSource.push(/<(script)[^>]*>.*<\/script>/ig);// разрешить теги <script>
CKEDITOR.config.protectedSource.push(/<\?[\s\S]*?\?>/g);// разрешить php-код
CKEDITOR.config.protectedSource.push(/<!--dev-->[\s\S]*<!--\/dev-->/g);
CKEDITOR.config.allowedContent = true; /* all tags */
CKEDITOR.config.filebrowserUploadUrl = '/panelcontrol/panelcontrol/fileupload/';
};
