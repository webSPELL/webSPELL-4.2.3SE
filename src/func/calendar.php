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

function getregisterfieldtype($registerfieldID){
	$result=safe_query("SELECT `type` FROM `".PREFIX."upcoming_registerfields` WHERE `registerfieldID`=".$registerfieldID);
	$any=mysql_num_rows($result);
	if($any){
		$row=mysql_fetch_array($result);
		$output=$row['type'];
		return $output;
	}
}

function getregisterfieldname($registerfieldID, $lang){
	$result=safe_query("SELECT `text` FROM `".PREFIX."translations` WHERE foreign_identifier=".$registerfieldID." AND `lang`='".$lang."' AND `type`='registerfield'");
	$any=mysql_num_rows($result);
	if($any){
		$row=mysql_fetch_array($result);
		$output=$row['text'];
		return $output;
	}
}

function getregisterfielddata($upID, $annID, $registerfieldID){
	$result=safe_query("SELECT `data` FROM `".PREFIX."upcoming_data` WHERE `upID`=".$upID." AND `annID`=".$annID." AND `registerfieldID`=".$registerfieldID);
	$any=mysql_num_rows($result);
	if($any){
		$row=mysql_fetch_array($result);
		$output=$row['data'];
	}
	else{
		$output=null;
	}
	return $output;
}

?>