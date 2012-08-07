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

$_language->read_module('navigation', true);
$_language->get_installed_languages();
	
$_language_cat = new Language;
$_language_cat->set_language($_language->language);
$_language_cat->db_read_module('menucat');

$_language_entry = new Language;
$_language_entry->set_language($_language->language);
$_language_entry->db_read_module('menuentry');

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

if($action=='newcat') {
	
	$any_categories=mysql_num_rows(safe_query("SELECT * FROM `".PREFIX."menu_categories` ORDER by `position`"));
	if($any_categories){
		$looplimit=$any_categories + 1;
	}
	else{
		$looplimit=1;
	}
	
	$select='<select name="position">';
	for($i=1; $i<=$looplimit; $i++){
		$select.='<option value="'.$i.'">'.$i.'</option>';
	}
	$select.='</select>';
	
	echo'<h1>&curren; '.$_language->module['newcat'].'</h1>';
	echo '<br />';
	echo '<form method="post" action="admincenter.php?site=navigation&action=savecat">';
	echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
		      <tr>
		        <td class="title" colspan="2"><b>'.$_language->module['language'].'</b></td>
		        <td class="title"><b>'.$_language->module['category_name'].'</b></td>
		      </tr>';
	$i=1;
	foreach($_language->installed_languages as $language => $lang){
		if($i%2) { $td='td1'; }
    else { $td='td2'; }
    $flag=insertflags($lang);
		echo '<tr>';
		echo '<td class="'.$td.'" width="5%" align="center">'.$flag.'</td>
		      <td class="'.$td.'" width="15%">'.$language.'</td>
		      <td class="'.$td.'" width="80%"><input type="text" name="'.$lang.'_catname" size="50" /></td>
		      ';
		echo '</tr>';
		$i++;
	}
	if($td=='td1') $td='td2'; else $td='td1';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['position'].'</b></td><td class="'.$td.'">'.$select.'</td></tr>';
	echo '</table><br /><input type="submit" value="'.$_language->module['save'].'" /></form>';
}
elseif($action=='savecat') {
	$position=$_POST['position'];
	
	safe_query("INSERT INTO `".PREFIX."menu_categories` (`position`) VALUES (".$position.")");
	$menucatID=mysql_insert_id();
	foreach($_language->installed_languages as $language => $lang){
		safe_query("INSERT INTO `".PREFIX."translations` (`type`, `lang`, `foreign_identifier`, `text`) VALUES ('menucat', '".$lang."', '".$menucatID."', '".$_POST[$lang.'_catname']."')");
	}
	echo '<script type="text/javascript">
					window.location = "admincenter.php?site=navigation";
				</script>
			 ';
}
elseif($action=='editcat') {
	
	$menucatID=$_GET['id'];
	
	$catdata=safe_query("SELECT `position` FROM `".PREFIX."menu_categories` WHERE menucatID=".$menucatID);
	$rowcatdata=mysql_fetch_array($catdata);
	$position=$rowcatdata['position'];
	
	$any_categories=mysql_num_rows(safe_query("SELECT * FROM `".PREFIX."menu_categories` ORDER by `position`"));
	$looplimit=$any_categories;
	
	$select='<select name="position">';
	for($i=1; $i<=$looplimit; $i++){
		if($i==$position){
			$select.='<option value="'.$i.'" selected>'.$i.'</option>';
		}
		else{
			$select.='<option value="'.$i.'">'.$i.'</option>';
		}
	}
	$select.='</select>';
	
	echo'<h1>&curren; '.$_language->module['newcat'].'</h1>';
	echo '<br />';
	echo '<form method="post" action="admincenter.php?site=navigation&action=saveeditcat&id='.$menucatID.'">';
	echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
		      <tr>
		        <td class="title" colspan="2"><b>'.$_language->module['language'].'</b></td>
		        <td class="title"><b>'.$_language->module['category_name'].'</b></td>
		      </tr>';
	$i=1;
	foreach($_language->installed_languages as $language => $lang){
		if($i%2) { $td='td1'; }
    else { $td='td2'; }
    $flag=insertflags($lang);
    $transdata=safe_query("SELECT `text` FROM `".PREFIX."translations` WHERE foreign_identifier=".$menucatID." AND `lang`='".$lang."' AND `type`='menucat'");
    $rowtransdata=mysql_fetch_array($transdata);
    $text=$rowtransdata['text'];
		echo '<tr>';
		echo '<td class="'.$td.'" width="5%" align="center">'.$flag.'</td>
		      <td class="'.$td.'" width="15%">'.$language.'</td>
		      <td class="'.$td.'" width="80%"><input type="text" name="'.$lang.'_catname" size="50" value="'.$text.'" /></td>
		      ';
		echo '</tr>';
		$i++;
	}
	if($td=='td1') $td='td2'; else $td='td1';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['position'].'</b></td><td class="'.$td.'">'.$select.'</td></tr>';
	echo '</table><br /><input type="submit" value="'.$_language->module['save'].'" /></form>';
}
elseif($action=='saveeditcat') {
	$position=$_POST['position'];
	$menucatID=$_GET['id'];
	safe_query("UPDATE `".PREFIX."menu_categories` SET `position`=".$position." WHERE `menucatID`=".$menucatID);
		
	foreach($_language->installed_languages as $language => $lang){
		safe_query("DELETE FROM `".PREFIX."translations` WHERE `type`='menucat' AND `lang`='".$lang."' AND `foreign_identifier`='".$menucatID."'");
		safe_query("INSERT INTO `".PREFIX."translations` (`type`, `lang`, `foreign_identifier`, `text`) VALUES ('menucat', '".$lang."', '".$menucatID."', '".$_POST[$lang.'_catname']."')");
	}
	
	echo '<script type="text/javascript">
			window.location = "admincenter.php?site=navigation";
		  </script>';
}
elseif($action=='newentry') {
	$menucatID=$_GET['menucatid'];
	$any_entries=mysql_num_rows(safe_query("SELECT * FROM `".PREFIX."menu_entries` WHERE `menucatID`=".$menucatID." ORDER by `position`"));
	if($any_entries){
		$looplimit=$any_entries + 1;
	}
	else{
		$looplimit=1;
	}
	
	$select='<select name="position">';
	for($i=1; $i<=$looplimit; $i++){
		$select.='<option value="'.$i.'">'.$i.'</option>';
	}
	$select.='</select>';
	
	echo'<h1>&curren; '.$_language->module['newentry'].'</h1>';
	echo '<br />';
	echo '<form method="post" action="admincenter.php?site=navigation&action=saveentry&menucatid='.$menucatID.'">';
	echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
		      <tr>
		        <td class="title" colspan="2"><b>'.$_language->module['language'].'</b></td>
		        <td class="title"><b>'.$_language->module['entry_name'].'</b></td>
		      </tr>';
	$i=1;
	foreach($_language->installed_languages as $language => $lang){
		if($i%2) { $td='td1'; }
    else { $td='td2'; }
    $flag=insertflags($lang);
		echo '<tr>';
		echo '<td class="'.$td.'" width="5%" align="center">'.$flag.'</td>
		      <td class="'.$td.'" width="15%">'.$language.'</td>
		      <td class="'.$td.'" width="80%"><input type="text" name="'.$lang.'_entryname" size="50" /></td>
		      ';
		echo '</tr>';
		$i++;
	}
	if($td=='td1') $td='td2'; else $td='td1';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['link'].'</b></td><td class="'.$td.'"><input type="text" name="link" size="80" /></td></tr>';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['position'].'</b></td><td class="'.$td.'">'.$select.'</td></tr>';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['newwindow'].'</b></td><td class="'.$td.'"><input name="newwindow" type="checkbox" value="1"></td></tr>';
	echo '</table><br /><input type="submit" value="'.$_language->module['save'].'" /></form>';
}
elseif($action=='saveentry') {
	$position=$_POST['position'];
	$link=$_POST['link'];
	$menucatID=$_GET['menucatid'];
	if(isset($_POST['newwindow'])){
	  $newwindow=1;
	}
	else{
	  $newwindow=0;
	}
	safe_query("INSERT INTO `".PREFIX."menu_entries` (`menucatID`, `link`, `position`, `newwindow`) VALUES (".$menucatID.", '".$link."', ".$position.", ".$newwindow.")");
	$menuentryID=mysql_insert_id();
	
	foreach($_language->installed_languages as $language => $lang){
		safe_query("INSERT INTO `".PREFIX."translations` (`type`, `lang`, `foreign_identifier`, `text`) VALUES ('menuentry', '".$lang."', '".$menuentryID."', '".$_POST[$lang.'_entryname']."')");
	}
		
	echo '<script type="text/javascript">
					window.location = "admincenter.php?site=navigation";
				</script>
			 ';
}
elseif($action=='editentry') {
	$menucatID=$_GET['menucatid'];
	$menuentryID=$_GET['id'];
	$any_entries=mysql_num_rows(safe_query("SELECT * FROM `".PREFIX."menu_entries` WHERE `menucatID`=".$menucatID." ORDER by `position`"));
	$looplimit=$any_entries;
	
	$entrydata=safe_query("SELECT position, link, newwindow FROM `".PREFIX."menu_entries` WHERE `menuentryID`=".$menuentryID." ORDER by `position`");
	$row_entry=mysql_fetch_array($entrydata);
	$position=$row_entry['position'];
	$link=$row_entry['link'];
	if($row_entry['newwindow']==1){
	  $newwindowchecked=' checked';
	}
	else{
	  $newwindowchecked='';
	}
	$select='<select name="position">';
	for($i=1; $i<=$looplimit; $i++){
		if($i==$position){
			$select.='<option value="'.$i.'" selected>'.$i.'</option>';
		}
		else{
			$select.='<option value="'.$i.'">'.$i.'</option>';
		}
	}
	$select.='</select>';
	
	echo'<h1>&curren; '.$_language->module['newentry'].'</h1>';
	echo '<br />';
	echo '<form method="post" action="admincenter.php?site=navigation&action=saveeditentry&menucatid='.$menucatID.'&menuentryid='.$menuentryID.'">';
	echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
		      <tr>
		        <td class="title" colspan="2"><b>'.$_language->module['language'].'</b></td>
		        <td class="title"><b>'.$_language->module['entry_name'].'</b></td>
		      </tr>';
	$i=1;
	foreach($_language->installed_languages as $language => $lang){
		if($i%2) { $td='td1'; }
    else { $td='td2'; }
    $flag=insertflags($lang);
    $transdata=safe_query("SELECT `text` FROM `".PREFIX."translations` WHERE foreign_identifier=".$menuentryID." AND `lang`='".$lang."' AND `type`='menuentry'");
    $rowtransdata=mysql_fetch_array($transdata);
    $text=$rowtransdata['text'];
		echo '<tr>';
		echo '<td class="'.$td.'" width="5%" align="center">'.$flag.'</td>
		      <td class="'.$td.'" width="15%">'.$language.'</td>
		      <td class="'.$td.'" width="80%"><input type="text" name="'.$lang.'_entryname" size="50" value="'.$text.'" /></td>
		      ';
		echo '</tr>';
		$i++;
	}
	if($td=='td1') $td='td2'; else $td='td1';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['link'].'</b></td><td class="'.$td.'"><input type="text" name="link" size="80" value="'.$link.'" /></td></tr>';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['position'].'</b></td><td class="'.$td.'">'.$select.'</td></tr>';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['newwindow'].'</b></td><td class="'.$td.'"><input name="newwindow" type="checkbox" value="1"'.$newwindowchecked.'></td></tr>';
	echo '</table><br /><input type="submit" value="'.$_language->module['save'].'" /></form>';
}
elseif($action=='saveeditentry') {
	$position=$_POST['position'];
	$link=$_POST['link'];
	if(isset($_POST['newwindow'])){
	  $newwindow=1;
	}
	else{
	  $newwindow=0;
	}
	$menucatID=$_GET['menucatid'];
	$menuentryID=$_GET['menuentryid'];
	safe_query("UPDATE `".PREFIX."menu_entries` SET `link`='".$link."', `position`=".$position.", `newwindow`=".$newwindow." WHERE `menuentryID`=".$menuentryID);

	$fetch = safe_query("SELECT `lang` FROM `".PREFIX."translations` WHERE `foreign_identifier`='".$menuentryID."' AND `type`='menuentry'");
	$lang_array = array();
	while($row = mysql_fetch_array($fetch)) {
		$lang_array[] = $row['lang'];
	}
					
	foreach($_language->installed_languages as $language => $lang){
		if(!in_array($lang, $lang_array)) safe_query("INSERT INTO `".PREFIX."translations` (`type`, `lang`, `foreign_identifier`, `text`) VALUES ('menuentry', '".$lang."', '".$menuentryID."', '".$_POST[$lang.'_entryname']."')");
		else safe_query("UPDATE `".PREFIX."translations` SET `text`='".$_POST[$lang.'_entryname']."' WHERE `type`='menuentry' AND `lang`='".$lang."' AND `foreign_identifier`='".$menuentryID."'");
	}
	
	echo '<script type="text/javascript">
					window.location = "admincenter.php?site=navigation";
				</script>
			 ';
}
elseif($action=='deletecat') {
	$menucatID=$_GET['id'];
	$result=safe_query("SELECT * FROM `".PREFIX."menu_entries` WHERE `menucatID`=".$menucatID);
	$any=mysql_num_rows($result);
	if($any){
		echo $_language->module['cannot_delete'];
	}
	else{
		safe_query("DELETE FROM `".PREFIX."menu_categories` WHERE `menucatID`=".$menucatID);
	  safe_query("DELETE FROM `".PREFIX."translations` WHERE `type`='menucat' AND `foreign_identifier`=".$menucatID);
	  echo '<script type="text/javascript">
						window.location = "admincenter.php?site=navigation";
					</script>
				 ';
	}
}
elseif($action=='deleteentry') {
	$menuentryID=$_GET['id'];
	safe_query("DELETE FROM `".PREFIX."menu_entries` WHERE `menuentryID`=".$menuentryID);
	safe_query("DELETE FROM `".PREFIX."translations` WHERE `type`='menuentry' AND `foreign_identifier`=".$menuentryID);
	echo '<script type="text/javascript">
					window.location = "admincenter.php?site=navigation";
				</script>
			 ';
}
elseif($action=='entryposition') {
	echo 'läuft';
	safe_query("UPDATE `".PREFIX."menu_entries` SET `position`=".$_GET['position']." WHERE `menuentryID`=".$_POST['id']);
}
elseif($action=='catposition') {
	echo 'läuft';
	safe_query("UPDATE `".PREFIX."menu_categories` SET `position`=".$_GET['position']." WHERE `menucatID`=".$_POST['id']);
}
else{
	echo'<h1>&curren; '.$_language->module['navigation'].'</h1>';
	echo '<br />';
	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=navigation&amp;action=newcat\');return document.MM_returnValue" value="'.$_language->module['newcat'].'" />';
	echo '<br /><br />';
	
	$categories=safe_query("SELECT * FROM `".PREFIX."menu_categories` ORDER by `position`");
	$any_categories=mysql_num_rows($categories);
	
	if($any_categories){
	  while($row_category=mysql_fetch_array($categories)){
	  	$catactions='<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=navigation&amp;action=editcat&amp;id='.$row_category['menucatID'].'\');return document.MM_returnValue" value="'.$_language->module['editcat'].'" /> <input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=navigation&amp;action=deletecat&amp;id='.$row_category['menucatID'].'\');return document.MM_returnValue" value="'.$_language->module['deletecat'].'" />';
	  	$catsort='<select onchange="fetch(\'direct.php?site=navigation&action=catposition&id='.$row_category['menucatID'].'&position=\' + this.options[selectedIndex].value,\'debugdiv\',\'replace\',\'event\');">';
			for($j=1; $j<=$any_categories; $j++){
				if($j==$row_category['position']){
					$catsort.='<option value="'.$j.'" selected>'.$j.'</option>';
				}
				else{
					$catsort.='<option value="'.$j.'">'.$j.'</option>';
				}
			}
			$catsort.='</select>';
	  	echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
				      <tr>
				        <td class="title" colspan="2"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=navigation&amp;action=newentry&amp;menucatid='.$row_category['menucatID'].'\');return document.MM_returnValue" value="'.$_language->module['newentry'].'" /> <b>'.$_language_cat->module[$row_category['menucatID']].'</b></td>
				        <td class="title" align="center">'.$catactions.'</td>
				        <td class="title" align="center">'.$catsort.'</td>
				      </tr>
				      <tr>
					      <td width="30%" class="td_head"><b>'.$_language->module['show'].'</b></td>
					      <td width="40%" class="td_head"><b>'.$_language->module['link'].'</b></td>
					      <td width="20%" class="td_head" align="center"><b>'.$_language->module['actions'].'</b></td>
					      <td width="10%" class="td_head" align="center"><b>'.$_language->module['sort'].'</b></td>
					    </tr>';
	  	$entries=safe_query("SELECT * FROM `".PREFIX."menu_entries` WHERE `menucatID`=".$row_category['menucatID']." ORDER by `position`");
	  	$any_entries=mysql_num_rows($entries);
	  	if($any_entries){
	  		$i=1;
	  		while($row_entry=mysql_fetch_array($entries)){
	  			if($i%2) { $td='td1'; }
          else { $td='td2'; }
	  			$show=$_language_entry->module[$row_entry['menuentryID']];
	  			$link=$row_entry['link'];
	  			$actions='<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=navigation&amp;action=editentry&amp;id='.$row_entry['menuentryID'].'&amp;menucatid='.$row_category['menucatID'].'\');return document.MM_returnValue" value="'.$_language->module['editlink'].'" /> <input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=navigation&amp;action=deleteentry&amp;id='.$row_entry['menuentryID'].'\');return document.MM_returnValue" value="'.$_language->module['deletelink'].'" />';
	  			$sort='<select onchange="fetch(\'direct.php?site=navigation&action=entryposition&id='.$row_entry['menuentryID'].'&position=\' + this.options[selectedIndex].value,\'debugdiv\',\'replace\',\'event\');">';
	  			for($j=1; $j<=$any_entries; $j++){
	  				if($j==$row_entry['position']){
	  					$sort.='<option value="'.$j.'" selected>'.$j.'</option>';
	  				}
	  				else{
	  					$sort.='<option value="'.$j.'">'.$j.'</option>';
	  				}
	  			}
	  			$sort.='</select>';
	  			echo '<tr>';
	  			echo '<td width="30%" class="'.$td.'">'.$show.'</td>
					      <td width="40%" class="'.$td.'">'.$link.'</td>
					      <td width="20%" class="'.$td.'" align="center">'.$actions.'</td>
					      <td width="10%" class="'.$td.'" align="center">'.$sort.'</td>';  
	  			echo '</tr>';
	  			$i++;
	  		}
	  	}
	  	echo '</table><br /><br />';
	  }
	  echo '<div id="debugdiv"></div>';
	}
	else{
		echo $_language->module['no_menu_defined'];
	}
}
?>