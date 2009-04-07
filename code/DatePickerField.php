<?php

// (!) What is this doing here?
// In an effort to reduce the amount of javascript in the popup, the legacy CTF requirements
// have been cleared, which really trips up the CalendarDateField.
// This class renders a jQuery compliant (and much nicer looking) datepicker.
// The DataObjectManager::getCustomFields() function sniffs out the date field and replaces it.

class DatePickerField extends DateField 
{
	
	static $dateFormat = "dd/mm/yy";
	
	static function HTMLField( $id, $name, $val ) {
		return <<<HTML
			<input type="text" readonly="readonly" id="$id" name="$name" value="$val" />
HTML;
	}
	
	public static function set_date_format($format)
	{
		self::$dateFormat = $format;
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
			"\$('#$id').datepicker({dateFormat : '".self::$dateFormat."', buttonImage : '/sapphire/images/calendar-icon.gif', buttonImageOnly : true});"
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