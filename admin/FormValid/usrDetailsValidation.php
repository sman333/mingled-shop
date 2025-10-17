<? require "../sysInfo.php";
require "../AuthenticateUser.php";
foreach (["Users", "Users_Add", "Users_Edit"] as $p) {
	$_GET["page"] = $p;
	require "../checkPerm.php";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");
	require "../FormFields.php";
	$tblName = "Usr";

	if ($_POST["deleteUsr"]??0) {
		$sql = "UPDATE Usr SET status='$DELETED' WHERE usr_id='".$_POST["deleteUsr"]."'";
		$log_action = "Delete";
		$log_rowId = $_POST["deleteUsr"];
		if (mysqli_query ($conn, $sql)) {
			$resp = "<p class='SuccessResponse'> User deleted. </p> <script> $(`#editUsr_".$_POST["deleteUsr"]."`).fadeOut(); </script>";
			$log_status = 1;
		}else {
			$resp = "<p class='FailedResponse'> Could not delete User. </p>";
			$log_status = 0;
		}
		require "log.php";
		exit ($resp);
    }
    
	if (empty ($_POST["usr_id"])) {
		$sql = "SELECT * FROM Usr WHERE username='".$_POST["username"]."'";
		$res = mysqli_query ($conn, $sql);
		if (mysqli_num_rows ($res) > 0) exit ("<p class='FailedResponse'> This Username already exists. </p>");
	}
    foreach ($FormFields["Usr_Prm"]["fields"] as $f => $x) $_POST[$f] = ($_POST[$f]??0) ? 1 : 0;

	$fs = array_merge ($FormFields["Usr"]["fields"], $FormFields["Usr_Prm"]["fields"]);

	if (empty ($_POST["usr_id"])) {
		$cols = $vals = "";
		foreach ($fs as $f => $x) {
			$cols .= "$f,";
			$vals .= "'".trim ($_POST[$f]??"")."',";
		}
		$cols .= "createAt,createBy";
		$vals .= "'$tdy $now','".$_SESSION["username"]."'";
		$sql = "INSERT INTO Usr($cols) VALUES($vals)";
		$script = "$(`form[id^='add']`)[0].reset();";
		$log_action = "Add";
	}else {
		$sql = "";
		foreach ($fs as $f => $x) $sql .= "$f='".trim ($_POST[$f]??"")."',";
		$sql .= "modifyAt='$tdy $now',modifyBy='".$_SESSION["username"]."'";
		$sql = "UPDATE Usr SET $sql WHERE usr_id='".$_POST["usr_id"]."'";
		$script = "location.reload(); /* $(`i.cancelEdit`).click(); */";
		$log_action = "Edit";
		$log_rowId = $_POST["usr_id"];

		$dSql = "SELECT * FROM Usr WHERE usr_id='".$_POST["usr_id"]."'";
		$data = http_build_query (mysqli_fetch_assoc (mysqli_query ($conn, $dSql))??"", "", ', ');
	}
	if (mysqli_query ($conn, $sql)) {
		echo "<p class='SuccessResponse'> User - ".$_POST["username"]." details saved. </p> <script> $script </script>";
		$log_status = 1;
	}else {
		echo "<p class='FailedResponse'> Could not save User details. </p>";
		$log_status = 0;
	}

	if ($log_action == "Edit") require "log.php";
	$data = $sql;
	require "log.php";
}
?>

