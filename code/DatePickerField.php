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
		switch($format) {
			case "mdy":
			self::$dateFormat = "mm/dd/yy";
			break;
			
			case "dmy":
			self::$dateFormat = "dd/mm/yy";
			break;
			
			default:
			self::$dateFormat = "dd/mm/yy";
			break;
		}
	}
	
	public static function dmy()
	{
		return self::$dateFormat == "dd/mm/yy";
	}
	
	public static function mdy()
	{
		return self::$dateFormat == "mm/dd/yy";
	}
	
	function setValue($val) {
		if(is_string($val) && preg_match('/^([\d]{2,4})-([\d]{1,2})-([\d]{1,2})/', $val)) {
			$this->value = self::mdy() ? 
				preg_replace('/^([\d]{2,4})-([\d]{1,2})-([\d]{1,2})/','\\2/\\3/\\1', $val) :
				preg_replace('/^([\d]{2,4})-([\d]{1,2})-([\d]{1,2})/','\\3/\\2/\\1', $val);		
		} else {
			$this->value = $val;
		}
	}
	
	function dataValue() {
		if(is_array($this->value)) {
			if(isset($this->value['Year']) && isset($this->value['Month']) && isset($this->value['Day'])) {
				return $this->value['Year'] . '-' . $this->value['Month'] . '-' . $this->value['Day'];
			} else {
				user_error("Bad DateField value " . var_export($this->value,true), E_USER_WARNING);
			}
		} elseif(preg_match('/^([\d]{1,2})\/([\d]{1,2})\/([\d]{2,4})/', $this->value, $parts)) {
			return self::mdy() ? "$parts[3]-$parts[1]-$parts[2]" : "$parts[3]-$parts[2]-$parts[1]";
		} elseif(!empty($this->value)) {
			return date('Y-m-d', strtotime($this->value));
		} else {
			return null;
		}
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