$(function() {
	if($('#ImportFolder select')) {
		$('#ImportFolder select').change(function() {
			if($(this).val() == '')  {
				$('#import-holder').html('');
				$('#action_saveUploadForm').show();
			}
			else {
				$('#import-holder').load($(this).val(), {},function() {
					$('#action_saveUploadForm').hide();
					$('#Form_ImportForm li').click(function() {
						i = $(this).find('input'); 
						c = i.attr('checked'); 
						i.attr('checked', !c); 
						$(this).toggleClass('current');
					});
				});
			}				 
		}).find('option:contains((0 files))').attr('disabled',true);
	}
});
