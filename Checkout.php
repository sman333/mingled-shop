<? require "Admin/ConfigDb.php"; require "Admin/FormFields.php";

require "razorpay/Razorpay.php";
use Razorpay\Api\Api;
$key_id = "rzp_live_PsOD0bRj0ZjJ3I";
$secret = "VuNz0vb1MdvuuQ80coSxWAib";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $respFlds = ["razorpay_payment_id", "razorpay_order_id", "razorpay_signature"];
    $fldEmpty = false;
	foreach ($respFlds as $f) if (empty (trim ($_POST[$f]??""))) $fldEmpty = true;
    if ($fldEmpty) {
		$sql = "SELECT razor_order_id FROM Odr WHERE odr_id=".($_SESSION["odr_id"]??0);
		$rz_oId = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["razor_order_id"]??0;
        if ($rz_oId) {
        	$api = new Api ($key_id, $secret);
            $res =  $api->order->fetch ($rz_oId);
            if ($res["status"] == "paid" && 0 < $res["amount"] && $res["amount"] == $res["amount_paid"]) {
			    echo "<script> alert (`Payment is successful. Your order has been confirmed.`); location=`$PATH", "Account/".$odr["odr_id"]."`; </script>";
			    include "sendOdrEmail.php";
			    include "Admin/sendNotif.php";
                $res = $res->payments();
        		$sql = "UPDATE Odr SET ".(($res["items"][0]["id"]??0) ? "razorpay_payment_id='".$res["items"][0]["id"]."',":"")." status='$PAID', modifyAt='$tdy $now' WHERE odr_id=".($_SESSION["odr_id"]??0);
        		mysqli_query ($conn, $sql);
            }
        }
    }else {
		$sql = "";
		foreach ($respFlds as $f) $sql .= "$f='".($_POST[$f]??"")."',";
		$sql .= "modifyAt='$tdy $now'";
// 		$sql = "UPDATE Odr SET $sql WHERE razor_order_id=".($_POST["razorpay_order_id"]??0);
		$sql = "UPDATE Odr SET $sql WHERE odr_id=".($_SESSION["odr_id"]??0);
		if (mysqli_query ($conn, $sql)) {
// 			$sql = "SELECT * FROM Odr WHERE razor_order_id=".($_POST["razorpay_order_id"]??0);
			$sql = "SELECT * FROM Odr WHERE odr_id=".($_SESSION["odr_id"]??0);
			$odr = mysqli_fetch_assoc (mysqli_query ($conn, $sql))??[];
			if (hash_hmac ("sha256", ($odr["razor_order_id"]??"")."|".($odr["razorpay_payment_id"]??""), $secret) == ($odr["razorpay_signature"]??"")) {
//				$sql = "UPDATE Odr SET status='$PAID' WHERE razor_order_id=".($_POST["razorpay_order_id"]??0);
				$sql = "UPDATE Odr SET status='$PAID' WHERE odr_id=".($_SESSION["odr_id"]??0);
				if (mysqli_query ($conn, $sql)) {
				    echo "<script> alert (`Payment is successful. Your order has been confirmed.`); location=`$PATH", "Account/".$odr["odr_id"]."`; </script>";
				    include "sendOdrEmail.php";
				    include "Admin/sendNotif.php";
				}
			}else {
//				$sql = "UPDATE Odr SET status='$PAY_ERROR' WHERE razor_order_id=".($_POST["razorpay_order_id"]??0);
				$sql = "UPDATE Odr SET status='$PAY_ERROR' WHERE odr_id=".($_SESSION["odr_id"]??0);
				mysqli_query ($conn, $sql);
				echo "<script> alert (`Payment verification failed! Please contact Customer Support.`); </script>";
			}
		}
    }
}else {
    $_SESSION["odr_id"] = ($_SESSION["odr_id"]??($_GET["odrId"]??0));
    if (!$_SESSION["odr_id"]) {
// add hacker slowdown code - track session or ip and add compounding delay
        exit ("<script> alert (`Invalid Order Id!`); location=`$PATH"."Shop`; </script>");
    }
	$sql = "SELECT * FROM Odr WHERE odr_id=".($_SESSION["odr_id"]??0);
	$odr = mysqli_fetch_assoc (mysqli_query ($conn, $sql))??[];
?>
<p class="py-2">
	<a href="<?=$PATH;?>Delivery" class="w3-text-black p-2 py-md-4"> <i class="fa fa-chevron-left w3-small"> </i> Delivery </a>
</p>
<div class="w3-light-gray w3-border-top w3-border-bottom">
	<p class="m-0 p-3" onclick="$(this).find(`.fa`).toggleClass(`fa-caret-down fa-caret-up`).parent().parent().next().slideToggle();"> Order Summary (<?=$_SESSION["itmCnt"]??"";?>) <span class="w3-right"> Rs. <?=($_SESSION["tot"]??0) + ($_SESSION["shipAmt"]??0);?> <i class="fa fa-caret-down"> </i> </span> </p>
	<div class="w3-small collapse px-3">
		<div class="w3-row -padding">
<?
    foreach (json_decode ($_SESSION["CART"]??"[]", true)??[] as $itm) {
		echo "<div class='w3-half'>";
		echo 	"<div class='w3-row w3-white w3-round-large p-2'>";
        echo    	"<i class='w3-col s4 m3 l2'> <img src='$PATH", "images/products/", $pt_ct[$itm["id"]], "_", $itm["id"], ".jpeg' class='w3-image w3-round'/> </i>";
        echo        "<div class='w3-col s8 m9 l10 pl-3 pr-1'>";
        echo           	"<p class='w3-tiny w3-text-gray m-0'>", $FormFields["Pdt"]["select"]["catId"][$pt_ct[$itm["id"]]], "</p>";
        echo           	"<p>", $pt_nm[$itm["id"]], "</p>";
        echo           	"<p class='m-0'> <i class='w3-tiny w3-text-gray'> Qty </i> <br>", $itm["qty"], "</p>";
        echo           	"<p class='w3-right-align itmTot'> Rs. ", $pt_pr[$itm["id"]] * $itm["qty"], "</p>";
        echo        "</div>";
		echo 	"</div>";
		echo "</div>";
	}
?>
		</div>
		<br>
		<p class="px-3"> Shipping (<?=($_SESSION["shipAmt"]??0) == 50 ? "In" : "Out";?>side Kerala) <span class="w3-right"> Rs. <?=$_SESSION["shipAmt"]??0;?> </span> </p>
		<p class="px-3"> Subtotal <span class="w3-right"> Rs. <?=($_SESSION["tot"]??0) + ($_SESSION["shipAmt"]??0);?> </span> </p>
		<!-- <p class="pl-2 pr-3"> Tax GST @ 18 % <span class="w3-right"> Rs. <?=(($_SESSION["tot"]??0) + ($_SESSION["shipAmt"]??0)) * 0.18;?> </span> </p> -->
		<b class="w3-block w3-border-top w3-border-gray p-3"> Total <span class="w3-right"> Rs. <?=($_SESSION["tot"]??0) + ($_SESSION["shipAmt"]??0);?> </span> </b>
	</div>
</div>
<br>
<div class="w3-light-gray w3-border-top w3-border-bottom">
	<p class="m-0 p-3" onclick="$(this).find(`.fa`).toggleClass(`fa-caret-down fa-caret-up`).parent().next().slideToggle();"> Delivery Address <i class="w3-right fa fa-caret-down"> </i> </p>
	<div class="collapse px-3">
		<div class="w3-small w3-white w3-round-large py-2">
			<p class="mb-2 px-3">
<?=$odr["cusName"]??"", "<br>", $odr["mob"]??"", ($odr["mob2"]??0) ? ", ":"", $odr["mob2"]??"";?>
			</p>
			<p class="w3-border-top m-0 pt-2 px-3">
<?=$odr["street1"]??"", ($odr["street2"]??0) ? "<br>":"", $odr["street2"]??"", ($odr["landmark"]??0) ? "<br>":"", $odr["landmark"]??"", "<br>", $odr["city"]??"", " ", $odr["pincode"]??"", "<br>", $odr["state"]??"";?>
			</p>
		</div>
		<p> </p>
	</div>
</div>
<div class="p-4 p-md-5">
<?
    $goToPay = true;
	$rz_oId = $odr["razor_order_id"]??0;
    if ($rz_oId) {
    	$api = new Api ($key_id, $secret);
        $res =  $api->order->fetch ($rz_oId);
        if ($res["status"] == "paid" && 0 < $res["amount"] && $res["amount"] == $res["amount_paid"]) {
		    include "sendOdrEmail.php";
		    include "Admin/sendNotif.php";
            $res = $res->payments();
    		$sql = "UPDATE Odr SET ".(($res["items"][0]["id"]??0) ? "razorpay_payment_id='".$res["items"][0]["id"]."',":"")." status='$PAID', modifyAt='$tdy $now' WHERE odr_id=".($_SESSION["odr_id"]??0);
    		mysqli_query ($conn, $sql);
?>		    
	<p class="w3-serif py-3 SuccessResponse"> Payment Received. <br> Your order has been confirmed. </p>
	<a href="<?=$PATH;?>Account/<?=$odr["odr_id"];?>" class="w3-button w3-round w3-hover-black px-md-5"> View in Account <i class="fa fa-arrow-right"> </i> </a>
    <script> alert (`Payment is successful. Your order has been confirmed.`); </script>
</div>
<?
            $goToPay = false;
        }
    }
    if ($goToPay) {
?>
<style>
	label small { white-space: wrap; }
	.razorpay-payment-button {
		background-color: #02042c;
		background-image: url("<?=$PATH;?>images/razorpay_logo.png");
		background-position: center;		
		background-repeat: no-repeat;
		background-size: auto 61.8%;
		border-radius: 10px;
		border: none;
		height: 50px;
		width: 100%;
	}
	.razorpay-payment-button:hover { background-color: darkblue; }
</style>
<div class="p-4 p-md-5">
	<h4 class="w3-serif py-3"> Payment Options </h4>
<?
        if ($odr["amount"]??0) {
        	$api = new Api ($key_id, $secret);
        	$rp_order = $api->order->create (["amount" => ($odr["amount"]*100), "currency" => "INR"]);
        	$sql = "UPDATE Odr SET razor_order_id='".$rp_order["id"]."' WHERE status='$ACTIVE' AND odr_id=".$odr["odr_id"];
        	if (mysqli_query ($conn, $sql)) {
?>
	<form action="https://mingled.in/Checkout/<?=$odr["odr_id"];?>" method="POST" class="w3-center">
		<script src="https://checkout.razorpay.com/v1/checkout.js"
			data-key="<?=$key_id;?>"
			data-amount="<?=$rp_order["amount"]*100;?>"
			data-currency="INR"
			data-order_id="<?=$rp_order["id"];?>"
			data-name="<?=$regName;?> Shop"
			data-description="odrId_<?=$odr["odr_id"];?>"
			data-image="https://mingled.in/images/logo.jpeg"
			data-prefill.name="<?=$odr["cusName"];?>"
			data-prefill.email="<?=$odr["email"];?>"
			data-prefill.contact="<?=$odr["mob"];?>"
			data-theme.color="#8B4513"
			data-buttontext=""
		></script>
	</form>
	<br>
<?
        	}
    	}else echo "<p class='FailedResponse'> Amount Rs.", $odr["amount"]??0, ". No payment options available. </p>"
?>
	<!-- <form method="POST" class="w3-row mx-auto checkout" style="max-width:1000px;"> -->
<?
	// foreach ($FormFields["Odr"]["pgList"] as $pg => $lbl) {
	// 	echo "<p class='w3-half m-0 px-1'>";
	// 	echo 	"<label class='w3-button w3-block w3-border w3-round-xlarge w3-hover-border-cyan w3-hover-pale-blue p-3", ($odr["pg"]??"") == $pg ? " w3-pale-green w3-border-green ":"", "'>";
	// 	echo 		"<input type='radio' name='pg' value='$pg'", ($odr["pg"]??"") == $pg ? " checked ":"", " onchange='form.submit()'/>";
	// 	echo 		$lbl;
	// 	echo 	"</label>";
	// 	echo "</p>";
	// }
?>
	<!-- </form> -->
</div>
<script>
	// $(document).ready (() => {
		// $(`form`).submit (function() {
		// 	event.preventDefault();
		// 	let formData = new FormData (this);
		// 	postFormData (`Checkout.php`, formData);
		// });
    // });
</script>
<?
    }
}
?>

