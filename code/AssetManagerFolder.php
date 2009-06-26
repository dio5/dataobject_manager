<?php

class AssetManagerFolder extends DataObjectDecorator
{
  
  public function updateCMSFields(Fieldset $fields)
  {
    $fields->removeFieldFromTab("Root.Files","Files");
    $fields->removeFieldFromTab("Root.Files","deletemarked");
    $fields->removeByName("Upload");
    $fields->addFieldToTab("Root.Files", $a = new AssetManager($this->owner,"Files"));
    $a->setUploadFolder($this->owner->Filename);
    $a->setColumnWidths(array(
      'Title' => 30,
      'Filename' => 70
    ));
    $a->setSourceFilter("Classname != 'Folder' AND ParentID = ".$this->owner->ID);
    $a->setParentClass("Folder");
    $a->setAddTitle(sprintf(_t('AssetManager.ADDFILESTO','files to "%s"'),$this->owner->Title));
    return $fields;
  }
}

?>