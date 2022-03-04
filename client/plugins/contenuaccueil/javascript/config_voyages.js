/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	
	config.extraPlugins = 'showblocks';
	
	// Define changes to default configuration here. For example:
	config.skin = 'moonocolor';
	config.language = 'fr';
	config.toolbar  = 'Basic';
	config.toolbar_Basic =
    [
        ['Format', 'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-','PasteText','ShowBlocks','Source']

    ];
  config.forcePasteAsPlainText = true;
  config.resize_enabled = false;
  config.format_tags = 'p;h2;h3;h4'; 
  config.height = "300px";
  config.startupOutlineBlocks = true;
  
  //Garder les fonds en cas de copie de tableaux
  config.pasteFromWordRemoveStyles = false;
  
  //souligner en XHTML Strict
  config.coreStyles_underline = { element : 'span', attributes : {'style': 'text-decoration:underline;'}};
  
  config.bodyClass='texte';
  config.contentsCss = ['/template/css/initialisation.css','/template/css/texte.css'];
  
  config.filebrowserBrowseUrl = '/client/plugins/ckeditor/kcfinder/browse.php?type=files';
  config.filebrowserImageBrowseUrl = '/client/plugins/ckeditor/kcfinder/browse.php?type=images';
  config.filebrowserFlashBrowseUrl = '/client/plugins/ckeditor/kcfinder/browse.php?type=flash';
  config.filebrowserUploadUrl = '/client/plugins/ckeditor/kcfinder/upload.php?type=files';
  config.filebrowserImageUploadUrl = '/client/plugins/ckeditor/kcfinder/upload.php?type=images';
  config.filebrowserFlashUploadUrl = '/client/plugins/ckeditor/kcfinder/upload.php?type=flash';
  
};

