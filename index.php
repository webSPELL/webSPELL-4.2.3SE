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

include("_mysql.php");
include("_settings.php");
include("_functions.php");

$_language->read_module('index');
$index_language = $_language->module;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Website using webSPELL 4 CMS - Society Edition" />
	<meta name="author" content="webspell.org" />
	<meta name="keywords" content="webspell, webspell4, clan, cms, society, edition" />
	<meta name="copyright" content="Copyright &copy; 2005 - 2011 by webspell.org" />
	<meta name="generator" content="webSPELL" />
	<title><?php echo PAGETITLE; ?></title>
	<link href="_stylesheet.css" rel="stylesheet" type="text/css" />
	<!--[if IE]>
	<style type="text/css">
	.td1 {  height: 18px; }
	.td2 {  height: 18px; }
	div#content .col2 { width: 74%; }
	div#content .col3 { width: 19%; }
	hr.grey { margin: 3px 0 3px 0; }
	</style>
	<![endif]-->
	<!--[if lte IE 7]>
	<style type="text/css">
	hr.grey { margin: 3px 0 -3px 0; }
	</style>
	<![endif]-->
	<!--[if gte IE 8]>
	<style type="text/css">
	hr.grey { margin: 3px 0 3px 0;}
	</style>
	<![endif]-->
	<script src="js/bbcode.js" language="JavaScript" type="text/javascript"></script>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="1000" align="center">
  <tr>
    <td colspan="7" id="head" valign="top">
    	<div style="height: 15px; margin: 0px; margin-top: 4px; padding: 0px; padding-left: 4px; padding-right: 4px; text-align: left; width: 992px;"><?php include('sc_scrolltext.php'); ?></div>
	    <div style="float: left; margin: 0px; padding: 0px; padding-left: 57px; padding-top: 22px; text-align: left; width: 443px; font-size: 22px; color: #F2F2F2;"><?php echo $myclanname; ?></div>
	    <div style="margin: 0px; padding: 0px; margin-top: 7px; text-align: center; width: 500px; float: right;">
	      <?php include('sc_bannerrotation.php'); ?>
	    </div>
    </td>
  </tr>
  <tr>
    <td colspan="7"><img src="images/2.jpg" width="1000" height="5" border="0" alt="" /></td>
  </tr>
  <tr>
    <td style="background-image:url(images/3.jpg);" width="5" valign="top"></td>
    <td bgcolor="#f2f2f2" width="202" valign="top">
	    <div class="menu">
	      <?php include("navigation.php"); ?>
	      <?php boxinclude(1); ?>
				<?php boxinclude(2); ?>
				<?php boxinclude(3); ?>
				<?php boxinclude(4); ?>
				<?php boxinclude(5); ?>
	    </div>
    </td>
    <td bgcolor="#2a2a2a" width="2" valign="top"></td>
    <td bgcolor="#ffffff" width="582" valign="top">
    <div class="pad">
     <?php
			if((!isset($site)) || $site=='') $site="news";
			
			$checkmodul=safe_query("SELECT `activated`, `access` FROM ".PREFIX."modules WHERE `filename`='".$site.".php'");
			$modulfound=mysql_num_rows($checkmodul);
			if($modulfound){
				$modulrow=mysql_fetch_array($checkmodul);
			  if($modulrow['activated']==1){
			  	if(hasaccess($modulrow['access'], $useraccessgroups)){
						$invalide = array('\\','/','/\/',':','.');
						$site = str_replace($invalide,' ',$site);
						if(!file_exists($site.".php")) $site = "news";
						include($site.".php");
			  	}
			  	else{
			  		echo '<br />'.$index_language['access_denied'];
			  	}
			  }
			  else{
			  	echo '<br />'.$index_language['mod_deactivated'];
			  }
			}
			else{
				echo '<br />'.$index_language['mod_not_available'];
		  }
		 	?>
    </div>
    </td>
    <td bgcolor="#2a2a2a" width="2" valign="top"></td>
    <td bgcolor="#f2f2f2" width="202" valign="top">
      <div class="menu">
	      <?php boxinclude(6); ?>
				<?php boxinclude(7); ?>
				<?php boxinclude(8); ?>
				<?php boxinclude(9); ?>
				<?php boxinclude(10); ?>
				<?php boxinclude(11); ?>
				<?php boxinclude(12); ?>
				<?php boxinclude(13); ?>
				<?php boxinclude(14); ?>
				<?php boxinclude(15); ?>
				<?php boxinclude(16); ?>
				<?php boxinclude(17); ?>
				<?php boxinclude(18); ?>
				<?php boxinclude(19); ?>
				<?php boxinclude(20); ?>
				<?php boxinclude(21); ?>
				<?php boxinclude(22); ?>
				<?php boxinclude(23); ?>
				<?php boxinclude(24); ?>
				<?php boxinclude(25); ?>
				<?php boxinclude(26); ?>
				<?php boxinclude(27); ?>
				<?php boxinclude(28); ?>
				<?php boxinclude(29); ?>
				<?php boxinclude(30); ?>
	    </div>
    </td>
    <td style="background-image:url(images/4.jpg);" width="5" valign="top"></td>
  </tr>
  <tr>
   <td colspan="7"><img src="images/5.jpg" width="1000" height="7" border="0" alt="" /></td>
  </tr>
</table>
<center><br style="line-height:2px;" />Copyright by <b><?php echo $myclanname ?></b>&nbsp; | &nbsp;CMS powered by <a href="http://www.webspell.org" target="_blank"><b>webSPELL.org</b></a> Society Edition&nbsp; | &nbsp;<a href="http://validator.w3.org/check?uri=referer" target="_blank">XHTML 1.0</a> &amp; <a href="http://jigsaw.w3.org/css-validator/check/refer" target="_blank">CSS 2.1</a> valid W3C standards&nbsp; | &nbsp;<a href="tmp/rss.xml" target="_blank"><img src="images/icons/rss.png" width="16" height="16" style="vertical-align:bottom;" alt="" /></a> <a href="tmp/rss.xml" target="_blank">RSS Feed</a></center>
</body>
</html>
