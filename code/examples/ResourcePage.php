<?php
class ResourcePage extends Page
{
	static $has_many = array (
		'Resources' => 'Resource'
	);
	
	public function getCMSFields()
	{
		$f = parent::getCMSFields();
		$manager = new FileDataObjectManager(
			$this, // Controller
			'Resources', // Source name
			'Resource', // Source class
			'Attachment', // File name on DataObject
			array(
				'Name' => 'Name', 
				'Description' => 'Description', 
				'Category' => 'Category'
			), // Headings 
			'getCMSFields_forPopup' // Detail fields (function name or FieldSet object)
			// Filter clause
			// Sort clause
			// Join clause
		);
		
		$manager->setFilter(
			'Category', // Name of field to filter
			'Filter by Category', // Label for filter
			singleton('Resource')->dbObject('Category')->enumValues() // Map for filter (could be $dataObject->toDropdownMap(), e.g.)
		);
		
		// If undefined, all types are allowed. Pass with or without a leading "."		
		$manager->setAllowedFileTypes(array('pdf','doc')); 
		
		// Label for the upload button in the popup
		$manager->setBrowseButtonText("Upload (PDF or DOC only)"); 
		
		// In grid view, what field will appear underneath the icon. If left out, it defaults to the file title.
		$manager->setGridLabelField('Name'); 
		
		// Plural form of the objects being managed. Used on the "Add" button.
		// If left out, this defaults to [MyObjectName]s
		$manager->setPluralTitle('Resources');
				
		$f->addFieldToTab("Root.Content.Resources", $manager);

		return $f;
	}

}

class ResourcePage_Controller extends Page_Controller
{
}
?>