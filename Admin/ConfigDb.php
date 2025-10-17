<?
// session_start();
$servername = "localhost";   //hostname
$username = "MMC_Manager";
$password = "$9INMZ(D(X_S";
$database = "mingled";
$port = "2083";
$conn = mysqli_connect ($servername, $username, $password, $database);
// $conn = mysqli_connect ($servername, $username, $password, $database, $port);
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	session_unset();
	session_destroy();
}
require_once "sysInfo.php";
?>