<?php

class AssetManager extends FileDataObjectManager
{
  
  public $default_view = "list";
  
  public function __construct($controller, $name, $sourceClass = "File", $headings = null)
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
      new SimpleTreeDropdownField('ParentID','Folder',"Folder")
    );
    
    parent::__construct($controller, $name, $sourceClass, null, $headings, $fields, "Classname != 'Folder'");
  }

}

?>