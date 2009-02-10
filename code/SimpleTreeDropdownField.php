<?php

class SimpleTreeDropdownField extends DropdownField
{
	function __construct( $name, $title, $className = "Folder", $value = null, $form = null, $emptyString = null) 
	{
		$this->className = $className;
		$optionArray = $this->getHierarchy(0);
		parent::__construct( $name, $title, $optionArray, $value, $form, $emptyString );
	}
	
	private function getHierarchy($parentID, $level = 0)
	{
		$options = array();		
		if($children = DataObject::get($this->className, "ParentID = $parentID")) {
			foreach($children as $child) {
				$indent="";
				for($i=0;$i<$level;$i++) $indent .= "&nbsp;&nbsp;";
				$options[$child->ID] = $indent.$child->Title;
				$options += $this->getHierarchy($child->ID, $level+1);
			}
		}
		return $options;
	}
}


?>