<?php

class SimpleHTMLEditorField extends TextareaField
{
	
	function __construct($name, $title = null, $rows = 5, $cols = 55
	, $value = "", $form = null) {
		parent::__construct($name, $title, $rows, $cols, $value, $form);
		$this->extraClasses = array('hidden');
	}
	
	public function FieldHolder()
	{
		Requirements::javascript('dataobject_manager/javascript/jquery.wysiwyg.js');
		Requirements::css('dataobject_manager/css/jquery.wysiwyg.css');
		Requirements::customScript("
			$(function() {
				$('#{$this->id()}').wysiwyg().parents('.simplehtmleditor').removeClass('hidden');
				
			});
		");
		return parent::FieldHolder();		
	}	
	
}




?>