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

$_language->read_module('data', true);

if(!ispageadmin($userID) OR (mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php" AND mb_substr(basename($_SERVER['REQUEST_URI']),0,10) != "direct.php")) die($_language->module['access_denied']);

if(isset($_GET['action'])){
	$action=$_GET['action'];
}
else{
	$action='';
}

if($action=='newdb'){
  $CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  echo'<h1>&curren; '.$_language->module['new_db'].'</h1>';
	echo '<br />';

	echo '<form method="post" action="admincenter.php?site=data&amp;action=savenewdb">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="20%"><b>'.$_language->module['dbname'].'</b></td>
      <td width="80%"><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['save'].'" /></td>
    </tr>
  </table>
  </form>';
}
elseif($action=='savenewdb'){
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("INSERT INTO ".PREFIX."database (name) VALUES ('".$_POST['name']."')");
		echo '<script type="text/javascript">
						window.location = "admincenter.php?site=data";
					</script>
				 ';
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='editdb'){
	echo'<h1>&curren; '.$_language->module['databases'].' - '.$_language->module['dbedit'].'</h1>';
	echo '<br />';
	$databaseID=$_GET['id'];
	$db=safe_query("SELECT * FROM ".PREFIX."database WHERE databaseID=".$databaseID);
	$anydb=mysql_num_rows($db);
	if($anydb){
		$dbrow=mysql_fetch_array($db);
		$CAPCLASS = new Captcha;
		$CAPCLASS->create_transaction();
		$hash = $CAPCLASS->get_hash();
	 	echo '<form method="post" action="admincenter.php?site=data&amp;action=saveeditdb&amp;id='.$databaseID.'">
	  <table width="100%" border="0" cellspacing="1" cellpadding="3">
	    <tr>
	      <td width="20%"><b>'.$_language->module['dbname'].'</b></td>
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
		echo $_language->module['db_na'];
	}
}
elseif($action=='saveeditdb'){
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."database SET `name`='".$_POST['name']."' WHERE `databaseID`=".$_GET['id']);
		echo '<script type="text/javascript">
						window.location = "admincenter.php?site=data";
					</script>
				 ';
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='deletefield'){
	//admincenter.php?site=data&action=deletefield&fieldid=7&databaseid=1
	$databaseid=(int)$_GET['databaseid'];
	$fieldid=(int)$_GET['fieldid'];
	safe_query("DELETE FROM `".PREFIX."database_fields` WHERE `databaseID`=".$databaseid." AND `database_fieldID`=".$fieldid);
	safe_query("DELETE FROM `".PREFIX."database_data` WHERE `databaseID`=".$databaseid." AND `database_fieldID`=".$fieldid);
	$dbfields=safe_query("SELECT * FROM `".PREFIX."database_fields` WHERE `databaseID`=".$databaseid);
	$anydbfields=mysql_num_rows($dbfields);
	if($anydbfields){
		$testforexistingvalidsort=safe_query("SELECT `sort_by` FROM `".PREFIX."database` WHERE `databaseID`=".$databaseid);
		$sortbyrow=mysql_fetch_array($testforexistingvalidsort);
		$sortby=$sortbyrow['sort_by'];
		$validsort=0;
		while($fieldrow=mysql_fetch_array($dbfields)){
			if($fieldrow['database_fieldID']==$sortby){
				$validsort=1;
				break;
			}
			else{
				$lastfield=$fieldrow['database_fieldID'];
			}
		}
		if(!$validsort){
			safe_query("UPDATE `".PREFIX."database` SET `sort_by`=".$lastfield." WHERE `databaseID`=".$databaseid);
		}
		echo '<script type="text/javascript">
						window.location = "admincenter.php?site=data&action=managefields&id='.$databaseid.'";
					</script>
				 ';
	}
	else{
		safe_query("UPDATE `".PREFIX."database` SET `sort_by`=0 WHERE `databaseID`=".$databaseid);
		echo '<script type="text/javascript">
						window.location = "admincenter.php?site=data&action=managefields&id='.$databaseid.'";
					</script>
				 ';
	}
}
elseif($action=='deletedb'){
	$databaseid=(int)$_GET['id'];
	safe_query("DELETE FROM `".PREFIX."database_data` WHERE `databaseID`=".$databaseid);
	safe_query("DELETE FROM `".PREFIX."database_entry` WHERE `databaseID`=".$databaseid);
	safe_query("DELETE FROM `".PREFIX."database_fields` WHERE `databaseID`=".$databaseid);
	safe_query("DELETE FROM `".PREFIX."database` WHERE `databaseID`=".$databaseid);	
	echo '<script type="text/javascript">
						window.location = "admincenter.php?site=data";
					</script>
				 ';
}
elseif($action=='managefields'){
	$databaseID=$_GET['id'];
	$db=safe_query("SELECT * FROM ".PREFIX."database WHERE databaseID=".$databaseID);
	$anydb=mysql_num_rows($db);
	if($anydb){
		$dbrow=mysql_fetch_array($db);
		$sort_by=$dbrow['sort_by'];
		$alpha_index=$dbrow['alpha_index'];
		$sortbyselect='<select onchange="fetch(\'direct.php?site=data&action=setsort&databaseid='.$databaseID.'&sort_by=\' + this.options[selectedIndex].value,\'debugdiv\',\'replace\',\'event\');">';
		echo'<h1>&curren; '.$_language->module['databases'].' - '.$_language->module['managefields'].': '.$dbrow['name'].'</h1>';
		echo '<br />';
		echo '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=data&amp;action=newfield&amp;id='.$databaseID.'\');return document.MM_returnValue" value="'.$_language->module['new_field'].'" />';
		echo '<br /><br />';
		$result=safe_query("SELECT * FROM ".PREFIX."database_fields WHERE `databaseID`=".$databaseID." ORDER BY `name` ASC");
		$any=mysql_num_rows($result);
		if($any){
			echo '<br />
				    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
				      <tr>
				        <td width="35%" class="title"><b>'.$_language->module['fieldname'].'</b></td>
				        <td width="35%" class="title"><b>'.$_language->module['identifier'].'</b></td>
				        <td width="30%" class="title"><b>'.$_language->module['actions'].'</b></td>
			      </tr>';
			$i=1; 
			while($row=mysql_fetch_array($result)){
				if($i%2) { $td='td1'; }
	      else { $td='td2'; }
	      if($sort_by==$row['database_fieldID']){
	      	$sortbyselect.='<option value="'.$row['database_fieldID'].'" selected>'.$row['name'].'</option>';
	      }
	      else{
	      	$sortbyselect.='<option value="'.$row['database_fieldID'].'">'.$row['name'].'</option>';
	      }
	      $actions = '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=data&amp;action=editfield&amp;databaseid='.$databaseID.'&amp;fieldid='.$row['database_fieldID'].'\');return document.MM_returnValue" value="'.$_language->module['fieldedit'].'" /> <input type="button" onclick="if(confirm(\''.$_language->module['really_delete_field'].'\')){window.location = \'admincenter.php?site=data&amp;action=deletefield&amp;fieldid='.$row['database_fieldID'].'&amp;databaseid='.$databaseID.'\'}" value="'.$_language->module['fielddelete'].'" />';
	      
	      echo '<tr>
				        <td class="'.$td.'" align="left">'.$row['name'].'</td>
				        <td class="'.$td.'" align="left">$'.$row['identifier'].'</td>
				        <td class="'.$td.'" align="left">'.$actions.'</td>
				      </tr>
			      ';
	      
	      $i++;
			}
			echo '</table>';
			echo '<br /><br />';
			$sortbyselect.='</select>';
			echo '<script language="javascript" type="text/javascript">
            function activatetoggle(id){
              checkbox=document.getElementById(id);
              if(checkbox.value==1){
                checkbox.value=0;
              }
              else{
                checkbox.value=1;
              }
              fetch(\'direct.php?site=data&action=setalpha&&databaseid='.$databaseID.'&alpha_index=\' + checkbox.value,\'debugdiv\',\'replace\',\'event\');
            }
		      </script>';
			if($alpha_index==1){
				$alphainput='<input id="alpha" onclick="activatetoggle(this.id)" type="checkbox" value="1" checked />';
			}
			else{
				$alphainput='<input id="alpha" onclick="activatetoggle(this.id)" type="checkbox" value="0" />';
			}
			echo $_language->module['sort_by'].': '.$sortbyselect.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.$_language->module['alpha_index'].': '.$alphainput;
			echo '<div id="debugdiv"></div>';
			echo '<br /><br />';
			$CAPCLASS = new Captcha;
			$CAPCLASS->create_transaction();
			$hash = $CAPCLASS->get_hash();
			echo '<br />
			      <form method="post" action="admincenter.php?site=data&amp;action=savetemplate&amp;id='.$databaseID.'">
				    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
				      <tr>
				        <td width="100%" class="title"><b>'.$_language->module['edit_template'].'</b></td>
				      </tr>
				      <tr>
				        <td class="td1" align="center"><textarea name="template" style="width: 100%; height: 200px; margin: 0px; padding: 0px;">'.$dbrow['template'].'</textarea></td>
				      </tr>
				      <tr>
				        <td class="td1" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" value="'.$_language->module['save'].'" /></td>
				      </tr>
				    </table>
				    </form>
				    ';
		}
		else{
			echo $_language->module['no_field_definitions'];
		}
	}
	else{
		echo $_language->module['db_na'];
	}
}
elseif($action=='setalpha'){
	safe_query("UPDATE ".PREFIX."database SET `alpha_index`='".$_GET['alpha_index']."' WHERE `databaseID`=".$_GET['databaseid']);
}
elseif($action=='setsort'){
	safe_query("UPDATE ".PREFIX."database SET `sort_by`='".$_GET['sort_by']."' WHERE `databaseID`=".$_GET['databaseid']);
}
elseif($action=='savetemplate'){
	$databaseID=$_GET['id'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."database SET `template`='".$_POST['template']."' WHERE `databaseID`=".$databaseID);
		echo '<script type="text/javascript">
						window.location = "admincenter.php?site=data&action=managefields&id='.$databaseID.'";
					</script>
				 ';
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='newfield'){
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  echo'<h1>&curren; '.$_language->module['new_field'].'</h1>';
	echo '<br />';

	echo '<form method="post" action="admincenter.php?site=data&amp;action=savenewfield&amp;databaseid='.(int)$_GET['id'].'">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="20%"><b>'.$_language->module['fieldname'].'</b></td>
      <td width="80%"><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td width="20%"><b>'.$_language->module['identifier'].'</b></td>
      <td width="80%"><input type="text" name="identifier" size="60" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['save'].'" /></td>
    </tr>
  </table>
  </form>';
}
elseif($action=='savenewfield'){
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("INSERT INTO ".PREFIX."database_fields (databaseID, name, identifier) VALUES ('".$_GET['databaseid']."', '".$_POST['name']."', '".$_POST['identifier']."')");
		$databasefieldid=mysql_insert_id();
		$testforexistingsort=mysql_num_rows(safe_query("SELECT * FROM `".PREFIX."database` WHERE `databaseID`=".$_GET['databaseid']." AND `sort_by`!='0'"));
		if($testforexistingsort){
			
		}
		else{
			safe_query("UPDATE `".PREFIX."database` SET `sort_by`=".$databasefieldid." WHERE `databaseID`=".$_GET['databaseid']);
		}
		echo '<script type="text/javascript">
						window.location = "admincenter.php?site=data&action=managefields&id='.$_GET['databaseid'].'";
					</script>
				 ';
	} else echo $_language->module['transaction_invalid'];
}
elseif($action=='editfield'){
	$databaseID=$_GET['databaseid'];
	$database_fieldID=$_GET['fieldid'];
	$fielddata=safe_query("SELECT * FROM ".PREFIX."database_fields WHERE `database_fieldID`=".$database_fieldID);
  $anyfielddata=mysql_num_rows($fielddata);
	echo'<h1>&curren; '.$_language->module['new_field'].'</h1>';
	echo '<br />';
  if($anyfielddata){
  	$fieldrow=mysql_fetch_array($fielddata);
		$CAPCLASS = new Captcha;
		$CAPCLASS->create_transaction();
		$hash = $CAPCLASS->get_hash();
		echo '<form method="post" action="admincenter.php?site=data&amp;action=saveeditfield&amp;databaseid='.$databaseID.'&amp;fieldID='.$database_fieldID.'">
	  <table width="100%" border="0" cellspacing="1" cellpadding="3">
	    <tr>
	      <td width="20%"><b>'.$_language->module['fieldname'].'</b></td>
	      <td width="80%"><input type="text" name="name" size="60" value="'.$fieldrow['name'].'" /></td>
	    </tr>
	    <tr>
	      <td width="20%"><b>'.$_language->module['identifier'].'</b></td>
	      <td width="80%"><input type="text" name="identifier" size="60" value="'.str_replace('$', '', $fieldrow['identifier']).'" /></td>
	    </tr>
	    <tr>
	      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
	      <td><input type="submit" name="save" value="'.$_language->module['save'].'" /></td>
	    </tr>
	  </table>
	  </form>';
  }
  else{
		echo $_language->module['field_na'];
	}
}
elseif($action=='saveeditfield'){
	$database_fieldID=$_GET['fieldID'];
	$databaseID=$_GET['databaseid'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."database_fields SET `name`='".$_POST['name']."', `identifier`='".$_POST['identifier']."' WHERE `database_fieldID`=".$database_fieldID);
		echo '<script type="text/javascript">
						window.location = "admincenter.php?site=data&action=managefields&id='.$databaseID.'";
					</script>
				 ';
	} else echo $_language->module['transaction_invalid'];
}
else {
	echo'<h1>&curren; '.$_language->module['databases'].'</h1>';
	echo '<br />';
	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=data&amp;action=newdb\');return document.MM_returnValue" value="'.$_language->module['new_db'].'" />';
	echo '<br /><br />';
	$result=safe_query("SELECT * FROM ".PREFIX."database ORDER BY `name` ASC");
	$any=mysql_num_rows($result);
	if($any){
		echo '<br />
			    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
			      <tr>
			        <td width="40%" class="title"><b>'.$_language->module['dbname'].'</b></td>
			        <td width="60%" class="title"><b>'.$_language->module['actions'].'</b></td>
		      </tr>';
		$i=1; 
		while($row=mysql_fetch_array($result)){
			if($i%2) { $td='td1'; }
      else { $td='td2'; }
      
      $actions = '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=data&amp;action=managefields&amp;id='.$row['databaseID'].'\');return document.MM_returnValue" value="'.$_language->module['managefields'].'" /> <input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=data&amp;action=editdb&amp;id='.$row['databaseID'].'\');return document.MM_returnValue" value="'.$_language->module['dbedit'].'" /> <input type="button" onclick="if(confirm(\''.$_language->module['really_delete_db'].'\')){window.location = \'admincenter.php?site=data&amp;action=deletedb&amp;id='.$row['databaseID'].'\'}" value="'.$_language->module['dbdelete'].'" />';
      
      echo '<tr>
			        <td class="'.$td.'" align="left"><a href="../index.php?site=data&action=show&id='.$row['databaseID'].'" target="_blank">'.$row['name'].'</a></td>
			        <td class="'.$td.'" align="left">'.$actions.'</td>
			      </tr>
		      ';
      
      $i++;
		}
		echo '</table>';
	}
	else{
		echo $_language->module['no_db_definitions'];
	}
}
?>