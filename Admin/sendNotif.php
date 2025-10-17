<?
require "WebPushPhp/autoload.php";
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

$usr_id = 2;
if (($_POST["testNtf"]??0) || (!($odr["cusName"]??0))) {
    require "ConfigDb.php";
    $odr = [];
    $odr["city"] = "Test Notification";
    $odr["cusName"] = "Test Notification";
    $odr["amount"] = 123.45;
    $usr_id = $_POST["usr_id"]??1;
}

if ($odr["cusName"]??0) {
    $sql = "SELECT subInfo,subInfo2 FROM Usr WHERE usr_id=$usr_id";
    $usr = mysqli_fetch_assoc (mysqli_query ($conn, $sql))??[];
    
    $ntf = '{"title":"'.$odr["city"].'",
            "body":"'.$odr["cusName"].' ₹'.$odr["amount"].'",
            "url":"/Admin/Login/Orders",
            "url2":"/Admin/Orders",
            "timestamp":"'.time().'"
        }';
    $auth = [
        "VAPID" => [
            "subject"       => "mailto:gayatrivm00@gmail.com",
            "publicKey"     => $pubKey,
            "privateKey"    => $priKey,
        ],
    ];
    
    if ($usr["subInfo"]??0) {
        $webPush = new WebPush ($auth);
        $report = $webPush->sendOneNotification (Subscription::create (json_decode (htmlspecialchars_decode ($usr["subInfo"]), true)), $ntf, ["TTL" => 3600]);
        // if ($_POST["testNtf"]??0) echo "<script> console.log (`", http_build_query ($report, "", ","), "`); </script>";
        if ($_POST["testNtf"]??0) echo "<script> console.log (`", http_build_query ($report), "`); </script>";
        else print_r ($report);
    }
    if ($usr["subInfo2"]??0) {
        $webPush = new WebPush ($auth);
        $report = $webPush->sendOneNotification (Subscription::create (json_decode (htmlspecialchars_decode ($usr["subInfo2"]), true)), $ntf, ["TTL" => 3600]);
        // if ($_POST["testNtf"]??0) echo "<script> console.log (`", http_build_query ($report, "", ","), "`); </script>";
        if ($_POST["testNtf"]??0) echo "<script> console.log (`", http_build_query ($report), "`); </script>";
        else print_r ($report);
    }
}
?>

