<? require "../sysInfo.php";
require "../AuthenticateUser.php";
foreach (["Services", "Services_Add", "Services_Edit"] as $p) {
	$_GET["page"] = $p;
	require "../checkPerm.php";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");
	require "../FormFields.php";
	$tblName = "Ser";

	if ($_POST["deleteSer"]??0) {
		$sql = "UPDATE Ser SET status='$DELETED' WHERE ser_id='".$_POST["deleteSer"]."'";
		$log_action = "Delete";
		$log_rowId = $_POST["deleteSer"];
		if (mysqli_query ($conn, $sql)) {
			$resp = "<p class='SuccessResponse'> Service deleted. </p> <script> $(`#editSer_".$_POST["deleteSer"]."`).fadeOut(); </script>";
			$log_status = 1;
		}else {
			$resp = "<p class='FailedResponse'> Could not delete service. </p>";
			$log_status = 0;
		}
		require "log.php";
		exit ($resp);
	}

	if (empty ($_POST["ser_id"])) {
		$sql = "SELECT * FROM Ser WHERE status='$ACTIVE' AND serName='".$_POST["serName"]."' AND serFor='".$_POST["serFor"]."'";
		$res = mysqli_query ($conn, $sql);
		if (mysqli_num_rows ($res) > 0) exit ("<p class='FailedResponse'> This service already exists. </p>");
	}

	$fs = $FormFields["Ser"]["fields"];

	if (empty ($_POST["ser_id"])) {
		$cols = $vals = "";
		foreach ($fs as $f => $x) {
			$cols .= "$f,";
			$vals .= "'".($_POST[$f]??"")."',";
		}
		$cols .= "createAt,createBy";
		$vals .= "'$tdy $now','".($_SESSION["username"]??"")."'";
		$sql = "INSERT INTO Ser($cols) VALUES($vals)";
		$script = $_POST["isScript"]??false ? "":"location.reload();";
		$log_action = "Add";
	}else {
		$sql = "";
		foreach ($fs as $f => $x) $sql .= "$f='".($_POST[$f]??"")."',";
		$sql .= "createAt='$tdy $now'";
		$sql = "UPDATE Ser SET $sql WHERE ser_id=".$_POST["ser_id"];
		$script = "$(`i.cancelEdit`).click();";
		$log_action = "Edit";
		$log_rowId = $_POST["ser_id"];

		$dSql = "SELECT * FROM Ser WHERE ser_id='".$_POST["ser_id"]."'";
		$data = http_build_query (mysqli_fetch_assoc (mysqli_query ($conn, $dSql))??"", null, ', ');
	}
	if (mysqli_query ($conn, $sql)) {
		echo "<p class='SuccessResponse'> Service details saved. </p> <script> $script </script>";
		$log_status = 1;
	}else {
		echo "<p class='FailedResponse'> Could not save service details. </p>";
		$log_status = 0;
	}

	if ($log_action == "Edit") require "log.php";
	$data = $sql;
	require "log.php";
}
?>

