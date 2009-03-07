<?php

class FileDataObjectManager extends DataObjectManager
{
	static $url_handlers = array(
		'import/$ID' => 'handleImport'
	);
	
	public $view = "grid";
	protected $allowedFileTypes;
	protected $limitFileTypes;
	protected $uploadLimit = "20";
	protected $allowUploadFolderSelection = false;
	protected $enableUploadDebugging = false;
	public $importClass = "File";

	protected $permissions = array(
		"add",
		"edit",
		"show",
		"delete",
		"upload",
		"import"
	);
	public $popupClass = "FileDataObjectManager_Popup";
	public $itemClass = "FileDataObjectManager_Item";
	public $template = "FileDataObjectManager";
	public $templatePopup = "DataObjectManager_popup";
	
	public $gridLabelField;
	public $pluralTitle;
	public $browseButtonText = "Upload files";
	

	
	
	
	public function __construct($controller, $name, $sourceClass, $fileFieldName, $fieldList = null, $detailFormFields = null, $sourceFilter = "", $sourceSort = "", $sourceJoin = "") 
	{
		parent::__construct($controller, $name, $sourceClass, $fieldList, $detailFormFields, $sourceFilter, $sourceSort, $sourceJoin);
		
		if(isset($_REQUEST['ctf'][$this->Name()])) {		
				$this->view = $_REQUEST['ctf'][$this->Name()]['view'];
		}
		
		$this->dataObjectFieldName = $name;
		$this->fileFieldName = $fileFieldName;
		$this->fileClassName = singleton($this->sourceClass())->has_one($this->fileFieldName);
		$this->controllerClassName = $controller->class;
		if($key = array_search($this->controllerClassName, singleton($this->sourceClass())->stat('has_one')))
			$this->controllerFieldName = $key;
		else
			$this->controllerFieldName = $this->controllerClassName;
		$this->controllerID = $controller->ID;

	}

	protected function getQueryString($params = array())
	{ 
		$view = isset($params['view'])? $params['view'] : $this->view;
		return parent::getQueryString($params)."&ctf[{$this->Name()}][view]={$view}";
	}
	
	public function setPluralTitle($title)
	{
		$this->pluralTitle = $title;
	}
	
	public function setGridLabelField($fieldName)
	{
		$this->gridLabelField = $fieldName;
	}
	
	public function PluralTitle()
	{
		return $this->pluralTitle ? $this->pluralTitle : $this->AddTitle()."s";
	}
		
	public function GridLink()
	{
		return $this->RelativeLink(array('view' => 'grid'));
	}
	
	public function ListLink()
	{
		return $this->RelativeLink(array('view' => 'list'));
	}

	public function GridView()
	{
		return $this->view == 'grid';
	}
	
	public function ListView()
	{
		return $this->view == 'list';
	}
	
	public function ImportDropdown()
	{
		return new DropdownField('ImportFolder','',$this->getImportFolderHierarchy(0),null, null, "-- Select a folder --");
	}
	
	protected function importLinkFor($file)
	{
		return $this->BaseLink()."/import/$file->ID";
	}
	
	protected function getImportFolderHierarchy($parentID, $level = 0)
	{
		$options = array();		
		if($children = DataObject::get("Folder", "ParentID = $parentID")) {
			foreach($children as $child) {
				$indent="";
				for($i=0;$i<$level;$i++) $indent .= "&nbsp;&nbsp;";
				$files = DataObject::get($this->importClass, "ClassName != 'Folder' AND ParentID = $child->ID");
				$count = $files ? $files->Count() : "0";
				$options[$this->importLinkFor($child)] = $indent.$child->Title . " <span>($count files)</span>";
				$options += $this->getImportFolderHierarchy($child->ID, $level+1);
			}
		}
		return $options;
	}

	protected function getUploadFolderHierarchy($parentID, $level = 0)
	{
		$options = array();		
		if($children = DataObject::get("Folder", "ParentID = $parentID")) {
			foreach($children as $child) {
				$indent="";
				for($i=0;$i<$level;$i++) $indent .= "&nbsp;&nbsp;";
				$options[$child->ID] = empty($child->Title) ? "<em>$indent Untitled</em>" : $indent.$child->Title;
				$options += $this->getUploadFolderHierarchy($child->ID, $level+1);
			}
		}
		return $options;
	}

	
	public function setAllowedFileTypes($types = array())
	{
		foreach($types as $type) {
			if(is_array($this->limitFileTypes) && !in_array(strtolower(str_replace(".","",$type)), $this->limitFileTypes))
				// To-do: get user_error working.
				die("<strong>".$this->class . "::setAllowedFileTypes() -- Only files of type " . implode(", ", $this->limitFileTypes) . " are allowed.</strong>");
		}
		$this->allowedFileTypes = $types;
	}
	
	public function getAllowedFileTypes()
	{
		return $this->allowedFileTypes;
	}
	
	public function setUploadLimit($num)
	{
		$this->uploadLimit = $num;
	}
	
	public function getUploadLimit()
	{
		return $this->uploadLimit;
	}
	
	public function setBrowseButtonText($text)
	{
		$this->browseButtonText = $text;
	}
	
	public function getBrowseButtonText()
	{
		return $this->browseButtonText;
	}
	
	public function ButtonAddTitle()
	{
		return $this->addTitle ? $this->addTitle : $this->PluralTitle();
	}
	
	public function allowUploadFolderSelection()
	{
		$this->allowUploadFolderSelection = true;
	}
	
	public function enableUploadDebugging()
	{
		$this->enableUploadDebugging = true;
	}
	
	public function upload()
	{
		if(!$this->can('upload')) return;
		
		return $this->customise(array(
			'DetailForm' => $this->UploadForm(),
		))->renderWith($this->templatePopup);
		
	}
	
	public function UploadLink()
	{
		return $this->BaseLink().'/upload';
	}
	
	protected function getUploadFields()
	{
		
		$fields = new FieldSet(
			new HeaderField($title = "Add ".$this->PluralTitle(), $headingLevel = 2),
			new HeaderField($title = "Upload from my computer", $headingLevel = 3),
			new SWFUploadField(
				"UploadForm",
				"Upload",
				"",
				array(
					'file_upload_limit' => $this->getUploadLimit(), // how many files can be uploaded
					'file_queue_limit' => $this->getUploadLimit(), // how many files can be in the queue at once
					'browse_button_text' => $this->getBrowseButtonText(),
					'upload_url' => Director::absoluteURL('FileDataObjectManager_Controller/handleswfupload'),
					'required' => 'true'			
				)
			)
		);

		if($this->allowUploadFolderSelection) 
			$fields->insertBefore(new DropdownField('UploadFolder','',$this->getUploadFolderHierarchy(0),null, null, "-- Select a folder --"),"Upload");
		return $fields;
	}
	
	public function UploadForm()
	{
		// Sync up the DB
		singleton('Folder')->syncChildren();
		$className = $this->sourceClass();
		$childData = new $className();
		$validator = $this->getValidatorFor($childData);
		SWFUploadConfig::addPostParams(array(
			'dataObjectClassName' => $this->sourceClass(),
			'dataObjectFieldName' => $this->dataObjectFieldName,
			'fileFieldName' => $this->fileFieldName,
			'fileClassName' => $this->fileClassName,
			'controllerFieldName' => $this->controllerFieldName,
			'controllerID' => $this->controllerID
		));
		
		if($this->allowUploadFolderSelection)
			SWFUploadConfig::addDynamicPostParam('UploadFolder','FileDataObjectManager_Popup_UploadForm_UploadFolder');

		if($this->getAllowedFileTypes()) 
			SWFUploadConfig::addFileTypes($this->getAllowedFileTypes());
		
		if($this->enableUploadDebugging)
			SWFUploadConfig::set_var('debug','true');
						

		$form = Object::create(
			$this->popupClass,
			$this,
			'UploadForm',
			$this->getUploadFields(),
			$validator,
			false,
			$childData
		);
		$form->setActions(new FieldSet(new FormAction("saveUploadForm","Upload")));

		$header = new HeaderField($title = "Import from an existing folder", $headingLevel = 3);
		$holder = 	new LiteralField("holder","<div class='ajax-loader'></div><div id='import-holder'></div>");
		if(!isset($_POST['uploaded_files']))
			return $form->forTemplate() . $header->Field() . $this->ImportDropdown()->FieldHolder() . $holder->Field();
		else
			return $form;
		
	}
	
	public function saveUploadForm()
	{
		if(isset($_POST['uploaded_files']) && is_array($_POST['uploaded_files'])) {
			return $this->customise(array(
				'DetailForm' => $this->EditUploadedForm()
			))->renderWith($this->templatePopup);
		}
	}
	
	protected function getChildDataObj()
	{
		$class = $this->sourceClass();
		return new $class();
	}
	
	public function getPreviewFieldFor($fileObject, $size = 150)
	{
		if($fileObject instanceof Image) {
			$URL = $fileObject->SetHeight($size)->URL;
			return new LiteralField("icon",
				"<div class='current-image'><img src='$URL' alt='' /><h3>$fileObject->Filename</h3></div>"
			);
		}
		else {
			$URL = $fileObject->Icon();			
			return new LiteralField("icon",
				"<h3><img src='$URL' alt='' /><span>$fileObject->Filename</span></h3>"
			);			
		}	
	}
	
	public function EditUploadedForm()
	{
		$childData = $this->getChildDataObj();
		$validator = $this->getValidatorFor($childData);
		$fields = $this->getFieldsFor($childData);
		$fields->removeByName($this->fileFieldName);
			$total = isset($_POST['totalsize']) ? $_POST['totalsize'] : sizeof($_POST['uploaded_files']);
			$index = isset($_POST['index']) ? $_POST['index'] + 1 : 1;
			$fields->push(new HiddenField('totalsize','',$total));
			$fields->push(new HiddenField('index','',$index));
			if(isset($_POST['uploaded_files']) && is_array($_POST['uploaded_files'])) {
				$remaining_files = $_POST['uploaded_files'];
				$current = $remaining_files[0];
				$fields->push(new HiddenField('current','',$current));
				unset($remaining_files[0]);
				foreach($remaining_files as $id)
						$fields->push(new LiteralField("u-$id","<input type='hidden' name='uploaded_files[]' value='$id' />"));
			
				$first = $fields->First()->Name();
				$fields->insertBefore(new HeaderField($title = "Editing file $index of $total", $headingLevel = 2), $first);
				$fileObject = DataObject::get_by_id($this->sourceClass(), $current)->obj($this->fileFieldName);
				$fields->insertBefore($this->getPreviewFieldFor($fileObject), $first);
			}
			$form = Object::create(
				$this->popupClass,
				$this,
				'EditUploadedForm',
				$fields,
				$validator,
				false,
				$childData
			);
			$form->setActions(new FieldSet(new FormAction("saveEditUploadedForm", $index == $total ? "Finish" : "Next")));
			return $form;
	}
	
	function saveEditUploadedForm($data, $form)
	{
		$obj = DataObject::get_by_id($this->sourceClass(), $data['current']);
		$form->saveInto($obj);
		$obj->write();
		if(isset($data['uploaded_files']) && is_array($data['uploaded_files'])) {
			return $this->customise(array(
				'DetailForm' => $this->EditUploadedForm()
			))->renderWith($this->templatePopup);
		}
		else {
			Requirements::clear();
			Requirements::customScript("
					var container = parent.jQuery('#".$this->id()."');
					parent.jQuery('#facebox').fadeOut(function() {
					parent.jQuery('#facebox .content').removeClass().addClass('content');
					parent.jQuery('#facebox_overlay').remove();
					parent.jQuery('#facebox .loading').remove();
					parent.refresh(container, container.attr('href'));
			});");
			return $this->customise(array(
				'DetailForm' => 'Closing...'
			))->renderWith($this->templatePopup);
		}
	}
	
	public function handleImport($request)
	{
		if(!$this->can('import')) return;
		$this->importFolderID = $request->param('ID');
		die($this->ImportForm($this->importFolderID)->forTemplate());
	}
	
	protected function getImportFields()
	{
		return new FieldSet(
				new HiddenField('dataObjectClassName','',$this->sourceClass()),
				new HiddenField('fileFieldName','', $this->fileFieldName),
				new HiddenField('controllerFieldName','', $this->controllerFieldName),
				new HiddenField('controllerID','',$this->controllerID)
			);
	}
	
	protected function ImportForm($folder_id = null)
	{
		$folder_id = isset($_POST['folder_id']) ? $_POST['folder_id'] : $this->importFolderID;;
		if($files = DataObject::get($this->importClass, "ClassName != 'Folder' AND ParentID = $folder_id"))
			$fields = $this->getImportFields();
			$fields->push(new HiddenField('folder_id','',$folder_id));
			$fields->push(new LiteralField("ul","<ul>"));
			foreach($files as $file) {
				$icon = $file instanceof Image ? $file->croppedImage(35,35)->URL : $file->Icon();
				$title = strlen($file->Title) > 30 ? substr($file->Title, 0, 30)."..." : $file->Title;
				$fields->push(new LiteralField("li-$file->ID",
					"<li>
						<span class='import-checkbox'><input type='checkbox' name='imported_files[]' value='$file->ID' /></span>
						<span class='import-icon'><img src='$icon' alt='' /></span>
						<span class='import-title'>".$title."</span>
					</li>"
				));
			}
			$fields->push(new LiteralField("_ul","</ul"));			
			return new Form(
				$this,
				"ImportForm",
				$fields,
				new FieldSet(new FormAction('saveImportForm','Import'))
			);
	}
	
	public function saveImportForm($data, $form)
	{
		if(isset($data['imported_files']) && is_array($data['imported_files'])) {
			$_POST['uploaded_files'] = array();
			foreach($data['imported_files'] as $file_id) {
				$file = DataObject::get_by_id("File",$file_id);
				// If something other than File has been specified as the linked file class,
				// we need to "upgrade" the imported file to the correct class.
				if($this->fileClassName != "File" && $file->ClassName != $this->fileClassName) {
					$file->ClassName = $this->fileClassName;
					$file->write();
				}
				$do_class = $data['dataObjectClassName'];
				$idxfield = $data['fileFieldName']."ID";
				$owner_id = $data['controllerFieldName']."ID";
				$obj = new $do_class();
				$obj->$idxfield = $file_id;
				$obj->$owner_id = $data['controllerID'];
				$obj->write();
				$_POST['uploaded_files'][] = $obj->ID;
			}

			return $this->customise(array(
				'DetailForm' => $this->EditUploadedForm()
			))->renderWith($this->templatePopup);		

		}
	}
}

class FileDataObjectManager_Controller extends Controller
{
	public function handleswfupload()
	{
		if(isset($_FILES['swfupload_file']) && !empty($_FILES['swfupload_file'])) {
			$do_class = $_POST['dataObjectClassName'];
			$file_class = $_POST['fileClassName'];
			$obj = new $do_class();
			$idxfield = $_POST['fileFieldName']."ID";
			$file = new $file_class();
			
			if(isset($_POST['UploadFolder'])) {
				$folder = DataObject::get_by_id("Folder",$_POST['UploadFolder']);
				$path = str_replace("assets/","",$folder->Filename);
			}
			else 
				$path = false;
			
			if(class_exists("Upload")) {
				$u = new Upload();
				$u->loadIntoFile($_FILES['swfupload_file'], $file, $path);
			}
			else
				$file->loadUploaded($_FILES['swfupload_file']);
			
			if(isset($_POST['UploadFolder']))
				$file->setField("ParentID",$folder->ID);

			$file->write();
			$obj->$idxfield = $file->ID;
			$ownerID = $_POST['controllerFieldName']."ID";
			$obj->$ownerID = $_POST['controllerID'];

			$obj->write();
			echo $obj->ID;
		}
		else {
			echo ' ';
		}
	
	
	}
}

class FileDataObjectManager_Item extends DataObjectManager_Item {
	function __construct(DataObject $item, ComplexTableField $parent, $start) 
	{
		parent::__construct($item, $parent, $start);
	}
	
	public function IsFile()
	{
		return $this instanceof File;
	}
	
	public function FileIcon()
	{
		$file = $this->obj($this->parent->fileFieldName);
		if($file && $file->ID)
			return ($file instanceof Image) ? $file->CroppedImage(50,50)->URL : $file->Icon();
		else return "file not found";
	}
	
	public function FileLabel()
	{
		if($this->parent->gridLabelField)
			$label = $this->obj($this->parent->gridLabelField);
		else if($file = $this->obj($this->parent->fileFieldName))
			$label = $file->Title;
		else
			$label = "";
		return strlen($label) > 30 ? substr($label, 0, 30)."..." : $label;
	}
	
}


class FileDataObjectManager_Popup extends DataObjectManager_Popup
{
	function __construct($controller, $name, $fields, $validator, $readonly, $dataObject) {
			parent::__construct($controller, $name, $fields, $validator, $readonly, $dataObject);
			
			// Hack!
			Requirements::block('jsparty/prototype.js');
			if($name == "UploadForm" && !isset($_POST['uploaded_files'])) SWFUploadConfig::bootstrap();
			
			Requirements::javascript('dataobject_manager/javascript/filedataobjectmanager_popup.js');			
	}
	
}

?>