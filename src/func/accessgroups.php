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

function getaccessgroupname($accessgroupID){
	$ds=mysql_fetch_array(safe_query("SELECT `name` FROM `".PREFIX."accessgroups` WHERE accessgroupID='".$accessgroupID."'"));
	return $ds['name'];
}

function inaccessgroup($accessgroupID, $userID){
	$ds=mysql_fetch_array(safe_query("SELECT COUNT(*) AS result FROM `".PREFIX."accessgroup_members` WHERE `accessgroupID`='".$accessgroupID."' AND `userID`='".$userID."'"));
  return $ds['result'];
}

function getaccessgroups($userID){
	$useraccessgroups=array('0');
	if($userID!=0){
		$useraccessgroups[]='-1';
		if(isclanmember($userID)) $useraccessgroups[]='-2';
	}
	$ds=safe_query("SELECT `accessgroupID` FROM `".PREFIX."accessgroup_members` WHERE `userID`=".$userID);
	while($row=mysql_fetch_array($ds)){
		$useraccessgroups[]=$row['accessgroupID'];
	}
	return $useraccessgroups;
}

function hasaccess($allowed, $usersgroupdata){
	if(!is_array($allowed)) $allowed=explode('|', str_replace(',', '|', $allowed));
	$intersection=array_intersect($allowed, $usersgroupdata);
	if(count($intersection)>0){
		return true;
	}
	else{
		return false;
	}
}

function generateaccessdropdown($selectname, $selectid, $allowed='', $jscallback=''){
	global $_language;
	if(!is_array($allowed)) $allowed=explode('|', str_replace(',', '|', $allowed));
	$accesses='';
	$accessgroups=safe_query("SELECT `accessgroupID`, `name` FROM `".PREFIX."accessgroups`");
	if($jscallback==''){
		$accesses.='<select id="'.$selectid.'" name="'.$selectname.'" multiple="multiple" style="width: 180px; height: 50px;">';
	}
	else{
		$accesses.='<select id="'.$selectid.'" name="'.$selectname.'" multiple="multiple" style="width: 180px; height: 50px;" onchange="'.$jscallback.'">';
	}
  
	if(in_array('0', $allowed)){
		$accesses.='<option value="0" selected>'.$_language->module['everyone'].'</option>';
	}
	else{
		$accesses.='<option value="0">'.$_language->module['everyone'].'</option>';
	}
  
	if(in_array('-1', $allowed)){
		$accesses.='<option value="-1" selected>'.$_language->module['registered'].'</option>';
	}
	else{
		$accesses.='<option value="-1">'.$_language->module['registered'].'</option>';
	}
  
	if(in_array('-2', $allowed)){
		$accesses.='<option value="-2" selected>'.$_language->module['members'].'</option>';
	}
	else{
		$accesses.='<option value="-2">'.$_language->module['members'].'</option>';
	}
  
	while($accessgrouprow=mysql_fetch_array($accessgroups)){
		if(in_array($accessgrouprow['accessgroupID'], $allowed)){
			$accesses.='<option value="'.$accessgrouprow['accessgroupID'].'" selected>'.$accessgrouprow['name'].'</option>';
		}
  	else{
  		$accesses.='<option value="'.$accessgrouprow['accessgroupID'].'">'.$accessgrouprow['name'].'</option>';
  	}
  }
  $accesses.='</select>';
  return $accesses;
}
?>