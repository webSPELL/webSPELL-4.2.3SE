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

$_language_box = new Language;
$_language_box->set_language($_language->language);
$_language_box->db_read_module('boxmodule');

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

if($action=='activatetoggle'){
	$value=$_GET['value'];
	$id=$_GET['id'];
	safe_query("UPDATE ".PREFIX."modules_boxed SET activated=".$value." WHERE modules_boxedID=".$id);
}
elseif($action=='delete'){
	safe_query("DELETE FROM ".PREFIX."modules_boxed WHERE modules_boxedID=".$_GET['id']);
	echo '<script type="text/javascript">
					window.location = "admincenter.php?site=modules_boxed";
				</script>
			 ';
}
elseif($action=='edit'){
	echo'<h1>&curren; '.$_language->module['modboxedit'].'</h1>';
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	
	$result=safe_query("SELECT * FROM ".PREFIX."modules_boxed WHERE modules_boxedID=".$_GET['id']);
	$any=mysql_num_rows($result);
	if($any){
		$row=mysql_fetch_array($result);
		if($row['activated']==1){
			$checked=' checked';
		}
		else{
			$checked='';
		}
	echo '<form method="post" action="admincenter.php?site=modules_boxed&action=saveedit&id='.$row['modules_boxedID'].'">';
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
    $transdata=safe_query("SELECT `text` FROM `".PREFIX."translations` WHERE foreign_identifier=".$_GET['id']." AND `lang`='".$lang."' AND `type`='boxmodule'");
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
	echo '<tr>
		      <td colspan="2" class="title"><b>'.$_language->module['filename'].'</b></td>
		      <td class="'.$td.'"><input type="text" name="filename" size="60" value="'.$row['filename'].'" /></td>
		    </tr>
		    <tr>
		      <td colspan="2" class="title"><b>'.$_language->module['placeholder'].'</b></td>
		      <td class="'.$td.'">
		        <select name="placeholder">
		          <option value="0"></option>';
	for($j=1; $j<=$maxboxmodules; $j++){
		if($j==$row['placeholder']){
			echo '<option value="'.$j.'" selected>'.$j.'</option>';	
		}
		else{
			echo '<option value="'.$j.'">'.$j.'</option>';	
		}
	}
	echo '		</select>
		      </td>
		    </tr>
		    <tr>
		      <td colspan="2" class="title"><b>'.$_language->module['activated'].'</b></td>
		      <td class="'.$td.'"><input type="checkbox" name="activated" value="1"'.$checked.' /></td>
		    </tr>';
	echo '</table><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" value="'.$_language->module['save'].'" /></form>';
	}
	else{
		echo $_language->module['boxmodule_not_exists'];
	}
}
elseif($action=='saveedit'){
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$modules_boxedID=$_GET['id'];
		$filename=trim($_POST['filename']);
		$placeholder=$_POST['placeholder'];
		if(!(($filename=='') || ($placeholder==0))){
			if(isset($_POST['activated'])){
				$activated=1;
			}
			else{
				$activated=0;
			}
	    if(file_exists('../'.$filename)){
	    	$test=safe_query("SELECT * FROM ".PREFIX."modules_boxed WHERE `modules_boxedID`!=".$modules_boxedID." AND (placeholder=".$placeholder." OR filename='".$filename."')");
	    	$anytest=mysql_num_rows($test);
	    	if($anytest){
	    		echo $_language->module['allready_defined_boxed'];
	    	}
	    	else{
	    		safe_query("UPDATE ".PREFIX."modules_boxed SET `filename`='".$filename."', `placeholder`=".$placeholder.", `activated`=".$activated." WHERE `modules_boxedID`=".$modules_boxedID);
	    		
				foreach($_language->installed_languages as $language => $lang){
					safe_query("DELETE FROM `".PREFIX."translations` WHERE `type`='boxmodule' AND `lang`='".$lang."' AND `foreign_identifier`='".$modules_boxedID."'");
					safe_query("INSERT INTO `".PREFIX."translations` (`type`, `lang`, `foreign_identifier`, `text`) VALUES ('boxmodule', '".$lang."', '".$modules_boxedID."', '".$_POST[$lang.'_modulename']."')");
				}
	    		echo '<script type="text/javascript">
					    window.location = "admincenter.php?site=modules_boxed";
					  </script>';
	    	}
	    }
	    else{
	    	echo $_language->module['file_not_available'];
	    }
		}
		else{
			echo $_language->module['missing_entry_boxed'];
		}
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='new'){
	echo'<h1>&curren; '.$_language->module['new_boxed'].'</h1>';
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	echo '<form method="post" action="admincenter.php?site=modules_boxed&action=savenew">';
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
	echo '<tr>
		      <td colspan="2" class="title"><b>'.$_language->module['filename'].'</b></td>
		      <td class="'.$td.'"><input type="text" name="filename" size="60" /></td>
		    </tr>
		    <tr>
		      <td colspan="2" class="title"><b>'.$_language->module['placeholder'].'</b></td>
		      <td class="'.$td.'">
		        <select name="placeholder">
		          <option value="0"></option>';
	for($j=1; $j<=$maxboxmodules; $j++){
	  echo '<option value="'.$j.'">'.$j.'</option>';	
	}
	echo '		</select>
		      </td>
		    </tr>
		    <tr>
		      <td colspan="2" class="title"><b>'.$_language->module['activated'].'</b></td>
		      <td class="'.$td.'"><input type="checkbox" name="activated" value="1" /></td>
		    </tr>';
	echo '</table><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" value="'.$_language->module['save'].'" /></form>';
}
elseif($action=='savenew'){
	
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$filename=trim($_POST['filename']);
		$placeholder=$_POST['placeholder'];
		if(!(($filename=='') || ($placeholder==0))){
			if(isset($_POST['activated'])){
				$activated=1;
			}
			else{
				$activated=0;
			}
	    if(file_exists('../'.$filename)){
	    	$test=safe_query("SELECT * FROM ".PREFIX."modules_boxed WHERE placeholder=".$placeholder." OR filename='".$filename."'");
	    	$anytest=mysql_num_rows($test);
	    	if($anytest){
	    		echo $_language->module['allready_defined_boxed'];
	    	}
	    	else{
	    		safe_query("INSERT INTO ".PREFIX."modules_boxed (filename, placeholder, activated) VALUES ('".$filename."',".$placeholder.",".$activated.")");
	    		$modules_boxedID=mysql_insert_id();
	    		foreach($_language->installed_languages as $language => $lang){
						safe_query("INSERT INTO `".PREFIX."translations` (`type`, `lang`, `foreign_identifier`, `text`) VALUES ('boxmodule', '".$lang."', '".$modules_boxedID."', '".$_POST[$lang.'_modulename']."')");
					}
	    		echo '<script type="text/javascript">
									window.location = "admincenter.php?site=modules_boxed";
								</script>
							 ';
	    	}
	    }
	    else{
	    	echo $_language->module['file_not_available'];
	    }
		}
		else{
			echo $_language->module['missing_entry_boxed'];
		}
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='moveup'){
	$minboxmodules=1;
	$oldplaceholder=$_GET['placeholder'];
	$oldid=$_GET['id'];
	if($oldplaceholder>$minboxmodules){
		$result=safe_query("SELECT modules_boxedID, placeholder FROM ".PREFIX."modules_boxed WHERE placeholder=".($oldplaceholder-1)." ORDER BY `placeholder` DESC LIMIT 0,1");
		$any=mysql_num_rows($result);
		if($any){
			$row=mysql_fetch_array($result);
			$newplaceholder=$row['placeholder'];
			$newid=$row['modules_boxedID'];
			safe_query("UPDATE ".PREFIX."modules_boxed SET placeholder=".$newplaceholder." WHERE modules_boxedID=".$oldid);
			safe_query("UPDATE ".PREFIX."modules_boxed SET placeholder=".$oldplaceholder." WHERE modules_boxedID=".$newid);
			echo '<script type="text/javascript">
							window.location = "admincenter.php?site=modules_boxed";
						</script>
					 ';
		}
		else{
			safe_query("UPDATE ".PREFIX."modules_boxed SET placeholder=".($oldplaceholder-1)." WHERE modules_boxedID=".$oldid);
			echo '<script type="text/javascript">
							window.location = "admincenter.php?site=modules_boxed";
						</script>
					 ';
		}
	}
	else{
			echo '<script type="text/javascript">
							window.location = "admincenter.php?site=modules_boxed";
						</script>
					 ';
		}
}
elseif($action=='movedown'){
	$oldplaceholder=$_GET['placeholder'];
	$oldid=$_GET['id'];
	if($oldplaceholder<$maxboxmodules){	
	  $result=safe_query("SELECT modules_boxedID, placeholder FROM ".PREFIX."modules_boxed WHERE placeholder=".($oldplaceholder+1)." ORDER BY `placeholder` ASC LIMIT 0,1");
		$any=mysql_num_rows($result);
	  if($any){
			$row=mysql_fetch_array($result);
			$newplaceholder=$row['placeholder'];
			$newid=$row['modules_boxedID'];
			safe_query("UPDATE ".PREFIX."modules_boxed SET placeholder=".$newplaceholder." WHERE modules_boxedID=".$oldid);
			safe_query("UPDATE ".PREFIX."modules_boxed SET placeholder=".$oldplaceholder." WHERE modules_boxedID=".$newid);
			echo '<script type="text/javascript">
							window.location = "admincenter.php?site=modules_boxed";
						</script>
					 ';
		}
		else{
			safe_query("UPDATE ".PREFIX."modules_boxed SET placeholder=".($oldplaceholder+1)." WHERE modules_boxedID=".$oldid);
			echo '<script type="text/javascript">
							window.location = "admincenter.php?site=modules_boxed";
						</script>
					 ';
		}
	}
	else{
			echo '<script type="text/javascript">
							window.location = "admincenter.php?site=modules_boxed";
						</script>
					 ';
		}
}
elseif($action=='setaccess'){
	$modules_boxedID=(int)$_GET['id'];
	$access=implode('|', explode(',', $_GET['value']));
	
	safe_query("UPDATE `".PREFIX."modules_boxed` SET `access`='".$access."' WHERE `modules_boxedID`=".$modules_boxedID);
}
else{
	echo'<h1>&curren; '.$_language->module['modules_boxed'].'</h1>';
	echo '<br />';
	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=modules_boxed&amp;action=new\');return document.MM_returnValue" value="'.$_language->module['new_boxed'].'" />';
	echo '<br /><br />';
	//list
	$result=safe_query("SELECT * FROM ".PREFIX."modules_boxed ORDER BY `placeholder` ASC");
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
              fetch(\'direct.php?site=modules_boxed&action=activatetoggle&id=\' + id + \'&value=\' + checkbox.value,\'debugdiv\',\'replace\',\'event\');
            }
            function submitaccess(dropdownid){
              options=getselection(dropdownid);
              fetch(\'direct.php?site=modules_boxed&action=setaccess&id=\' + dropdownid + \'&value=\' + options,\'debugdiv\',\'replace\',\'event\');
            }
		      </script>';
		echo'<br /><div id="debugdiv"></div>
    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
      <tr>
        <td width="20%" class="title"><b>'.$_language->module['modulename'].'</b></td>
        <td width="19%" class="title"><b>'.$_language->module['filename'].'</b></td>
        <td width="25%" class="title"><b>'.$_language->module['access_for'].'</b></td>
        <td width="10%" class="title" colspan="2"><b>'.$_language->module['placeholder'].'</b></td>
        <td width="10%" class="title"><b>'.$_language->module['activated'].'</b></td>
        <td width="16%" class="title"></td>
      </tr>';
		$i=1; 
		while($row=mysql_fetch_array($result)){
			
			if($i%2) { $td='td1'; }
      else { $td='td2'; }
      
      $actions = '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=modules_boxed&amp;action=edit&amp;id='.$row['modules_boxedID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> <input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=modules_boxed&amp;action=delete&amp;id='.$row['modules_boxedID'].'\');return document.MM_returnValue" value="'.$_language->module['delete'].'" />';
      
      if($row['activated']==1){
      	$value=1;
      	$selected=' checked';
      }
      else{
      	$value=0;
      	$selected='';
      }
      $accesses=generateaccessdropdown($row['modules_boxedID'], $row['modules_boxedID'], $row['access'], "submitaccess(this.id)");
      echo '<tr>
	        <td class="'.$td.'" valign="top" align="left">'.$_language_box->module[$row['modules_boxedID']].'</td>
	        <td class="'.$td.'" valign="top" align="left">'.$row['filename'].'</td>
	        <td class="'.$td.'" valign="top" align="center">'.$accesses.'</td>
	        <td class="'.$td.'" valign="top" align="center">'.$row['placeholder'].'</td>
	        <td class="'.$td.'" valign="top" align="center"><a href="admincenter.php?site=modules_boxed&action=moveup&id='.$row['modules_boxedID'].'&placeholder='.$row['placeholder'].'"><img src="../images/icons/desc.gif" border="0"></a> <a href="admincenter.php?site=modules_boxed&action=movedown&id='.$row['modules_boxedID'].'&placeholder='.$row['placeholder'].'"><img src="../images/icons/asc.gif" border="0"></a></td>
	        <td class="'.$td.'" valign="top" align="center"><input type="checkbox" onclick="activatetoggle('.$row['modules_boxedID'].');" id="check_'.$row['modules_boxedID'].'" value="'.$value.'"'.$selected.'></td>
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