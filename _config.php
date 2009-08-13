<?php
/** 
 * DataObjectManager config file
 * This can be used to store registrations for SortableDataObject
 * e.g. SortableDataObject::add_sortable_class(MyDataObject')
 * Followed by /dev/build
 *
 * Otherwise, put SortableDataObject registrations in mysite/_config.php
 *
 */
 
  // Allow DataObjectManager to take control of the AssetAdmin using the AssetManager field
  DataObjectManager::allow_assets_override(true);
  
  // Allow DataObjectManager to override some of the core CSS in the CMS (work in progress)
  DataObjectManager::allow_css_override(false);
  
  /*Object::add_extension('Form','DataObjectManagerForm');
  $handlers = Object::get_static('Form','url_handlers');
  $handlers['field/$FieldName!'] = 'handleDOMField';
  Object::add_static_var('Form','url_handlers', $handlers, true);
  Object::add_static_var('Form','allowed_actions',array(
    'httpSubmission',
    'handleField',
    'handleAction',
    'handleDOMField'
  ), true);*/
  
  
  /** SimpleWysiwygField: a lightweight alternative to TinyMCE. Mainly used for DOM Popups.
   *
   *  Usage: each array starts a new row. To group buttons, use a pipe '|' for a dotted separator.
   *
   *
   *  Configuring: use SimpleWysiwygField::set_default_configuration() for a global config (seen below)
   *               or use $object->configure(); for an instance-level config.
   *
   *  Available buttons:
   *
   *  cut          Cuts the selected text from the HtmlBox and places it in the clipboard. 
   *  copy         Copies the selected text in the HtmlBox and places it in the clipboard. 
   *  paste        Pastes the text from the clipboard in the HtmlBox at the place of the cursor. 
   *  bold         Makes bold the selected text in the HtmlBox. 
   *  italic       Makes italic the selected text in the HtmlBox. 
   *  underline    Makes underlined the selected text in the HtmlBox. 
   *  strike       Makes striked the selected text in the HtmlBox. 
   *  sup          Makes supersript the selected text in the HtmlBox. 
   *  sub          Makes subscript  the selected text in the HtmlBox. 
   *  left         Aligns the selected text in the HtmlBox to the left. 
   *  center       Aligns the selected text in the HtmlBox to the center. 
   *  right        Aligns the selected text in the HtmlBox to the right. 
   *  justify      Aligns the selected text in the HtmlBox justified to the right. 
   *  ol           Places selected lines in the HtmlBox in an ordered list. 
   *  ul           Places selected lines in the HtmlBox in an unordered list. 
   *  indent       Indents the selected text in the HtmlBox. 
   *  outdent      Outdents the selected text in the HtmlBox. 
   *  hyperlink    Creates a hyperlink from the selected text in the HtmlBox, after the user is prompted to insert a web address. 
   *  image        Creates an image in the HtmlBox, after the user is prompted to insert a the path to the image. 
   *  code         Shows the HTML code of the HtmlBox. The generated HTML differs in the different browsers. 
   *  fontsize     Changes the font size of the selected text in the HtmlBox. 
   *  fontfamily   Changes the font family of the selected text in the HtmlBox. 
   *  fontcolor    Changes the font color of the selected text in the HtmlBox. 
   *  highlight    Highlights the selected text in the HtmlBox. 
   *
   */
   
  SimpleWysiwygField::set_default_configuration(array(
    array('cut','copy','paste','|','bold','italic','underline','|','left','center','right'),
    array('ol','ul','|','hyperlink','unlink','image','|','formats')
  ));

?>