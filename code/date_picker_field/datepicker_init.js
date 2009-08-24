var datepicker_date_format;
(function($) {
  $(function() {
    $.get('DatePickerField_Controller/dateformat/',function(data) {date_format = data});
    $('.datepicker input').livequery(function() {
      $(this).datepicker({dateFormat : datepicker_date_format, buttonImage : '/sapphire/images/calendar-icon.gif', buttonImageOnly : true});
    });
  });
})(jQuery)
