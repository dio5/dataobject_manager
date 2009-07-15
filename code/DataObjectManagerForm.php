<?php

class DataObjectManagerForm extends Extension 
{
  
  public function handleDOMField($request)
  {		
    $fieldName = $request->param('FieldName');
		if(stristr($fieldName,"-")) {
		  $parts = explode("-",$fieldName);
		  if(sizeof($parts == 2)) {
        list($parentName, $childName) = $parts;
		    $parentDOM = $this->owner->dataFieldByName($parentName);
		    $parentClass = $parentDOM->sourceClass();
        $fields = $parentDOM->getFieldsFor(new $parentClass);
        $childDOM = $fields->fieldByName($childName);
        $childDOM->setForm($this->owner);
        return $childDOM;
		  }
		  return $this->owner->dataFieldByName($fieldName);
		}
		
		return $this->owner->dataFieldByName($fieldName);
  }
  


}

