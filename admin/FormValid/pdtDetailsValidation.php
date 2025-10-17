<? require "../sysInfo.php";
require "../AuthenticateUser.php";
foreach (["Products", "Products_Add", "Products_Edit"] as $p) {
	$_GET["page"] = $p;
	require "../checkPerm.php";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");
	require "../FormFields.php";
	$tblName = "Pdt";

	if ($_POST["deletePdt"]??0) {
		$sql = "UPDATE Pdt SET status='$DELETED' WHERE pdt_id='".$_POST["deletePdt"]."'";
		$log_action = "Delete";
		$log_rowId = $_POST["deletePdt"];
		if (mysqli_query ($conn, $sql)) {
			$resp = "<p class='SuccessResponse'> Product deleted. </p> <script> $(`#editPdt_".$_POST["deletePdt"]."`).fadeOut(); </script>";
			$log_status = 1;
		}else {
			$resp = "<p class='FailedResponse'> Could not delete product. </p>";
			$log_status = 0;
		}
		require "log.php";
		exit ($resp);
	}



	if ($_POST["deleteImg"]??0) {
        $resp = "";
	    if ($_POST["pId"]??0) {
    	    $fileName = "../../images/products/".$pt_ct[$_POST["pId"]]."_".$_POST["pId"]."_".$_POST["deleteImg"].".jpeg";
    	    if (file_exists ($fileName)) {
        		if (unlink ($fileName)) $resp = "<p class='SuccessResponse'> Photo deleted. </p> <script> $(`#editPdt_".$_POST["pId"]." [name='photo".$_POST["deleteImg"]."']`).prev().css (`background-image`, ``); </script>";
        		else $resp = "<p class='FailedResponse'> Could not delete photo. </p>";
    	    }else $resp = "<p class='FailedResponse'> Image not found. </p>";
	    }else $resp = "<p class='FailedResponse'> Invalid product id. </p>";
		exit ($resp);
	}



	$allowedTypes = ["jpg", "jpeg", "JPG", "JPEG"];
	$maxSize = 1024 * 1024;		// 1 MB
	if ($_FILES["photo"]["size"]??0) {
    	$ext = explode (".", $_FILES["photo"]["name"]);
    	$ext = end ($ext);
    	if (!in_array ($ext, $allowedTypes)) exit ("<p class='FailedResponse'> Photo --> file type ($ext) not allowed. </p>");
		move_uploaded_file ($_FILES["photo"]["tmp_name"], "../../images/products/photo.jpeg");
		if ($maxSize < $_FILES["photo"]["size"]??0)
    		try {
        		$imagick = new Imagick ("../../images/products/photo.jpeg");
        		$imagick->scaleImage (1000, 0);     // width 1000px, height auto
    			$imagick->writeImage ("../../images/products/photo.jpeg");
        		$imagick->clear();
        		$imagick->destroy();
    		}catch (Exception $e) { echo "<p class='FailedResponse'> Photo Error: ", $e->getMessage(), "</p>"; }
	}
    $imgCnt = 4;
    for ($i = 1; $i <= $imgCnt; $i++) {
    	if (!($_FILES["photo$i"]["size"]??0)) continue;
		$ext = explode (".", $_FILES["photo$i"]["name"]);
		$ext = end ($ext);
		if (!in_array ($ext, $allowedTypes)) exit ("<p class='FailedResponse'> Image $i --> file type ($ext) not allowed. </p>");
		move_uploaded_file ($_FILES["photo$i"]["tmp_name"], "../../images/products/photo_$i.jpeg");
		if ($maxSize < $_FILES["photo$i"]["size"]??0)
    		try {
        		$imagick = new Imagick ("../../images/products/photo_$i.jpeg");
        		$imagick->scaleImage (1000, 0);     // width 1000px, height auto
    			$imagick->writeImage ("../../images/products/photo_$i.jpeg");
        		$imagick->clear();
        		$imagick->destroy();
    		}catch (Exception $e) { echo "<p class='FailedResponse'> Image $i Error: ", $e->getMessage(), "</p>"; }
	}
	if ($ext??0) exit();



    $_POST["pdtName"] = ucwords ($_POST["pdtName"]??"");
    $catId = "";
	if (empty ($_POST["pdt_id"])) {
		$sql = "SELECT * FROM Pdt WHERE status='$ACTIVE' AND pdtName='".($_POST["pdtName"]??"")."' AND colour='".($_POST["colour"]??"")."' AND catId='".($_POST["catId"]??0)."'";
		$res = mysqli_query ($conn, $sql);
		if (mysqli_num_rows ($res) > 0) exit ("<p class='FailedResponse'> This product already exists. </p>");
	}else {
		$sql = "SELECT catId FROM Pdt WHERE pdt_id='".$_POST["pdt_id"]."'";
		$catId = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["catId"]??"";
	}

	$fs = $FormFields["Pdt"]["fields"];
	$cfs = $FormFields["Pdt"]["clrFields"];

	if (empty ($_POST["pdt_id"])) {
		$cols = $vals = "";
		foreach ($fs as $f => $x) {
			$cols .= "$f,";
			$vals .= "'".($_POST[$f]??"")."',";
		}
		$cols .= "createAt,createBy,modifyBy";
		$vals .= "'$tdy $now','".($usr["usr_id"]??"")."','".($usr["usr_id"]??"")."'";
		$sql = "INSERT INTO Pdt($cols) VALUES($vals)";
		$log_action = "Add";
	}else {
		$sql = "";
		foreach ($fs as $f => $x) $sql .= "$f='".($_POST[$f]??"")."',";
		$sql .= "modifyAt='$tdy $now',modifyBy='".($usr["usr_id"]??"")."'";
		$sql = "UPDATE Pdt SET $sql WHERE pdt_id=".$_POST["pdt_id"];
		$log_action = "Edit";
		$log_rowId = $_POST["pdt_id"];

		$dSql = "SELECT * FROM Pdt WHERE pdt_id='".$_POST["pdt_id"]."'";
		$data = http_build_query (mysqli_fetch_assoc (mysqli_query ($conn, $dSql))??"", "", ', ');
	}
	if (mysqli_query ($conn, $sql)) {
    	if (empty ($_POST["pdt_id"])) {
			$sql2 = "SELECT pdt_id FROM Pdt WHERE status='$ACTIVE' AND catId='".$_POST["catId"]."' AND pdtName='".$_POST["pdtName"]."' ORDER BY pdt_id DESC LIMIT 1";
			$_POST["pdt_id"] = mysqli_fetch_assoc (mysqli_query ($conn, $sql2))["pdt_id"]??0;
		}

/* 	-------------------------------- colours -------------------------------- colours -------------------------------- colours -------------------------------- */
		$clrCnt = 16;
		$clrStr = join (",", array_map (function ($v) { return "p$v"; }, range (1, $clrCnt)));
		for ($i = 1; $i < $clrCnt; $i++) if (empty ($_POST["p$i"])) $_POST["p$i"] = 0;
		$sql2 = "SELECT * FROM Clr WHERE ".$_POST["pdt_id"]." IN ($clrStr)";
		if (mysqli_num_rows (mysqli_query ($conn, $sql2))) {
    		$sql2 = "";
    		foreach ($cfs as $f => $x) $sql2 .= "$f='".($_POST[$f]??"")."',";
    		$sql2 .= "c16='".($_POST["colour"]??"")."',p16=".$_POST["pdt_id"];
    		$sql2 = "UPDATE Clr SET $sql2 WHERE ".$_POST["pdt_id"]." IN ($clrStr)";
    	}else {
    		$sql2 = "SELECT * FROM Clr WHERE ".$_POST["p1"]." IN ($clrStr)";
        	$res = mysqli_query ($conn, $sql2);
    		if (mysqli_num_rows ($res)) {
            	$res = mysqli_fetch_assoc ($res);
        		for ($i = 1; $i < $clrCnt; $i++) if ($res["p$i"] == 0) break;
        		if ($i == $clrCnt) $sql2 = false;
        		else $sql2 = "UPDATE Clr SET c$i='".($_POST["colour"]??"")."',p$i=".$_POST["pdt_id"]." WHERE ".$_POST["p1"]." IN ($clrStr)";
        	}else {
        		$cols = $vals = "";
        		foreach ($cfs as $f => $x) {
        			$cols .= "$f,";
        			$vals .= "'".($_POST[$f]??"")."',";
        		}
        		$cols .= "c$clrCnt,p$clrCnt";
        		$vals .= "'".($_POST["colour"]??"")."',".($_POST["pdt_id"]??"");
        		$sql2 = "INSERT INTO Clr($cols) VALUES($vals)";
        	}
    	}
    	if ($sql2) {
        	if (mysqli_query ($conn, $sql2)) echo "<p class='SuccessResponse'> Colours saved. </p>";
        	else echo "<p class='FailedResponse'> Could not save colours. </p>";
    	}elseif ($_POST["c1"] || $_POST["c2"] || $_POST["p1"] || $_POST["p2"]) echo "<p class='FailedResponse'> Colour list full! Could not add this product to the entered colour list. </p>";

/* 	-------------------------------- photo -------------------------------- photo -------------------------------- photo -------------------------------- */
    	if (file_exists ("../../images/products/photo.jpeg")) {
	        if (rename ("../../images/products/photo.jpeg", "../../images/products/".$_POST["catId"]."_".$_POST["pdt_id"].".jpeg"))
			    echo "<p class='SuccessResponse'> Photo uploaded. </p>";
			else echo "<p class='FailedResponse'> Failed to upload photo. </p>";
    	}elseif (file_exists ("../../images/products/$catId"."_".$_POST["pdt_id"].".jpeg")) {
	        if (rename ("../../images/products/$catId"."_".$_POST["pdt_id"].".jpeg", "../../images/products/".$_POST["catId"]."_".$_POST["pdt_id"].".jpeg"))
	            echo "<p class='SuccessResponse'> Photo renamed. </p>";
	        else echo "<p class='FailedResponse'> Photo could not be renamed. </p>";
		}
// 		if ($_FILES["photo"]["size"]) {
// 			if (move_uploaded_file ($_FILES["photo"]["tmp_name"], "../../images/products/".$_POST["catId"]."_".$_POST["pdt_id"].".jpeg"))
// 			    echo "<p class='SuccessResponse'> Photo uploaded. </p>";
// 			else echo "<p class='FailedResponse'> Failed to upload photo. </p>";
//     	}elseif (file_exists ("../../images/products/$catId"."_".$_POST["pdt_id"].".jpeg")) {
// 	        if (rename ("../../images/products/$catId"."_".$_POST["pdt_id"].".jpeg", "../../images/products/".$_POST["catId"]."_".$_POST["pdt_id"].".jpeg"))
// 	            echo "<p class='SuccessResponse'> Photo renamed. </p>";
// 	        else echo "<p class='FailedResponse'> Photo could not be renamed. </p>";
// 		}

/* 	-------------------------------- more images -------------------------------- more images -------------------------------- more images -------------------------------- */
        for ($i = 1; $i <= $imgCnt; $i++) {
        	if (file_exists ("../../images/products/photo_$i.jpeg")) {
    	        if (rename ("../../images/products/photo_$i.jpeg", "../../images/products/".$_POST["catId"]."_".$_POST["pdt_id"]."_$i.jpeg"))
    			    echo "<p class='SuccessResponse'> Image $i uploaded. </p>";
    			else echo "<p class='FailedResponse'> Failed to upload image $i. </p>";
        	}elseif (file_exists ("../../images/products/".($catId??0)."_".$_POST["pdt_id"]."_$i.jpeg")) {
    	        if (rename ("../../images/products/$catId"."_".$_POST["pdt_id"]."_$i.jpeg", "../../images/products/".$_POST["catId"]."_".$_POST["pdt_id"]."_$i.jpeg"))
    	            echo "<p class='SuccessResponse'> Image $i renamed. </p>";
    	        else echo "<p class='FailedResponse'> Image $i could not be renamed. </p>";
    		}
	    }
    //     for ($i = 1; $i <= $imgCnt; $i++) {
    // 		if ($_FILES["photo$i"]["size"]) {
    // 			if (move_uploaded_file ($_FILES["photo$i"]["tmp_name"], "../../images/products/".$_POST["catId"]."_".$_POST["pdt_id"]."_$i.jpeg"))
    // 			    echo "<p class='SuccessResponse'> Image $i uploaded. </p>";
    // 			else echo "<p class='FailedResponse'> Failed to upload image $i. </p>";
    //     	}elseif (file_exists ("../../images/products/$catId"."_".$_POST["pdt_id"]."_$i.jpeg")) {
    // 	        if (rename ("../../images/products/$catId"."_".$_POST["pdt_id"]."_$i.jpeg", "../../images/products/".$_POST["catId"]."_".$_POST["pdt_id"]."_$i.jpeg"))
    // 	            echo "<p class='SuccessResponse'> Image $i renamed. </p>";
    // 	        else echo "<p class='FailedResponse'> Image $i could not be renamed. </p>";
    // 		}
	   // }
	   
		echo "<p class='SuccessResponse'> Product details saved. </p> <script> location.reload(); </script>";
		$log_status = 1;
	}else {
		echo "<p class='FailedResponse'> Could not save product details. </p>";
		$log_status = 0;
	}

	if ($log_action == "Edit") require "log.php";
	$data = $sql;
	require "log.php";
}
?>

