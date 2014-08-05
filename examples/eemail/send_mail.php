<?php
/**

ExEngine Mailer Class Example
=============================

ExEngine Mailer Class can send mail using:

	Basic:
	- PHP mail() (Plain and HTML)
	- PEAR´s Mail and Mail_Mime (SMTP->SSL, Plain, HTML and Attachements)

	Advanced:
	- PHPMailer (SMTP->SSL, Plain, HTML and Attachements)
	- SwiftMailer (SMTP->SSL/TLS, Plain, HTML and Attachements) -> Recommended method, will be updated with more functions.

This example will use SwiftMailer as the recommended method to send emails.

NOTE: You need to install SwiftMailer in the /eefx/extended/core/swiftmailer/5.0.3/

Version 5.0.3 is supported, other versions (older) may fail.

*/

error_reporting(E_ALL ^ E_NOTICE); 
ini_set("display_errors", 1); 

include_once( "../../ee.php" ); // load exengine.

$ee = new \ExEngine\Core(["SilentMode"=>true]); // initiate exengine.

//if is not loaded Mailer Class (in MVCOnly mode for example) you can load anytime
// $ee->libLoadRes("mail");

$mail = new eemail();
$mail->SMTP_Cfg_Array = eemail::createSMTPCfgArray(
"smtp_server.com", 25, //smtp server host and port
"smtp_username", 
"smtp_password", 
true,  // SMTP Authentication required?
"tls"); //encryption: ssl or tls

$mail->FromOnlyAddress = "smtp_username@mydomain.com";
$mail->FromName ="No Reply"; // optional

$mail->To = "gchiappe@outlook.com.pe";
$mail->ToName = "Giancarlo Chiappe"; // optional

$mail->Subject = "ExEngine Mailer Test";
$mail->Message = "<b> I Love PHP Coding ! </b>"; //html body (it can also be plain text).
//$mail->MessageTextOnly = "HTML is not supported by your email client."; // alternate plain text body.

// Add attachment (you add more if you want)
//$mail->addAttachment("/path/to/attachment.ext");

if (!$mail->send_swiftmailer_smtp())
	print $mail->LastError; // print last error if sending fails.

?>