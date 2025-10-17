<? require "Admin/ConfigDb.php"; require "Admin/FormFields.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");
	$_SESSION["email"] = $_POST["email"]??"";
	$_POST["itmsJson"] = $_SESSION["CART"]??"";

	$_SESSION["shipAmt"] = 80;
	if (in_array (substr ($_POST["pincode"]??"", 0, 2), [67, 68, 69])) $_SESSION["shipAmt"] = 50;
	
$_SESSION["shipAmt"] = 0;
	
	$_POST["amount"] = ($_SESSION["tot"]??0) + ($_SESSION["shipAmt"]??0);

	$fs = $FormFields["Odr"]["fields"];
	unset ($fs["discount"]);
	$fs["amount"] = $fs["pg"] = $fs["itmsJson"] = "";

	if (empty ($_POST["odr_id"])) {
		$cols = $vals = "";
		foreach ($fs as $f => $x) {
			$cols .= "$f,";
			$vals .= "'".($_POST[$f]??"")."',";
		}
		$cols .= "createAt";
		$vals .= "'$tdy $now'";
		$sql = "INSERT INTO Odr($cols) VALUES($vals)";
	}else {
		$sql = "";
		foreach ($fs as $f => $x) $sql .= "$f='".($_POST[$f]??"")."',";
		$sql .= "modifyAt='$tdy $now'";
		$sql = "UPDATE Odr SET $sql WHERE odr_id=".$_POST["odr_id"];
	}
	if (mysqli_query ($conn, $sql)) {
		if (empty ($_POST["odr_id"])) {
			$sql = "SELECT odr_id FROM Odr ORDER BY odr_id DESC LIMIT 1";
			$_POST["odr_id"] = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["odr_id"]??0;
		}
		$_SESSION["odr_id"] = $_POST["odr_id"];
		echo "<p class='SuccessResponse'> Delivery details saved. </p> <script> location=`$PATH", (($_SESSION["tot"]??0) && ($_SESSION["itmCnt"]??0)) ? "Checkout/".$_SESSION["odr_id"]  : "Shop", "`; </script>";
	}
	else echo "<p class='FailedResponse'> Could not save delivery details. </p>";

}else {
	$fields = $FormFields["Odr"]["fields"];
	foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields["keys"], $vals);
	$sql = "SELECT * FROM Odr WHERE odr_id=".($_SESSION["odr_id"]??0);
	$odr = mysqli_fetch_assoc (mysqli_query ($conn, $sql))??[];
?>
<p class="py-2">
	<a href="<?=$PATH;?>Cart" class="w3-text-black p-2 py-md-4"> <i class="fa fa-chevron-left w3-small"> </i> Cart </a>
</p>
<div class="w3-light-gray w3-border-top w3-border-bottom">
	<p class="m-0 p-3" onclick="$(this).find(`.fa`).toggleClass(`fa-caret-down fa-caret-up`).parent().parent().next().slideToggle();"> Order Summary (<?=$_SESSION["itmCnt"]??"";?>) <span class="w3-right"> Rs. <?=($_SESSION["tot"]??0) + ($_SESSION["shipAmt"]??0);?> <i class="fa fa-caret-down"> </i> </span> </p>
	<div class="w3-small collapse px-3">
<?
    foreach (json_decode ($_SESSION["CART"]??"[]", true)??[] as $itm) {
		echo    "<div class='w3-row w3-white w3-round-large p-2'>";
        echo        "<i class='w3-col s4 m3 l2'> <img src='images/products/", $pt_ct[$itm["id"]], "_", $itm["id"], ".jpeg' class='w3-image w3-round'/> </i>";
        echo        "<div class='w3-col s8 m9 l10 pl-3 pr-1'>";
        echo            "<p class='w3-tiny w3-text-gray m-0'>", $FormFields["Pdt"]["select"]["catId"][$pt_ct[$itm["id"]]], "</p>";
        echo            "<p>", $pt_nm[$itm["id"]], "</p>";
        echo            "<p class='m-0'> <i class='w3-tiny w3-text-gray'> Qty </i> <br>", $itm["qty"], "</p>";
        echo            "<p class='w3-right-align itmTot'> Rs. ", $pt_pr[$itm["id"]] * $itm["qty"], "</p>";
        echo        "</div>";
		echo    "</div>";
		echo    "<p> </p>";
	}
?>
		<br>
		<p class="px-3"> Subtotal <span class="w3-right"> Rs. <?=$_SESSION["tot"]??0;?> </span> </p>
		<!-- <p class="pl-2 pr-3"> Tax GST @ 18 % <span class="w3-right"> Rs. <?=($_SESSION["tot"]??0) * 0.18;?> </span> </p> -->
		<b class="w3-block w3-border-top w3-border-gray p-3"> Total <span class="w3-right"> Rs. <?=$_SESSION["tot"]??0;?> </span> </b>
	</div>
</div>

<style>
	.addr p {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		width: 190px;
	}
</style>
<div class="p-4 px-md-5">
	<h4 class="w3-serif py-3"> Delivery Address </h4>
	<div class="noScrollBar" style="overflow-x:auto;">
<?
	$sql = "SELECT DISTINCT cusName,mob,email,street1,city,pincode,state FROM Odr WHERE email='".($_SESSION["email"]??0)."'";
	$addrs = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
	echo "<div class='w3-bar' style='width:", count ($addrs) * 200, "px;'>";
	foreach ($addrs as $i => $a) {
		echo "<div class='w3-bar-item px-1 addr'>";
		echo 	"<p class='w3-card w3-small w3-border w3-round-large p-2 m-0' onclick='popu($i)'>";
		echo 		$a["cusName"]??"", "<span class='w3-right'>", $a["mob"]??"", "</span>";
		echo 		"<br>", $a["street1"]??"", ($a["street2"]??0) ? "<br>":"", $a["street2"]??"", ($a["landmark"]??0) ? "<br>":"", $a["landmark"]??"", "<br>", $a["city"]??"", " ", $a["pincode"]??"";
		echo 	"</p>";
		echo "</div>";
	}
	echo "</div>";
?>
	</div> <br>

	<form method="POST" class="mx-auto" style="max-width:1000px;">
		<div class="w3-row">
			<input type="hidden" name="odr_id" value="<?=$odr["odr_id"]??"";?>" required />
<?
	foreach (["cusName", "email", "mob", "mob2", "street1", "street2", "landmark", "city", "pincode", "state"] as $name) {
		$attr = $fields[$name];
		echo "<p class='w3-col l", $attr["col"], " px-1'> <i class='w3-block w3-tiny w3-text-gray pl-1'> ", $attr["label"], " </i>";
		echo 	"<input type='", $attr["type"], "' class='w3-input p-1' name='$name' value='", $odr[$name]??"", "' ", $name == "pincode" ? " min=100000 max=999999 ":"",
				in_array ($name, ["mob2", "street2", "landmark"]) ? "":" required ", $name == "state" ? "list='stateList'":"", "/>";
		echo "</p>";
	}
	echo "<datalist id='stateList'>";
	foreach ($FormFields["Odr"]["states"] as $s) echo "<option value='$s'/>";
	echo "</datalist>";
?>
		</div>
		<button type="submit" class="w3-button w3-block w3-black w3-round px-md-5 save"> Save &amp; Proceed </button>
	</form>
	<div class="statusBar"> </div>
</div>
<script>
	const I_P = $(`form input:not([name='odr_id'],[name='email'])`);
	let ADDRS = [];
<? foreach ($addrs as $i => $a) echo "ADDRS[$i] = {cusName:`", $a["cusName"]??"", "`,mob:`", $a["mob"]??"", "`,mob2:`", $a["mob2"]??"", "`,street1:`", $a["street1"]??"", "`,street2:`", $a["street2"]??"", "`,landmark:`", $a["landmark"]??"", "`,city:`", $a["city"]??"", "`,pincode:`", $a["pincode"]??"", "`,state:`", $a["state"]??"", "`};\n"; ?>

	$(document).ready (() => {
		$(`form`).submit (function() {
			event.preventDefault();
			let formData = new FormData (this);
			postFormData (`Delivery.php`, formData);
		});
		$(`input[required]`).prev().append (`<b class='w3-text-red'>*</b>`);
    });
	function popu (a) { I_P.each ((i, x) => x.value = ADDRS[a][x.name]); }
</script>
<?
}
?>

