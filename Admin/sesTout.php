<? require "sysInfo.php"; require "AuthenticateUser.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");
    $_SESSION["timeout"] = ($_POST["usrActive"]??false) ? time() + $usr["sesTimeout"]??100 : 0;
    $_GET["page"] = $_POST["page"];
    require "checkPerm.php";
}
?>