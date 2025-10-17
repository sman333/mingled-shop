<?
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	require "Admin/ConfigDb.php";
	array_walk_recursive ($_POST, "validate");
	if ($_POST["email"]??0) {
		$sql = "SELECT * FROM Odr WHERE email='".$_POST["email"]."' ORDER BY odr_id DESC LIMIT 1";
		$res = mysqli_query ($conn, $sql);
		if (mysqli_num_rows ($res) == 0) {
			if ($_POST["cusName"]??0) {
				$sql = "INSERT INTO Odr(itmsJson,amount,cusName,email,createAt)
					VALUES('".($_SESSION["CART"]??"")."',".($_SESSION["tot"]??0).",'".($_POST["cusName"]??"")."','".($_POST["email"]??0)."','$tdy $now')";
				if (!mysqli_query ($conn, $sql)) exit ("<script> alert (`Could not send OTP. \n Please try again.`); </script>");
				$sql = "SELECT * FROM Odr WHERE email='".($_POST["email"]??0)."' ORDER BY odr_id DESC LIMIT 1";
				$res = mysqli_query ($conn, $sql);
			}else {
				$_SESSION["email"] = $_POST["email"]??"";
				exit ("<script> alert (`This email has not been registered. Please create an account to register this email.`); location=`Login#Create`; </script>");
			}
		}
		$odr = mysqli_fetch_assoc ($res)??[];
		$otp = mt_rand (1000, 9999);
		$sql = "UPDATE Odr SET otp='$otp' WHERE odr_id=".$odr["odr_id"];
		if (!mysqli_query ($conn, $sql)) exit ("<script> alert (`Could not generate OTP. Please contact Customer Support.`); </script>");
		
		$_SESSION["odr_id"] = $odr["odr_id"]??"";
		$to = $_SESSION["email"] = $odr["email"]??"";
		if (!$to) exit ("<script> alert (`No email address found. Please contact Customer Support.`); </script>");
		
		$modeTxt = "Sign";
		if (($_POST["redirectTo"]??0) == "Account") $modeTxt = "Log";
		$modeTxt2 = strtolower ($modeTxt);
		$modeTxt3 = join (" ", str_split (strtoupper ($modeTxt)));
		$sub = "Mingled Acc. $modeTxt In OTP - [$otp]";
		$msg = "<h2 style='color:darkslategray;'> Welcome back, ".($odr["cusName"]??"")." </h2>
                <p> Your one time password for $modeTxt2 in to Mingled Account is </p>
                <h1> <b style='background:dodgerblue; border-radius:10px; color:white; letter-spacing:10px; padding:5px 10px 5px 20px;'> $otp </b> </h1>
                <h1 style='padding:30px;'> O R </h1>
                <h2> <a href='https://mingled.in/$modeTxt"."In/$otp/' style='background:hotpink; border-radius:50px; color:white; padding:15px 50px;'> $modeTxt3 &nbsp; I N </a> </h2>";
		if ($_POST["cusName"]??0) {
			$_SESSION["cusName"] = $_POST["cusName"]??"";
			$sub = "Mingled Acc. Registration OTP - [$otp]";
    		$msg = "<h2 style='color:darkslategray;'> Welcome ".($_POST["cusName"]??"")." </h2>
                    <p> Your one time password for registration is </p>
                    <h1> <b style='background:dodgerblue; border-radius:10px; color:white; letter-spacing:10px; padding:5px 10px 5px 20px;'> $otp </b> </h1>
                    <h1 style='padding:30px;'> O R </h1>
                    <h2> <a href='https://mingled.in/LogIn/$otp/' style='background:hotpink; border-radius:50px; color:white; padding:15px 50px;'> Complete Registration </a> </h2>";
		}
		$msg = "<div style='background:lightgray; font-family:Arial,Helvetica,sans-serif;'>
                    <div style='background:white; margin:auto; max-width:400px; text-align:center;'>
                        <h1 style='background:hsl(30, 14%, 67%); padding:20px;'> MINGLED </h1>
                        <p style='background:lightblue; border-radius:35px; font-size:50px; height:70px; line-height:90px; margin:auto; width:72px;'>".($odr["cusName"][0]??"")."</p>
                        $msg
                        <p style='padding:40px;'> If you didn’t request to log in to your Mingled Account, you can safely ignore this email. </p>
                        <div style='background:black; padding:15px 25px;'>
                            <img src='https://mingled.in/images/logo.jpeg' style='float:left; width:40px;'/>
                            <p style='height:40px; margin:0; text-align:right;'>
                                <a target='_blank' style='color:white; line-height:20px; text-decoration:none;' href='https://mingled.in/Terms_and_Conditions'> Terms and Conditions </a>
                                <br>
                                <a target='_blank' style='color:white; line-height:20px; text-decoration:none;' href='https://mingled.in/Privacy_Policy'> Privacy Policy </a>
                            </p>
                        </div>
                    </div>
                </div>";

		require "Admin/mail/mailSend.php";
		if (mailSend ($to, $sub, $msg))
			$resp = "RESP.html (`<p> OTP has been sent. <br> <i> Please check your <b> Inbox </b> and <b> Spam </b> folders. </i> </p>
						<form class='resp' method='POST'>
							<label class='w3-block pb-3'> <i class='w3-small pl-2'> OTP </i> <input type='number' class='w3-input' name='otp'/> </label>
							<button type='submit' class='w3-button w3-black badge-pill px-5 py-2'> <b> Verify </b> </button>
						</form>`);
					$(`[name='otp']`).focus();
					$(`form.resp`).submit (function() {
						event.preventDefault();
						let formData = new FormData (this);
						formData.append (`redirectTo`, REDIRECT_TO);
						postFormData (`SignIn.php`, formData);
					});";
		else $resp = "alert (`Failed to send email.`);";
		exit ("<script> $resp </script>");
	}
	if ($_POST["otp"]??0) {
		$sql = "SELECT * FROM Odr WHERE email='".($_SESSION["email"]??0)."' AND otp='".($_POST["otp"]??"")."' ORDER BY odr_id DESC LIMIT 1";
		if (mysqli_num_rows (mysqli_query ($conn, $sql)) == 0) {
			echo "<script> alert (`Incorrect OTP.`); </script>";
			if ($_POST["directLink"]??0) echo "<div class='w3-center sticky-top py-2 pt-md-4' style='top:50px;'> <h6 class='w3-red py-2'> Incorrect OTP. </h6> </div>";
		}else {
			$_SESSION["signedIn"] = true;
			echo "<script> location=`$PATH", ($_POST["redirectTo"]??"LogIn"), "`; </script>";
		}
	}
}else {
	if ($_GET["otp"]??0) {
		$_POST["directLink"] = true;
		$_POST["otp"] = $_GET["otp"];
		$_POST["redirectTo"] = ($_GET["acc"]??0) ? "Account" : "Delivery";
		$_SERVER["REQUEST_METHOD"] = "POST";
		require "SignIn.php";
	}
?>
<div class="py-3 p-md-5">
<?
	if ($_GET["acc"]??0) echo "<a href='$PATH' class='p-2 py-md-4'> <i class='fa fa-chevron-left w3-small'> </i> Home </a>";
	else echo "<a href='$PATH"."Cart' class='w3-text-black p-2 py-md-4'> <i class='fa fa-chevron-left w3-small'> </i> Cart </a>";
?>
    <div class="w3-row">
<? if (($_SESSION["tot"]??0) && ($_SESSION["itmCnt"]??0)) { ?>
        <div class="w3-half px-2 p-md-4 px-lg-5 chkout">
			<div class="px-2 p-md-4 px-lg-5">
				<h4 class="w3-serif w3-center py-4"> Guest Account </h4>
				<div class="p-3">
                    <p class="py-2">
                        <a href="<?=$PATH;?>Delivery" class="w3-button w3-block w3-round w3-padding-16 w3-black"> <b> Delivery </b> <i class="fa fa-arrow-right"> </i> </a>
                    </p>
				</div>
				<hr class="w3-gray w3-hide-medium w3-hide-large">
				<br> <br> <br>
			</div>
        </div>
<? } ?>        
        <div class="w3-half px-2 p-md-4 px-lg-5">
			<div class="px-2 p-md-4 px-lg-5">
				<h4 class="w3-serif w3-center py-4"> Sign In </h4>
				<div class="p-3">
					<form class="static" method="POST">
						<label class="w3-block pb-3"> <i class="w3-small pl-2"> Email </i> <input type="email" class="w3-input" name="email" value="<?=$_SESSION["email"]??"";?>" required/> </label>
						<button type="submit" class="w3-button w3-black badge-pill px-5 py-2 getOtp"> <b> Get OTP </b> </button>
					</form>
					<div class="pt-3"> </div>
				</div>
				<h4 class="w3-serif w3-center py-4"> Create Account </h4>
				<div class="p-3">
					<form class="static" method="POST">
						<label class="w3-block pb-3"> <i class="w3-small pl-2"> Name </i> <input type="text" class="w3-input" name="cusName" value="<?=$_SESSION["cusName"]??"";?>" required/> </label>
						<label class="w3-block pb-3"> <i class="w3-small pl-2"> Email </i> <input type="email" class="w3-input" name="email" value="<?=$_SESSION["email"]??"";?>" required/> </label>
						<button type="submit" class="w3-button w3-black badge-pill px-5 py-2 getOtp"> <b> Create Account </b> </button>
					</form>
					<div class="pt-3"> </div>
				</div>
			</div>
        </div>
    </div>
</div>
<script>
	const GET_OTP = $(`:submit`);
	const REDIRECT_TO = `<?=($_GET["acc"]??0) ? "Account" : "Delivery";?>`;
	let RESP;
	$(document).ready (() => {
		$(`form.static`).submit (function() {
			event.preventDefault();
			let formData = new FormData (this);
			formData.append (`redirectTo`, REDIRECT_TO);			
			postFormData (`SignIn.php`, formData);
			$(this).next().html (``);
			RESP = $(this).next();
			GET_OTP.prop (`disabled`, true);
			setTimeout (() => GET_OTP.prop (`disabled`, false), 15000);
		});
		if (location.hash == `#Create`) $(`[name='cusName']`).focus();
    });
</script>
<?
}
?>

