<?php

class DataObjectManager extends ComplexTableField
{
	
	protected $template = "DataObjectManager";
	protected $view = "list";
	protected $per_page = "10";
	protected $showAll = "0";
	protected $search = "";
	protected $filter = "";
	protected $sort_dir = "DESC";
	protected $sort = "Created";
	protected $filter_map = array();
	protected $filtered_field;
	protected $filter_label = "Filter results";
	protected $filter_empty_string = true;
	public $itemClass = "DataObjectManager_Item";
	public $addTitle;
	public $singleTitle;

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
		Requirements::javascript('dataobject_manager/javascript/jquery-ui.1.5.3.js');
		Requirements::javascript('dataobject_manager/javascript/dataobject_manager.js');
		Requirements::javascript('dataobject_manager/javascript/tooltip.js');
		
		

		if(isset($_REQUEST['ctf'][$this->Name()])) {
			$this->per_page = $_REQUEST['ctf'][$this->Name()]['per_page'];
			$this->showAll = $_REQUEST['ctf'][$this->Name()]['showall'];
			$this->search = $_REQUEST['ctf'][$this->Name()]['search'];
			$this->filter = $_REQUEST['ctf'][$this->Name()]['filter'];			
			$this->sort = $_REQUEST['ctf'][$this->Name()]['sort'];
			$this->sort_dir = $_REQUEST['ctf'][$this->Name()]['sort_dir'];
		}
		$this->setPageSize($this->per_page);
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
		$per_page = isset($params['per_page'])? $params['per_page'] : $this->per_page;
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
	
	function saveComplexTableField($data, $form, $params) {
		$className = $this->sourceClass();
		$childData = new $className();
		$form->saveInto($childData);
		$childData->write();
		$form->sessionMessage('Added new ' . $this->SingleTitle() .' successfully', 'good');
		if($form->getFileField()) {
			$form->clearMessage();
			Director::redirect($this->BaseLink().'/item/'.$childData->ID.'/edit');
		}
		else Director::redirectBack();

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
	
	function AddForm($childID = null)
	{
		$form = parent::AddForm($childID);
		$actions = new FieldSet();	
		$text = ($field = $form->getFileField()) ? "Save and add " . $field->Title() : "Save";
		$actions->push(
			$saveAction = new FormAction("saveComplexTableField", $text)
		);	
		$saveAction->addExtraClass('save');
		$form->setActions($actions);
		return $form;

		
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
		$map = $this->filter_empty_string ? array($this->RelativeLink(array('filter' => '')) => '-- No filter --') : array();
		foreach($this->filter_map as $k => $v) {
			$map[$this->RelativeLink(array('filter' => $this->filtered_field.'_'.$k))] = $v;
		}
		$value = !empty($this->filter) ? $this->RelativeLink(array('filter' => $this->filter)) : null;
		$dropdown = new DropdownField('Filter',$this->filter_label . " (<a href='#' class='refresh'>refresh</a>)", $map, $value);
		return $dropdown->FieldHolder();
	}
	
	public function PerPageDropdown()
	{
		$map = array(
			$this->RelativeLink(array('per_page' => '10')) => '10',
			$this->RelativeLink(array('per_page' => '20')) => '20',
			$this->RelativeLink(array('per_page' => '30')) => '30',
			$this->RelativeLink(array('per_page' => '40')) => '40',
			$this->RelativeLink(array('per_page' => '50')) => '50'									
		);
		
		$value = !empty($this->per_page) ? $this->RelativeLink(array('per_page' => $this->per_page)) : null;
		
		return new FieldGroup(
				new LabelField('show', 'Show '),
				new DropdownField('PerPage','', $map, $value),
				new LabelField('per',' results per page')
			);
	}
	
	public function SearchValue()
	{
		return !empty($this->search) ? $this->search : false;
	}
	
	public function AddTitle()
	{
		return $this->addTitle ? $this->addTitle : $this->Title();
	}
	
	public function SingleTitle()
	{
		return $this->singleTitle ? $this->singleTitle : $this->AddTitle();
	}
	
	public function setAddTitle($title)
	{
		$this->addTitle = $title;
	}
	
	public function setSingleTitle($title)
	{
		$this->singleTitle = $title;
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
		Requirements::block('/jsparty/behaviour.js');
		Requirements::block('sapphire/javascript/Validator.js');
		Requirements::block('jsparty/prototype.js');
		Requirements::block('jsparty/behavior.js');
		Requirements::clear('jsparty/behavior.js');

		Requirements::block('sapphire/javascript/i18n.js');
		Requirements::block('assets/base.js');
		Requirements::block('sapphire/javascript/lang/en_US.js');
		Requirements::css(SAPPHIRE_DIR . '/css/Form.css');
		Requirements::css(CMS_DIR . '/css/typography.css');
		Requirements::css(CMS_DIR . '/css/cms_right.css');
		Requirements::css('dataobject_manager/css/dataobject_manager.css');
 		if($this->dataObject->hasMethod('getRequirementsForPopup')) {
			$this->dataObject->getRequirementsForPopup();
		}
		
		Requirements::javascript('dataobject_manager/javascript/jquery.1.3.js');
		
		// File iframe fields force horizontal scrollbars in the popup. Not cool.
		// Override the close popup method.
		Requirements::customScript("
			jQuery(function() {
				jQuery('iframe').css({'width':'433px'});				
			});
		");
		
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
	
	public function getFileField()
	{
		foreach($this->Fields() as $field) {
			if($field instanceof FileIFrameField || $field instanceof ImageField)
				return $field;
		}
		
		return false;
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

	function saveComplexTableField($data, $form, $request) {
		$form->saveInto($this->dataObj());
		$this->dataObj()->write();
		
		$form->sessionMessage('Saved '.$this->ctf->SingleTitle(). ' successfully', 'good');

		Director::redirectBack();
	}
	
	


}

// (!) What is this doing here?
// In an effort to reduce the amount of javascript in the popup, the legacy CTF requirements
// have been cleared, which really trips up the CalendarDateField.
// This class renders a jQuery compliant (and much nicer looking) datepicker.
// The DataObjectManager::getCustomFields() function sniffs out the date field and replaces it.

class DatePickerField extends DateField 
{
	
	static function HTMLField( $id, $name, $val ) {
		return <<<HTML
			<input type="text" id="$id" name="$name" value="$val" />
HTML;
	}
	
	function Field() {
		$id = $this->id();
		$val = $this->attrValue();
		Requirements::javascript("jsparty/jquery/jquery.js");

		Requirements::javascript("dataobject_manager/javascript/jquery-ui.1.6.js");
		Requirements::css("dataobject_manager/css/ui/ui.core.css");
		Requirements::css("dataobject_manager/css/ui/ui.datepicker.css");
		Requirements::css("dataobject_manager/css/ui/ui.theme.css");

		Requirements::customScript(
			"\$('#$id').datepicker({dateFormat : 'dd/mm/yy', buttonImage : '/sapphire/images/calendar-icon.gif', buttonImageOnly : true});"
		);
		$field = parent::Field();
				
		$innerHTML = self::HTMLField( $id, $this->name, $val );
		
		return <<<HTML
			<div class="datepicker field">
				$innerHTML
			</div>
HTML;
	}
}




?>