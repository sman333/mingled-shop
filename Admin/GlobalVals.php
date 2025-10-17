<? require "AuthenticateUser.php";

$sql = "SELECT inDateTime FROM LoginHist WHERE usertype='".$_SESSION["usertype"]."' AND username='".$_SESSION["username"]."' AND inDateTime<'".$_SESSION["inDateTime"]."' ORDER BY sr_no DESC LIMIT 1";
$lastLogin = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["inDateTime"]??"";

// $sql = "SELECT COUNT(*) AS bookPendCnt FROM Chb WHERE status='$ACTIVE' AND progDate BETWEEN '$tdy' AND '".date ("Y-m-d", strtotime ("next month"))."' AND (paid<tot OR bal>0)";
// $bookPendCnt = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["bookPendCnt"]??0;

?>

