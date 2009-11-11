(function($) {
  $(function() {
    $('body').removeClass('loading');
<<<<<<< .mine
		$('iframe').css({'width':'433px'});
		fields_height = window.parent.jQuery('#facebox iframe').height() - 70;
    $('#FieldsWrap').css({'height' : fields_height + 'px'});
=======
		$('iframe').css({'width':'433px'});		
		if($('#duplicate-form')) {
			$('#duplicate-form').hide();
			$('#duplicate-link').click(function() {
				$('#duplicate-form').slideToggle();
				return false;
			});
			$('#duplicate-form form').submit(function() {
				if(isNaN($('#copies').val()))
					alert('Number of copies must be an integer.');
				else {
					$t = $(this);
					$.post(
						$t.attr('action'),
						{
							'Count' : $('#copies').val(),
							'Relations' : $('#relations').attr('checked') ? "1" : "0"
						},
						function(data) {
							$('#message').html(data).show();
						}					
					);
				}
				return false;
			});
		}
>>>>>>> .r319
  });
})(jQuery);