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
 $testing = false;

require_once("../../interface/globals.php");
require_once("$srcdir/sql.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/acl.inc");

//make sure to get the dates
if ( ! $_POST['from_date']) {

    $from_date = date("Y-m-d");

} else {
    $from_date = $_POST['from_date'];
}

if ( !$_POST['to_date']) {
    // If a specific patient, default to 2 years ago.
    $to_date = date('Y-m-d', strtotime("+1 day"));
} else{

    $to_date = date('Y-m-d', strtotime("+1 day", strtotime($_POST['to_date'])));
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

function ifTestingTrue($testing){

    if($testing)
        return " Limit 0,10";
    else return "";

}

function getDiagsFromIssueEncounter($testing = false, $from_date, $to_date){

    $query = "SELECT patient_data.pid, issue_encounter.encounter, form_encounter.date, type, lists.title, lists.diagnosis, activity, dob, city, state, status, sex, race, ethnicity ";
    $query .= "FROM `issue_encounter` ";
    $query .= "join lists on list_id = lists.id ";
    $query .= "join patient_data on lists.pid = patient_data.pid ";
    $query .= "join form_encounter on issue_encounter.encounter = form_encounter.encounter";
    $query .= " where form_encounter.date >= '$from_date' and form_encounter.date < '$to_date' ";
    $query .= ifTestingTrue($testing);

    return $query;
}


function getDiagsFromBillingEncounter(){


}


if($_POST['func']=="list_all_users")
{

    $query = getDiagsFromIssueEncounter($testing, $from_date, $to_date);

    $result = sqlStatement($query);

    while ($row = sqlFetchArray($result)) {



            ?>

            <tr id="<?= $row["logid"]; ?>">
                <td align="center"><?= xl($row["pid"]); ?></td>
                <td align="center"><?= xl($row["sex"]); ?></td>
                <td align="center"><?= xl($row["dob"]); ?></td>
                <td align="center"><?= xl($row["ethnicity"]); ?></td>
                <td align="center"><?= xl($row["diagnosis"]); ?></td>
                <td align="left"><?= xl($row["title"]); ?></td>

            </tr>
            <?php

    }




}




?>
