<?
function mailSend ($to, $sub, $msg) {
    $args = [
        "to"        => $to,
        "replyTo"   => "info@mingled.in",
        "subject"   => $sub,
        "body"      => $sub,
        "htmlBody"  => $msg,
    ];
    $curl = curl_init ("https://script.google.com/macros/s/AKfycbxtbADnC-c9LAEAyq_udkGP9-4IaiQJoGbUdicc3r3BJI99JG99T9V8v5vOKUTqFOrf/exec");
    $curlOps = [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $args,
    ];
    curl_setopt_array ($curl, $curlOps);
    $res = curl_exec ($curl);
    // echo curl_error ($curl);
    // echo $res;
    curl_close ($curl);
    return $res == 1;
}
?>

