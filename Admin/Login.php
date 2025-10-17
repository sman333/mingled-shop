<? require "sysInfo.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 	array_walk_recursive ($_POST, "validate");
    require "ConfigDb.php";

	$resetPassword = false;
	$usertype = "";
	if (strlen (str_replace (" ", "", $_POST["password"]??"")) < 8) echo "<script> alert (`Invalid Password !`); </script>";
	else {
		$sql = "SELECT * FROM Usr WHERE BINARY username='".$_POST["username"]."'";
		$usr = mysqli_fetch_assoc (mysqli_query ($conn, $sql));
		if ($usr) {
			$sql = "SELECT * FROM Usr WHERE BINARY username='".$_POST["username"]."' AND (password='".crt ($usr, $_POST["password"])."' OR otp='".$_POST["password"]."')";
			$res = mysqli_query ($conn, $sql);

			if (mysqli_num_rows ($res) > 0) {
				$res = mysqli_fetch_assoc ($res);
				$usertype = $res["usertype"]??"";
				if (($res["otp"]??"") == $_POST["password"]) {
		
					$sql = "UPDATE Usr SET otp='".sha1 (mt_rand())."' WHERE username='".$_POST["username"]."'";
					mysqli_query ($conn, $sql);

					if (15 * 60 < time() - strtotime ($res["resetOn"])){
						echo "<script> alert (`OTP has expired. Please reset password again.`); </script>";
						$usertype = "";
					}else $resetPassword = true;
				}
			}else {
				echo "<script> alert (`Username and Password do not match`); </script>";
				$res = [];
			}
		}else echo "<script> alert (`Invalid Username!`); </script>";
	}

	session_unset();
	session_destroy();
	session_start();
	
	$_SESSION["usertype"] = $usertype;
	$_SESSION["username"] = $_POST["username"];
	$_SESSION["inDateTime"] = "$tdy $now";
	$_SESSION["timeout"] = time() + ($res["sesTimeout"]??0);
	
	$sql = "INSERT INTO LoginHist(
				usertype,
				username,
				ip,
				sessionId,
				inDateTime
			)VALUES(
				'$usertype',
				'" . $_POST["username"] . "',
				'" . $_SERVER["REMOTE_ADDR"] . "',
				'" . session_id() . "',
				'" . $_SESSION["inDateTime"] . "'
			)";
	mysqli_query ($conn, $sql);

	if (strlen ($usertype)) exit ("<script> location=".($resetPassword ? "`profile`" : "R_D")."; </script>");
}else {
?>
<!DOCTYPE html>
<html lang="en" style="scroll-behavior:smooth;">
<title> <?=$APP_NAME;?> Log In </title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?=$PATH;?>favicon.png"/>
<link rel="stylesheet" href="<?=$PATH;?>lib/w3.min.css"/>
<link rel="stylesheet" href="<?=$PATH;?>lib/bootstrap.min.css"/>
<link rel="stylesheet" href="<?=$PATH;?>lib/font-awesome-4.7.0/css/font-awesome.min.css"/>
<script src="<?=$PATH;?>lib/jquery-3.6.4.min.js"></script>
<script src="<?=$PATH;?>lib/angular.min.js"></script>
<? require "PostFormData.php"; ?>
<style>
	body { background-color: black; }
	.w3-spin-o { animation: w3-spin-o 2s infinite linear; }
	@keyframes w3-spin-o {
		0% { transform: rotate(359deg); }
		100% { transform: rotate(0deg) }
	}
	.sr { display: inline-block; }
	.sr1, .sr2, .sr3, .sr4, .sr5 {
		align-items: center;
		aspect-ratio: 1;
		border: .2em solid lightpink;
		border-radius: 50%;
		border-top: .2em solid transparent;
		display: flex;
		justify-content: center;
		margin: auto;
	}
	.sr1 {
		animation: spin 1s linear infinite;
		width: 2em;
	}
	.sr2 {
		animation: spin-o .6s linear infinite;
		width: 1.6em;
	}
	.sr3 {
		animation: spin .5s linear infinite;
		width: 1.2em;
	}
	.sr4 {
		animation: spin-o .4s linear infinite;
		width: .8em;
	}
	.sr5 {
		animation: spin .3s linear infinite;
		width: .4em;
	}
	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
	@keyframes spin-o {
		0% { transform: rotate(360deg); }
		100% { transform: rotate(0deg); }
	}
</style>
<body ng-app="" ng-init="PW_HIDE=true">
<!-- <div style="background-image: linear-gradient(purple, indigo); color:white; padding-top:1vh; height:100vh;"> -->
<div style="background: white;
			background-image: url('<?=$PATH;?>images/logo.jpeg');
			background-position: center;
			background-repeat: no-repeat;
			background-size: cover;
			color: white;
			min-height: max(95vh,95vw);
			padding-top: 10vh;">
	<div class="w3-content w3-round-xxlarge w3-animate-zoom sticky-top px-5 pb-4 pt-5" style="top:20vh; backdrop-filter:blur(5px); max-width:400px; background:rgba(0, 0, 0, 0.5);">
	    
		<!-- <div class="w3-row-paddingg w3-hover-text-amberr">
    		<img class="w3-col s6 img-fluid mx-auto " src="/images/logo.webp" width="100px" height="auto" />
    		<h1 class="w3-col s6 w3-center w3-serif" style="padding-top:25px; text-shadow:8px 8px 3px black;"> <?=$APP_NAME;?> </h1>
		</div> -->
		
		<form>
            <b>
                <!-- <p class="w3-center mb-1"> Username </p> -->
                <p class="w3-row w3-black w3-round-xxlarge badge-pill pr-4 mb-4">
                    <i class="w3-col s2 w3-xlarge fa fa-user p-2"> </i>
                    <input type="text" class="w3-input w3-col s10 w3-black" name="username" required placeholder="Username" value="<?=$_SESSION["username"]??""; ?>" />
                </p>
                <!-- <p class="w3-center mb-1"> Password </p> -->
                <p class="w3-row w3-black w3-round-xxlarge badge-pill pr-0 mb-4">
                    <i class="w3-col s2 w3-xlarge fa fa-key p-2"> </i>
					<input type="{{PW_HIDE ? 'password' : 'text'}}" class="w3-input w3-col s8 w3-black password" required placeholder="Password" />
					<span class="w3-col s1 w3-btn w3-round-xxlarge" ng-click="PW_HIDE=!PW_HIDE" onclick="$(`.password`)[0].name=Date.now();">
						<i class="fa fa-eye{{PW_HIDE ? '':'-slash'}}"> </i>
					</span>
                </p>
				<button type="submit" class="w3-btn w3-block w3-pink w3-serif badge-pill"> LOG IN <span class="pl-3" id="spinner"> </span> </button>
			</b>
			<br>
			<div id="statusBar"> </div>
			<a href="#" class="w3-block w3-center" onclick="forgotPassword()" style="color:red;"> <b> <i> Forgot Password </i> </b> </a>
		</form>
    </div>
    <div class="w3-bottom w3-black w3-center">
        <a class="w3-btn w3-text-deep-orange fa fa-phone" href="tel:<?=str_replace (" ", "", $wa2);?>"> <i class="w3-hide-small"> <?=$wa2;?> </i> </a>
        <a class="w3-btn w3-text-light-green fa fa-whatsapp" href="https://wa.me/<?=str_replace (" ", "", $wa);?>?text=Query%20From%20<?=$APP_NAME;?>%20Login%0A%0D"> <i class="w3-hide-small"> <?=$wa;?> </i> </a>
        <a class="w3-btn w3-text-amber fa fa-envelope" href="mailto:<?=$em;?>"> <i class="w3-hide-small"> <?=$em;?> </i> </a>
    </div>
</div>
<script>
	const S_B = $(`#statusBar`);
	const R_D = `<?=$PATH, "Admin/", $_GET["rd"]??"Home", ($_GET["arg"]??0) ? "/".$_GET["arg"]:"";?>`;
	$(document).ready (() => {
		$(`form`).submit (function() {
			event.preventDefault();
			let formData = new FormData (this);
			formData.append (`password`, $(`.password`).val());
			login (formData);
		});
	});
	function forgotPassword() {
		if ($(`[name='username']`).val() == ``) {
			$(`#loginSubmit`).click();
			return;
		}
		if (confirm (`A new temporary password will be sent to your registered email (username).`)) {
			var formData = new FormData();
			formData.append (`username`, $(`[name='username']`).val());
			postFormData (`forgotPassword.php`, formData);
		}
	}
	function login (formData) {
		$.ajax ({
			type: `POST`,
			url: `<?=$PATH;?>Admin/Login.php`,
			enctype: `multipart/form-data`,
			dataType: `text`,
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: () => {
				$(`#spinner`).html (`<b class="sr"><i class="sr1"><b class="sr2"><i class="sr3"><b class="sr4"><i class="sr5"></i></b></i></b></i></b>`);
				S_B.html (``);
			},
			success: resp => {
				S_B.html (resp);
				$(`#spinner`).html (``);
			},
			error: () => {
				S_B.html (`<p> Server error, please try again.`);
				$(`#spinner`).html (``);
			}
		});
	}
</script>
</body>
</html>
<? } ?>

