(function($) {

$.fn.DataObjectManager = function() {
	this.each(function() {
		$.fn.DataObjectManager.init(this);
	});
};

$.fn.DataObjectManager.init = function(obj) {
		var $container = $(obj);
		var container_id = '#'+$container.attr('id');
		var nested = $('.DataObjectManager').hasClass('isNested');
		
		var facebox_close = function() {			
			$('#facebox').fadeOut(function() {
				$('#facebox .content').removeClass().addClass('content');
				$('#facebox_overlay').remove();
				$('#facebox .loading').remove();
				refresh($container, $container.attr('href'));		
			})
		};
		
		// Popup links
		
		// For Nested DOMs
		if(nested) {
      $('body').append(
         $('<div id="iframe_'+$container.attr('id')+'" class="iframe_wrap" style="display:none;"><a href="javascript:void(0)" class="nested-close">close</a><iframe src="" frameborder="0" width="450" height="1"></iframe></div>')
      );
      var $iframeWrap = $('#iframe_'+$container.attr('id'));
  		$container.find('a.popup-button').unbind('click').click(function(e) {
  		  $link = $(this);
				var $iframe = $iframeWrap.find('iframe');
        $iframe.attr('src',$link.attr('href'));
        //$('body').css({'opacity':.3});
        top = $.fn.DataObjectManager.getPageScroll()[1] + ($.fn.DataObjectManager.getPageHeight() / 10);
        $iframeWrap.show().css({
        	'position':'absolute',
        	'z-index':'999',
        	'left':'50%',
        	'top' : top,
        	'margin-left':'-215px'
        });
        $iframe.load(function() {
	        iframe_height = $iframe.contents().find('body').height()+28;
	        $iframe.attr('height',iframe_height);
        });
        return false;
      });
      $('#iframe_'+$container.attr('id')).find('.nested-close').click(function() {
      	$iframeWrap.hide();
      	$iframeWrap.find('iframe').attr('src','');
      	refresh($container,$container.attr('href'));
      	return false;
      });   		
		}
		// For normal DOMs
		else {
  		$container.find('a.popup-button').unbind('click').click(function(e) {
  			$(document).unbind('close.facebox').bind('close.facebox', facebox_close);
  			w = $(this).attr('rel') == 'hasNested' ? 850 : 500;
  			width = new String(w);
  			$.facebox('<iframe src="'+$(this).attr('href')+'" frameborder="0" width="'+width+'" height="' + ($.fn.DataObjectManager.getPageHeight()*.6) + '"></iframe>');
  			e.stopPropagation();
  			return false;
  		});
		}
		// Delete

		$container.find('a.deletelink').unbind('click').click(function(e) {
			params = $('#SecurityID') ? {'forceajax' : '1', 'SecurityID' : $('#SecurityID').attr('value')} : {'forceajax' : '1'};
			$target = $(this);
			$.post(
				$target.attr('href'),
				params,
				function() {$($target).parents('li:first').fadeOut();$(".ajax-loader").fadeOut("fast");}
			);
			e.stopPropagation();
			return false;
		});

		// Pagination
		$container.find('.Pagination a').unbind('click').click(function() {
			refresh($container, $(this).attr('href'));
			return false;
		});
		
		// View
		if($container.hasClass('FileDataObjectManager') && !$container.hasClass('ImageDataObjectManager')) {
			$container.find('a.viewbutton').unbind('click').click(function() {
				refresh($container, $(this).attr('href'));
				return false;
			});
		}

		// Sortable
		$container.find('.sort-control input').unbind('click').click(function(e) {
			refresh($container, $(this).attr('value'));
			$(this).attr('disabled', true);
			e.stopPropagation();
		});
		$container.find("ul[class^='sortable-']").sortable({
			update : function(e) {
				$list = $(this);
				do_class = $list.attr('class').replace('sortable-','').replace('ui-sortable','');
				$.post('DataObjectManager_Controller/dosort/' + do_class, $list.sortable("serialize"));
				e.stopPropagation();
			},
			items : 'li:not(.head)',
			containment : 'document',
			tolerance : 'intersect',
			handle : ($('#list-holder').hasClass('grid') ? '.handle' : null)
		});
		
		// Column sort
		if(!$container.hasClass('ImageDataObjectManager')) {
			$container.find('li.head a').unbind('click').click(function() {
				refresh($container, $(this).attr('href'));
				return false;
			});
		}
		
		// Filter
		$container.find('.dataobjectmanager-filter select').unbind('change').change(function(e) {
			refresh($container, $(this).attr('value'));
		});

		// Page size
		$container.find('.per-page-control select').unbind('change').change(function(e) {
			refresh($container, $(this).attr('value'));
		});

		
		// Refresh filter
		$container.find('.dataobjectmanager-filter .refresh').unbind('click').click(function(e) {
			refresh($container, $container.attr('href'));
			e.stopPropagation();
			return false;
		})
	
		// Search
		var request = false;
		$container.find('#srch_fld').focus(function() {
			if($(this).attr('value') == "Search") $(this).attr('value','').css({'color' : '#333'});
		}).unbind('blur').blur(function() {
			if($(this).attr('value') == '') $(this).attr('value','Search').css({'color' : '#666'});
		}).unbind('keyup').keyup(function(e) {
				if(request) window.clearTimeout(request);
				$input = $(this);
				request = window.setTimeout(function() {
					url = $(container_id).attr('href').replace(/\[search\]=(.)*?&/, '[search]='+$input.attr('value')+'&');
					refresh($container, url);
					
				},500)
			e.stopPropagation();
		});
		
		$container.find('#srch_clear').unbind('click').click(function() {
			$container.find('#srch_fld').attr('value','').keyup();
		});
		

    $container.find('a.tooltip').tooltip({
		  delay: 500,
		  showURL: false,
		  track: true,
		  bodyHandler: function() {
			  return $(this).parents('li').find('span.tooltip-info').html();
		  }
    });
    
    
    // Add the slider to the ImageDataObjectManager
    if($container.hasClass('ImageDataObjectManager')) {
			var MIN_IMG_SIZE = 25
			var MAX_IMG_SIZE = 300;
			var START_IMG_SIZE = 100;
			var new_image_size;
			$('.size-control').slider({
				
				// Stupid thing doesn't work. Have to force it with CSS
				//startValue : (START_IMG_SIZE - MIN_IMG_SIZE) / ((MAX_IMG_SIZE - MIN_IMG_SIZE) / 100),
				slide : function(e, ui) {
					new_image_size = MIN_IMG_SIZE + (ui.value * ((MAX_IMG_SIZE - MIN_IMG_SIZE)/100));
					$('.grid li img.image').css({'width': new_image_size+'px'});
					$('.grid li').css({'width': new_image_size+'px', 'height' : new_image_size +'px'});
				},
				
				stop : function(e, ui) {
					new_image_size = MIN_IMG_SIZE + (ui.value * ((MAX_IMG_SIZE - MIN_IMG_SIZE)/100));				
					url = $(container_id).attr('href').replace(/\[imagesize\]=(.)*/, '[imagesize]='+Math.floor(new_image_size));
					refresh($container, url);
				}
			});
			
			$('.ui-slider-handle').css({'left' : $('#size-control-wrap').attr('class').replace('position','')+'px'});    
    
    }  
    // RelationDataObjectManager
    
    if($container.hasClass('RelationDataObjectManager')) {
			var $checkedList = $(container_id+'_CheckedList');
			$container.find('.actions input, .file-label input').unbind('click').click(function(e){
				if($(this).attr('type') == "radio") {
					$(this).parents('li').siblings('li').removeClass('selected');
					$(this).parents('li').toggleClass('selected');
					$checkedList.attr('value', ","+$(this).val()+",");
				}
				else {
					$(this).parents('li').toggleClass('selected');
					val = ($(this).attr('checked')) ? $checkedList.val() + $(this).val()+"," : $checkedList.val().replace(","+$(this).val()+",",",");
					$checkedList.attr('value', val);
				}
				e.stopPropagation();
			});
	
			$container.find('.actions input, .file-label input').each(function(i,e) {
				if($checkedList.val().indexOf(","+$(e).val()+",") != -1)
					$(e).attr('checked',true).parents('li').toggleClass('selected');
				else
					$(e).attr('checked',false);
					
			});	
			
			$container.find('a[rel=clear]').unbind('click').click(function(e) {
			 $container.find('.actions input, .file-label input').each(function(i,e) {
			   $(e).attr('checked', false).parents('li').removeClass('selected');
			   $checkedList.attr('value','');
			 });
			});	
    }
		
    // Columns. God forbid there are more than 10.
    cols = $('.list #dataobject-list li.head .fields-wrap .col').length;
    if(cols > 10) {
    	$('.list #dataobject-list li .fields-wrap .col').css({'width' : ((Math.floor(100/cols)) - 0.1) + '%' });
    }
		

};

$.fn.DataObjectManager.getPageHeight = function() {
    var windowHeight
    if (self.innerHeight) {	// all except Explorer
      windowHeight = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
      windowHeight = document.documentElement.clientHeight;
    } else if (document.body) { // other Explorers
      windowHeight = document.body.clientHeight;
    }	
    return windowHeight;
};

$.fn.DataObjectManager.getPageScroll = function() {
    var xScroll, yScroll;
    if (self.pageYOffset) {
      yScroll = self.pageYOffset;
      xScroll = self.pageXOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
      yScroll = document.documentElement.scrollTop;
      xScroll = document.documentElement.scrollLeft;
    } else if (document.body) {// all other Explorers
      yScroll = document.body.scrollTop;
      xScroll = document.body.scrollLeft;	
    }
    return new Array(xScroll,yScroll) 
};


$().ajaxSend(function(r,s){  
 $(".ajax-loader").show();  
});  
   
$().ajaxStop(function(r,s){  
  $(".ajax-loader").fadeOut("fast");  
}); 
 
$('.DataObjectManager').livequery(function(){
   $(this).DataObjectManager();                           
});

})(jQuery);


function refresh($div, link)
{
	 // Kind of a hack. Pass the list of ids to the next refresh
	 var listValue = ($div.hasClass('RelationDataObjectManager')) ? jQuery('#'+$div.attr('id')+'_CheckedList').val() : false;
	 	 
	 jQuery.ajax({
	   type: "GET",
	   url: link,
	   success: function(html){
	   		if(!$div.next().length && !$div.prev().length)
	   			$div.parent().html(html);
	   		else
				$div.replaceWith(html);
        	
			if(listValue) {
				 jQuery('#'+$div.attr('id')+'_CheckedList').attr('value',listValue);
			}
			jQuery('#'+$div.attr('id')).DataObjectManager();
		}
	 });
}
