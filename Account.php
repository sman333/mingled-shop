<? require "Admin/ConfigDb.php"; require "Admin/FormFields.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST["LogOut"]??0) {
		session_unset();
		session_destroy();
		session_start();
		echo "<script> alert (`Logged Out.`); location=`/LogIn`; </script>";
	}
	if ($_POST["payNow"]??0) {
    	$sql = "SELECT * FROM Odr WHERE odr_id='".($_POST["payNow"]??"")."'";
    	$odr = mysqli_fetch_assoc (mysqli_query ($conn, $sql))??[];
	    if (!$odr) exit ("<script> alert (`Invalid Order Id!`); </script>");
	    
	    $_SESSION["odr_id"] = $_POST["payNow"];
	    $_SESSION["CART"] = htmlspecialchars_decode ($odr["itmsJson"]??"");
        $_SESSION["tot"] = $_SESSION["itmCnt"] = 0;
        foreach (json_decode ($_SESSION["CART"]??"[]", true)??[] as $itm) {
            $_SESSION["tot"] += $pt_pr[$itm["id"]] * $itm["qty"];
            $_SESSION["itmCnt"] += $itm["qty"];
        }
    	$_SESSION["shipAmt"] = 80;
    	if (in_array (substr ($odr["pincode"]??"", 0, 2), [67, 68, 69])) $_SESSION["shipAmt"] = 50;
		echo "<script> if (confirm (`Proceed to payment?`)) location=`/Cart`; </script>";
	}
}else {
    if (!($_SESSION["email"]??0)) exit ("<script> alert (`Invalid user credentials!`); location=`/LogIn`; </script>");
	$sql = "SELECT * FROM Odr WHERE email='".($_SESSION["email"]??0)."' LIMIT 1";
	$acc = mysqli_fetch_assoc (mysqli_query ($conn, $sql))??[];
?>
<p class="py-2">
	<a href="<?=$PATH;?>Shop" class="w3-text-black p-2 py-md-4"> <i class="fa fa-chevron-left w3-small"> </i> Shop </a>
</p>

<div class="p-3 px-lg-5">
	<i class="w3-serif w3-large"> Welcome, <?=$acc["cusName"]??"";?> </i>
	<p class="w3-right w3-small btn pr-0" onclick="logOut()"> Sign Out <i class="fa fa-sign-out"> </i> </p>
	<p class="w3-serif w3-xlarge"> Your Account </p>
	<br>
	<div class="w3-row w3-small w3-border-bottom">
<? 	foreach (["Orders", "Addresses"] as $x) echo "<p class='w3-col s6 w3-button w3-hover-white m-0 tabBtn tabBtn$x' onclick='showTab(`$x`)'> $x </p>"; ?>
	</div>
	<div class="px-2 p-md-4 tab tabOrders">
<?
	foreach ([
		"Confirmed" 		=> ["green", 	"check-circle-o", 		"'$PAID'"],
		"Payment Pending" 	=> ["red", 		"exclamation-circle", 	"'$ACTIVE'"],
		"Shipped" 			=> ["blue", 	"truck", 				"'$SHIPPED'"],
		"Delivered" 		=> ["indigo", 	"check", 				"'$DELIVERED'"],
	] as $txt => $x) {
		$sql = "SELECT * FROM Odr WHERE status IN (".$x[2].") AND email='".($_SESSION["email"]??0)."' ORDER BY odr_id DESC";
		$odrs = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
		if (!count ($odrs) || !strlen (trim ($odrs[0]["itmsJson"], "[]{},"))) continue;
		echo "<div class='pt-2'>";
		echo 	"<p class='w3-button w3-large w3-block w3-left-align w3-border w3-round-large w3-hover-white w3-hover-border-gray p-3 m-0 fa fa-caret-right' onclick='$(this).toggleClass(`fa-caret-right fa-caret-down`).next().slideToggle();'> <small> &nbsp; ", count ($odrs), "&nbsp; Order $txt </small> <i class='w3-right fa fa-", $x[1], " w3-text-", $x[0], "'> </i> </p>";
		echo 	"<div class='w3-row collapse py-1'>";
		foreach ($odrs as $odr) {
			echo "<div class='w3-col m6 l4 p-1'>";
			echo 	"<div class='w3-border w3-round-large w3-border-", ($_GET["odrId"]??0) == $odr["odr_id"] ? "green w3-bottombar" : "gray", " odrId_", $odr["odr_id"], "'>";
			echo 		"<div class='p-2 m-0'>";
			echo 			date_create ($odr["modifyAt"]??"")->format (" d M Y ");
			echo            "<p class='w3-right ", $odr["status"] == $ACTIVE ? "w3-button w3-border w3-hover-black py-1 px-4 mt-n1 payNow":"", "' data-odrid='", $odr["odr_id"], "'> <small> Rs. </small> ", $odr["amount"]??0, $odr["status"] == $ACTIVE ? "<i class='fa fa-arrow-right pl-2'> </i>":"", "</p>";
			echo 		"</div>";

			echo 		"<div class='w3-row w3-border-top p-1'>";
			foreach (json_decode (htmlspecialchars_decode ($odr["itmsJson"]??"")??"[]", true)??[] as $itm) {
				echo 		"<div class='w3-col pb-1'>";
				echo    		"<div class='w3-row'>";
				echo        		"<i class='w3-col s3 pr-2'> <img src='/images/products/", $pt_ct[$itm["id"]], "_", $itm["id"], ".jpeg' class='w3-image w3-round-large'/> </i>";
				echo        		"<div class='w3-col s9 w3-small '>";
				echo            		"<p class='m-0'> <b class='w3-tiny w3-text-gray'>", $FormFields["Pdt"]["select"]["catId"][$pt_ct[$itm["id"]]], "</b> <br>", $pt_nm[$itm["id"]], "</p>";
				echo            		" x ", $itm["qty"], "<p class='w3-right m-0'> Rs. ", $pt_pr[$itm["id"]] * $itm["qty"], "</p>";
				echo        		"</div>";
				echo    		"</div>";
				echo 		"</div>";
			}
			echo 		"</div>";

			echo 		"<p class='w3-small w3-border-top p-2 m-0'>";
			echo 			$odr["cusName"]??"",
							"<br>", $odr["mob"]??"", ($odr["mob2"]??0) ? ", ":"", $odr["mob2"]??"",
							"<br>", $odr["street1"]??"",
							($odr["street2"]??0) ? "<br>":"", $odr["street2"]??"",
							($odr["landmark"]??0) ? "<br>":"", $odr["landmark"]??"",
							"<br>", $odr["city"]??"", " ", $odr["pincode"]??"",
							($odr["state"]??0) ? "<br>":"", $odr["state"]??"";
			echo 		"</p>";
			echo 	"</div>";
			echo "</div>";
		}
		echo 	"</div>";
		echo "</div>";
	}
?>
	</div>
	<div class="px-2 p-md-4 tab tabAddresses">
<?
	$sql = "SELECT DISTINCT * FROM Odr WHERE email='".($_SESSION["email"]??0)."' ORDER BY odr_id DESC";
	$addrs = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
	echo "<div class='w3-row'>";
	foreach ($addrs as $i => $a) {
		echo "<div class='w3-col m6 l4 pt-2 px-1'>";
		echo 	"<p class='w3-small w3-white w3-border w3-round-large p-2 m-0'>";
		echo 		$a["cusName"]??"", "<span class='w3-right'>", $a["mob"]??"", "</span>";
		echo 		"<br>", $a["street1"]??"", ($a["street2"]??0) ? "<br>":"", $a["street2"]??"", ($a["landmark"]??0) ? "<br>":"", $a["landmark"]??"", "<br>", $a["city"]??"", " ", $a["pincode"]??"";
		echo 	"</p>";
		echo "</div>";
	}
	echo "</div>";
?>
	</div>
	<br> <br>
</div>
<script>
	const P_N = $(`.payNow`);
	const TABS = $(`.tab`);
	const TAB_BTNS = $(`.tabBtn`);
	$(document).ready (() => {
		P_N.click (function() {
			let formData = new FormData();
			formData.append (`payNow`, this.getAttribute (`data-odrid`));
			postFormData (`Account.php`, formData);
		});
		$(`.odrId_<?=$_GET["odrId"]??0;?>`).parent().parent().slideDown();
    });
	function logOut (x) {
		let formData = new FormData();
		formData.append (`LogOut`, true);
		postFormData (`Account.php`, formData);
	}
	function showTab (x) {
		TABS.hide();
		$(`.tab${x}`).slideDown();
		$(`.tabBtn`).removeClass (`w3-border-bottom w3-border-black w3-text-black w3-bold`).addClass (`w3-text-gray`);
		$(`.tabBtn${x}`).removeClass (`w3-text-gray`).addClass (`w3-border-bottom w3-border-black w3-text-black w3-bold`);
	}
	showTab (`Orders`);
</script>
<?
}
?>

