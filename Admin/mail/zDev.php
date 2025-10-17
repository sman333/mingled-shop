<?
include 'mailsend.php';
$mailSend = new MailSend;

$to = 'roysman333@gmail.com';
$sub = "Subject Line";
$msg = "<i>This is the HTML message body <b>in bold!</b></i>";

$mailSend->send_Mail ($to, $msg, $sub);
?>
