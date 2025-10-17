<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

$fields = $FormFields["Odr"]["fields"];
$fields["city_pin"] = ["", "", "Town / City - Pincode"];
foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields["keys"], $vals);

$sql = "SELECT * FROM Cat WHERE status IN ('$ACTIVE', '$DISABLED')";
$cats = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
$cats = array_combine (array_column ($cats, "cat_id"), $cats);

$sql = "SELECT * FROM Pdt WHERE status IN ('$ACTIVE', '$DISABLED')";
$pdts = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
$pdts = array_combine (array_column ($pdts, "pdt_id"), $pdts);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");

	$sql = "SELECT * FROM Odr WHERE status='$PAID' AND modifyAt>'".$_POST["lastUpdatedAt"]."'";
	$odrs = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
    if (!count ($odrs)) exit();
    $x[0] = "amber";
	echo 	"<div class='w3- py-2'>";
	foreach ($odrs as $odr) {
		$odr["city_pin"] = $odr["city"]." - ".$odr["pincode"];
		require "Forms/Odr.php";
	}
	echo 	"</div>";

}else {
?>
<h6 class="w3-center"> Orders </h6>
<div id="statusBar" class="sticky-top"> </div>
<div class="p-md-4">
<?
    foreach ([
    	"Paid" 			=> ["amber", 	"'$PAID'"],
    	"Shipped" 		=> ["blue", 	"'$SHIPPED'"],
    	"Delivered" 	=> ["green", 	"'$DELIVERED'"],
    	"Pending" 		=> ["dark-gray", "'$ACTIVE'"],
    ] as $txt => $x) {
    	$sql = "SELECT * FROM Odr WHERE status IN (".$x[1].")";
    	$odrs = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
    	echo "<div class='py-3'>";
    	echo 	"<p class='w3-large w3-block w3-round-large p-3 m-0 fa fa-caret-right w3-", $x[0], " div$txt' onclick='$(this).toggleClass(`fa-caret-right fa-caret-down`).next().slideToggle();'> <small> &nbsp; ", count ($odrs), "&nbsp; $txt </small> </p>";
    	echo 	"<div class='collapse py-2'>";
    	foreach ($odrs as $odr) {
    		$odr["city_pin"] = $odr["city"]." - ".$odr["pincode"];
    		require "Forms/Odr.php";
    	}
    	echo 	"</div>";
    	echo "</div>";
    }
?>
</div>
<script defer>
const D_P = $(`.divPaid`).next();
let lastUpdatedAt;
$(document).ready (() => {
    lastUpdatedAt = timeFormat (`%Y-%M-%D %h:%m:%s`);
});
function updateList() {
	let formData = new FormData();
	formData.append (`lastUpdatedAt`, lastUpdatedAt);
	$.ajax ({
		type: `POST`,
		url: `<?=$PATH;?>Admin/Orders.php`,
		enctype: `multipart/form-data`,
		dataType: `text`,
		data: formData,
		contentType: false,
		cache: false,
		processData: false,
		success: resp => D_P.prepend (resp),
		error: () => D_P.prepend (`<p> Server error, please refresh the page. </p>`)
	});
    lastUpdatedAt = timeFormat (`%Y-%M-%D %h:%m:%s`);
}
setInterval (updateList, 60*1000);
<? if ($usr[$FormFields["permissions"]["Orders_Edit"]??""]??0) { ?>
function shippedOdr (id) {
	let formData = new FormData();
	formData.append (`shippedOdr`, id);
	postFormData (`FormValid/odrDetailsValidation.php`, formData);
}
function deliveredOdr (id) {
	let formData = new FormData();
	formData.append (`deliveredOdr`, id);
	postFormData (`FormValid/odrDetailsValidation.php`, formData);
}
function deleteOdr (id) {
	if (confirm ("Are you sure you want to delete this Order?")) {
		let formData = new FormData();
		formData.append (`deleteOdr`, id);
		postFormData (`FormValid/odrDetailsValidation.php`, formData);
	}
}
<? } ?>
</script>
<?
}
?>

