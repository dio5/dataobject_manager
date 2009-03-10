<?php
class TestimonialPage extends Page
{
	static $has_many = array (
		'Testimonials' => 'Testimonial'
	);
	
	public function getCMSFields()
	{
		$f = parent::getCMSFields();
		$manager = new DataObjectManager(
			$this, // Controller
			'Testimonials', // Source name
			'Testimonial', // Source class
			array('Date' => 'Date', 'Author' => 'Author', 'Quote' => 'Quote'), // Headings
			'getCMSFields_forPopup' // Detail fields function or FieldSet
			// Filter clause
			// Sort clause
			// Join clause
		);
		
		
		$f->addFieldToTab("Root.Content.Testimonials", $manager);
		
		return $f;
	}

}
class TestimonialPage_Controller extends Page_Controller
{
}
?>