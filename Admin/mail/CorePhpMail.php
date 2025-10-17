<?
function sendMail ($to, $sub, $msg) {
    $headers = "MIME-Version: 1.0"."\r\n".
    			"Content-type: text/html; charset=UTF-8"."\r\n".
    			"From: accounts@mingled.in"."\r\n".
    			"Reply-To: info@mingled.in"."\r\n".
    			"Bcc: sumanvishwagroup@gmail.com"."\r\n".
    			"X-Mailer: PHP/".phpversion();

    if (mail ($to, $sub, $msg, $headers)) return true;
    else {
        echo "PHP Mail error.";
        return false;
    }
}
?>

