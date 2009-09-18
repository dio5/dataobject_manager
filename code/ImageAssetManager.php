<?php

class ImageAssetManager extends ImageDataObjectManager
{
  
  
  public function __construct($controller, $name, $sourceClass = "Image", $headings = null)
  {
    if($headings === null) {
      $headings = array(
        'Title' => 'Title',
        'Filename' => 'Filename'
      );
    }
    
    $fields = new FieldSet(
      new TextField('Name'),
      new TextField('Title'),
      new ReadonlyField('Filename'),
      new TextareaField('Content'),
      new SimpleTreeDropdownField('ParentID','Folder',"Folder"),
      new HiddenField('ID',$controller->ID)
    );
    
    parent::__construct($controller, $name, $sourceClass, null, $headings, $fields, "Classname != 'Folder'");
  }

}