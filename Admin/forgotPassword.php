<? require "sysInfo.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 	array_walk_recursive ($_POST, "validate");
    require "ConfigDb.php";

	$u = $_POST["username"];
	$tP = mt_rand (10000000, 99999999);
	
	$sql = "SELECT email FROM Usr WHERE status='ACTIVE' AND BINARY (username='$u' OR email='$u')";
	$res = mysqli_query ($conn, $sql);
	if (mysqli_num_rows ($res) == 0) exit ("<script> alert('Invalid username.'); </script>");

	$sql = "UPDATE Usr SET otp='$tP',resetOn='$tdy $now' WHERE BINARY (username='$u' OR email='$u')";
	if (!mysqli_query ($conn, $sql)) exit ("<script> alert('Could not reset password. Please try again.'); </script>");

	$to = mysqli_fetch_assoc ($res)["email"]??0;
	if (!$to) exit ("<script> alert('No email address found. Please contact System Admin.'); </script>");

	$sub = "OTP";
	$msg = "<h3> Reset Password</h3> <br>
		<p> Temporary password: <b> $tP </b> </p> <br>
		<p> <i> Note: This temporary password is only valid for <u> 15 minutes </u> <i>. </p>";

	include 'PHPMailer/mailsend.php';
	$mailSend = new MailSend;

	if ($mailSend->send_Mail ($to, $msg, $sub)) $msg = "A temporary password has been sent to your email. It is valid for 15 minutes.";
	else $msg = "Failed to send email.\n Mailer error :\n" . $mail->ErrorInfo;

	echo "<script> alert('" . $msg . "'); </script>";
}
?>

