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

$_language->read_module('data');

$chars=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

function getdataentry($databaseID, $database_fieldID, $data_entryID){
	$data=safe_query("SELECT `data`, `data_entryID` FROM `".PREFIX."database_data` WHERE `databaseID`=".$databaseID." AND `database_fieldID`=".$database_fieldID." AND `data_entryID`=".$data_entryID);
	$datarow=mysql_fetch_array($data);
	return $datarow['data'];
}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action='';

if($action=="editentry"){
	if(isdbauthor($userID)){
		$id=(int)$_GET['id'];
		$dataentryid=(int)$_GET['dataentryid'];
		
		$dbdata=safe_query("SELECT `name` FROM `".PREFIX."database` WHERE `databaseID`=".$id);
		$db_row=mysql_fetch_array($dbdata);
		
		$db_fields=safe_query("SELECT `database_fieldID`, `databaseID`, `name`, `identifier` FROM `".PREFIX."database_fields` WHERE `databaseID`=".$id);
		$anyfields=mysql_num_rows($db_fields);
	  if($anyfields){
	  	eval ("\$data_editentry_head = \"".gettemplate("data_editentry_head")."\";");
	    echo $data_editentry_head;
	    while($fieldsrow=mysql_fetch_array($db_fields)){
		    $fieldid=$fieldsrow['database_fieldID'];
		    $name=$fieldsrow['name'];
		    $data=getdataentry($id, $fieldid, $dataentryid);
	    	eval ("\$data_editentry_content = \"".gettemplate("data_editentry_content")."\";");
			  echo $data_editentry_content;
	    }
	    eval ("\$data_editentry_foot = \"".gettemplate("data_editentry_foot")."\";");
	    echo $data_editentry_foot;
	  }
	  else{
	    echo $_language->module['no_fields_defined'];
	  }
	}
	else{
		echo $_language->module['no_access'];
	}
}
elseif($action=="saveeditentry"){
	if(isdbauthor($userID)){	
	  $id=(int)$_GET['id'];
	  $dataentryid=(int)$_GET['dataentryid'];
	  $db_fields=safe_query("SELECT `database_fieldID` FROM `".PREFIX."database_fields` WHERE `databaseID`=".$id);
		while($fieldrow=mysql_fetch_array($db_fields)){
			//test if exists already or not
			$allreadyexists=mysql_num_rows(safe_query("SELECT * FROM `".PREFIX."database_data` WHERE `databaseID`=".$id." AND `data_entryID`=".$dataentryid." AND `database_fieldID`=".$fieldrow['database_fieldID']));
			if($allreadyexists){
				safe_query("UPDATE `".PREFIX."database_data` SET `data`='".$_POST[$fieldrow['database_fieldID']]."' WHERE `databaseID`=".$id." AND `data_entryID`=".$dataentryid." AND `database_fieldID`=".$fieldrow['database_fieldID']);
			}
			else{
				safe_query("INSERT INTO `".PREFIX."database_data` (`databaseID`, `database_fieldID`, `data_entryID`, `data`) VALUES (".$id.", ".$fieldrow['database_fieldID'].", ".$dataentryid.", '".$_POST[$fieldrow['database_fieldID']]."')");
			}
		}
	  echo '<script type="text/javascript">
						window.location = "index.php?site=data&action=show&id='.$id.'";
					</script>
				 ';
	}
	else{
	  echo $_language->module['no_access'];
	}
}
elseif($action=="newentry"){
	if(isdbauthor($userID)){
		$id=(int)$_GET['id'];
		$submitter='';
		
		$dbdata=safe_query("SELECT `name` FROM `".PREFIX."database` WHERE `databaseID`=".$id);
		$db_row=mysql_fetch_array($dbdata);
		
	  $db_fields=safe_query("SELECT `database_fieldID`, `databaseID`, `name`, `identifier` FROM `".PREFIX."database_fields` WHERE `databaseID`=".$id);
		$anyfields=mysql_num_rows($db_fields);
	  if($anyfields){
	  	eval ("\$data_newentry_head = \"".gettemplate("data_newentry_head")."\";");
	    echo $data_newentry_head;
	    while($fieldsrow=mysql_fetch_array($db_fields)){
		    $fieldid=$fieldsrow['database_fieldID'];
		    $name=$fieldsrow['name'];
	    	eval ("\$data_newentry_content = \"".gettemplate("data_newentry_content")."\";");
			  echo $data_newentry_content;
	    }
	    $submitter='<input type="submit" value="'.$_language->module['save'].'" />';
	    eval ("\$data_newentry_foot = \"".gettemplate("data_newentry_foot")."\";");
	    echo $data_newentry_foot;
	  }
	  else{
	    echo $_language->module['no_fields_defined'];
	  }
	}
	else{
		echo $_language->module['no_access'];
	}
}
elseif($action=="savenewentry"){
	if(isdbauthor($userID)){	
	  $id=(int)$_GET['id'];
		safe_query("INSERT INTO `".PREFIX."database_entry` (`databaseID`) VALUES (".$id.")");
		$dataentryid=mysql_insert_id();
		$db_fields=safe_query("SELECT `database_fieldID` FROM `".PREFIX."database_fields` WHERE `databaseID`=".$id);
		while($fieldrow=mysql_fetch_array($db_fields)){
			safe_query("INSERT INTO `".PREFIX."database_data` (`databaseID`, `database_fieldID`, `data_entryID`, `data`) VALUES (".$id.", ".$fieldrow['database_fieldID'].", ".$dataentryid.", '".$_POST[$fieldrow['database_fieldID']]."')");
		}
		echo '<script type="text/javascript">
						window.location = "index.php?site=data&action=show&id='.$id.'";
					</script>
				 ';
	}
	else{
	  echo $_language->module['no_access'];
	}
}
elseif($action=="deleteentry"){
	if(isdbauthor($userID)){
		safe_query("DELETE FROM `".PREFIX."database_entry` WHERE `database_entryID`=".(int)$_GET['dataentryid']." AND `databaseID`=".(int)$_GET['id']);
		safe_query("DELETE FROM `".PREFIX."database_data` WHERE `data_entryID`=".(int)$_GET['dataentryid']." AND `databaseID`=".(int)$_GET['id']);
		echo '<script type="text/javascript">
						window.location = "index.php?site=data&action=show&id='.(int)$_GET['id'].'";
					</script>
				 ';
	}
	else{
	  echo $_language->module['no_access'];
	}
}
elseif($action=="show"){
	
	$id=(int)$_GET['id'];
	
	if(isset($_GET['alphaindex'])){
		$alphaindex=$_GET['alphaindex'];
	}
	else{
		$alphaindex='a';
	}
	
	$db_result=safe_query("SELECT `databaseID`, `name`, `template`,	`alpha_index`, `sort_by` FROM `".PREFIX."database` WHERE `databaseID`=".$id);
	$any_db=mysql_num_rows($db_result);
	if($any_db){
		$db_row=mysql_fetch_array($db_result);
		
		eval ("\$data_specific = \"".gettemplate("data_specific")."\";");
	  echo $data_specific;
		
    if(isdbauthor($userID)){
			echo '<input type="button" value="'.$_language->module['make_new_entry'].'" onclick="window.location = \'index.php?site=data&amp;action=newentry&amp;id='.$id.'\'" /><br /><br />';
		}
	  
	  if($db_row['alpha_index']==1){
			foreach($chars AS $char){
				eval("\$data_charbuttons = \"".gettemplate("data_charbuttons")."\";");
		    echo $data_charbuttons;
			}
			echo '<br /><br />';
		}
		
		$db_fields=safe_query("SELECT `database_fieldID`, `databaseID`, `name`, `identifier` FROM `".PREFIX."database_fields` WHERE `databaseID`=".$id);
    $dbfields=array();
		while($fieldrow=mysql_fetch_array($db_fields)){
    	$dbfields[]=array($fieldrow['database_fieldID'], $fieldrow['databaseID'], $fieldrow['name'], $fieldrow['identifier']);
    }
		
		if($db_row['alpha_index']==1){
			if($db_row['sort_by']!=0){
				$dataquery="SELECT `data`, `data_entryID` FROM `".PREFIX."database_data` WHERE `databaseID`=".$id." AND `database_fieldID`=".$db_row['sort_by']." AND `DATA` LIKE '".$alphaindex."%' ORDER BY `data` ASC";			
			}
			else{
				$dataquery="SELECT `data`, `data_entryID` FROM `".PREFIX."database_data` WHERE `databaseID`=".$id." AND `database_fieldID`=".$db_row['sort_by']." AND `DATA` LIKE '".$alphaindex."%' ORDER BY `data` ASC";			
			}
		}
		else{
      if($db_row['sort_by']!=0){
      	$dataquery="SELECT `data`, `data_entryID` FROM `".PREFIX."database_data` WHERE `databaseID`=".$id." AND `database_fieldID`=".$db_row['sort_by']." ORDER BY `data` ASC";			
      }
      else{
      	$dataquery="SELECT `data`, `data_entryID` FROM `".PREFIX."database_data` WHERE `databaseID`=".$id." AND `database_fieldID`=".$db_row['sort_by']." ORDER BY `data` ASC";			
      }
		}
    
		$db_data=safe_query($dataquery);
		$any_db_data=mysql_num_rows($db_data);
		if($any_db_data){
			while($datarow=mysql_fetch_array($db_data)){
				foreach($dbfields AS $dbfielddata){
					if($dbfielddata[0]==$db_row['sort_by']){ //fields is the same as the one sorted by (no extra db queries needed to get the content of this field)
						$$dbfielddata[3]=htmloutput($datarow['data']);
					}
					else{ //fields is not the same as the one sorted by, extra db queries needed to get its data
						$$dbfielddata[3]=htmloutput(getdataentry($id, $dbfielddata[0], $datarow['data_entryID']));
					}
				}
				if(isdbauthor($userID)){
					echo '<div style="width: 100%; text-align: right;">  
					        <input type="button" value="'.$_language->module['edit_entry'].'" onclick="window.location = \'index.php?site=data&amp;action=editentry&amp;id='.$id.'&amp;dataentryid='.$datarow['data_entryID'].'\'" /> 
					        <input type="button" value="'.$_language->module['delete_entry'].'" onclick="if(confirm(\''.$_language->module['really_delete'].'\')){window.location = \'index.php?site=data&amp;action=deleteentry&amp;id='.$id.'&amp;dataentryid='.$datarow['data_entryID'].'\'}" /> 
					      </div>
					      ';
				}
				eval("\$templated = \"".str_replace("\"","\\\"",$db_row['template'])."\";");
				echo $templated;
			}
		}
		else{
			echo '<br /><br />'.$_language->module['no_entries'];
		}
	}
	else{
		echo '<br />'.$_language->module['na_db'];
	}
}
else{
	eval ("\$title_data = \"".gettemplate("title_data")."\";");
	echo $title_data;
	$db_result=safe_query("SELECT `databaseID`, `name` FROM `".PREFIX."database`");
	$any_db=mysql_num_rows($db_result);
	if($any_db){
		while($row=mysql_fetch_array($db_result)){
			$dbid=$row['databaseID'];
			$dbname=$row['name'];
			eval("\$data = \"".gettemplate("data")."\";");
		  echo $data;
		}
	}
	else{
		echo '<br />'.$_language->module['no_db'];
	}
}
?>