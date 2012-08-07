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

$_language->read_module('registerfields', true);
$_language->get_installed_languages();

$_language_reg = new Language;
$_language_reg->set_language($_language->language);
$_language_reg->db_read_module('registerfield');

function insertflags($lang){
	return '<img src="../images/flags/'.$lang.'.gif" />';
}

if(!ispageadmin($userID) OR (mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php" AND mb_substr(basename($_SERVER['REQUEST_URI']),0,10) != "direct.php")) die($_language->module['access_denied']);

if(isset($_GET['action'])){
	$action=$_GET['action'];
}
else{
	$action='';
}

if($action=='edit'){
	echo'<h1>&curren; '.$_language->module['edit'].'</h1>';
	echo '<br />';
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	$result=safe_query("SELECT * FROM ".PREFIX."upcoming_registerfields WHERE registerfieldID=".$_GET['id']);
	$row=mysql_fetch_array($result);
	$type=$row['type'];
	
	echo '<form method="post" action="admincenter.php?site=registerfields&action=saveedit&id='.$row['registerfieldID'].'">';
	echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
		      <tr>
		        <td class="title" colspan="2"><b>'.$_language->module['language'].'</b></td>
		        <td class="title"><b>'.$_language->module['description'].'</b></td>
		      </tr>';
	$i=1;
	foreach($_language->installed_languages as $language => $lang){
		if($i%2) { $td='td1'; }
    else { $td='td2'; }
    $flag=insertflags($lang);
    $transdata=safe_query("SELECT `text` FROM `".PREFIX."translations` WHERE foreign_identifier=".$_GET['id']." AND `lang`='".$lang."' AND `type`='registerfield'");
	  $rowtransdata=mysql_fetch_array($transdata);
	  $text=$rowtransdata['text'];
		echo '<tr>';
		echo '<td class="'.$td.'" width="5%" align="center">'.$flag.'</td>
		      <td class="'.$td.'" width="15%">'.$language.'</td>
		      <td class="'.$td.'" width="80%"><input type="text" name="'.$lang.'_text" size="50" value="'.$text.'" /></td>
		      ';
		echo '</tr>';
		$i++;
	}
	if($td=='td1') $td='td2'; else $td='td1';
	if($type=='checkbox'){
		echo '<tr><td class="title" colspan="2"><b>'.$_language->module['type'].'</b></td><td class="'.$td.'"><select name="type"><option value="checkbox" selected>'.$_language->module['checkbox'].'</option><option value="text">'.$_language->module['text'].'</option><option value="textarea">'.$_language->module['textarea'].'</option></select></td></tr>';
	}
	elseif($type=='text'){
		echo '<tr><td class="title" colspan="2"><b>'.$_language->module['type'].'</b></td><td class="'.$td.'"><select name="type"><option value="checkbox">'.$_language->module['checkbox'].'</option><option value="text" selected>'.$_language->module['text'].'</option><option value="textarea">'.$_language->module['textarea'].'</option></select></td></tr>';		
	}
	elseif($type=='textarea'){
		echo '<tr><td class="title" colspan="2"><b>'.$_language->module['type'].'</b></td><td class="'.$td.'"><select name="type"><option value="checkbox">'.$_language->module['checkbox'].'</option><option value="text">'.$_language->module['text'].'</option><option value="textarea" selected>'.$_language->module['textarea'].'</option></select></td></tr>';		
	}
	else{
		
	}
	echo '</table><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" value="'.$_language->module['save'].'" /></form>';
}
elseif($action=='saveedit'){
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		
		$fieldsfilled=0;
		foreach($_language->installed_languages as $language => $lang){
			if((trim($_POST[$lang.'_text']))==''){
				$fieldsfilled=1;
				break;
			}
		}
		
		if(!($fieldsfilled)){
			$type=$_POST['type'];
	 		safe_query("UPDATE ".PREFIX."upcoming_registerfields SET `type`='".$type."' WHERE registerfieldID=".$_GET['id']);
  			$registerfieldID=$_GET['id'];
			
			foreach($_language->installed_languages as $language => $lang){
				safe_query("DELETE FROM `".PREFIX."translations` WHERE `type`='registerfield' AND `lang`='".$lang."' AND `foreign_identifier`='".$registerfieldID."'");
				safe_query("INSERT INTO `".PREFIX."translations` (`type`, `lang`, `foreign_identifier`, `text`) VALUES ('registerfield', '".$lang."', '".$registerfieldID."', '".$_POST[$lang.'_text']."')");
			}
  		echo '<script type="text/javascript">
				window.location = "admincenter.php?site=registerfields";
			  </script>';
	  }
		else{
			echo $_language->module['missing_entry'];
		}
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='new'){
	echo'<h1>&curren; '.$_language->module['new'].'</h1>';
	echo '<br />';
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	echo '<form method="post" action="admincenter.php?site=registerfields&action=savenew">';
	echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
		      <tr>
		        <td class="title" colspan="2"><b>'.$_language->module['language'].'</b></td>
		        <td class="title"><b>'.$_language->module['description'].'</b></td>
		      </tr>';
	$i=1;
	foreach($_language->installed_languages as $language => $lang){
		if($i%2) { $td='td1'; }
    else { $td='td2'; }
    $flag=insertflags($lang);
		echo '<tr>';
		echo '<td class="'.$td.'" width="5%" align="center">'.$flag.'</td>
		      <td class="'.$td.'" width="15%">'.$language.'</td>
		      <td class="'.$td.'" width="80%"><input type="text" name="'.$lang.'_text" size="50" /></td>
		      ';
		echo '</tr>';
		$i++;
	}
	if($td=='td1') $td='td2'; else $td='td1';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['type'].'</b></td><td class="'.$td.'"><select name="type"><option value="checkbox" selected>'.$_language->module['checkbox'].'</option><option value="text">'.$_language->module['text'].'</option><option value="textarea">'.$_language->module['textarea'].'</option></select></td></tr>';
	echo '</table><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" value="'.$_language->module['save'].'" /></form>';
}
elseif($action=='savenew'){
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		
		$fieldsfilled=0;
		foreach($_language->installed_languages as $language => $lang){
			if((trim($_POST[$lang.'_text']))==''){
				$fieldsfilled=1;
				break;
			}
		}
		
		if(!($fieldsfilled)){
			$type=$_POST['type'];
	 		safe_query("INSERT INTO ".PREFIX."upcoming_registerfields (`type`) VALUES ('".$type."')");
  		$registerfieldID=mysql_insert_id();
  		foreach($_language->installed_languages as $language => $lang){
				safe_query("INSERT INTO `".PREFIX."translations` (`type`, `lang`, `foreign_identifier`, `text`) VALUES ('registerfield', '".$lang."', '".$registerfieldID."', '".$_POST[$lang.'_text']."')");
			}
  		echo '<script type="text/javascript">
							window.location = "admincenter.php?site=registerfields";
						</script>
					 ';
	  }
		else{
			echo $_language->module['missing_entry'];
		}
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='delete'){
	$id=(int)$_GET['id'];
	safe_query("DELETE FROM `".PREFIX."upcoming_registerfields` WHERE `registerfieldID`=".$id);
	safe_query("DELETE FROM `".PREFIX."translations` WHERE `type`='registerfield' AND `foreign_identifier`=".$id);
	safe_query("DELETE FROM `".PREFIX."upcoming_data` WHERE `registerfieldID`=".$id);
	$updateupcoming=safe_query("SELECT `upID`, `registerfieldIDs` FROM `".PREFIX."upcoming`");
	while($row=mysql_fetch_array($updateupcoming)){
		$inarray=explode('|', $row['registerfieldIDs']);
		$outarray=array();
		foreach($inarray AS $value){
			if($value!=$id){
				$outarray[]=$value;
			}
		}
		$outstring=implode('|', $outarray);
		safe_query("UPDATE `".PREFIX."upcoming` SET `registerfieldIDs`='".$outstring."' WHERE `upID`=".$row['upID']);
	}
	echo '<script type="text/javascript">
					window.location = "admincenter.php?site=registerfields";
				</script>
			 ';
}
else{
	echo'<h1>&curren; '.$_language->module['register_fields'].'</h1>';
	echo '<br />';
	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=registerfields&amp;action=new\');return document.MM_returnValue" value="'.$_language->module['new'].'" />';
	echo '<br /><br />';
	//list
	$result=safe_query("SELECT * FROM ".PREFIX."upcoming_registerfields");
	$any=mysql_num_rows($result);
	if($any){
		echo'<br />
    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
      <tr>
        <td width="60%" class="title"><b>'.$_language->module['description'].'</b></td>
        <td width="20%" class="title"><b>'.$_language->module['type'].'</b></td>
        <td width="20%" class="title"></td>
      </tr>';
		$i=1; 
		while($row=mysql_fetch_array($result)){
			
			if($i%2) { $td='td1'; }
      else { $td='td2'; }
      
      $actions = '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=registerfields&amp;action=edit&amp;id='.$row['registerfieldID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> <input type="button" onclick="if(confirm(\''.$_language->module['really_delete_registerfield'].'\')){window.location = \'admincenter.php?site=registerfields&amp;action=delete&amp;id='.$row['registerfieldID'].'\'}" value="'.$_language->module['delete'].'" />';
      
      echo '<tr>
	        <td class="'.$td.'" align="left">'.$_language_reg->module[$row['registerfieldID']].'</td>
	        <td class="'.$td.'" align="left">'.$_language->module[$row['type']].'</td>
	        <td class="'.$td.'" align="center">'.$actions.'</td>
        </tr>
      ';
           
      $i++;
		}
		echo '</table><div id="debugdiv"></div>';
	}
	else{
		echo $_language->module['no_reg_definitions'];
	}
}
?>