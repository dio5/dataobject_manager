var date_picker_format;
(function($) {
  $(function() {
    $.get('DatePickerField_Controller/dateformat/',function(data) {
      date_picker_format = data;
      $('.datepicker input').livequery(function() {
        $(this).datepicker({dateFormat : date_picker_format, buttonImage : '/sapphire/images/calendar-icon.gif', buttonImageOnly : true});
      });
    });
  });
})(jQuery)
