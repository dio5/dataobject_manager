<?php

class DataObjectManager extends ComplexTableField
{
	
	protected $template = "DataObjectManager";
	protected $view = "list";
	protected $showAll = "0";
	protected $search = "";
	protected $filter = "";
	protected $sort_dir = "DESC";
	protected $sort = "Created";
	protected $filter_map = array();
	protected $filtered_field;
	protected $filter_label = "Filter results";
	public $itemClass = "DataObjectManager_Item";

	public $actions = array(
		'edit' => array(
			'label' => 'Edit',
			'icon' => null,
			'class' => 'popuplink editlink',
		),
		'delete' => array(
			'label' => 'Delete',
			'icon' => null,
			'class' => 'deletelink',
		)
	);
	
	public $popupClass = "DataObjectManager_Popup";
	public $templatePopup = "DataObjectManager_popup";
	
	
	function __construct($controller, $name, $sourceClass, $fieldList = null, $detailFormFields = null, $sourceFilter = "", $sourceSort = "", $sourceJoin = "") 
	{
		parent::__construct($controller, $name, $sourceClass, $fieldList, $detailFormFields, $sourceFilter, $sourceSort, $sourceJoin);
		Requirements::block(THIRDPARTY_DIR . "/greybox/AmiJS.js");
		Requirements::block(THIRDPARTY_DIR . "prototype.js");
		Requirements::block(THIRDPARTY_DIR . "/greybox/greybox.js");
		Requirements::block(SAPPHIRE_DIR . "/javascript/ComplexTableField.js");
		Requirements::block(SAPPHIRE_DIR . "/javascript/TableListField.js");

		Requirements::block(THIRDPARTY_DIR . "/greybox/greybox.css");
		Requirements::block(SAPPHIRE_DIR . "/css/ComplexTableField.css");
		Requirements::css('dataobject_manager/css/dataobject_manager.css');
		Requirements::css('dataobject_manager/css/facebox.css');
		Requirements::javascript('dataobject_manager/javascript/facebox.js');	
		Requirements::javascript('dataobject_manager/javascript/ui.js');
		Requirements::javascript('dataobject_manager/javascript/sort.js');
		Requirements::javascript('dataobject_manager/javascript/dataobject_manager.js');
		Requirements::javascript('dataobject_manager/javascript/tooltip.js');
		
		

		if(isset($_REQUEST['ctf'][$this->Name()])) {
			$this->showAll = $_REQUEST['ctf'][$this->Name()]['showall'];
			$this->search = $_REQUEST['ctf'][$this->Name()]['search'];
			$this->filter = $_REQUEST['ctf'][$this->Name()]['filter'];			
			$this->sort = $_REQUEST['ctf'][$this->Name()]['sort'];
			$this->sort_dir = $_REQUEST['ctf'][$this->Name()]['sort_dir'];
		}
		
		$this->loadSort();
		$this->loadSourceFilter();
	}
	
	protected function loadSort()
	{
		if($this->ShowAll()) 
			$this->setPageSize(999);
		
		if($this->Sortable() && (!isset($_REQUEST['ctf'][$this->Name()]['sort']) || $_REQUEST['ctf'][$this->Name()]['sort'] == "SortOrder")) {
			$this->sort = "SortOrder";
			$this->sourceSort = "SortOrder ASC";
		}
		elseif(isset($_REQUEST['ctf'][$this->Name()]['sort']))
			$this->sourceSort = $_REQUEST['ctf'][$this->Name()]['sort'] . " " . $this->sort_dir;
		else 
			$this->sourceSort = null;
	
	}
	
	protected function loadSourceFilter()
	{
		$filter_string = "";
		if(!empty($this->filter)) {
			$break = strpos($this->filter, "_");
			$field = substr($this->filter, 0, $break);
			$value = substr($this->filter, $break+1, strlen($this->filter) - strlen($field));
			$filter_string = $field . "='$value'";
		}	

		$search_string = "";
		if(!empty($this->search)) {
			$search = array();
			foreach(parent::Headings() as $field)
				$search[] = "UPPER($field->Name) LIKE '%".strtoupper($this->search)."%'";
			$search_string = "(".implode(" OR ", $search).")";
		}
		$and = (!empty($this->filter) && !empty($this->search)) ? " AND " : "";
		$source_filter = $filter_string.$and.$search_string;
		if(!$this->sourceFilter) $this->sourceFilter = $source_filter;
		else if($this->sourceFilter && !empty($source_filter)) $this->sourceFilter .= " AND " . $source_filter;		
	}
	
	public function handleItem($request) {
		return new DataObjectManager_ItemRequest($this, $request->param('ID'));
	}

	protected function getQueryString($params = array())
	{ 
		$show_all = isset($params['show_all'])? $params['show_all'] : $this->showAll;
		$sort 	  = isset($params['sort'])? $params['sort'] 				: $this->sort;
		$sort_dir = isset($params['sort_dir'])? $params['sort_dir'] : $this->sort_dir;
		$filter   = isset($params['filter'])? $params['filter'] 		: $this->filter;
		$search   = isset($params['search'])? $params['search'] 		: $this->search;
		return "ctf[{$this->Name()}][showall]={$show_all}&ctf[{$this->Name()}][sort]={$sort}&ctf[{$this->Name()}][sort_dir]={$sort_dir}&ctf[{$this->Name()}][search]={$search}&ctf[{$this->Name()}][filter]={$filter}";
	}

	public function ListStyle()
	{
		return $this->view;
	}
	
	public function Headings()
	{
		$headings = parent::Headings();
		foreach($headings as $heading) {
			$heading->IsSorted = (isset($_REQUEST['ctf'][$this->Name()]['sort'])) && ($_REQUEST['ctf'][$this->Name()]['sort'] == $heading->Name);
			if(isset($_REQUEST['ctf'][$this->Name()]['sort_dir'])) 
				$dir = $_REQUEST['ctf'][$this->Name()]['sort_dir'] == "ASC" ? "DESC" : "ASC";
			else 
				$dir = "ASC"; 
			$heading->SortDirection = $dir;
			$heading->SortLink = $this->RelativeLink(array(
				'sort_dir' => $heading->SortDirection, 
				'sort' => $heading->Name
			));
		}
		return $headings;
	}
	
	function getCustomFieldsFor($childData) {
		if(is_a($this->detailFormFields,"Fieldset")) 
			$fields = $this->detailFormFields;
		else {
			if(!is_string($this->detailFormFields)) $this->detailFormFields = "getCMSFields";
			$functioncall = $this->detailFormFields;
			if(!$childData->hasMethod($functioncall)) $functioncall = "getCMSFields";
			
			$fields = $childData->$functioncall();
		}
		
		foreach($fields as $field) {
			if($field->class == "CalendarDateField")
				$fields->replaceField($field->Name(), new DatePickerField($field->Name(), $field->Title()));
		}
		return $fields;
	}
	
	public function Link()
	{
		return parent::Link()."?".$this->getQueryString();
	}
	
	public function BaseLink()
	{
		return parent::Link();
	}
	
	public function CurrentLink()
	{
		return $this->Link();
	}	
	
	public function RelativeLink($params = array())
	{
		return parent::Link()."?".$this->getQueryString($params);
	}	
	public function FirstLink()
	{
		return parent::FirstLink() ? parent::FirstLink()."&".$this->getQueryString() : false;
	}
	
	public function PrevLink()
	{
		return parent::PrevLink() ? parent::PrevLink()."&".$this->getQueryString() : false;
	}
	
	public function NextLink()
	{
		return parent::NextLink() ? parent::NextLink()."&".$this->getQueryString() : false;
	}
	
	public function LastLink()
	{
		return parent::LastLink() ? parent::LastLink()."&".$this->getQueryString() : false;
	}
	
	public function ShowAllLink()
	{
		return $this->RelativeLink(array('show_all' => '1'));
	}
	
	public function PaginatedLink()
	{
		return $this->RelativeLink(array('show_all' => '0'));
	}

	public function AddLink() {
		return $this->BaseLink() . '/add';
	}
	
	
		
	public function ShowAll()
	{
		return $this->showAll == "1";
	}
	
	public function Paginated()
	{
		return $this->showAll == "0";
	}
		
	public function Sortable()
	{
		return SortableDataObject::is_sortable_class($this->sourceClass());
	}
	
	public function setFilter($field, $label, $map)
	{
		if(is_array($map)) {
			$this->filter_map = $map;
			$this->filtered_field = $field;
			$this->filter_label = $label;
		}
	}

	public function HasFilter()
	{
		return !empty($this->filter_map);
	}
	
	public function FilterDropdown()
	{
		$map = array($this->RelativeLink(array('filter' => '')) => '-- No filter --');
		foreach($this->filter_map as $k => $v) {
			$map[$this->RelativeLink(array('filter' => $this->filtered_field.'_'.$k))] = $v;
		}
		$value = !empty($this->filter) ? $this->RelativeLink(array('filter' => $this->filter)) : null;
		$dropdown = new DropdownField('Filter',$this->filter_label, $map, $value);
		return $dropdown->FieldHolder();
	}
	
	public function SearchValue()
	{
		return !empty($this->search) ? $this->search : false;
	}

}

class DataObjectManager_Item extends ComplexTableField_Item {
	function __construct(DataObject $item, ComplexTableField $parent, $start) 
	{
		parent::__construct($item, $parent, $start);
	}
	
	function Link() {
		return $this->parent->BaseLink() . '/item/' . $this->item->ID;
	}
}

class DataObjectManager_Controller extends Controller
{
	function dosort()
	{
		if(!empty($_POST) && is_array($_POST) && isset($this->urlParams['ID'])) {
			$className = $this->urlParams['ID'];
			foreach($_POST as $group => $map) {
				if(substr($group, 0, 7) == "record-") {
 					foreach($map as $sort => $id) {
 						$obj = DataObject::get_by_id($className, $id);
 						$obj->SortOrder = $sort;
 						$obj->write();
 					}
				}
			}
		}
	}

}


class DataObjectManager_Popup extends Form {
	protected $sourceClass;
	protected $dataObject;

	function __construct($controller, $name, $fields, $validator, $readonly, $dataObject) {
		$this->dataObject = $dataObject;
		Requirements::clear();
		Requirements::block('assets/base.js');
		Requirements::block('sapphire/javascript/lang/en_US.js');
		Requirements::javascript('jsparty/behaviour.js');
		Requirements::css(SAPPHIRE_DIR . '/css/Form.css');
		Requirements::css(CMS_DIR . '/css/typography.css');
		Requirements::css(CMS_DIR . '/css/cms_right.css');
		Requirements::css('dataobject_manager/css/dataobject_manager.css');
 		if($this->dataObject->hasMethod('getRequirementsForPopup')) {
			$this->dataObject->getRequirementsForPopup();
		}
		
		Requirements::javascript('dataobject_manager/javascript/jquery.js');
		
		// File iframe fields force horizontal scrollbars in the popup. Not cool.
		// Override the close popup method.
		Requirements::customScript("
			jQuery(function() {
				jQuery('iframe').css({'width':'433px'});
				
				jQuery('small a').attr('onclick','').click(function() {
					var container = parent.\$container;
					parent.jQuery('#facebox').fadeOut(function() {
						parent.jQuery('#facebox .content').removeClass().addClass('content');
						parent.jQuery('#facebox_overlay').remove();
						parent.jQuery('#facebox .loading').remove();
						container.load(container.attr('href'),{}, function(){
							parent.jQuery(container).DataObjectManager();	
						});
					});			
				});
			});
		");
		
		Requirements::javascript('jsparty/behaviour.js');
		$actions = new FieldSet();	
		if(!$readonly) {
			$actions->push(
				$saveAction = new FormAction("saveComplexTableField", "Save")
			);	
			$saveAction->addExtraClass('save');
		}
		
		parent::__construct($controller, $name, $fields, $actions, $validator);
		
		$this->unsetValidator();
	}

	function FieldHolder() {
		return $this->renderWith('ComplexTableField_Form');
	}
	
}

class DataObjectManager_ItemRequest extends ComplexTableField_ItemRequest 
{
	function __construct($ctf, $itemID) 
	{
		parent::__construct($ctf, $itemID);
	}

	function Link() 
	{
		return $this->ctf->BaseLink() . '/item/' . $this->itemID;
	}
	


}



?>