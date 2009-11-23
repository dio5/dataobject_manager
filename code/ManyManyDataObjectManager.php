<?php

class ManyManyDataObjectManager extends HasManyDataObjectManager
{

	private $manyManyParentClass;
	
	public $itemClass = 'ManyManyDataObjectManager_Item';

	/**
	 * Most of the code below was copied from ManyManyComplexTableField.
	 * Painful, but necessary, until PHP supports multiple inheritance.
	 */
	

		
	function __construct($controller, $name, $sourceClass, $fieldList, $detailFormFields = null, $sourceFilter = "", $sourceSort = "Created DESC", $sourceJoin = "") {

		parent::__construct($controller, $name, $sourceClass, $fieldList, $detailFormFields, $sourceFilter, $sourceSort, $sourceJoin);
		$manyManyTable = false;
		$classes = array_reverse(ClassInfo::ancestry($this->controllerClass()));
		foreach($classes as $class) {
			if($class != "Object") {
				$singleton = singleton($class);
				$manyManyRelations = $singleton->uninherited('many_many', true);
				if(isset($manyManyRelations) && array_key_exists($this->name, $manyManyRelations)) {
					$this->manyManyParentClass = $class;
					$manyManyTable = $class . '_' . $this->name;
					break;
				}
				$belongsManyManyRelations = $singleton->uninherited( 'belongs_many_many', true );
				 if( isset( $belongsManyManyRelations ) && array_key_exists( $this->name, $belongsManyManyRelations ) ) {
					$this->manyManyParentClass = $class;
					$manyManyTable = $belongsManyManyRelations[$this->name] . '_' . $this->name;
					break;
				}
			}
		}
		if(!$manyManyTable) user_error("I could not find the relation $this-name in " . $this->controllerClass() . " or any of its ancestors.",E_USER_WARNING);
		$tableClasses = ClassInfo::dataClassesFor($this->sourceClass);
		$source = array_shift($tableClasses);
		$sourceField = $this->sourceClass;
		if($this->manyManyParentClass == $sourceField)
			$sourceField = 'Child';
		$parentID = $this->controller->ID;
		
		$this->sourceJoin .= " LEFT JOIN `$manyManyTable` ON (`$source`.`ID` = `{$sourceField}ID` AND `{$this->manyManyParentClass}ID` = '$parentID')";
		
		$this->joinField = 'Checked';
	}
		
	function getQuery($limitClause = null) {
		if($this->customQuery) {
			$query = $this->customQuery;
			$query->select[] = "{$this->sourceClass}.ID AS ID";
			$query->select[] = "{$this->sourceClass}.ClassName AS ClassName";
			$query->select[] = "{$this->sourceClass}.ClassName AS RecordClassName";
		}
		else {
			$query = singleton($this->sourceClass)->extendedSQL($this->sourceFilter, $this->sourceSort, $limitClause, $this->sourceJoin);
			
			// Add more selected fields if they are from joined table.

			$SNG = singleton($this->sourceClass);
			foreach($this->FieldList() as $k => $title) {
				if(! $SNG->hasField($k) && ! $SNG->hasMethod('get' . $k))
					$query->select[] = $k;
			}
			$parent = $this->controllerClass();
			$query->select[] = "IF(`{$this->manyManyParentClass}ID` IS NULL, '0', '1') AS Checked";
		}
		return clone $query;
	}
		
	function getParentIdName($parentClass, $childClass) {
		return $this->getParentIdNameRelation($parentClass, $childClass, 'many_many');
	}
			
	function ExtraData() {
		$items = array();
		foreach($this->unpagedSourceItems as $item) {
			if($item->{$this->joinField})
				$items[] = $item->ID;
		}
		$list = implode(',', $items);
		$value = ",";
		$value .= !empty($list) ? $list."," : "";
		$inputId = $this->id() . '_' . $this->htmlListEndName;
		return <<<HTML
		<input id="$inputId" name="{$this->name}[{$this->htmlListField}]" type="hidden" value="$value"/>
HTML;
	}


}

class ManyManyDataObjectManager_Item extends DataObjectManager_Item {
	
	function MarkingCheckbox() {
		$name = $this->parent->Name() . '[]';
		$disabled = $this->parent->hasMarkingPermission() ? "" : "disabled='disabled'";
		
		if($this->parent->IsReadOnly)
			return "<input class=\"checkbox\" type=\"checkbox\" name=\"$name\" value=\"{$this->item->ID}\" disabled=\"disabled\"/>";
		else if($this->item->{$this->parent->joinField})
			return "<input class=\"checkbox\" type=\"checkbox\" name=\"$name\" value=\"{$this->item->ID}\" checked=\"checked\" $disabled />";
		else
			return "<input class=\"checkbox\" type=\"checkbox\" name=\"$name\" value=\"{$this->item->ID}\" $disabled />";
	}
}




?>