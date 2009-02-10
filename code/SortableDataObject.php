<?php

class SortableDataObject extends DataObjectDecorator
{
	
	static $sortable_classes = array();
	
	public static function add_sortable_class($className)
	{
		DataObject::add_extension($className,'SortableDataObject');
		singleton($className)->set_stat('default_sort','SortOrder ASC');		
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
		return in_array($classname, self::$sortable_classes);	
	}
	
	public function extraStatics()
	{
		return array (
			'db' => array (
				'SortOrder' => 'Int'
			)
		);
	}	

}

?>