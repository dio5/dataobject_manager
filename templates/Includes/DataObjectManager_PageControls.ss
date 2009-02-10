<div class="dataobjectmanager-actions">
	<% if Can(add) %>
		<a class="popuplink" href="$AddLink" alt="add">
			<span class="addlink"><% sprintf(_t('ADDITEM', 'Add %s', PR_MEDIUM, 'Add [name]'),$Title) %></span>
		</a>
	<% end_if %>
	<% if Can(upload) %>
		<a class="popuplink" href="$UploadLink" alt="upload">
			<span class="uploadlink"><% _t('UPLOADITEM', 'Upload') %></span>
		</a>	
	<% end_if %>
	<% if Can(import) %>
		<a class="popuplink importlink" href="$ImportLink" alt="import">
			<span class="importlink"><% _t('IMPORTITEM', 'Import') %></span>
		</a>	
	<% end_if %>
</div>
<div class="dataobjectmanager-filter">
	<% if HasFilter %>$FilterDropdown<% end_if %>
</div>
<div class="top-controls">
	<div class="rounded_table_top_right">
		<div class="rounded_table_top_left">
			<div class="view-controls">
				<a href="$ListLink" class="viewbutton ListView" title="<% _t('LISTVIEW', 'List view') %>" <% if ListView %>class="current"<% end_if %>>List view</a>
				<a href="$GridLink" class="viewbutton GridView" title="<% _t('GRIDVIEW', 'Grid view') %>" <% if GridView %>class="current"><% end_if %>>Grid view</a>
			</div>
			<div class="Pagination">
				<% if FirstLink %><a class="First" href="$FirstLink" title="<% _t('VIEWFIRST', 'View first') %> $PageSize"><img src="dataobject_manager/images/resultset_first.png" alt="<% _t('VIEWFIRST', 'View first') %> $PageSize" />GOO</a>
				<% else %><span class="First"><img  src="dataobject_manager/images/resultset_first_disabled.png" alt="<% _t('VIEWFIRST', 'View first') %> $PageSize" /></span><% end_if %>
				<% if PrevLink %><a class="Prev" href="$PrevLink" title="<% _t('VIEWPREVIOUS', 'View previous') %> $PageSize"><img src="dataobject_manager/images/resultset_previous.png" alt="<% _t('VIEWPREVIOUS', 'View previous') %> $PageSize" /></a>
				<% else %><img class="Prev" src="dataobject_manager/images/resultset_previous_disabled.png" alt="<% _t('VIEWPREVIOUS', 'View previous') %> $PageSize" /><% end_if %>
				<span class="Count">
					<% _t('DISPLAYING', 'Displaying') %> $FirstItem <% _t('TO', 'to') %> $LastItem <% _t('OF', 'of') %> $TotalCount
				</span>
				<% if NextLink %><a class="Next" href="$NextLink" title="<% _t('VIEWNEXT', 'View next') %> $PageSize"><img src="dataobject_manager/images/resulset_next.png" alt="<% _t('VIEWNEXT', 'View next') %> $PageSize" /></a>
				<% else %><img class="Next" src="dataobject_manager/images/resulset_next_disabled.png" alt="<% _t('VIEWNEXT', 'View next') %> $PageSize" /><% end_if %>
				<% if LastLink %><a class="Last" href="$LastLink" title="<% _t('VIEWLAST', 'View last') %> $PageSize"><img src="dataobject_manager/images/resulset_last.png" alt="<% _t('VIEWLAST', 'View last') %> $PageSize" /></a>
				<% else %><span class="Last"><img src="dataobject_manager/images/resulset_last_disabled.png" alt="<% _t('VIEWLAST', 'View last') %> $PageSize" /></span><% end_if %>
			</div>
			<div class="dataobjectmanager-search">
				<span class="sbox_l"></span><span class="sbox"><input value="<% if SearchValue %>$SearchValue<% else %>Search<% end_if %>" type="search" id="srch_fld"  /></span><span class="sbox_r" id="srch_clear"></span>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
</div>
