$j = jQuery.noConflict();



$j.fn.DataObjectManager = function() {
	this.each(function() {
		$j.fn.DataObjectManager.init(this);
	});
};

$j.fn.DataObjectManager.init = function(obj) {
		var $container = $j(obj);
		var container_id = '#'+$container.attr('id');
		
		var facebox_close = function() {			
			$j('#facebox').fadeOut(function() {
				$j('#facebox .content').removeClass().addClass('content');
				$j('#facebox_overlay').remove();
				$j('#facebox .loading').remove();
				refresh($container, $container.attr('href'));		
			})
		};
		
		// Popup links
		$container.find('a.popuplink').unbind('click').click(function(e) {
			$j(document).unbind('close.facebox').bind('close.facebox', facebox_close);
			$j.facebox('<iframe src="'+$j(this).attr('href')+'" frameborder="0" width="500" height="' + ($j.fn.DataObjectManager.getPageHeight()*.6) + '"></iframe>');
			e.stopPropagation();
			return false;
		});
		
		// Delete

		$container.find('a.deletelink').unbind('click').click(function(e) {
			params = $('#SecurityID') ? {'forceajax' : '1', 'SecurityID' : $('#SecurityID').attr('value')} : {'forceajax' : '1'};
			$target = $j(this);
			$j.post(
				$target.attr('href'),
				params,
				function() {$($target).parents('li:first').fadeOut();$j(".ajax-loader").fadeOut("fast");}
			);
			e.stopPropagation();
			return false;
		});

		// Pagination
		$container.find('.Pagination a').unbind('click').click(function() {
			refresh($container, $j(this).attr('href'));
			return false;
		});
		
		// View
		if($container.hasClass('FileDataObjectManager') && !$container.hasClass('ImageDataObjectManager')) {
			$container.find('a.viewbutton').unbind('click').click(function() {
				refresh($container, $j(this).attr('href'));
				return false;
			});
		}

		// Sortable
		$container.find('.sort-control input').unbind('click').click(function(e) {
			refresh($container, $j(this).attr('value'));
			$j(this).attr('disabled', true);
			e.stopPropagation();
		});
		$container.find("ul[class^='sortable-']").sortable({
			update : function(e) {
				$list = $j(this);
				do_class = $list.attr('class').replace('sortable-','').replace('ui-sortable','');
				$j.post('DataObjectManager_Controller/dosort/' + do_class, $list.sortable("serialize"));
				e.stopPropagation();
			},
			items : 'li',
			containment : 'document',
			tolerance : 'intersect',
			handle : ($j('#list-holder').hasClass('grid') ? '.handle' : null)
		});
		
		// Column sort
		if(!$container.hasClass('ImageDataObjectManager')) {
			$container.find('li.head a').unbind('click').click(function() {
				refresh($container, $j(this).attr('href'));
				return false;
			});
		}
		
		// Filter
		$container.find('.dataobjectmanager-filter select').unbind('change').change(function(e) {
			refresh($container, $j(this).attr('value'));
		});

		// Page size
		$container.find('.per-page-control select').unbind('change').change(function(e) {
			refresh($container, $j(this).attr('value'));
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
			if($j(this).attr('value') == "Search") $j(this).attr('value','').css({'color' : '#333'});
		}).unbind('blur').blur(function() {
			if($j(this).attr('value') == '') $j(this).attr('value','Search').css({'color' : '#666'});
		}).unbind('keyup').keyup(function(e) {
				if(request) window.clearTimeout(request);
				$input = $j(this);
				request = window.setTimeout(function() {
					url = $j(container_id).attr('href').replace(/\[search\]=(.)*?&/, '[search]='+$input.attr('value')+'&');
					refresh($container, url);
				},200)
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
			  return $j(this).parents('li').find('span.tooltip-info').html();
		  }
    });
    
    
    // Add the slider to the ImageDataObjectManager
    if($container.hasClass('ImageDataObjectManager')) {
			var MIN_IMG_SIZE = 25
			var MAX_IMG_SIZE = 300;
			var START_IMG_SIZE = 100;
			var new_image_size;
			$j('.size-control').slider({
				
				// Stupid thing doesn't work. Have to force it with CSS
				//startValue : (START_IMG_SIZE - MIN_IMG_SIZE) / ((MAX_IMG_SIZE - MIN_IMG_SIZE) / 100),
				slide : function(e, ui) {
					new_image_size = MIN_IMG_SIZE + (ui.value * ((MAX_IMG_SIZE - MIN_IMG_SIZE)/100));
					$j('.grid li img.image').css({'width': new_image_size+'px'});
					$j('.grid li').css({'width': new_image_size+'px', 'height' : new_image_size +'px'});
				},
				
				stop : function(e, ui) {
					new_image_size = MIN_IMG_SIZE + (ui.value * ((MAX_IMG_SIZE - MIN_IMG_SIZE)/100));				
					url = $j(container_id).attr('href').replace(/\[imagesize\]=(.)*/, '[imagesize]='+Math.floor(new_image_size));
					refresh($container, url);
				}
			});
			
			$j('.ui-slider-handle').css({'left' : $j('#size-control-wrap').attr('class').replace('position','')+'px'});    
    
    }  
		
    // Columns. God forbid there are more than 10.
    cols = $j('.list #dataobject-list li.head .fields-wrap .col').length;
    if(cols > 10) {
    	$j('.list #dataobject-list li .fields-wrap .col').css({'width' : ((Math.floor(100/cols)) - 0.1) + '%' });
    }
		

};

$j.fn.DataObjectManager.getPageHeight = function() {
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

function refresh($div, link)
{
	 $j.ajax({
	   type: "GET",
	   url: link,
	   success: function(html){
	   		if(!$div.next().length && !$div.prev().length) {
	   			$div.parent().html(html);
	   		}
	   		else {
				$div.replaceWith(html);
	   		}

			$j('#'+$div.attr('id')).DataObjectManager();
		}
	 });
}

$j().ajaxSend(function(r,s){  
 $j(".ajax-loader").show();  
});  
   
$j().ajaxStop(function(r,s){  
  $j(".ajax-loader").fadeOut("fast");  
});  

Behaviour.register({
	'.DataObjectManager' : {
		initialize : function() {$j(this).DataObjectManager();}
	}
});