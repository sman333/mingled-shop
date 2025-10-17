<? require "../sysInfo.php";
require "../AuthenticateUser.php";
foreach (["Orders", "Orders_Add", "Orders_Edit"] as $p) {
	$_GET["page"] = $p;
	require "../checkPerm.php";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");
	require "../FormFields.php";
	$tblName = "Odr";

	if ($_POST["deleteOdr"]??0) {
		$sql = "UPDATE Odr SET status='$DELETED' WHERE odr_id='".$_POST["deleteOdr"]."'";
		$log_action = "Delete";
		$log_rowId = $_POST["deleteOdr"];
		if (mysqli_query ($conn, $sql)) {
			$resp = "<p class='SuccessResponse'> Order deleted. </p> <script> $(`#odr_".$_POST["deleteOdr"]."`).fadeOut(); </script>";
			$log_status = 1;
		}else {
			$resp = "<p class='FailedResponse'> Could not delete order. </p>";
			$log_status = 0;
		}
	}

	if ($_POST["deliveredOdr"]??0) {
		$sql = "UPDATE Odr SET status='$DELIVERED' WHERE odr_id='".$_POST["deliveredOdr"]."'";
		$log_action = "Edit";
		$log_rowId = $_POST["deliveredOdr"];
		if (mysqli_query ($conn, $sql)) {
			$resp = "<p class='SuccessResponse'> Order set as DELIVERED. </p> <script> $(`#odr_".$_POST["deliveredOdr"]."`).fadeOut(); </script>";
			$log_status = 1;
		}else {
			$resp = "<p class='FailedResponse'> Could not set order as DELIVERED. </p>";
			$log_status = 0;
		}
	}

	if ($_POST["shippedOdr"]??0) {
		$sql = "UPDATE Odr SET status='$SHIPPED' WHERE odr_id='".$_POST["shippedOdr"]."'";
		$log_action = "Edit";
		$log_rowId = $_POST["shippedOdr"];
		if (mysqli_query ($conn, $sql)) {
			$resp = "<p class='SuccessResponse'> Order set as SHIPPED. </p> <script> $(`#odr_".$_POST["shippedOdr"]."`).fadeOut(); </script>";
			$log_status = 1;
		}else {
			$resp = "<p class='FailedResponse'> Could not set order as SHIPPED. </p>";
			$log_status = 0;
		}
	}

	require "log.php";
	exit ($resp);
}
?>

