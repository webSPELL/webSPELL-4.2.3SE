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

//php below this line ;)

if(isset($site)) $_language->read_module('joinus');

eval ("\$title_joinus = \"".gettemplate("title_joinus")."\";");
echo $title_joinus;

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = "";
$show=true;
if($action=="save" && isset($_POST['post'])) {

	if(isset($_POST['squad'])) $squad = $_POST['squad'];
	else $squad = 0;
	$nick = $_POST['nick'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$messenger = $_POST['messenger'];
	$age = $_POST['age'];
	$city = $_POST['city'];
	$info = $_POST['info'];
	$street = $_POST['street'];
	$run=0;
  
  $error = array();
	if(!(mb_strlen(trim($nick)))) $error[]=$_language->module['forgot_nickname'];
	if(!(mb_strlen(trim($name)))) $error[]=$_language->module['forgot_realname'];
	if(!(mb_strlen(trim($name)))) $error[]=$_language->module['forgot_street'];
    if(! validate_email($email)) $error[]=$_language->module['email_not_valid'];
    //if(!(mb_strlen(trim($messenger)))) $error[]=$_language->module['forgot_messenger'];
    if(!(mb_strlen(trim($age)))) $error[]=$_language->module['forgot_age'];
	if(!(mb_strlen(trim($city)))) $error[]=$_language->module['forgot_city'];
	  
  if($userID) {
		$run=1;
	}
	else {
		$CAPCLASS = new Captcha;
		if(!$CAPCLASS->check_captcha($_POST['captcha'], $_POST['captcha_hash'])) $error[]=$_language->module['wrong_security_code'];
		else $run=1;
	}

	if(!count($error) and $run) {
		//$ergebnis=safe_query("SELECT userID FROM ".PREFIX."squads_members WHERE joinmember='1' AND squadID='".$squad."'");
		/*while($ds=mysql_fetch_array($ergebnis)) {
			$touser[]=$ds['userID'];
		}*/

		
		$message = ''.$_language->module['someone_want_to_join'].'
		
'.$_language->module['nick'].': '.$nick.'
'.$_language->module['name'].': '.$name.'
'.$_language->module['age'].': '.$age.'
'.$_language->module['mail'].': '.$email.'
'.$_language->module['messenger'].': '.$messenger.'
'.$_language->module['street'].': '.$street.'
'.$_language->module['city'].': '.$city.'
		
'.$_language->module['info'].':
'.$info.'
';
			/*foreach($touser as $id) {
				sendmessage($id,'Join us',$message);
			}*/
		
		$header=$_language->module['membership_application'].': '.$myclanname;
			
		if(mail($admin_email, $header, $message, "From:".$admin_email."\nContent-type: text/plain; charset=utf-8\n")){
		  echo $_language->module['thanks_you_will_get_mail'];	
		}
		else{		
			echo $_language->module['mail_problems'];		
		}
		
		unset($_POST['nick'], $_POST['name'], $_POST['email'],$_POST['messenger'],$_POST['age'],$_POST['city'],$_POST['clanhistory'],$_POST['info']);
		$show=false;
	}
	else {
		$fehler=implode('<br />&#8226; ',$error);
		$show=true;
    $showerror = '<div class="errorbox">
      <b>'.$_language->module['problems'].':</b><br /><br />
      &#8226; '.$fehler.'
    </div>';
	}
}
if($show==true){
  $bg1 = BG_1;

	if($loggedin) {
		if(!isset($showerror)) $showerror='';
		$res = safe_query("SELECT *, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), '%y') 'age' FROM ".PREFIX."user WHERE userID = '$userID'");
    $ds = mysql_fetch_assoc($res);
		$nickname = getinput($ds['firstname']);
		$name = getinput($ds['lastname']);
		$email = getinput($ds['email']);
		$messenger = getinput($ds['icq']);
		$age = $ds['age'];
		$city = getinput($ds['postcode'].' '.$ds['town']);
		$street = getinput($ds['street'].' '.$ds['streetnr']);
		
    if(isset($_POST['clanhistory'])) $clanhistory=getforminput($_POST['clanhistory']);
    else $clanhistory='';
    if(isset($_POST['info'])) $info=getforminput($_POST['info']);
    else $info='';

		eval ("\$joinus_loggedin = \"".gettemplate("joinus_loggedin")."\";");
		echo $joinus_loggedin;
	}
	else {
		$CAPCLASS = new Captcha;
		$captcha = $CAPCLASS->create_captcha();
		$hash = $CAPCLASS->get_hash();
		$CAPCLASS->clear_oldcaptcha();
		
    if(!isset($showerror)) $showerror='';
    if(isset($_POST['nick'])) $nick= getforminput($_POST['nick']);
    else $nick='';
    if(isset($_POST['name'])) $name= getforminput($_POST['name']);
    else $name='';
    if(isset($_POST['email'])) $email= getforminput($_POST['email']);
    else $email='';
    if(isset($_POST['messenger'])) $messenger= getforminput($_POST['messenger']);
    else $messenger='';
    if(isset($_POST['age'])) $age= getforminput($_POST['age']);
    else $age='';
    if(isset($_POST['city'])) $city= getforminput($_POST['city']);
    else $city='';
    if(isset($_POST['clanhistory'])) $clanhistory= getforminput($_POST['clanhistory']);
    else $clanhistory='';
    if(isset($_POST['info'])) $info= getforminput($_POST['info']);
    else $info='';

		eval ("\$joinus_notloggedin = \"".gettemplate("joinus_notloggedin")."\";");
		echo $joinus_notloggedin;
	}
}
?>