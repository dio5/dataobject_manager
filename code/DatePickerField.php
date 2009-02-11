<?php
/**
 * This field creates a date field that shows a calendar on pop-up
 * @package forms
 * @subpackage fields-datetime
 */
class DatePickerField extends DateField {
	protected $futureOnly;
	
	static function HTMLField( $id, $name, $val ) {
		return <<<HTML
			<input type="text" id="$id" name="$name" value="$val" />
HTML;
	}
	
	function Field() {
		$id = $this->id();
		$val = $this->attrValue();

		Requirements::javascript("dataobject_manager/javascript/jquery.js");
		Requirements::javascript("dataobject_manager/javascript/jquery-ui.js");
		Requirements::css("dataobject_manager/css/datepicker/ui.core.css");
		Requirements::css("dataobject_manager/css/datepicker/ui.datepicker.css");
		Requirements::css("dataobject_manager/css/datepicker/ui.theme.css");

		Requirements::customScript(
			"\$('#$id').datepicker({dateFormat : 'dd/mm/yy', buttonImage : '/sapphire/images/calendar-icon.gif', buttonImageOnly : true});"
		);
		$field = parent::Field();
				
		$innerHTML = self::HTMLField( $id, $this->name, $val );
		
		return <<<HTML
			<div class="calendardate$futureClass">
				$innerHTML
			</div>
HTML;
	}
}

?>