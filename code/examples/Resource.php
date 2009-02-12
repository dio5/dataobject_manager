<?php

class Resource extends DataObject
{
	static $db = array (
		'Name' => 'Text',
		'Description' => 'Text',
		'Category' => "Enum('Industry, Finance, Education')"
	);
	
	static $has_one = array (
		'Attachment' => 'File',
		'ResourcePage' => 'ResourcePage'
	);
	
	public function getCMSFields_forPopup()
	{
		return new FieldSet(
			new TextField('Name'),
			new TextareaField('Description'),
			new DropdownField('Category','Category', singleton('Resource')->dbObject('Category')->enumValues()),
			new FileIFrameField('Attachment')
		);
	}
}

?>