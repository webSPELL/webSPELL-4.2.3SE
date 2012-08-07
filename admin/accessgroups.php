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

$_language->read_module('accessgroups', true);

if(!ispageadmin($userID) OR (mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php" AND mb_substr(basename($_SERVER['REQUEST_URI']),0,10) != "direct.php")) die($_language->module['access_denied']);

if(isset($_GET['action'])){
	$action=$_GET['action'];
}
else{
	$action='';
}



if($action=='removegroupuser') {
	$accessgroupID=(int)$_GET['accessgroupID'];
	$removeuser=(int)$_GET['userID'];
	safe_query("DELETE FROM `".PREFIX."accessgroup_members` WHERE `accessgroupID`=".$accessgroupID." AND `userID`=".$removeuser);
}
elseif($action=='addgroupuser') {
	$accessgroupID=(int)$_GET['accessgroupID'];
	$adduser=(int)$_GET['userID'];
	safe_query("INSERT INTO `".PREFIX."accessgroup_members` (`accessgroupID`, `userID`) VALUES (".$accessgroupID.", ".$adduser.")");
}
elseif($action=='addgroup') {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  echo'<h1>&curren; '.$_language->module['new'].'</h1>';
	echo '<br />';

	echo '<form method="post" action="admincenter.php?site=accessgroups&amp;action=saveaddgroup">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="20%"><b>'.$_language->module['accessgroupname'].'</b></td>
      <td width="80%"><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['save'].'" /></td>
    </tr>
  </table>
  </form>';
}
elseif($action=='saveaddgroup') {
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("INSERT INTO ".PREFIX."accessgroups (name) VALUES ('".$_POST['name']."')");
		echo '<script type="text/javascript">
						window.location = "admincenter.php?site=accessgroups";
					</script>
				 ';
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='editgroup') {
	echo'<h1>&curren; '.$_language->module['editgroup'].'</h1>';
	echo '<br />';
	$accessgroupID=(int)$_GET['id'];
	$db=safe_query("SELECT * FROM `".PREFIX."accessgroups` WHERE `accessgroupID`=".$accessgroupID);
	$anydb=mysql_num_rows($db);
	if($anydb){
		$dbrow=mysql_fetch_array($db);
		$CAPCLASS = new Captcha;
		$CAPCLASS->create_transaction();
		$hash = $CAPCLASS->get_hash();
	 	echo '<form method="post" action="admincenter.php?site=accessgroups&amp;action=saveeditgroup&amp;id='.$accessgroupID.'">
	  <table width="100%" border="0" cellspacing="1" cellpadding="3">
	    <tr>
	      <td width="20%"><b>'.$_language->module['accessgroupname'].'</b></td>
	      <td width="80%"><input type="text" name="name" size="60" value="'.$dbrow['name'].'" /></td>
	    </tr>
	    <tr>
	      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
	      <td><input type="submit" name="save" value="'.$_language->module['save'].'" /></td>
	    </tr>
	  </table>
	  </form>';
	}
	else{
		echo $_language->module['na'];
	}
}
elseif($action=='saveeditgroup'){
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE `".PREFIX."accessgroups` SET `name`='".$_POST['name']."' WHERE `accessgroupID`=".$_GET['id']);
		echo '<script type="text/javascript">
						window.location = "admincenter.php?site=accessgroups";
					</script>
				 ';
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='deletegroup'){
	$accessgroupID=(int)$_GET['id'];
	safe_query("DELETE FROM `".PREFIX."accessgroup_members` WHERE `accessgroupID`=".$accessgroupID);
	safe_query("DELETE FROM `".PREFIX."accessgroups` WHERE `accessgroupID`=".$accessgroupID);
	echo '<script type="text/javascript">
					window.location = "admincenter.php?site=accessgroups";
				</script>
			 ';
}
elseif($action=='groupmembers') {
	$accessgroupID=(int)$_GET['id'];
	if(isset($_GET['offset'])){
		$offset=(int)$_GET['offset'];
	}
	else{
		$offset=0;
	}
	if(isset($_GET['filter']) && $_GET['filter']!=''){
		$filterstring=" WHERE `nickname` LIKE '%".$_GET['filter']."%' OR `firstname` LIKE '%".$_GET['filter']."%' OR `lastname` LIKE '%".$_GET['filter']."%'";
		$filterurlstring='&filter='.$_GET['filter'];
		$filter=$_GET['filter'];
	}
	else{
		$filterstring='';
		$filterurlstring='';
		$filter='';
	}
	$perpage=25;
	
	$usercount=mysql_fetch_array(safe_query("SELECT COUNT(*) AS totalusers FROM `".PREFIX."user`".$filterstring));
	$totalusers=$usercount['totalusers'];
	
	if($offset==0){
		$previousoffset=0;
		$previousoffsetstatus=false;
	}
	else{
		$previousoffset=$offset-$perpage;
		$previousoffsetstatus=true;
	}
	
	if(($offset+$perpage)>($totalusers-1)){
		$nextoffset=0;
		$nextoffsetstatus=false;
	}
	else{
		$nextoffset=$offset+$perpage;
		$nextoffsetstatus=true;
	}
	
	echo'<h1>&curren; '.$_language->module['groupmembers'].' - '.getaccessgroupname($accessgroupID).'</h1>';
	echo '<br />';
	//fetch(\'direct.php?site=modules&action=activatetoggle&id=\' + id + \'&value=\' + checkbox.value,\'debugdiv\',\'replace\',\'event\');
	echo '<script type="text/javascript">
					function tooglegroupmembership(id){
					  accessgroupID='.$accessgroupID.';
					  if(document.getElementById(id).value==1){
					    document.getElementById(id).value=0;
					    fetch(\'direct.php?site=accessgroups&action=removegroupuser&accessgroupID=\' + accessgroupID + \'&userID=\' + id,\'debugdiv\',\'replace\',\'event\');
					  }
					  else{
					    document.getElementById(id).value=1;
					    fetch(\'direct.php?site=accessgroups&action=addgroupuser&accessgroupID=\' + accessgroupID + \'&userID=\' + id,\'debugdiv\',\'replace\',\'event\');
					  }
					}
				</script>';
	echo '<input type="text" value="'.$filter.'" id="filter" size="20" /> <input type="button" value="'.$_language->module['filter'].'" onclick="window.location=\'admincenter.php?site=accessgroups&action=groupmembers&id='.$accessgroupID.'&offset='.$offset.'&filter=\' + document.getElementById(\'filter\').value;" /><br /><br/>';
	echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="50%" class="title"><b>'.$_language->module['username'].'</b></td>
      <td width="50%" class="title"><b>'.$_language->module['accessgroupmembership'].'</b></td>
    </tr>';
	$i=1; 
	$users=safe_query("SELECT `userID`, `nickname` FROM `".PREFIX."user`".$filterstring." ORDER BY `nickname` ASC LIMIT ".$offset.",".$perpage);
	while($userrow=mysql_fetch_array($users)){
		if($i%2) { $td='td1'; }
    else { $td='td2'; }
    if(inaccessgroup($accessgroupID, $userrow['userID'])){
    	$checked=' checked';
    	$value=1;
    }
    else{
    	$checked='';
    	$value=0;
    }
		echo '<tr>
						<td class="'.$td.'">'.$userrow['nickname'].' <span class="small">('.getfirstname($userrow['userID']).' '.getlastname($userrow['userID']).')</span></td>
						<td class="'.$td.'"><input onclick="tooglegroupmembership(this.id)" id="'.$userrow['userID'].'" type="checkbox" value="'.$value.'"'.$checked.'></td>
					</tr>';
	}
	$previousbutton='';
	$nextbutton='';
	if($previousoffsetstatus){
		$previousbutton='<input value="&laquo; '.$_language->module['previous'].'" type="button" onclick="window.location=\'admincenter.php?site=accessgroups&amp;action=groupmembers&amp;id='.$accessgroupID.'&amp;offset='.$previousoffset.$filterurlstring.'\'">';
	}
	else{
		$previousbutton='';
	}
	if($nextoffsetstatus){
		$nextbutton='<input value="'.$_language->module['next'].' &raquo;" type="button" onclick="window.location=\'admincenter.php?site=accessgroups&amp;action=groupmembers&amp;id='.$accessgroupID.'&amp;offset='.$nextoffset.$filterurlstring.'\'">';
	}
	else{
		$nextbutton='';
	}
	echo '<tr>
					<td colspan="2" class="title" align="right">
					  '.$previousbutton.' '.$nextbutton.'
					</td>
				</tr>';
	echo '</table><div id="debugdiv"></div>';
}
else{
	echo'<h1>&curren; '.$_language->module['access_groups'].'</h1>';
	echo '<br />';
	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=accessgroups&amp;action=addgroup\');return document.MM_returnValue" value="'.$_language->module['new'].'" />';
	echo '<br /><br />';
	//list
	$result=safe_query("SELECT * FROM `".PREFIX."accessgroups` ORDER BY `name` ASC");
	$any=mysql_num_rows($result);
	if($any){
		echo '<br />
    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
      <tr>
        <td width="50%" class="title"><b>'.$_language->module['accessgroupname'].'</b></td>
        <td width="50%" class="title"><b>'.$_language->module['actions'].'</b></td>
      </tr>';
		$i=1; 
		while($row=mysql_fetch_array($result)){
			if($i%2) { $td='td1'; }
      else { $td='td2'; }
      echo '<tr>
      				<td class="'.$td.'">'.$row['name'].'</td>
      				<td class="'.$td.'">
      				  <input type="button" value="'.$_language->module['groupmembers'].'" onclick="window.location=\'admincenter.php?site=accessgroups&amp;action=groupmembers&amp;id='.$row['accessgroupID'].'\'" />
      				  <input type="button" value="'.$_language->module['editgroup'].'" onclick="window.location=\'admincenter.php?site=accessgroups&amp;action=editgroup&amp;id='.$row['accessgroupID'].'\'" />
      				  <input type="button" value="'.$_language->module['deletegroup'].'" onclick="if(confirm(\''.$_language->module['really_delete_group'].'\')){window.location = \'admincenter.php?site=accessgroups&amp;action=deletegroup&amp;id='.$row['accessgroupID'].'\'}" />
      				</td>
      			</tr>';
      $i++;  
		}
		echo '</table>';
	}
	else{
		echo $_language->module['no_grp_definitions'];
	}
}
?>