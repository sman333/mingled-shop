<? require "../sysInfo.php";
require "../AuthenticateUser.php";
foreach (["Category", "Category_Add", "Category_Edit"] as $p) {
	$_GET["page"] = $p;
	require "../checkPerm.php";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");
	require "../FormFields.php";
	$tblName = "Cat";

	if ($_POST["deleteCat"]??0) {
		$sql = "UPDATE Cat SET status='$DELETED' WHERE cat_id='".$_POST["deleteCat"]."'";
		$log_action = "Delete";
		$log_rowId = $_POST["deleteCat"];
		if (mysqli_query ($conn, $sql)) {
			$resp = "<p class='SuccessResponse'> Category deleted. </p> <script> $(`#editCat_".$_POST["deleteCat"]."`).fadeOut(); </script>";
			$log_status = 1;
		}else {
			$resp = "<p class='FailedResponse'> Could not delete category. </p>";
			$log_status = 0;
		}
		require "log.php";
		exit ($resp);
	}

    $_POST["catName"] = ucwords ($_POST["catName"]??"");
	if (empty ($_POST["cat_id"])) {
		$sql = "SELECT * FROM Cat WHERE status='$ACTIVE' AND catName='".$_POST["catName"]."'";
		$res = mysqli_query ($conn, $sql);
		if (mysqli_num_rows ($res) > 0) exit ("<p class='FailedResponse'> This category already exists. </p>");
	}

	if ($_FILES["photo"]["size"]) {
		$ext = explode (".", $_FILES["photo"]["name"]);
		$ext = end ($ext);
		$allowedTypes = ["jpg", "jpeg", "JPG", "JPEG"];
		if (!in_array ($ext, $allowedTypes)) exit ("<p class='FailedResponse'> Photo --> file type ($ext) not allowed. </p>");
	}

	$fs = $FormFields["Cat"]["fields"];

	if (empty ($_POST["cat_id"])) {
		$cols = $vals = "";
		foreach ($fs as $f => $x) {
			$cols .= "$f,";
			$vals .= "'".($_POST[$f]??"")."',";
		}
		$cols .= "createAt,createBy,modifyBy";
		$vals .= "'$tdy $now','".($usr["usr_id"]??"")."','".($usr["usr_id"]??"")."'";
		$sql = "INSERT INTO Cat($cols) VALUES($vals)";
		$script = "location.reload();";
		$log_action = "Add";
	}else {
		$sql = "";
		foreach ($fs as $f => $x) $sql .= "$f='".($_POST[$f]??"")."',";
		$sql .= "modifyAt='$tdy $now',modifyBy='".($usr["usr_id"]??"")."'";
		$sql = "UPDATE Cat SET $sql WHERE cat_id=".$_POST["cat_id"];
		$script = "$(`i.cancelEdit`).click();";
		$log_action = "Edit";
		$log_rowId = $_POST["cat_id"];

		$dSql = "SELECT * FROM Cat WHERE cat_id='".$_POST["cat_id"]."'";
		$data = http_build_query (mysqli_fetch_assoc (mysqli_query ($conn, $dSql))??"", "", ', ');
	}
	if (mysqli_query ($conn, $sql)) {
    	if (empty ($_POST["cat_id"])) {
			$sql2 = "SELECT cat_id FROM Cat WHERE status='$ACTIVE' AND catName='".$_POST["catName"]."' ORDER BY cat_id DESC LIMIT 1";
			$_POST["cat_id"] = mysqli_fetch_assoc (mysqli_query ($conn, $sql2))["cat_id"]??0;
		}
/* 	-------------------------------- photo -------------------------------- photo -------------------------------- photo -------------------------------- */
		if ($_FILES["photo"]["size"]) {
			if (move_uploaded_file ($_FILES["photo"]["tmp_name"], "../../images/category/".$_POST["cat_id"].".jpeg")) {
        		try {
            		$imagick = new Imagick ("../../images/category/".$_POST["cat_id"].".jpeg");
            		$imagick->scaleImage (500, 0);     // width 500px, height auto
        			$imagick->writeImage ("../../images/category/".$_POST["cat_id"].".jpeg");
            		$imagick->clear();
            		$imagick->destroy();
        		}catch (Exception $e) { echo "<p class='FailedResponse'> Photo Error: ", $e->getMessage(), "</p>"; }
			    echo "<p class='SuccessResponse'> Photo uploaded. </p>";
			}else echo "<p class='FailedResponse'> Failed to upload photo. </p>";
		}
		echo "<p class='SuccessResponse'> Category details saved. </p> <script> $script </script>";
		$log_status = 1;
	}else {
		echo "<p class='FailedResponse'> Could not save category details. </p>";
		$log_status = 0;
	}

	if ($log_action == "Edit") require "log.php";
	$data = $sql;
	require "log.php";
}
?>

