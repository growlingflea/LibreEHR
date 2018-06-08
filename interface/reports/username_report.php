<?php
/**
 * interface/reports/claims_viewer.php List number of documents.
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 * Copyright (c) 2018 Growlingflea Software <daniel@growlingflea.com>
 * File adapted for user activity log.
 * @package ppc-db
 * @author  Tony
 */
 $fake_register_globals=false;
 $sanitize_all_escapes=true;

require_once("../globals.php");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formatting.inc.php");

?>
<head>
<?php html_header_show();?>
<title><?php xl('User Activity Report','e'); ?></title>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<style type="text/css">
@import "<?php echo $GLOBALS['webroot'] ?>/assets/js/datatables/media/css/demo_page.css";
@import "<?php echo $GLOBALS['webroot'] ?>/assets/js/datatables/media/css/demo_table.css";
@import "<?php echo $GLOBALS['webroot'] ?>/assets/js/css/jquery-ui-1-12-1/jquery-ui.css";
<!-- @import "<?php echo $GLOBALS['webroot'] ?>/library/css/jquery.tooltip.css"; -->
.mytopdiv { float: left; margin-right: 1em; }
</style>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/assets/js/datatables/media/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/assets/js/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/tooltip.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/assets/js/fancybox-1.3.4/jquery.fancybox-1.3.4.pack.js"></script>
<script type='text/javascript' src='<?php echo $GLOBALS['webroot'] ?>/library/dialog.js'></script>
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/assets/js/fancybox-1.3.4/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />

<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['webroot'] ?>/assets/js/DataTables-1.10.16/datatables.css">
<script type="text/javascript" charset="utf8" src="<?php echo $GLOBALS['webroot'] ?>/assets/js/DataTables-1.10.16/datatables.js"></script>
<!-- this is a 3rd party script -->
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/assets/js/datatables/extras/ColReorder/media/js/ColReorderWithResize.js"></script>
<link rel="stylesheet" href="../../library/css/jquery.datetimepicker.css">
<script>
$(document).ready(function() {
	listusers();

	// OnClick handler for the rows
	$('#document_table tbody tr').live('click',function(){
		var newpid=$(this).attr("id");

	 });



});

var iter=0;
var oTable;
//Function to initiate datatables plugin
function init_datatables()
{
	oTable=$('#document_table').dataTable({
		"iDisplayLength": 100
		// language strings are included so we can translate them


	});

	iter=1;
}

//Function to populate document list
function listusers()
{
	if(iter==1)
	oTable.fnClearTable();

    var details=0;
    if ($('#details_selector').is(':checked'))
    {
        details=1;
    }
    else
    {
        details=0;
    }

    $.ajax({
	  type: "POST",
	  url: "../../library/ajax/username_report_ajax.php",
	  data: {func:"list_users",details:details},
        beforeSend: function(){
            $('#image').show();
        },

	  success:function(data)
			  {
			    $('#users_list').html(data);
				init_datatables();
			  },
        complete: function(){
            $('#image').hide();
        }
	  
	});


	
}
</script>
</head>
<body class="body_top formtable">&nbsp;&nbsp;

<label><input type="checkbox" id="details_selector" onchange="listusers();"><?php echo xla('Show All'); ?></label>
&nbsp;&nbsp;
<img id="image" src="/images/loading.gif" width="200" height="200">



<table cellpadding="0" cellspacing="0" border="0" class="display formtable" id="document_table">
	<thead>
		<tr>
			<th><?php echo xla('Date'); ?></th>
			<th><?php echo xla('User'); ?></th>
			<th><?php echo xla('Last'); ?></th>
			<th><?php echo xla('First'); ?></th>
			<th><?php echo xla('Action'); ?></th>
			<th><?php echo xla('Session Time'); ?></th>

		</tr>
	</thead>
	<tbody id="users_list">
	</tbody>
</table>
</body>
