<?php

class SimpleTreeDropdownField extends DropdownField
{
	protected $sourceClass;
	function __construct($name, $title = "", $sourceClass = "SiteTree", $value = "", $form = null, $emptyString = null)
	{
		$this->sourceClass = $sourceClass;
		parent::__construct($name, $title, $this->getHierarchy(0), $value, $form, $emptyString);
	}
	
	private function getHierarchy($parentID, $level = 0)
	{
		$options = array();		
		if($children = DataObject::get($this->sourceClass, "ParentID = $parentID")) {
			foreach($children as $child) {
				$indent="";
				for($i=0;$i<$level;$i++) $indent .= "&nbsp;&nbsp;";
				$options[$child->ID] = empty($child->Title) ? "<em>$indent Untitled</em>" : $indent.$child->MenuTitle;
				$options += $this->getHierarchy($child->ID, $level+1);
			}
		}
		return $options;	
	}
}
