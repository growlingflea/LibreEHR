<?php
/**
 * library/ajax/claims_viewer_ajax.php Ajax file to process imported X12 filesgstat
 * file adapted to present user activity log
 * Copyright (C) 2012 Medical Information Integration <info@mi-squared.com>
 * Copyright (c) 2018 Growlingflea Software <daniel@growlingflea.com>
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
 *
 */
 $fake_register_globals=false;
 $sanitize_all_escapes=true;
 
require_once("../../interface/globals.php");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/acl.inc");

//This function will find the date-time of the last
function getLastSessionActivityTime($user, $date){
    $query = "Select date from log where user = '$user' and date < '$date' order by date desc limit 0,1";
    $result = sqlStatement($query);
    $row = sqlFetchArray($result);
    return $row['date'];

}

function getSessionTime($user, $date){
	//get the datetime of the next login.
	$query = "Select min(date) as date from log where date > '$date' and event = 'login' and user = '$user'";
	$result = sqlStatement($query);
	$row = sqlFetchArray($result);
    //send the next login time to get the last session activity of the current session
    if($row['date'] == null){

        return 0;

    }else {
        $date2 = getLastSessionActivityTime($user, $row['date']);
        $datetime1 = strtotime($date);
        $datetime2 = strtotime($date2);

        $interval = $datetime2 - $datetime1;

        $interval = round($interval / (60 * 60), 2);

        return $interval;

    }
}



//Enter false if searching for users that aren't active
function getUsersArray($active = true){

    $users = array();
    $query = "Select distinct(username) from users ";
    $query .= $active = true ? "where active = 1" : " ";
    $query .= " AND username != ''";
    $result = sqlStatement($query);
    while ($row = sqlFetchArray($result)) {
        array_push($users, $row['username']);

    }
    return $users;
}

//To list out records with given criteria
if($_POST['func']=="list_users")
{

	$qstring = "Select  *, log.id as logid from log join users on username = user where event like '%log%' and event not like '%attempt%' and users.active = 1";

	$result = sqlStatement($qstring);

    $gua_string = getUsersArray();

	while ($row = sqlFetchArray($result)) {
		if ($row['event'] == 'login') {
			$diff = getSessionTime($row['user'], $row['date']);

			?>

			<tr id="<?= $row["logid"]; ?>">
				<td align="center"><?= xl($row["date"]); ?></td>
				<td align="center"><?= xl($row["user"]); ?></td>
				<td align="center"><?= xl($row["lname"]); ?></td>
				<td align="center"><?= xl($row["fname"]); ?></td>
				<td align="center" label=""><?php echo xl($row["event"]) . " | " . $row['comments'] ?></td>
				<td align="center"><?php echo xl($diff); ?></td>
			</tr>
			<?php
		}
	}
}
?>
