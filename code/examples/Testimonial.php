<?php

class Testimonial extends DataObject
{
	static $db = array (
		'Date' => 'Date',
		'Author' => 'Text',
		'Quote' => 'Text'
	);
	
	static $has_one = array (
		'TestimonialPage' => 'TestimonialPage'
	);
	
	public function getCMSFields_forPopup()
	{
		return new FieldSet(
			new CalendarDateField('Date'),
			new TextField('Author'),
			new TextareaField('Quote')
		);
	}
}

?>