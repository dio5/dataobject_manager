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
  
  
	LeftAndMain::require_javascript("dataobject_manager/javascript/jquery-ui.1.6.js");
	LeftAndMain::require_javascript("dataobject_manager/code/date_picker_field/datepicker.js");
	LeftAndMain::require_javascript("dataobject_manager/code/date_picker_field/datepicker_init.js");
	LeftAndMain::require_css("dataobject_manager/css/ui/ui.core.css");
	LeftAndMain::require_css("dataobject_manager/css/ui/ui.datepicker.css");
	LeftAndMain::require_css("dataobject_manager/css/ui/ui.theme.css");

  SimpleWysiwygField::set_default_configuration(array(
    array('cut','copy','paste','|','bold','italic','underline','|','left','center','right'),
    array('ol','ul','|','hyperlink','unlink','image','|','formats')
  ));
?>