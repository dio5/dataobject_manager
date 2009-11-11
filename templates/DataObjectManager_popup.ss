<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<% base_tag %>
    <meta content="text/html; charset=utf-8" http-equiv="Content-type"/> 		
	</head>
	<body class="DataObjectManager-popup loading 
		<% if String %>
			<% if NestedController %>nestedController<% end_if %>
		<% else %>
			<% if DetailForm.NestedController %>nestedController<% end_if %>
		<% end_if %>
	">
		<div class="right $PopupClasses">
			<% control DetailForm %>
        <form $FormAttributes>
        	<% if Message %>
        	<p id="{$FormName}_error" class="message $MessageType">$Message</p>
        	<% else %>
        	<p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
        	<% end_if %>
        	<div id="FieldsWrap">
          	<fieldset>
          		<legend>$Legend</legend>
          		<% control Fields %>
          			$FieldHolder
          		<% end_control %>
          		<div class="clear"><!-- --></div>
          	</fieldset>
          </div>
      <% end_control %> 
          		<div id="pagination">
          				<div class="prev">
          				  <% if PrevRecordLink %>
          				    <a href="$PrevRecordLink" title=<% _t('PREVIOUS','Previous') %>">&laquo;<% _t('PREVIOUS','Previous') %></a>
          				  <% else %>
          				    &nbsp;
          				  <% end_if %>
          				</div>
          			<% control DetailForm %>
                	<% if Actions %>
                	<div class="Actions">
                		<% control Actions %>
                			$Field
                		<% end_control %>
                	</div>
                	<% end_if %>          			
          			<% end_control %>
          				<div class="next">
          				  <% if NextRecordLink %>
          				    <a href="$NextRecordLink" title=<% _t('NEXT','Next') %>"><% _t('NEXT','Next') %>&raquo;</a>
          				  <% else %>
          				    &nbsp;
          				  <% end_if %>
          				</div>
          		</div>

        </form>
		</div>
	</body>
</html>
