<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2011 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################

##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                            Society / Edition                           #
#                                   /                                    #
#                                                                        #
#   modified by webspell|k3rmit (Stefan Giesecke) in 2009                #
#                                                                        #
#   - Modifications are released under the GNU GENERAL PUBLIC LICENSE    #
#   - It is NOT allowed to remove this copyright-tag                     #
#   - http://www.fsf.org/licensing/licenses/gpl.html                     #
#                                                                        #
##########################################################################
*/

/* define calendar functions */

function print_calendar($mon,$year) {
	global $dates, $first_day, $start_day, $_language;
  systeminc('func/calendar');
	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	$first_day = mktime(0,0,0,$mon,1,$year);
	$start_day = date("w",$first_day);
	if($start_day == 0) $start_day = 7;
	$res = getdate($first_day);
	$month_name = $res["month"];
	$no_days_in_month = date("t",$first_day);

	//If month's first day does not start with first Sunday, fill table cell with a space
	for ($i = 1; $i <= $start_day;$i++) $dates[1][$i] = " ";

	$row = 1;
	$col = $start_day;
	$num = 1;
	while($num<=31)	{
		if ($num > $no_days_in_month) break;
		else {
			$dates[$row][$col] = $num;
			if (($col + 1) > 7)	{
				$row++;
				$col = 1;
			}
			else  $col++;

			$num++;
		}
	}

	$mon_num = date("n",$first_day);
	$temp_yr = $next_yr = $prev_yr = $year;

	$prev = $mon_num - 1;
	if ($prev<10) $prev="0".$prev;
	$next = $mon_num + 1;
	if ($next<10) $next="0".$next;

	//If January is currently displayed, month previous is December of previous year
	if ($mon_num == 1){
		$prev_yr = $year - 1;
		$prev = 12;
	}

	//If December is currently displayed, month next is January of next year
	if ($mon_num == 12)	{
		$next_yr = $year + 1;
		$next = 1;
	}

	echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=01">'.mb_substr($_language->module['jan'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=02">'.mb_substr($_language->module['feb'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=03">'.mb_substr($_language->module['mar'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=04">'.mb_substr($_language->module['apr'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=05">'.mb_substr($_language->module['may'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=06">'.mb_substr($_language->module['jun'], 0, 3).'</a></td>
    </tr>
    <tr>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=07">'.mb_substr($_language->module['jul'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=08">'.mb_substr($_language->module['aug'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=09">'.mb_substr($_language->module['sep'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=10">'.mb_substr($_language->module['oct'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=11">'.mb_substr($_language->module['nov'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=12">'.mb_substr($_language->module['dec'], 0, 3).'</a></td>
    </tr>
    </table>
    <br />';	 

	echo'<a name="event"></a><table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td class="title" align="center">&laquo; <a class="titlelink" href="index.php?site=calendar&amp;month='.$prev.'&amp;year='.$prev_yr.'">'.mb_substr($_language->module[strtolower(date('M', mktime(0, 0, 0, $prev, 1, $prev_yr)))], 0, 3).'</a></td>
      <td class="title" align="center" colspan="5">'.$_language->module[strtolower(date("M",$first_day))].' '.$temp_yr.'</td>
      <td class="title" align="center"><a class="titlelink" href="index.php?site=calendar&amp;month='.$next.'&amp;year='.$next_yr.'">'.mb_substr($_language->module[strtolower(date('M', mktime(0, 0, 0, $next, 1, $next_yr)))], 0, 3).'</a> &raquo;</td>
    </tr>
    <tr>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['mon'].'</td>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['tue'].'</td>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['wed'].'</td>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['thu'].'</td>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['fri'].'</td>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['sat'].'</td>
      <td bgcolor="'.BGCAT.'" width="16%" align="center">'.$_language->module['sun'].'</td>
    </tr><tr>';

	$days = date("t", mktime(0, 0, 0, $mon, 1, $year)); //days of selected month
  switch($days) {
	  case 28: 
			$end = ($start_day > 1) ? 5:4;
	  break;
	  case 29: 
			$end = 5;
	  break;
	  case 30: 
			$end = ($start_day == 7) ? 6:5;
	  break;
	  case 31: 
			$end = ($start_day > 5) ? 6:5;
	  break;
	 	default: 
			$end = 6;
  }
	$count=0;
	for ($row=1;$row<=$end;$row++) {
		for ($col=1;$col<=7;$col++) {
			if (!isset($dates[$row][$col])) $dates[$row][$col] = " ";
			if (!strcmp($dates[$row][$col]," ")) $count++;

			$t = $dates[$row][$col];
			if($t < 10) $tag = "0$t";
			else $tag = $t;

			// DATENBANK ABRUF
			$start_date = mktime(0, 0, 0, $mon, (int)$t, $year);
			$end_date = mktime(23, 59, 59, $mon, (int)$t, $year);
			
      unset($termin);

			$ergebnis = safe_query("SELECT * FROM ".PREFIX."upcoming");
			$anz = mysql_num_rows($ergebnis);
			if($anz) {
				$termin = '';
				while ($ds = mysql_fetch_array($ergebnis)) {
					if($ds['type']=="d") {
						if(($start_date<=$ds['date'] && $end_date>=$ds['date']) || ($start_date>=$ds['date'] && $end_date<=$ds['enddate']) || ($start_date<=$ds['enddate'] && $end_date>=$ds['enddate']))
						$termin.='<a href="index.php?site=calendar&amp;tag='.$t.'&amp;month='.$mon.'&amp;year='.$year.'#event">'.clearfromtags($ds['short']).'</a><br />';
					}
					else {
						if($ds['date']>=$start_date && $ds['date']<=$end_date) {
							$begin = date("H:i", $ds['date']);
							$termin.='<a href="index.php?site=calendar&amp;tag='.$t.'&amp;month='.$mon.'&amp;year='.$year.'">'.$begin.' '.clearfromtags($ds['opptag']).'</a><br />';
						}
					}
				}
			}
			else $termin="<br /><br />";
			// DB ABRUF ENDE

			//If date is today, highlight it
			if (($t == date("j")) && ($mon == date("n")) && ($year == date("Y"))) echo'<td height="40" valign="top" bgcolor="'.BG_4.'"><b>'.$t.'</b><br />'.$termin.'</td>';
			//  If the date is absent ie after 31, print space
			else {
				if($t==' ') echo'<td height="40" valign="top" style="background-color:'.BG_1.';">&nbsp;</td>';
				else echo'<td height="40" valign="top" style="background-color:'.BG_2.';">'.$t.'<br />'.$termin.'</td>';
			}

		}
		if (($row + 1) != ($end+1)) echo'</tr><tr>';
		else echo'</tr>';
	}
	echo '<tr>
		      <td bgcolor="'.BGCAT.'" colspan="7" align="center"><a class="category" href="index.php?site=calendar#event"><b>'.$_language->module['today_events'].'</b></a></td>
		    </tr>
		  </table>
		  <br /><br />';
}

function print_termine($tag,$month,$year) {
	global $wincolor;
	global $loosecolor;
	global $drawcolor;
	global $userID;
	global $_language;

	$_language->read_module('calendar');

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	$start_date = mktime(0, 0, 0, $month, $tag, $year);
	$end_date = mktime(23, 59, 59, $month, $tag, $year);
	unset($termin);

	$ergebnis = safe_query("SELECT * FROM ".PREFIX."upcoming");
	$anz = mysql_num_rows($ergebnis);
	if($anz) {
		while ($ds=mysql_fetch_array($ergebnis)) {
			if(($start_date<=$ds['date'] && $end_date>=$ds['date']) || ($start_date>=$ds['date'] && $end_date<=$ds['enddate']) || ($start_date<=$ds['enddate'] && $end_date>=$ds['enddate'])) {
				$date = date("d.m.Y", $ds['date']);
				$time = date("H:i", $ds['date']);
				$enddate = date("d.m.Y", $ds['enddate']);
				$endtime = date("H:i", $ds['enddate']);
				$title=clearfromtags($ds['title']);
				$location='<a href="'.$ds['locationhp'].'" target="_blank">'.clearfromtags($ds['location']).'</a>';
				$dateinfo=cleartext($ds['dateinfo']);
				$dateinfo = toggle($dateinfo, $ds['upID']);
				$country="[flag]".$ds['country']."[/flag]";
				$country=flags($country);
				$register_flag=$ds['registering'];
				$register_text=$ds['register_text'];
				$registered_people = '';
        $announce = '';
				$adminaction = '';
				$annoucements='';
				
				if(isclanwaradmin($userID)) $adminaction='<div align="right"><input type="button" value="'.$_language->module['show_applies'].'" onclick="document.getElementById(\'upcoming_'.$ds['upID'].'\').style.display=\'block\'" /> <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=editdate&amp;upID='.$ds['upID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'calendar.php?action=delete&amp;upID='.$ds['upID'].'\')" value="'.$_language->module['delete'].'" /></div>';
				
				if(($ds['date']>time()) && ($ds['registering']==1)) $announce='&#8226; <a href="index.php?site=calendar&amp;action=announce&amp;upID='.$ds['upID'].'">'.$_language->module['announce_here'].'</a>';
				else $announce='';	
				
				if(isclanwaradmin($userID)){
					$annoucements='<tr>
											     <td colspan="2" bgcolor="'.$bghead.'" class="title">&nbsp; &#8226; '.$_language->module['announcements'].' <a href="calendar.php?action=createcsv&amp;upID='.$ds['upID'].'">'.$_language->module['csv_download'].'</a></td>
											   </tr>';
					$announces = safe_query("SELECT * FROM ".PREFIX."upcoming_announce WHERE `upID`=".$ds['upID']." ORDER BY `annID` ASC");
					$any_announces=mysql_num_rows($announces);
					if($any_announces){
						$registerfieldIDs=explode('|', $ds['registerfieldIDs']);
						while($announcerow=mysql_fetch_array($announces)){
							foreach($registerfieldIDs AS $registerfieldID){
								$registerfielddata=null;
								$type=getregisterfieldtype($registerfieldID);
								$registerfielddata=getregisterfielddata($ds['upID'], $announcerow['annID'], $registerfieldID);
								if($type=='text'){
									if(is_null($registerfielddata) || $registerfielddata==''){
										$registerfielddata=$_language->module['nothing_entered'];
									}
								}
								elseif($type=='checkbox'){
									if(is_null($registerfielddata) || $registerfielddata=''){
										$registerfielddata=$_language->module['no'];
									}
									else{
										$registerfielddata=$_language->module['yes'];
									}
								}
								else{
									$registerfielddata='ERROR: wrong registerfield type';
								}
								
								$registered_people.='<tr>';
								$registered_people.='<td bgcolor="'.$bgcat.'" align="right" width="25%">'.getregisterfieldname($registerfieldID, $_language->language).': </td>';
								$registered_people.='<td bgcolor="'.$bgcat.'" align="left" width="75%">'.getinput($registerfielddata).'</td>';
								$registered_people.='</tr>';
							}
							$registered_people.='<tr>';
							$registered_people.='<td bgcolor="'.$bgcat.'" align="right" width="25%">'.$_language->module['systemuser'].': </td>';
							$registered_people.='<td bgcolor="'.$bgcat.'" align="left" width="75%"><a href="index.php?site=profile&id='.$announcerow['userID'].'">'.getfirstname($announcerow['userID']).' \''.getnickname($announcerow['userID']).'\' '.getlastname($announcerow['userID']).'</a></td>';
							$registered_people.='</tr>';
							$registered_people.='<tr><td colspan="2" bgcolor="'.$bgcat.'"><hr></td></tr>';
						}
					}
					else{
						$registered_people.='<tr><td colspan="2" bgcolor="'.$bgcat.'">'.$_language->module['no_announced'].'</td></tr>';
					}
				}
				
				$bg1=BG_1;
				$bg2=BG_2;
				$bg3=BG_3;
				$bg4=BG_4;

				eval ("\$upcoming_date_details = \"".gettemplate("upcoming_date_details")."\";");
				echo $upcoming_date_details;
			}
		}
	}
	else echo $_language->module['no_entries'];
}

/* beginn processing file */

if(isset($_GET['action'])) $action = $_GET['action'];
else $action='';

if($action=="delete"){
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
		
	$_language->read_module('calendar');
	if(!isclanwaradmin($userID)) die($_language->module['no_access']);
	$upID = $_GET['upID'];

	safe_query("DELETE FROM ".PREFIX."upcoming WHERE upID='$upID'");
	safe_query("DELETE FROM ".PREFIX."upcoming_announce WHERE upID='$upID'");
	safe_query("DELETE FROM ".PREFIX."upcoming_data WHERE upID='$upID'");
	
	header("Location: index.php?site=calendar");
}
elseif($action=="createcsv"){
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	systeminc('func/calendar');
	$_language->read_module('calendar');
	$upID=$_GET['upID'];
	if(isclanwaradmin($userID)){
		$upcomings = safe_query("SELECT * FROM ".PREFIX."upcoming WHERE `upID`=".$upID);
		$ds=mysql_fetch_array($upcomings);
		$csv='';
		$announces = safe_query("SELECT * FROM ".PREFIX."upcoming_announce WHERE `upID`=".$upID." ORDER BY `annID` ASC");
		$any_announces=mysql_num_rows($announces);
		if($any_announces){
			$registerfieldIDs=explode('|', $ds['registerfieldIDs']);
			//create header
			foreach($registerfieldIDs AS $registerfieldID){
				$csv.=getregisterfieldname($registerfieldID, $_language->language).';';
			}
			$csv.="\n";
			//create entries
			while($announcerow=mysql_fetch_array($announces)){
				foreach($registerfieldIDs AS $registerfieldID){
					$registerfielddata=null;
					$type=getregisterfieldtype($registerfieldID);
					$registerfielddata=getregisterfielddata($upID, $announcerow['annID'], $registerfieldID);
					if($type=='text' || $type=='textarea'){
						if(is_null($registerfielddata) || $registerfielddata==''){
							$csv.=$_language->module['nothing_entered'].';';
						}
						else{
							$csv.=$registerfielddata.';';
						}
					}
					elseif($type=='checkbox'){
						if(is_null($registerfielddata) || $registerfielddata=''){
							$csv.=$_language->module['no'].';';
						}
						else{
							$csv.=$_language->module['yes'].';';
						}
					}
					else{
						$csv.='ERROR: wrong registerfield type'.';';
					}
				}
				$csv.="\n";
			}
		}
		$test='test.csv';
		header('Content-Type: text/plain; charset=utf-8');
		header("Content-Disposition: attachment; filename=\"".$test."\"");
		header("Content-Type: application/octet-stream;");
		header("Content-Transfer-Encoding: binary");
		print $csv;
	}
}
elseif($action=="saveannounce"){
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	include("src/func/calendar.php");
	$_language->read_module('calendar');
	
	$ds=mysql_fetch_assoc(safe_query("SELECT date, title, registering_max FROM ".PREFIX."upcoming WHERE upID=".(int)$_POST['upID']." AND date>".time()));
	if(isset($ds['date'])) {
		//echo '<br><br>';
		
		if($userID){
			$usID=$userID;
		}
		else{
			$usID='';
		}
		
		safe_query("INSERT INTO ".PREFIX."upcoming_announce (upID, userID) values('".(int)$_POST['upID']."', '".$usID."')");
		
		$allreadyregistered=mysql_fetch_array(safe_query("SELECT COUNT(*) AS allreadyregistered FROM ".PREFIX."upcoming_announce WHERE upID=".(int)$_POST['upID']));
		$registeredamount=$allreadyregistered['allreadyregistered'];
		if(($registeredamount>$ds['registering_max']) && ($ds['registering_max']!=0)){
			$waitinglist=1;
			$mailbody=$_language->module['confirmation_waitinglist']."\n\n".$_language->module['confirmation_data']."\n\n";
		}
		else{
			$waitinglist=0;
			$mailbody=$_language->module['confirmation'].' '.$ds['title']."\n\n".$_language->module['confirmation_data']."\n\n";
		}
		
		$annID=mysql_insert_id();
				
		$ToEmail=getemail($userID);
		$header=$_language->module['registration'].': '.$ds['title'];
		foreach($_POST['registerfield'] AS $registerfieldID => $data){
			safe_query("INSERT INTO ".PREFIX."upcoming_data (upID, annID, registerfieldID, data) values('".(int)$_POST['upID']."', '".$annID."', '".$registerfieldID."', '".$data."') ");
			$mailbody.=getregisterfieldname($registerfieldID, $_language->language).': ';
			$mailbody.=getinput($data)."\n";
		}
		mail($ToEmail,$header, $mailbody, "From:".$admin_email."\nContent-type: text/plain; charset=utf-8\n");
		//safe_query("INSERT INTO ".PREFIX."upcoming_announce ( upID, userID, status ) values( '".(int)$_POST['upID']."', '$usID', '".$_POST['status']{0}."' ) ");
		//header("Location: index.php?site=calendar");
		//echo $_language->module['emailnotifier'];
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<meta name="description" content="Website using webSPELL 4 CMS" />
					<meta name="author" content="webspell.org" />
					<meta name="keywords" content="webspell, webspell4, cms, society, edition" />
					<meta name="copyright" content="Copyright &copy; 2005 - 2011 by webspell.org" />
					<meta name="generator" content="webSPELL" />
					<title>'.PAGETITLE.'</title>
					<link href="_stylesheet.css" rel="stylesheet" type="text/css" />
					<meta http-equiv="refresh" content="5;URL=index.php?site=calendar" />
					</head>
					<body bgcolor="'.PAGEBG.'">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td height="500" align="center">
							<table width="350" border="0" cellpadding="10" cellspacing="0" style="border:1px solid '.BORDER.'" bgcolor="'.BG_1.'">
								<tr>
									<td align="center">'.$_language->module['emailnotifier'].'</td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
					</body>
					</html>';
	}
	else{
		header("Location: index.php?site=calendar");
	}
}
elseif($action=="saveeditdate") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('calendar');
	if(!isclanwaradmin($userID)) die($_language->module['no_access']);

	$hour = $_POST['hour'];
	$minute = $_POST['minute'];
	$day = $_POST['day'];
	$month = $_POST['month'];
	$year = $_POST['year'];

	$date=mktime($hour,$minute,0,$month,$day,$year);
	$enddate=mktime($_POST['endhour'],$_POST['endminute'],0,$_POST['endmonth'],$_POST['endday'],$_POST['endyear']);
	
	if(isset($_POST['register_flag'])){
		if($_POST['register_flag']==1){
			$register_flag=1;
		}
		else{
			$register_flag=0;
		}
	}
	else{
		$register_flag=0;
	}
	
	if(isset($_POST['register_flag'])){
		$registerfields=$_POST['registerfields'];
  	$registerfieldslength=count($registerfields);
  	$registerfieldIDs=$registerfields[0];
  	for($i=1;$i<$registerfieldslength;$i++){
  		$registerfieldIDs.='|'.$registerfields[$i];
  	}
  	safe_query("UPDATE ".PREFIX."upcoming SET date='$date',
	                                 						enddate='$enddate', 
																						  short='".$_POST['short']."',
																						  title='".$_POST['title']."',
																						  country='".$_POST['country']."',
																						  location='".$_POST['location']."',
																						  locationhp='".$_POST['locationhp']."',
																						  dateinfo='".$_POST['message']."',
																						  registering=1,
																						  registering_max='".$_POST['register_max']."',
																						  register_text='".$_POST['register_text']."',
																						  registerfieldIDs='".$registerfieldIDs."'
																					WHERE upID='".$_POST['upID']."'");
	
		header("Location: index.php?site=calendar&tag=$day&month=$month&year=$year");
	}
	else{
		safe_query("UPDATE ".PREFIX."upcoming SET date='$date',
	                                 						enddate='$enddate', 
																						  short='".$_POST['short']."',
																						  title='".$_POST['title']."',
																						  country='".$_POST['country']."',
																						  location='".$_POST['location']."',
																						  locationhp='".$_POST['locationhp']."',
																						  dateinfo='".$_POST['message']."',
																						  registering=0,
																						  register_text='',
																						  registerfieldIDs=''
																					WHERE upID='".$_POST['upID']."'");
	
		header("Location: index.php?site=calendar&tag=$day&month=$month&year=$year");
	}
}
elseif($action=="savedate") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('calendar');
	if(!isclanwaradmin($userID)) die($_language->module['no_access']);

	$date=mktime((int)$_POST['hour'],(int)$_POST['minute'],0,$_POST['month'],$_POST['day'],$_POST['year']);
	$enddate=mktime($_POST['endhour'],$_POST['endminute'],0,$_POST['endmonth'],$_POST['endday'],$_POST['endyear']);
	if($date>$enddate) {
		$temp=$date;
		$date=$enddate;
		$enddate=$temp;
		unset($temp);
	}
	if(isset($_POST['register_flag'])){
		if($_POST['register_flag']==1){
			$register_flag=1;
		}
		else{
			$register_flag=0;
		}
	}
	else{
		$register_flag=0;
	}
  if(isset($_POST['register_flag'])){
  	$registerfields=$_POST['registerfields'];
  	$registerfieldslength=count($registerfields);
  	$registerfieldIDs=$registerfields[0];
  	for($i=1;$i<$registerfieldslength;$i++){
  		$registerfieldIDs.='|'.$registerfields[$i];
  	}
  	safe_query("INSERT INTO ".PREFIX."upcoming ( date, type, enddate, short, title, country, location, locationhp, dateinfo, registering, registering_max, register_text, registerfieldIDs)
                values( '$date', 'd', '".$enddate."', '".$_POST['short']."', '".$_POST['title']."', '".$_POST['country']."', '".$_POST['location']."', '".$_POST['locationhp']."', '".$_POST['message']."', ".$register_flag.", '".$_POST['register_max']."','".$_POST['register_text']."', '".$registerfieldIDs."') ");
	  redirect("index.php?site=calendar&amp;tag=".$_POST['day']."&amp;month=".$_POST['month']."&amp;year=".$_POST['year'],"",0);
  }
  else{
	  safe_query("INSERT INTO ".PREFIX."upcoming ( date, type, enddate, short, title, country, location, locationhp, dateinfo)
                values( '$date', 'd', '".$enddate."', '".$_POST['short']."', '".$_POST['title']."', '".$_POST['country']."', '".$_POST['location']."', '".$_POST['locationhp']."', '".$_POST['message']."') ");
	  redirect("index.php?site=calendar&amp;tag=".$_POST['day']."&amp;month=".$_POST['month']."&amp;year=".$_POST['year'],"",0);
  }
	
}
elseif($action=="adddate") {
	$_language->read_module('calendar');
	if(isclanwaradmin($userID)) {

		eval ("\$title_calendar = \"".gettemplate("title_calendar")."\";");
		echo $title_calendar;

		$day = '';
		$month = '';
		$year = '';

		for($i=1; $i<32; $i++) {
			if($i==date("d", time())) $day.='<option selected="selected">'.$i.'</option>';
			else $day.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", time())) $month.='<option value="'.$i.'" selected="selected">'.date("M", time()).'</option>';
			else $month.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<2016; $i++) {
			if($i==date("Y", time())) $year.='<option value="'.$i.'" selected="selected">'.date("Y", time()).'</option>';
			else $year.='<option value="'.$i.'">'.$i.'</option>';
		}
		$squads=getsquads();

		$bg1=BG_1;
		//generate registerfields
		$registerfields='';
		$regfields=safe_query("SELECT * FROM ".PREFIX."upcoming_registerfields");
		$anyregfields=mysql_num_rows($regfields);
		if($anyregfields){
			$registerfields.='<select id="registerfields" name="registerfields[]" multiple="multiple" size="10" disabled>';
			while($regfieldrow=mysql_fetch_array($regfields)){
				$transdata=safe_query("SELECT `text` FROM `".PREFIX."translations` WHERE foreign_identifier=".$regfieldrow['registerfieldID']." AND `lang`='".$_language->language."' AND `type`='registerfield'");
		    $rowtransdata=mysql_fetch_array($transdata);
		    $text=$rowtransdata['text'];
				$registerfields.='<option value="'.$regfieldrow['registerfieldID'].'">'.$text.'</option>';
			}
			$registerfields.='</select>';
		}
		else{
			echo $_language->module['no_registerfields'];
		}
		
		eval ("\$upcoming_date_new = \"".gettemplate("upcoming_date_new")."\";");
		echo $upcoming_date_new;
	}
	else redirect('index.php?site=calendar', $_language->module['no_access']);
}
elseif($action=="editdate") {
	$_language->read_module('calendar');
	if(isclanwaradmin($userID)) {

		eval ("\$title_calendar = \"".gettemplate("title_calendar")."\";");
		echo $title_calendar;

		$day='';
		$month='';
		$year='';
		$endday='';
		$endmonth='';
		$endyear='';

		$upID = $_GET['upID'];
		$ds=mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."upcoming WHERE upID='$upID'"));
		for($i=1; $i<32; $i++) {
			if($i==date("d", $ds['date'])) $day.='<option selected="selected">'.$i.'</option>';
			else $day.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", $ds['date'])) $month.='<option value="'.$i.'" selected="selected">'.date("M", $ds['date']).'</option>';
			else $month.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<2016; $i++) {
			if($i==date("Y", $ds['date'])) $year.='<option selected="selected">'.$i.'</option>';
			else $year.='<option>'.$i.'</option>';
		}
		for($i=1; $i<32; $i++) {
			if($i==date("d", $ds['enddate'])) $endday.='<option selected="selected">'.$i.'</option>';
			else $endday.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", $ds['enddate'])) $endmonth.='<option value="'.$i.'" selected="selected">'.date("M", $ds['enddate']).'</option>';
			else $endmonth.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<2016; $i++) {
			if($i==date("Y", $ds['enddate'])) $endyear.='<option selected="selected">'.$i.'</option>';
			else $endyear.='<option>'.$i.'</option>';
		}
		$countries=str_replace(' selected="selected"', '', $countries);
		$countries=str_replace('value="'.$ds['country'].'"', 'value="'.$ds['country'].'" selected="selected"', $countries);

		$hour=date("H", $ds['date']);
		$endhour=date("H", $ds['enddate']);
		$minute=date("i", $ds['date']);
		$endminute=date("i", $ds['enddate']);

		$short = htmlspecialchars($ds['short']);
		$title = htmlspecialchars($ds['title']);
		$location = htmlspecialchars($ds['location']);
		$locationhp = htmlspecialchars($ds['locationhp']);
		$dateinfo = htmlspecialchars($ds['dateinfo']);
		
		if($ds['registering']==1){
			$checked_register_flag=' checked';
			$register_max=$ds['registering_max'];
		}
		else{
			$checked_register_flag='';
			$register_max='';
		}
    $register_text = htmlspecialchars($ds['register_text']);
		
		$bg1=BG_1;
		
		//generate registerfields
		$registerfieldIDs=explode('|', $ds['registerfieldIDs']);
		
		$registerfields='';
		$regfields=safe_query("SELECT * FROM ".PREFIX."upcoming_registerfields");
		$anyregfields=mysql_num_rows($regfields);
		if($anyregfields){
			$registerfields.='<select id="registerfields" name="registerfields[]" multiple="multiple" size="10" disabled>';
			while($regfieldrow=mysql_fetch_array($regfields)){
				$transdata=safe_query("SELECT `text` FROM `".PREFIX."translations` WHERE foreign_identifier=".$regfieldrow['registerfieldID']." AND `lang`='".$_language->language."' AND `type`='registerfield'");
		    $rowtransdata=mysql_fetch_array($transdata);
		    $text=$rowtransdata['text'];
		    if(in_array($regfieldrow['registerfieldID'], $registerfieldIDs)){
		    	$registerfields.='<option value="'.$regfieldrow['registerfieldID'].'" selected>'.$text.'</option>';
		    }
		    else{
		    	$registerfields.='<option value="'.$regfieldrow['registerfieldID'].'">'.$text.'</option>';
		    }
			}
			$registerfields.='</select>';
		}
		else{
			echo $_language->module['no_registerfields'];
		}
		
		//enable form fields if necessary
		if($ds['registering']==1){
			$activateformfields='<script language="JavaScript" type="text/javascript">
													 	 document.getElementById("register_text").disabled=false;
													 	 document.getElementById("registerfields").disabled=false;
													 	 document.getElementById("register_max").disabled=false;
													 </script>';
		}
		else{
			$activateformfields='';
		}
		
		eval ("\$upcoming_date_edit = \"".gettemplate("upcoming_date_edit")."\";");
		echo $upcoming_date_edit;
	}
	else redirect('index.php?site=calendar', $_language->module['no_access']);
}
elseif($action=="announce") {

	$_language->read_module('calendar');

	eval ("\$title_calendar = \"".gettemplate("title_calendar")."\";");
	echo $title_calendar;
  if($userID){
		if(isset($_GET['upID'])) {
			$upID = (int)$_GET['upID'];
	    $result=safe_query("SELECT title, registering, register_text, registerfieldIDs FROM ".PREFIX."upcoming WHERE upID='".$upID."' AND registering=1");
			$anyresult=mysql_num_rows($result);
			if($anyresult){		
				$upcomingrow=mysql_fetch_array($result);
				$longtitle=$upcomingrow['title'];
				$registertext=htmloutput($upcomingrow['register_text']);
				$registerfieldIDs=explode('|', $upcomingrow['registerfieldIDs']);
				$registerfieldoutput='<table cellspacing="0" cellpadding="2" border="0">';
				foreach($registerfieldIDs AS $registerfieldID){
					$registerfieldoutput.='<tr>';
					$registerfielddata=safe_query("SELECT type FROM ".PREFIX."upcoming_registerfields WHERE registerfieldID=".$registerfieldID);
					$registerfieldrow=mysql_fetch_array($registerfielddata);
					$type=$registerfieldrow['type'];
					$transdata=safe_query("SELECT `text` FROM `".PREFIX."translations` WHERE foreign_identifier=".$registerfieldID." AND `lang`='".$_language->language."' AND `type`='registerfield'");
				  $rowtransdata=mysql_fetch_array($transdata);
				  $text=$rowtransdata['text'];
				  $registerfieldoutput.='<td align="right" valign="top">'.$text.'</td><td valign="top">';
				  if($type=='text'){
						$registerfieldoutput.='<input name="registerfield['.$registerfieldID.']" type="text" style="width: 400px;" />';
					}
					elseif($type=='checkbox'){
						$registerfieldoutput.='<input name="registerfield['.$registerfieldID.']" type="checkbox" value="1" />';
					}
					elseif($type=='textarea'){
						$registerfieldoutput.='<textarea name="registerfield['.$registerfieldID.']" style="width: 400px; height: 50px;" /></textarea>';
					}
					else{
						
					}
					$registerfieldoutput.='</td></tr>';
				}
				$registerfieldoutput.='</table>';
				eval ("\$upcomingannounce = \"".gettemplate("upcomingannounce")."\";");
				echo $upcomingannounce;
			}
			else{
				echo $_language->module['upcoming_na'];
			}
		}
  }
  else{
  	echo $_language->module['register_needed'];
  }
}
else {

	$_language->read_module('calendar');

	eval ("\$title_calendar = \"".gettemplate("title_calendar")."\";");
	echo $title_calendar;

	if(isclanwaradmin($userID)) echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=adddate\');return document.MM_returnValue" value="'.$_language->module['add_event'].'" /><br /><br />';

	if(isset($_GET['month'])) $month = (int)$_GET['month'];
	else $month = date("m");
	
  if(isset($_GET['year'])) $year = (int)$_GET['year'];
	else $year = date("Y");

	if(isset($_GET['tag'])) $tag = (int)$_GET['tag'];
	else $tag = date("d");

	print_calendar($month,$year);
	print_termine($tag, $month, $year);
}

?>