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

$_language->read_module('squads');

if(!isuseradmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['delete'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		$squadID = $_GET['squadID'];
		$ergebnis=safe_query("SELECT userID FROM ".PREFIX."squads_members WHERE squadID='$squadID'");
		while($ds=mysql_fetch_array($ergebnis)) {
			$squads=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."squads_members WHERE userID='$ds[userID]'"));
			if($squads<2 AND !issuperadmin($ds['userID'])) safe_query("DELETE FROM ".PREFIX."user_groups WHERE userID='$ds[userID]'");
		}
		safe_query("DELETE FROM ".PREFIX."squads_members WHERE squadID='$squadID' ");
		safe_query("DELETE FROM ".PREFIX."squads WHERE squadID='$squadID' ");
	
		$ergebnis=safe_query("SELECT upID FROM ".PREFIX."upcoming WHERE squad='$squadID'");
		while($ds=mysql_fetch_array($ergebnis)) {
			safe_query("DELETE FROM ".PREFIX."upcoming_announce WHERE upID='$ds[upID]'");
		}
		safe_query("DELETE FROM ".PREFIX."upcoming WHERE squad='$squadID' ");
	
		$filepath = "../images/squadicons/";
		if(file_exists($filepath.$squadID.'.gif')) unlink($filepath.$squadID.'.gif');
		if(file_exists($filepath.$squadID.'.jpg')) unlink($filepath.$squadID.'.jpg');
		if(file_exists($filepath.$squadID.'.png')) unlink($filepath.$squadID.'.png');
		if(file_exists($filepath.$squadID.'_small.gif')) unlink($filepath.$squadID.'_small.gif');
		if(file_exists($filepath.$squadID.'_small.jpg')) unlink($filepath.$squadID.'_small.jpg');
		if(file_exists($filepath.$squadID.'_small.png')) unlink($filepath.$squadID.'_small.png');
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_POST['sortieren'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$sort = $_POST['sort'];
		if(is_array($sort)) {
			foreach($sort as $sortstring) {
				$sorter=explode("-", $sortstring);
				safe_query("UPDATE ".PREFIX."squads SET sort='$sorter[1]' WHERE squadID='$sorter[0]' ");
			}
		}
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_POST['save'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		
		safe_query("INSERT INTO ".PREFIX."squads (name, info, sort ) VALUES ('".$_POST['name']."', '".$_POST['message']."', '1' )");
		
		$icon = $_FILES['icon'];
		$icon_small = $_FILES['icon_small'];
		$id=mysql_insert_id();
		$filepath = "../images/squadicons/";
		
		if($icon['name'] != "") {
			move_uploaded_file($icon['tmp_name'], $filepath.$icon['name'].".tmp");
			@chmod($filepath.$icon['name'].".tmp", 0755);
			$getimg = getimagesize($filepath.$icon['name'].".tmp");
			
			$pic = '';
			if($getimg[2] == IMAGETYPE_GIF) $pic=$id.'.gif';
			elseif($getimg[2] == IMAGETYPE_JPEG) $pic=$id.'.jpg';
			elseif($getimg[2] == IMAGETYPE_PNG) $pic=$id.'.png';
			if($pic != "") {
				if(file_exists($filepath.$id.'.gif')) unlink($filepath.$id.'.gif');
				if(file_exists($filepath.$id.'.jpg')) unlink($filepath.$id.'.jpg');
				if(file_exists($filepath.$id.'.png')) unlink($filepath.$id.'.png');
				rename($filepath.$icon['name'].".tmp", $filepath.$pic);
				safe_query("UPDATE ".PREFIX."squads SET icon='".$pic."' WHERE squadID='".$id."'");
			}  else {
				@unlink($filepath.$icon['name'].".tmp");
				$error = $_language->module['format_incorrect'];
				die('<b>'.$error.'</b><br /><br /><a href="admincenter.php?site=squads&amp;action=edit&amp;squadID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
			}
		}
		
		if($icon_small['name'] != "") {
			move_uploaded_file($icon_small['tmp_name'], $filepath.$icon_small['name'].".tmp");
			@chmod($filepath.$icon_small['name'].".tmp", 0755);
			$getimg = getimagesize($filepath.$icon_small['name'].".tmp");
			
			$pic = '';
			if($getimg[2] == IMAGETYPE_GIF) $pic=$id.'_small.gif';
			elseif($getimg[2] == IMAGETYPE_JPEG) $pic=$id.'_small.jpg';
			elseif($getimg[2] == IMAGETYPE_PNG) $pic=$id.'_small.png';
			if($pic != "") {
				if(file_exists($filepath.$id.'_small.gif')) unlink($filepath.$id.'_small.gif');
				if(file_exists($filepath.$id.'_small.jpg')) unlink($filepath.$id.'_small.jpg');
				if(file_exists($filepath.$id.'_small.png')) unlink($filepath.$id.'_small.png');
				rename($filepath.$icon_small['name'].".tmp", $filepath.$pic);
				safe_query("UPDATE ".PREFIX."squads SET icon_small='".$pic."' WHERE squadID='".$id."'");
			}  else {
				@unlink($filepath.$icon_small['name'].".tmp");
				$error = $_language->module['format_incorrect'];
				die('<b>'.$error.'</b><br /><br /><a href="admincenter.php?site=squads&amp;action=edit&amp;squadID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
			}
		}
		
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_POST['saveedit'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
	  safe_query("UPDATE ".PREFIX."squads SET name='".$_POST['name']."', info='".$_POST['message']."' WHERE squadID='".$_POST['squadID']."' ");
		$filepath = "../images/squadicons/";
		$icon = $_FILES['icon'];
		$icon_small = $_FILES['icon_small'];
		$id=$_POST['squadID'];
		
		if($icon['name'] != "") {
			move_uploaded_file($icon['tmp_name'], $filepath.$icon['name'].".tmp");
			@chmod($filepath.$icon['name'].".tmp", 0755);
			$getimg = getimagesize($filepath.$icon['name'].".tmp");
			
			$pic = '';
			if($getimg[2] == IMAGETYPE_GIF) $pic=$id.'.gif';
			elseif($getimg[2] == IMAGETYPE_JPEG) $pic=$id.'.jpg';
			elseif($getimg[2] == IMAGETYPE_PNG) $pic=$id.'.png';
			if($pic != "") {
				if(file_exists($filepath.$id.'.gif')) unlink($filepath.$id.'.gif');
				if(file_exists($filepath.$id.'.jpg')) unlink($filepath.$id.'.jpg');
				if(file_exists($filepath.$id.'.png')) unlink($filepath.$id.'.png');
				rename($filepath.$icon['name'].".tmp", $filepath.$pic);
				safe_query("UPDATE ".PREFIX."squads SET icon='".$pic."' WHERE squadID='".$id."'");
			}  else {
				@unlink($filepath.$icon['name'].".tmp");
				$error = $_language->module['format_incorrect'];
				die('<b>'.$error.'</b><br /><br /><a href="admincenter.php?site=squads&amp;action=edit&amp;squadID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
			}
		}
		
		if($icon_small['name'] != "") {
			move_uploaded_file($icon_small['tmp_name'], $filepath.$icon_small['name'].".tmp");
			@chmod($filepath.$icon_small['name'].".tmp", 0755);
			$getimg = getimagesize($filepath.$icon_small['name'].".tmp");
			
			$pic = '';
			if($getimg[2] == IMAGETYPE_GIF) $pic=$id.'_small.gif';
			elseif($getimg[2] == IMAGETYPE_JPEG) $pic=$id.'_small.jpg';
			elseif($getimg[2] == IMAGETYPE_PNG) $pic=$id.'_small.png';
			if($pic != "") {
				if(file_exists($filepath.$id.'_small.gif')) unlink($filepath.$id.'_small.gif');
				if(file_exists($filepath.$id.'_small.jpg')) unlink($filepath.$id.'_small.jpg');
				if(file_exists($filepath.$id.'_small.png')) unlink($filepath.$id.'_small.png');
				rename($filepath.$icon_small['name'].".tmp", $filepath.$pic);
				safe_query("UPDATE ".PREFIX."squads SET icon_small='".$pic."' WHERE squadID='".$id."'");
			}  else {
				@unlink($filepath.$icon_small['name'].".tmp");
				$error = $_language->module['format_incorrect'];
				die('<b>'.$error.'</b><br /><br /><a href="admincenter.php?site=squads&amp;action=edit&amp;squadID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
			}
		}
		
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="add") {

  echo'<h1>&curren; <a href="admincenter.php?site=squads" class="white">'.$_language->module['squads'].'</a> &raquo; '.$_language->module['add_squad'].'</h1>';

	$filepath="../images/squadicons/";
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	$_language->read_module('bbcode', true);
	
	eval ("\$addbbcode = \"".gettemplate("addbbcode", "html", "admin")."\";");
	eval ("\$addflags = \"".gettemplate("flags_admin", "html", "admin")."\";");
	
	echo '<script language="JavaScript" type="text/javascript">
		<!--
			function chkFormular() {
				if(!validbbcode(document.getElementById(\'message\').value, \'admin\')) {
					return false;
				}
			}
		-->
	</script>';
  
	echo '<form method="post" id="post" name="post" action="admincenter.php?site=squads" enctype="multipart/form-data" onsubmit="return chkFormular();">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td width="15%"><b>'.$_language->module['icon_upload'].'</b></td>
        <td width="85%"><input name="icon" type="file" size="40" /></td>
      </tr>
		<tr>
        <td><b>'.$_language->module['icon_upload_small'].'</b></td>
        <td><input name="icon_small" type="file" size="40" /> <small>('.$_language->module['icon_upload_info'].')</small></td>
      </tr>
      <tr>
        <td><b>'.$_language->module['squad_name'].'</b></td>
        <td><input type="text" name="name" size="60" /></td>
      </tr>
    </table>
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td colspan="2"><b>'.$_language->module['squad_info'].'</b><br />
		  <table width="99%" border="0" cellspacing="0" cellpadding="0">
		    <tr>
			   <td valign="top">'.$addbbcode.'</td>
				<td valign="top">'.$addflags.'</td>
		    </tr>
		  </table>
		  <br /><textarea id="message" rows="5" cols="" name="message" style="width: 100%;">'.$ds['info'].'</textarea>
		  </td>
      </tr>
      <tr>
        <td colspan="2"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="save" value="'.$_language->module['add_squad'].'" /></td>
      </tr>
    </table>
    </form>';
}

elseif($action=="edit") {

  echo'<h1>&curren; <a href="admincenter.php?site=squads" class="white">'.$_language->module['squads'].'</a> &raquo; '.$_language->module['edit_squad'].'</h1>';

	$squadID = (int)$_GET['squadID'];
	$filepath="../images/squadicons/";
	
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."squads WHERE squadID='$squadID'");
	$ds=mysql_fetch_array($ergebnis);
		
	if(!empty($ds['icon'])) $pic='<img src="'.$filepath.$ds['icon'].'" border="0" alt="" />';
	else $pic=$_language->module['no_icon'];
	if(!empty($ds['icon_small'])) $pic_small='<img src="'.$filepath.$ds['icon_small'].'" border="0" alt="" />';
	else $pic_small=$_language->module['no_icon'];
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	$_language->read_module('bbcode', true);
	
	eval ("\$addbbcode = \"".gettemplate("addbbcode", "html", "admin")."\";");
	eval ("\$addflags = \"".gettemplate("flags_admin", "html", "admin")."\";");
	
	echo '<script language="JavaScript" type="text/javascript">
		<!--
			function chkFormular() {
				if(!validbbcode(document.getElementById(\'message\').value, \'admin\')) {
					return false;
				}
			}
		-->
	</script>';
  
	echo '<form method="post" id="post" name="post" action="admincenter.php?site=squads" enctype="multipart/form-data" onsubmit="return chkFormular();">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['current_icon'].'</b></td>
      <td width="85%">'.$pic.'</td>
    </tr>
	 <tr>
      <td width="15%"><b>'.$_language->module['current_icon_small'].'</b></td>
      <td width="85%">'.$pic_small.'</td>
    </tr>
    <tr>
      <td><b>'.$_language->module['icon_upload'].'</b></td>
      <td><input name="icon" type="file" size="40" /></td>
    </tr>
	 <tr>
      <td><b>'.$_language->module['icon_upload_small'].'</b></td>
      <td><input name="icon_small" type="file" size="40" /> <small>('.$_language->module['icon_upload_info'].')</small></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['squad_name'].'</b></td>
      <td><input type="text" name="name" value="'.htmlspecialchars($ds['name']).'" size="60" /></td>
    </tr>
  </table>
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td colspan="2"><b>'.$_language->module['squad_info'].'</b>
      <table width="99%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top">'.$addbbcode.'</td>
          <td valign="top">'.$addflags.'</td>
        </tr>
      </table>
      <br /><textarea rows="5" cols="" name="message" style="width: 100%;">'.htmlspecialchars($ds['info']).'</textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="squadID" value="'.getforminput($squadID).'" /><input type="submit" name="saveedit" value="'.$_language->module['edit_squad'].'" /></td>
    </tr>
  </table>
  </form>';
}

else {

  echo'<h1>&curren; '.$_language->module['squads'].'</h1>';

	echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=squads&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_squad'].'" /><br /><br />';

	echo'<form method="post" action="admincenter.php?site=squads">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="27%" class="title"><b>'.$_language->module['squad_name'].'</b></td>
      <td width="45%" class="title"><b>'.$_language->module['squad_info'].'</b></td>
      <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
      <td width="8%" class="title"><b>'.$_language->module['sort'].'</b></td>
    </tr>';

	$squads=safe_query("SELECT * FROM ".PREFIX."squads ORDER BY sort");
	$anzsquads=mysql_num_rows($squads);
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
	if($anzsquads) {
    $i=1;
		while($db=mysql_fetch_array($squads)) {
			if($i%2) { $td='td1'; }
      else { $td='td2'; }
      
      echo'<tr>
        <td class="'.$td.'" valign="top"><a href="../index.php?site=squads&amp;squadID='.$db['squadID'].'" target="_blank">'.$db['name'].'</a></td>
        <td class="'.$td.'" valign="top">'.cleartext($db['info'],1,'admin').'</td>
        <td class="'.$td.'" align="center" valign="top"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=squads&amp;action=edit&amp;squadID='.$db['squadID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
        <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=squads&amp;delete=true&amp;squadID='.$db['squadID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
        <td class="'.$td.'" align="center" valign="top"><select name="sort[]">';
        
			for($j=1; $j<=$anzsquads; $j++) {
				if($db['sort'] == $j) echo'<option value="'.$db['squadID'].'-'.$j.'" selected="selected">'.$j.'</option>';
				
        else echo'<option value="'.$db['squadID'].'-'.$j.'">'.$j.'</option>';
			}
			echo'</select>
        </td>
      </tr>';
      
      $i++;
		}
	}
	
  echo'<tr>
      <td class="td_head" colspan="5" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></td>
    </tr>
  </table>
  </form>';
}
?>