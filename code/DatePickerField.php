<?php

// (!) What is this doing here?
// In an effort to reduce the amount of javascript in the popup, the legacy CTF requirements
// have been cleared, which really trips up the CalendarDateField.
// This class renders a jQuery compliant (and much nicer looking) datepicker.
// The DataObjectManager::getCustomFields() function sniffs out the date field and replaces it.

class DatePickerField extends DateField 
{
	
	protected $dateFormat = "dd/mm/yyyy";
	
	static function HTMLField( $id, $name, $val ) {
		return <<<HTML
			<input type="text" readonly="readonly" id="$id" name="$name" value="$val" />
HTML;
	}
	
	public function setDateFormat($format)
	{
		$this->dateFormat = $format;
	}
	
	function Field() {
		$id = $this->id();
		$val = $this->attrValue();
		if(!$val) $val = date("d/m/Y");
		Requirements::javascript("jsparty/jquery/jquery.js");

		Requirements::javascript("dataobject_manager/javascript/jquery-ui.1.6.js");
		Requirements::css("dataobject_manager/css/ui/ui.core.css");
		Requirements::css("dataobject_manager/css/ui/ui.datepicker.css");
		Requirements::css("dataobject_manager/css/ui/ui.theme.css");

		Requirements::customScript(
			"\$('#$id').datepicker({dateFormat : '$this->dateFormat', buttonImage : '/sapphire/images/calendar-icon.gif', buttonImageOnly : true});"
		);
		$field = parent::Field();
				
		$innerHTML = self::HTMLField( $id, $this->name, $val );
		
		return "
					<div class='datepicker field'>
						$innerHTML
					</div>
		";	
		}
}
?>