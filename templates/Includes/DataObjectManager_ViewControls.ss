<div class="ViewControls">
<% if Sortable %>
	<input id="showall" type="checkbox" <% if ShowAll %>checked="checked"<% end_if %> value="<% if Paginated %>$ShowAllLink<% else %>$PaginatedLink<% end_if %>" /><label for="showall">allow sorting</label>
<% end_if %>
<% if ListView %><a href="$GridLink" class="GridView" title="<% _t('GRIDVIEW', 'Grid view') %>">grid view</a><% else %>grid view<% end_if %>
<% if GridView %><a href="$ListLink" class="ListView" title="<% _t('LISTVIEW', 'List view') %>">list view</a><% else %>list view<% end_if %>
<% if HasFilter %>$FilterDropdown<% end_if %>
</div>
