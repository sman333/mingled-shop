<?
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require "PHPMailer/src/Exception.php";
require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";

function mailSend ($to, $sub, $msg) {
    $from = "accounts@mingled.in";
    $replyTo = "gayatrivm00@gmail.com";
    try {
        $mail = new PHPMailer (true);
        // $mail->SMTPDebug    = SMTP::DEBUG_SERVER;
        $mail->SMTPDebug    = false;
        $mail->isSMTP();
        $mail->Host         = "mail.mingled.in";
        $mail->SMTPAuth     = true;
        $mail->Username     = $from;
        $mail->Password     = "bPIl[5;tD_X{]OT*";
        $mail->SMTPSecure   = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port         = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        // $mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS;
        // $mail->Port         = 587;
        $mail->setFrom ($from, "Mingled Shop");
        $mail->addAddress ($to);
        $mail->addReplyTo ($replyTo, "Mingled_Admin");
        $mail->addBCC ("sumanvishwagroup@gmail.com");
        $mail->Subject      = $sub;
        $mail->AltBody      = $sub;
        $mail->isHTML (true);
        $mail->Body         = $msg;
    	$mail->MsgHTML ($msg);

        $mail->send();
        echo "Email has been sent";

    }catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
    return true;
}
?>

