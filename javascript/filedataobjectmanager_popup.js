(function($) {
	$(function() {
		if($('select#ImportFolder')) {
			$('select#ImportFolder').change(function() {
				if($(this).val() == '')  {
					$('#import-holder').html('');
					$('#action_saveUploadForm').show();
				}
				else {
					$('#import-holder').load($(this).val(), {},function() {
						$('#action_saveUploadForm').hide();
						$('#Form_ImportForm li').click(function(e) {
							if(e.target.nodeName != "INPUT") {
								i = $(this).find('input'); 
								c = i.attr('checked'); 
								i.attr('checked', !c);
							}
							$(this).toggleClass('current');
							e.stopPropagation();
						});
					});
				}				 
			}).find('option:contains((0 files))').attr('disabled',true);
		}
	});
	
	$().ajaxSend(function(r,s){  
	 $(".ajax-loader").fadeIn("fast");  
	});  
	   
	$().ajaxStop(function(r,s){  
	  $(".ajax-loader").fadeOut("fast");  
	});  
	
})(jQuery);
