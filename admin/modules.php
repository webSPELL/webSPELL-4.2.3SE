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

$_language->read_module('modules', true);
$_language->get_installed_languages();

$_language_mod = new Language;
$_language_mod->set_language($_language->language);
$_language_mod->db_read_module('module');

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



if($action=='edit') {
	echo'<h1>&curren; '.$_language->module['modedit'].'</h1>';
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	$result=safe_query("SELECT * FROM ".PREFIX."modules WHERE moduleID=".$_GET['id']);
	$any=mysql_num_rows($result);
	if($any){
		$row=mysql_fetch_array($result);
		if($row['activated']==1){
			$checked=' checked';
		}
		else{
			$checked='';
		}
		echo '<form method="post" action="admincenter.php?site=modules&action=saveedit&id='.$row['moduleID'].'">';
		echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
			      <tr>
			        <td class="title" colspan="2"><b>'.$_language->module['language'].'</b></td>
			        <td class="title"><b>'.$_language->module['modulename'].'</b></td>
			      </tr>';
		$i=1;
		foreach($_language->installed_languages as $language => $lang){
			if($i%2) { $td='td1'; }
	    else { $td='td2'; }
	    $flag=insertflags($lang);
	    $transdata=safe_query("SELECT `text` FROM `".PREFIX."translations` WHERE foreign_identifier=".$_GET['id']." AND `lang`='".$lang."' AND `type`='module'");
	    $rowtransdata=mysql_fetch_array($transdata);
	    $text=$rowtransdata['text'];
			echo '<tr>';
			echo '<td class="'.$td.'" width="5%" align="center">'.$flag.'</td>
			      <td class="'.$td.'" width="15%">'.$language.'</td>
			      <td class="'.$td.'" width="80%"><input type="text" name="'.$lang.'_modulename" size="50" value="'.$text.'" /></td>
			      ';
			echo '</tr>';
			$i++;
		}
		if($td=='td1') $td='td2'; else $td='td1';
		echo '<tr><td class="title" colspan="2"><b>'.$_language->module['filename'].'</b></td><td class="'.$td.'"><input type="text" name="filename" size="60" value="'.$row['filename'].'" /></td></tr>';
		echo '<tr><td class="title" colspan="2"><b>'.$_language->module['activated'].'</b></td><td class="'.$td.'"><input type="checkbox" name="activated" value="1"'.$checked.' /></td></tr>';
		echo '</table><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" value="'.$_language->module['save'].'" /></form>';
	}
	else{
		echo $_language->module['module_not_exists'];
	}
}
elseif($action=='saveedit'){
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		
		$fieldsfilled=0;
		foreach($_language->installed_languages as $language => $lang){
			if((trim($_POST[$lang.'_modulename']))==''){
				$fieldsfilled=1;
				break;
			}
		}
		
		$filename=trim($_POST['filename']);
		if(!($fieldsfilled || ($filename==''))){
			if(isset($_POST['activated'])){
				$activated=1;
			}
			else{
				$activated=0;
			}
	    
	    if(file_exists('../'.$filename)){
    		safe_query("UPDATE ".PREFIX."modules SET `filename`='".$filename."', `activated`='".$activated."' WHERE `moduleID`=".$_GET['id']);

			foreach($_language->installed_languages as $language => $lang){
				safe_query("DELETE FROM `".PREFIX."translations` WHERE `type`='module' AND `lang`='".$lang."' AND `foreign_identifier`='".$_GET['id']."'");
				safe_query("INSERT INTO `".PREFIX."translations` (`type`, `lang`, `foreign_identifier`, `text`) VALUES ('module', '".$lang."', '".$_GET['id']."', '".$_POST[$lang.'_modulename']."')");
			}
				
    		echo '<script type="text/javascript">
				    window.location = "admincenter.php?site=modules";
				  </script>';
	    }
	    else{
	    	echo $_language->module['file_not_available'];
	    }
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
	
	echo '<form method="post" action="admincenter.php?site=modules&action=savenew">';
	echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
		      <tr>
		        <td class="title" colspan="2"><b>'.$_language->module['language'].'</b></td>
		        <td class="title"><b>'.$_language->module['modulename'].'</b></td>
		      </tr>';
	$i=1;
	foreach($_language->installed_languages as $language => $lang){
		if($i%2) { $td='td1'; }
    else { $td='td2'; }
    $flag=insertflags($lang);
		echo '<tr>';
		echo '<td class="'.$td.'" width="5%" align="center">'.$flag.'</td>
		      <td class="'.$td.'" width="15%">'.$language.'</td>
		      <td class="'.$td.'" width="80%"><input type="text" name="'.$lang.'_modulename" size="50" /></td>
		      ';
		echo '</tr>';
		$i++;
	}
	if($td=='td1') $td='td2'; else $td='td1';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['filename'].'</b></td><td class="'.$td.'"><input type="text" name="filename" size="60" /></td></tr>';
	echo '<tr><td class="title" colspan="2"><b>'.$_language->module['activated'].'</b></td><td class="'.$td.'"><input type="checkbox" name="activated" value="1" /></td></tr>';
	echo '</table><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" value="'.$_language->module['save'].'" /></form>';
}
elseif($action=='savenew'){
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		
		$fieldsfilled=0;
		foreach($_language->installed_languages as $language => $lang){
			if((trim($_POST[$lang.'_modulename']))==''){
				$fieldsfilled=1;
				break;
			}
		}
		
		$filename=trim($_POST['filename']);
		if(!($fieldsfilled || ($filename==''))){
			if(isset($_POST['activated'])){
				$activated=1;
			}
			else{
				$activated=0;
			}
	    
	    if(file_exists('../'.$filename)){
	    	$test=safe_query("SELECT * FROM ".PREFIX."modules WHERE filename='".$filename."'");
	    	$anytest=mysql_num_rows($test);
	    	if($anytest){
	    		echo $_language->module['allready_defined'];
	    	}
	    	else{
	    		safe_query("INSERT INTO ".PREFIX."modules (filename, activated) VALUES ('".$filename."',".$activated.")");
	    		$moduleID=mysql_insert_id();
	    		foreach($_language->installed_languages as $language => $lang){
						safe_query("INSERT INTO `".PREFIX."translations` (`type`, `lang`, `foreign_identifier`, `text`) VALUES ('module', '".$lang."', '".$moduleID."', '".$_POST[$lang.'_modulename']."')");
					}
	    		echo '<script type="text/javascript">
									window.location = "admincenter.php?site=modules";
								</script>
							 ';
	    	}
	    }
	    else{
	    	echo $_language->module['file_not_available'];
	    }
		}
		else{
			echo $_language->module['missing_entry'];
		}
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='activatetoggle'){
	$value=$_GET['value'];
	$id=$_GET['id'];
	safe_query("UPDATE ".PREFIX."modules SET activated=".$value." WHERE moduleID=".$id);
}
elseif($action=='delete'){
	safe_query("DELETE FROM ".PREFIX."modules WHERE moduleID=".$_GET['id']);
	echo '<script type="text/javascript">
					window.location = "admincenter.php?site=modules";
				</script>
			 ';
}
elseif($action=='setaccess'){
	$moduleID=(int)$_GET['id'];
	$access=implode('|', explode(',', $_GET['value']));
	
	safe_query("UPDATE `".PREFIX."modules` SET `access`='".$access."' WHERE `moduleID`=".$moduleID);
}
else {
	echo'<h1>&curren; '.$_language->module['modules'].'</h1>';
	echo '<br />';
	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=modules&amp;action=new\');return document.MM_returnValue" value="'.$_language->module['new'].'" />';
	echo '<br /><br />';
	//list
	$result=safe_query("SELECT * FROM `".PREFIX."modules`");
	$any=mysql_num_rows($result);
	if($any){
		echo '<script language="javascript" type="text/javascript">
            function activatetoggle(id){
              checkbox=document.getElementById(\'check_\' + id);
              if(checkbox.value==1){
                checkbox.value=0;
                //checkbox.checked=false;
              }
              else{
                checkbox.value=1;
                //checkbox.checked=true;
              }
              fetch(\'direct.php?site=modules&action=activatetoggle&id=\' + id + \'&value=\' + checkbox.value,\'debugdiv\',\'replace\',\'event\');
            }
            
            function submitaccess(dropdownid){
              options=getselection(dropdownid);
              fetch(\'direct.php?site=modules&action=setaccess&id=\' + dropdownid + \'&value=\' + options,\'debugdiv\',\'replace\',\'event\');
            }
		      </script>';
		echo '<br /><div id="debugdiv"></div>
    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
      <tr>
        <td width="25%" class="title"><b>'.$_language->module['modulename'].'</b></td>
        <td width="20%" class="title"><b>'.$_language->module['filename'].'</b></td>
        <td width="25%" class="title"><b>'.$_language->module['access_for'].'</b></td>
        <td width="10%" class="title"><b>'.$_language->module['activated'].'</b></td>
        <td width="20%" class="title"></td>
      </tr>';
		$i=1; 
		while($row=mysql_fetch_array($result)){
			
			if($i%2) { $td='td1'; }
      else { $td='td2'; }
      
      $actions = '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=modules&amp;action=edit&amp;id='.$row['moduleID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> <input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=modules&amp;action=delete&amp;id='.$row['moduleID'].'\');return document.MM_returnValue" value="'.$_language->module['delete'].'" />';
      
      if($row['activated']==1){
      	$value=1;
      	$selected=' checked';
      }
      else{
      	$value=0;
      	$selected='';
      }
      
      $accesses=generateaccessdropdown($row['moduleID'], $row['moduleID'], $row['access'], "submitaccess(this.id)");
      echo '<tr>
			        <td class="'.$td.'" valign="top" align="left">'.$_language_mod->module[$row['moduleID']].'</td>
			        <td class="'.$td.'" valign="top" align="left">'.$row['filename'].'</td>
			        <td class="'.$td.'" valign="top" align="center">'.$accesses.'</td>
			        <td class="'.$td.'" valign="top" align="center"><input type="checkbox" onclick="activatetoggle('.$row['moduleID'].');" id="check_'.$row['moduleID'].'" value="'.$value.'"'.$selected.'></td>
			        <td class="'.$td.'" valign="top" align="center">'.$actions.'</td>
		        </tr>
		      ';
           
      $i++;
		}
		echo '</table><div id="debugdiv"></div>';
	}
	else{
		echo $_language->module['no_mod_definitions'];
	}
}
?>