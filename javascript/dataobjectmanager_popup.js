(function($) {
  $(function() {
    $('body').removeClass('loading');
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
  });
})(jQuery);