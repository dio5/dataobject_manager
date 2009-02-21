<div id="$id" class="RequestHandler FormField DataObjectManager FileDataObjectManager ImageDataObjectManager field" href="$CurrentLink">
	<div class="ajax-loader"></div>
	<div class="dataobjectmanager-actions <% if HasFilter %>filter<% end_if %>">
		<% if Can(upload) %>
			<a class="popuplink" href="$UploadLink" alt="upload">
				<span class="uploadlink"><img src="dataobject_manager/images/add.png" alt="" /><% sprintf(_t('ADDITEM', 'Add %s', PR_MEDIUM, 'Add [name]'),$PluralTitle) %></span>
			</a>	
		<% end_if %>
	</div>
	<div class="dataobjectmanager-filter">
		<% if HasFilter %>$FilterDropdown<% end_if %>
	</div>
	<div style="clear:both;">&nbsp;</div>
	<div class="top-controls">
		<div class="rounded_table_top_right">
			<div class="rounded_table_top_left">
				
				<div id="size-control-wrap" class="position{$SliderPosition}"><img src="dataobject_manager/images/zoom_out.gif" class="out" /><div class="size-control"></div><img src="dataobject_manager/images/zoom_in.gif" class="in"/></div>
				<div class="Pagination">
					<% if FirstLink %><a class="First" href="$FirstLink" title="<% _t('VIEWFIRST', 'View first') %> $PageSize"><img src="dataobject_manager/images/resultset_first.png" alt="" /></a>
					<% else %><span class="First"><img  src="dataobject_manager/images/resultset_first_disabled.png" alt="" /></span><% end_if %>
					<% if PrevLink %><a class="Prev" href="$PrevLink" title="<% _t('VIEWPREVIOUS', 'View previous') %> $PageSize"><img src="dataobject_manager/images/resultset_previous.png" alt="" /></a>
					<% else %><img class="Prev" src="dataobject_manager/images/resultset_previous_disabled.png" alt="" /><% end_if %>
					<span class="Count">
						<% _t('DISPLAYING', 'Displaying') %> $FirstItem <% _t('TO', 'to') %> $LastItem <% _t('OF', 'of') %> $TotalCount
					</span>
					<% if NextLink %><a class="Next" href="$NextLink" title="<% _t('VIEWNEXT', 'View next') %> $PageSize"><img src="dataobject_manager/images/resultset_next.png" alt="" /></a>
					<% else %><img class="Next" src="dataobject_manager/images/resultset_next_disabled.png" alt="" /><% end_if %>
					<% if LastLink %><a class="Last" href="$LastLink" title="<% _t('VIEWLAST', 'View last') %> $PageSize"><img src="dataobject_manager/images/resultset_last.png" alt="" /></a>
					<% else %><span class="Last"><img src="dataobject_manager/images/resultset_last_disabled.png" alt="" /></span><% end_if %>
				</div>
				<div class="dataobjectmanager-search">
					<span class="sbox_l"></span><span class="sbox"><input value="<% if SearchValue %>$SearchValue<% else %>Search<% end_if %>" type="text" id="srch_fld"  /></span><span class="sbox_r" id="srch_clear"></span>
				</div>
				<div style="clear:both;"></div>
			</div>
		</div>
	</div>
	<div class="$ListStyle column{$Headings.Count}" id="list-holder" style="width:100%;">
		<ul id="dataobject-list" <% if ShowAll %>class="sortable-{$sourceClass}"<% end_if %>>
			<% if ListView %>
				<li class="head">
					<div class="fields-wrap">
					<% control Headings %>
					<div class="col $FirstLast">
						<div class="pad">
								<a href="$SortLink">$Title &nbsp;
								<% if IsSorted %>
									<% if SortDirection = ASC %>
									<img src="cms/images/bullet_arrow_up.png" alt="" />
									<% else %>
									<img src="cms/images/bullet_arrow_down.png" alt="" />
									<% end_if %>
								<% end_if %>
								</a>
						</div>
					</div>
					<% end_control %>
					</div>
					<div class="actions col">&nbsp;</div>
				</li>
			<% end_if %>
			<% if Items %>
			<% control Items %>
				<li id="record-$Parent.id-$ID" style="width:{$ImageSize}px; height:{$ImageSize}px;">
							<div class="pad">
								<% if Top.ShowAll %><span class="handle"><img src="dataobject_manager/images/move_icon.jpg" /></span><% end_if %>
								<div class="file-icon"><a href="$EditLink" class="popuplink editlink tooltip"><img class="image" src="$FileIcon" alt="" style="width:{$ImageSize}px;" /></a></div>
								<div class="delete"><a href="$DeleteLink" class="deletelink"><img src="dataobject_manager/images/trash.gif" height="12px" alt="delete" /></a></div>
								<span class="tooltip-info" style="display:none">
									<% control Fields %>
										<strong>$Name</strong>: $Value<% if Last %><% else %><br /><% end_if %>
									<% end_control %>
								</span>
							</div>
					

				</li>
			<% end_control %>
			<% else %>
					<li><i><% _t('NOITEMSFOUND', 'No items found') %></i></li>
			<% end_if %>
			
		</ul>
	</div>
	<div class="bottom-controls">
		<div class="rounded_table_bottom_right">
			<div class="rounded_table_bottom_left">
				<div class="sort-control">
					<% if Sortable %>
						<input id="showall-{$id}" type="checkbox" <% if ShowAll %>checked="checked"<% end_if %> value="<% if Paginated %>$ShowAllLink<% else %>$PaginatedLink<% end_if %>" /><label for="showall-{$id}">Allow drag & drop reordering</label>
					<% end_if %>
				</div>
			</div>
		</div>
	</div>
</div>