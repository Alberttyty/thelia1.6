/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	
	// Define changes to default configuration here. For example:
	config.skin = 'moonocolor';
	config.language = 'fr';
	config.toolbar  = 'Basic';
	config.toolbar_Basic =
    [
        ['Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'Image', 'Table'],
        ['Undo','Redo','-','Cut','Copy','Paste','PasteText','PasteFromWord','ShowBlocks','Source']

    ];
  config.forcePasteAsPlainText = true;
  config.resize_enabled = false;
  config.height = "300px";
  config.startupOutlineBlocks = true;
  
  //Verificateur d'orthographe du navigateur
  config.disableNativeSpellChecker = false;
  
  //Garder les fonds en cas de copie de tableaux
  //config.pasteFromWordRemoveStyles = false;
  
  //souligner en XHTML Strict
  config.coreStyles_underline = { element : 'span', attributes : {'style': 'text-decoration:underline;'}};
  
  config.bodyClass='wysiwyg_body texte';
  
  config.filebrowserBrowseUrl = '/client/plugins/ckeditor/kcfinder/browse.php?type=files';
  config.filebrowserImageBrowseUrl = '/client/plugins/ckeditor/kcfinder/browse.php?type=images';
  config.filebrowserFlashBrowseUrl = '/client/plugins/ckeditor/kcfinder/browse.php?type=flash';
  config.filebrowserUploadUrl = '/client/plugins/ckeditor/kcfinder/upload.php?type=files';
  config.filebrowserImageUploadUrl = '/client/plugins/ckeditor/kcfinder/upload.php?type=images';
  config.filebrowserFlashUploadUrl = '/client/plugins/ckeditor/kcfinder/upload.php?type=flash';
  
};

CKEDITOR.on( 'instanceReady', function( ev )
{
	var writer = ev.editor.dataProcessor.writer; 
	// The character sequence to use for every indentation step.
	writer.indentationChars = '';
 
	/*var dtd = CKEDITOR.dtd;
	// Elements taken as an example are: block-level elements (div or p), list items (li, dd), and table elements (td, tbody).
	for ( var e in CKEDITOR.tools.extend( {}, dtd.$block, dtd.$listItem, dtd.$tableContent ) )
	{
		ev.editor.dataProcessor.writer.setRules( e, {
			// Indicates that an element creates indentation on line breaks that it contains.
			indent : false,
			// Inserts a line break before a tag.
			breakBeforeOpen : true,
			// Inserts a line break after a tag.
			breakAfterOpen : false,
			// Inserts a line break before the closing tag.
			breakBeforeClose : false,
			// Inserts a line break after the closing tag.
			breakAfterClose : true
		});
	}*/
	
	/***No Drag and Drop from Outside***/
	
	var dragstart_outside = true;
	ev.editor.document.on('dragstart', function (ev) {
	   dragstart_outside = false;
	});
	ev.editor.document.on('drop', function (ev) {
	   if (dragstart_outside) {
		  ev.data.preventDefault(true);
	   }
	   dragstart_outside = true;
	});
	
	/***********************************/
	
	
});

CKEDITOR.on('dialogDefinition', function( ev ) {
  var dialogName = ev.data.name;
  var dialogDefinition = ev.data.definition;
	
  if(dialogName === 'link') {
	  dialogDefinition.removeContents('target');
	  var infoTab = dialogDefinition.getContents( 'info' );
	  /*infoTab.remove('linkType');*/
  }
  if(dialogName === 'image') {
	dialogDefinition.removeContents('Link');
	var infoTab = dialogDefinition.getContents( 'info' );
	infoTab.remove('txtBorder');
	infoTab.remove('txtHSpace');
	infoTab.remove('txtVSpace');
	/*infoTab.remove('txtUrl');*/
	infoTab.remove('txtAlt');
	/*infoTab.remove( 'cmbAlign' );*/
  }
	
  if(dialogName === 'table') {
    var infoTab = dialogDefinition.getContents('info');
    var cellSpacing = infoTab.get('txtCellSpace');
    cellSpacing['default'] = "0";
    var cellPadding = infoTab.get('txtCellPad');
    cellPadding['default'] = "0";
    var border = infoTab.get('txtBorder');
    border['default'] = "0";
	var width = infoTab.get('txtWidth');
    width['default'] = "100%";
	infoTab.remove('txtCellSpace');
	infoTab.remove('txtCellPad');
	infoTab.remove('txtBorder');
	/*infoTab.remove('txtCaption');
	infoTab.remove('txtSummary');*/
	infoTab.remove('selHeaders');
	infoTab.remove('cmbAlign');
	/*var widthtype = infoTab.get('WidthType');
    widthtype['default'] = "percents";*/
  }
});