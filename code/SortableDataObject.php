<?php

class SortableDataObject extends DataObjectDecorator
{
	
	static $sortable_classes = array();
	static $sort_dir = "ASC";
	
	public static function set_sort_dir($dir)
	{
		self::$sort_dir = $dir;
	}
	
	
	public static function add_sortable_class($className)
	{
		DataObject::add_extension($className,'SortableDataObject');
		$dir = self::$sort_dir;
		singleton($className)->set_stat("default_sort","SortOrder $dir");		
		self::$sortable_classes[] = $className;
	}
	
	public static function add_sortable_classes(array $classes)
	{
		foreach($classes as $class) 
			self::add_sortable_class($class);
	}
	
	public static function remove_sortable_class($class)
	{
		Object::remove_extension($class, 'SortableDataObject');
	}
	
	public static function is_sortable_class($classname)
	{
		if(in_array($classname, self::$sortable_classes))
			return true;
		foreach(self::$sortable_classes as $class) {
			if(is_subclass_of($classname, $class))
				return true;
		}
		return false;
			
	}
	
	public function extraStatics()
	{
		return array (
			'db' => array (
				'SortOrder' => 'Int'
			)
		);
	}
	
	public function onBeforeWrite()
	{
		if(!$this->owner->ID) {
			if($peers = DataObject::get($this->owner->class))
				$this->owner->SortOrder = $peers->Count()+1;
		}
	}	

}

?>