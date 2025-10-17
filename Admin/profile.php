<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";
$fields = $FormFields["Usr"]["fields"];
foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields["keys"], $vals);
$permFields = $FormFields["Usr_Prm"]["fields"];
foreach ($permFields as $key => $vals) $permFields[$key] = array_combine ($FormFields["keys"], $vals);
$dvcFlds = ["", 2];
$ntfFlds = ["dvcName"   => [4,  "text",     "Device Name"],
            "dvcTyp"    => [4,  "text",     "Device Type"],
            "subInfo"   => [" d-none", "hidden", ""]];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");

    if (($_POST["subInfo"]??0) || ($_POST["subInfo2"]??0) || ($_POST["dvcName"]??0) || ($_POST["dvcName2"]??0)) {
		$sql = "";
        foreach ($dvcFlds as $i) foreach ($ntfFlds as $f => $x) $sql .= "$f$i='".trim ($_POST[$f.$i]??"")."',";
		$sql .= "modifyAt='$tdy $now',modifyBy='".$_SESSION["username"]."'";
		$sql = "UPDATE Usr SET $sql WHERE usr_id='".$_POST["usr_id"]."'";
    	if (mysqli_query ($conn, $sql)) echo "<p class='SuccessResponse'> Notification settings saved. </p>";
    	else echo "<p class='FailedResponse'> Could not save notification settings. </p>";
    }
    if ($_POST["theme"]??0) {
        $sql = "UPDATE Usr SET theme=".((int) !($_POST["theme"]))." WHERE usr_id='".$usr["usr_id"]."'";
        if (mysqli_query ($conn, $sql)) echo "<p class='SuccessResponse'> Theme preference has been saved. </p>";
        else echo "<p class='FailedResponse'> Could not save theme preference. </p>";
    }
    if ($_POST["pass1"]??0) {
        $msg = false;
        $_POST["pass1"] = str_replace (" ", "", $_POST["pass1"]);
        if ($_POST["pass1"] !== $_POST["pass2"])    $msg = "Passwords do not match.";
        if (strchr ($_POST["pass1"], " "))          $msg = "Do not use space in Password.";
        if (strlen ($_POST["pass1"]) < 8)           $msg = "Password must be atleast 8 characters.";
        
        if ($msg) exit ("<p class='FailedResponse'> ".$msg." </p>");
        $p = crt ($usr, $_POST["pass1"]);
        $sql = "UPDATE Usr SET password='$p',resetOn='$tdy $now' WHERE usr_id='".$usr["usr_id"]."'";

        if (mysqli_query ($conn, $sql)) {
            echo "<p class='SuccessResponse'> New Password has been set. </p>";
            if ($usr["email"]??0) {
            	include 'mail/mailsend.php';
            	$mailSend = new MailSend;
            	$sub = "Password Reset Successful";
            	$msg = "Your <b> Account Password </b> has been reset.";
                $mailSend->send_Mail ($usr["email"], $msg, $sub);
            }
        }else echo "<p class='FailedResponse'> Could not reset Password. </p>";
    }
}else {
?>
<style> form p span { color: red; font-weight: bold; } </style>
<div class="sticky-top" id="statusBar" style="top:40px;"> </div>

<div class="w3-content w3-card w3-border w3-border-blue w3-topbar w3-round-xxlarge w3-content w3-{{THEME ? 'white' : 'black'}} px-5 py-4" style="max-width:700px;">
    <h6 class="w3-center"> User Profile </h6> <br>
<? foreach ($fields as $name => $attr) { ?>
    <p class="w3-row"> <i class="w3-col s4 m3 w3-text-gray w3-right-align" style="white-space:nowrap;"> <?=$attr["label"];?> &nbsp; : </i> <span class="w3-col s8 m9"> <b> &nbsp; <?=$usr[$name];?> </b> </span> </p>
<? } ?>
</div>
<br>
<style>
	p.custom-control.custom-switch:has(input.custom-control-input[type='checkbox']:checked) { background-color: hsl(150,100%,{{THEME ? '70' : '10'}}%); }
	p.custom-control.custom-switch:has(input.custom-control-input[type='checkbox']) { background-color: hsl(0,100%,{{THEME ? '80' : '10'}}%); }
	.custom-control-label { font-weight: bold !important; }
</style>
<form class="w3-content w3-card w3-border w3-border-brown w3-topbar w3-round-xxlarge px-5 py-4 ntf" style="max-width:700px;">
    <h6 class="w3-center"> Notification Settings </h6>
    <i class="w3-right w3-btn w3-text-amber w3-border w3-border-amber w3-hover-amber fa fa-bell circ p-2 testNtf" title="Send Test Notification"> </i>
    <br>
    <p class="console"> </p>
    <input type="hidden" name="usr_id" value="<?=$usr["usr_id"];?>" required />
    <div class="w3-row">
<?
foreach ($dvcFlds as $i) {
    foreach ($ntfFlds as $name => $attr) {
        echo "<p class='w3-col m6 l", $attr[0], " mb-0 p-1'> <i class='w3-block w3-small pl-2'>", $attr[2], " $i </i>";
        echo    "<input type='", $attr[1], "' class='w3-input' name='$name$i' value='", $usr[$name.$i]??"", "'/>";
        echo "</p>";
    }
    echo "<p class='w3-col l4 w3-border w3-border-brown w3-left-align custom-control custom-switch badge-pill btn pt-3' id='ntf$i", "'>";
    echo    "<input type='checkbox' class='custom-control-input' id='chk_ntf$i' name='ntf$i' ", ($usr["subInfo$i"]??0) ? "checked":"", "/>";
    echo    "<label class='w3-text-", $usr["theme"] ? "brown" :"aqua", " custom-control-label btn pb-3 pt-0 ml-5 px-4' for='chk_ntf$i'> Notifications </label>";
    echo "</p>";
}
?>
    </div>
</form>
<br>
<form class="w3-content w3-card w3-border w3-border-red w3-topbar w3-round-xxlarge px-5 py-4 pw" style="max-width:700px;">
    <h6 class="w3-center"> Reset Password </h6> <br>
    <p> <i> Password must be atleast 8 characters. </i> </p>
    <div class="w3-row-padding" ng-init="SHOW_PASS=false" >
        <p class="w3-col m5">
            New Password <span>*</span>
            <input type="{{SHOW_PASS ? 'text' : 'password'}}" minlength="8" class="w3-input" name="pass1" id="pass1" required />
        </p>
        <i class="w3-right w3-large fa fa-eye{{SHOW_PASS ? '-slash':''}}" ng-click="SHOW_PASS=!SHOW_PASS"> </i>
        <p class="w3-col m5">
            Confirm Password <span>*</span>
            <input type="{{SHOW_PASS ? 'text' : 'password'}}" minlength="8" class="w3-input" name="pass2" id="pass2" required />
        </p>
        <p class="w3-col m5 w3-text-red font-weight-bold" id="passStatus"> </p>
    </div>
    <b class="w3-center w3-block"> <button type="submit" class="w3-btn w3-wide w3-orange w3-hover-red w3-round-xxlarge px-5"> SAVE </button> </b>
</form>
<br>

<script>
    const consoleDiv = $(`.console`);
    navigator.serviceWorker.register (`sw.js`);
	$(document).ready(() => {
	    $(`form.pw`).submit (function() {
			event.preventDefault();
			if (!chkIp()) return;
			var formData = new FormData (this);
			postFormData (`profile.php`, formData);
	    });
		$(`.custom-switch`).click (function() {
			let chk = $(`#chk_${this.id}`);
			chk[0].checked = !chk[0].checked;
		});
		$(`form.ntf :checkbox`).change (function() {
			if (this.checked) enNtf (this.name.replace (`ntf`, ``));
			else dsNtf (this.name.replace (`ntf`, ``));
		});
	    $(`form.ntf input:not(:checkbox)`).change (function() {
			var formData = new FormData ($(`form.ntf`)[0]);
			postFormData (`profile.php`, formData);
	    });
	    $(`.testNtf`).click (function() {
			var formData = new FormData();
			formData.append (`testNtf`, true);
			formData.append (`usr_id`, <?=$usr["usr_id"];?>);
			postFormData (`sendNotif.php`, formData);
	    });
	});
	function chkIp() {
        var msg = ``;
        if ($(`#pass1`).val().length < 8) msg = `Password must be atleast 8 characters.`;
        if ($(`#pass1`).val().match (/\s/)??0) msg = `Do not use space in Password.`;
        if ($(`#pass1`).val() != $(`#pass2`).val()) msg = `Passwords do not match.`;
        $(`#passStatus`).text (msg);
        return (msg == ``);
	}
    function enNtf (id) {
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
    					$(`input[name='subInfo${id}']`).val (JSON.stringify (pushSubscription)).change();
                        console.log (JSON.stringify (pushSubscription));
                        // consoleDiv.html (JSON.stringify (pushSubscription));
    				});
    			});
    	});
    }
    function dsNtf (id) {
		navigator.serviceWorker.ready.then (swReg => {
			swReg.pushManager.getSubscription().then (sub => {
			    sub.unsubscribe().then (resp => {
			        $(`input[name='subInfo${id}']`).val (``).change();
                    console.log (resp);
                    // consoleDiv.html (resp);
		        });
	        });
		});
    }
</script>
<?
}
?>

