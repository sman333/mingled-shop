<?
if (empty ($_SESSION["username"])) exit ("<script> alert (`Session expired ! Please log in again.`); location=`".($PATH??"/")."Admin/Login`+((typeof R_D == 'undefined') ? `/Home` : R_D); usrLastActiveTime = new Date().getTime() + 5000; </script>");
if (!in_array ($_SESSION["usertype"], ["Admin", "Accountant", "BackOffice", "Clerk", "Dev", "Manager"]))
	exit ("<script> alert (`Unauthorised request! Contact system Administrator for this permission.`); location=`".($PATH??"/")."Admin/Login`; </script>");
else {
	require "ConfigDb.php";
	require "FormFields.php";
	$sql = "SELECT * FROM Usr WHERE username='".$_SESSION["username"]."'";
	$usr = mysqli_fetch_assoc (mysqli_query ($conn, $sql));
	$_SESSION["usertype"] = $usr["usertype"];
	$_SESSION["name"] = $usr["name"];
	if (time() < $_SESSION["timeout"]) $_SESSION["timeout"] = time() + ($usr["sesTimeout"]??100);
	else exit ("<script> alert (`Session expired ! Please log in again.`); location=`".($PATH??"/")."Admin/Login`+((typeof R_D == 'undefined') ? `/Home` : R_D); usrLastActiveTime = new Date().getTime() + 5000; </script>");
	if ($usr["status"] != $ACTIVE) exit ("<script> alert (`Your account has been disabled. Contact System Administrator to enable account.`); location=`".($PATH??"/")."Admin/Login`; </script>");
}
?>

