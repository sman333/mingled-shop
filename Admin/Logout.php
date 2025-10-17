<? require "AuthenticateUser.php";
if (isset ($usr["username"])) {
	require_once "ConfigDb.php";
	$sql = "UPDATE LoginHist SET 
			outDateTime='$tdy $now' 
		WHERE
			usertype='".$usr["usertype"]."' 
			AND username='".$usr["username"]."' 
			AND sessionId='".session_id()."' 
			AND inDateTime='".$_SESSION["inDateTime"]."'";
	mysqli_query ($conn, $sql);
}
session_unset();
session_destroy();
?>
<script>
	location="Login";
	alert (`Logged out`);
</script>
<h4> Logged out </h4>
<?
// header ("Location: Login");
?>

