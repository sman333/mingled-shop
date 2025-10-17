<?
// google fcm api keys (console.firebase.google.com)
$pubKey = "BO384V8VcuvD3RLqzl7mNR419WpsOnbV98dOh88yoSfpZvT3GVMxGiMbPPCeBkTzY1zeEE7WMNZ9ilrSbEftG5U";
$priKey = "dn5DmziCU9jIhvzmZGXmpupuibcncVci2MI-nrDSRwM";
// // (web-push-codelab.glitch.me)
// $pubKey = "BAEbBDf9UErhI8QSpaue9yaZJD9cBt4JZDlVEjD3TNTlxeD4YJVn8EGKARIFyPrfKdYlkhiuFLZ374oqNU5Nm8w";
// $priKey = "Qswylyi6eRzRnP_h_5onQG9SxFc8tHjsPfvui1NidH0";
// $pubKey = "BNeWKu3updjLXtAiYu8DF7DPqFk9xtHdf_7WOC1hiGlmbAhCI2iJHicg6ep7d6Ca1VPCHt-3Nf_Dnf-ctPWtXjg";
// $priKey = "fgSnaYLHOh4a1BjJikJFHfELxieB0QGNkZWxnaU8h8M";

$subInfo = '
{"endpoint":"https://fcm.googleapis.com/fcm/send/c5V6cxfaBUM:APA91bEhR6l4NM7-NGG6_I9gN6hp3afMtfukxq-_ncSb7SfszLkwpw0peXQSCsMBKwcKCqLx-lCpcTQWsPg3CSBIdjaGBVIIGcFvNIYLsGhXmzCu0SpICf0VcKFVxDnmy593zeJkCHfT","expirationTime":null,"keys":{"p256dh":"BKjsZ8pkU-q1OsUCYajPdGy-qQ89T4I9apzpegJf_eea8z8CYmtS-eqB3bUHJprwA4beMKYJ8vcy0dLFNkfsx-s","auth":"OSA-YU8s25osK6h61l4Xmg"}}
';
$ntf = '{"title":"3 New Orders",
        "body":"last cusName amount",
        "url":"/Admin/Login/Orders",
        "url2":"/Admin/Orders",
        "timestamp":"'.time().'"
    }';
$auth = [
    "VAPID" => [
        "subject"       => "mailto:roysman333@gmail.com",
        "publicKey"     => $pubKey,
        "privateKey"    => $priKey,
    ],
];

require "WebPushPhp/autoload.php";
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

$webPush = new WebPush ($auth);
$report = $webPush->sendOneNotification (Subscription::create (json_decode ($subInfo, true)), $ntf, ["TTL" => 3600]);

print_r ($report);



    // $ch = curl_init ("https://fcm.googleapis.com/fcm/send/fwra8YXGDQs:APA91bHY9K4cT9wLEQULJkHsnl5ekfYUUfLVbBAvQ4X4MujDFYOmFSdsf4cO8I07MHcmPKe06yYK6ULTSgzEgAW-0S_mYVtWGJp2ikDjVciFUrtSRrVctkNcexGaWK4rBFImPpxMm3tF");
    // curl_setopt ($ch, CURLOPT_POST, true);
    // curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt ($ch, CURLOPT_HTTPHEADER, [
    //     "Authorization: key=BNcv_BIcmomTxLPHb8KrzTMXAG3_lC7484g1Qabzp-Ej2Qt0JnQGzhLR4cwUIldivm6elnFj4uybJcw661IQMpI",
    //     "Content-Type: application/json"
    // ]);
    
        // "to"            => "https://fcm.googleapis.com/fcm/send/fwra8YXGDQs:APA91bHY9K4cT9wLEQULJkHsnl5ekfYUUfLVbBAvQ4X4MujDFYOmFSdsf4cO8I07MHcmPKe06yYK6ULTSgzEgAW-0S_mYVtWGJp2ikDjVciFUrtSRrVctkNcexGaWK4rBFImPpxMm3tF",
        // "to"            => "fwra8YXGDQs:APA91bHY9K4cT9wLEQULJkHsnl5ekfYUUfLVbBAvQ4X4MujDFYOmFSdsf4cO8I07MHcmPKe06yYK6ULTSgzEgAW-0S_mYVtWGJp2ikDjVciFUrtSRrVctkNcexGaWK4rBFImPpxMm3tF",
    // $data = json_encode ([
    //     "title"         => "3 New Order!",
    //     "body"          => $odr["cusName"]." ".$odr["amount"],
    //     "icon"          => "/favicon.png",
    //     "click_action"  => "https://mingled.in/Admin/Login/Orders"
    // ]);
    // curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
    // echo curl_exec ($ch);
    // curl_close ($ch);



?>
<!DOCTYPE html>
<html>
<head>
<title>Web Push Notifications in PHP</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
</head>
<body>
    <h1> Web Push Notification using PHP in a Browser </h1>
    <p class="console"> console </p>
    <button type="button" onclick="enNtf()"> Enable Notifications </button>
    <button type="button" onclick="dsNtf()"> Disable Notifications </button>
<script>
    const consoleDiv = $(`.console`);

    navigator.serviceWorker.register (`sw.js`);
    function enNtf() {
    	if (!(`Notification` in window)) {
    	    alert (`Web browser does not support Notification!`);
    	    return;
    	}
    	Notification.requestPermission().then (permission => {
    		if (permission === `granted`)
    			navigator.serviceWorker.ready.then (swReg => {
    				swReg.pushManager.subscribe ({
    					userVisibleOnly: true,
    					applicationServerKey: "<?=$pubKey;?>"
    				}).then (pushSubscription => {
    					console.log (JSON.stringify (pushSubscription));
    					consoleDiv.html (JSON.stringify (pushSubscription));
// postFormData ();
    				});
    			});
    	});
    }
    function dsNtf() {
		navigator.serviceWorker.ready.then (swReg => {
			swReg.pushManager.getSubscription().then (sub => {
			    sub.unsubscribe().then (resp => {
			        if (resp) {
// postFormData ();
			        }
			        console.log (resp);
					consoleDiv.html (resp);
		        });
	        });
		});
    }
</script>
</body>
</html>

