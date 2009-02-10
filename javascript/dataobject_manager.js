$j = jQuery.noConflict();

var $container;

$j.fn.DataObjectManager = function() {
	$container = $(this);
	container_id = '#'+$container.attr('id');
	$j.fn.DataObjectManager.init();
};

$j.fn.DataObjectManager.init = function() {
		
		// Popup links
		$container.find('a.popuplink').click(function() {
			$j(document).bind('close.facebox', function(e) {
				$container.parent().load($container.attr('href'),{}, function(){
						$j(container_id).DataObjectManager();
				});
				e.stopPropagation();
			});
			$j.facebox('<iframe src="'+$j(this).attr('href')+'" frameborder="0" width="500" height="' + ($j.fn.DataObjectManager.getPageHeight()*.6) + '"></iframe>');
			return false;
		});
		
		// Delete
		$container.find('a.deletelink').click(function() {
			params = $('#SecurityID') ? {'forceajax' : '1', 'SecurityID' : $('#SecurityID').attr('value')} : {'forceajax' : '1'};
			$target = $j(this);
			$j.post(
				$target.attr('href'),
				params,
				function() {$($target).parents('li:first').fadeOut()}
			);
			return false;
		});

		// Pagination
		$container.find('.Pagination a, .viewbutton').click(function() {
			$container.parent().load($j(this).attr('href'), {}, function() {$j(container_id).DataObjectManager();});
			return false;
		});

		// Sortable
		$container.find('.sort-control input').click(function(e) {
			$container.parent().load($j(this).attr('value'), {}, function() {$j(container_id).DataObjectManager();});
			$j(this).attr('disabled', true);
			e.stopPropagation();
		});
		$j("ul[class^='sortable-']").sortable({
			update : function(e) {
				$list = $j(this);
				do_class = $list.attr('class').replace('sortable-','').replace('ui-sortable','');
				$j.post('/DataObjectManager_Controller/dosort/' + do_class, $list.sortable("serialize"));
				e.stopPropagation();
			},
			items : 'li',
			containment : 'document',
			tolerance : 'intersect',
			handle : ($j('#list-holder').hasClass('grid') ? '.handle' : null)
		});
		$container.find('li.head a').click(function() {
			$container.parent().load($j(this).attr('href'), {}, function() {$j(container_id).DataObjectManager();});
			return false;
		});
		
		// Import
		/*
		$j('#ImportFolder option:contains(0 files)').attr('disabled', true);
		$j('#ImportFolder').change(function() {
				$j(document).bind('close.facebox', function(e) {
					$container.parent().load($container.attr('href'),{}, function(){
							$j(container_id).DataObjectManager();
					});
					e.stopPropagation();
				});
				$j.facebox('<iframe src="'+$j(this).attr('value')+'" frameborder="0" width="500" height="' + ($j.fn.DataObjectManager.getPageHeight()*.6) + '"></iframe>');		
		});
		*/
		$j('.dataobjectmanager-filter select').change(function() {
			$container.parent().load($j(this).attr('value'),{}, function() {
				$j(container_id).DataObjectManager();
			});
		});
	
		// Search
		var request = false;
		$j('#srch_fld').focus(function() {
			if($j(this).attr('value') == "Search") $j(this).attr('value','').css({'color' : '#333'});
		}).blur(function() {
			if($j(this).attr('value') == '') $j(this).attr('value','Search').css({'color' : '#666'});
		}).keyup(function(e) {
				if(request) window.clearTimeout(request);
				$input = $j(this);
				request = window.setTimeout(function() {
					url = $j(container_id).attr('href').replace(/\[search\]=(.)*?&/, '[search]='+$input.attr('value')+'&');
					$container.parent().load(url,{}, function() {$j(container_id).DataObjectManager();$input.focus();})
				},200)
			e.stopPropagation();
		});
		
		$j('#srch_clear').click(function() {
			$j('#srch_fld').attr('value','').keyup();
		});
    $j('a.tooltip').tooltip({
		  delay: 500,
		  showURL: false,
		  track: true,
		  bodyHandler: function() {
			  return $j(this).parents('li').find('span.tooltip-info').html();
		  }
    });
    
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