<?

// $debug = true;
$debug = false;



$wa = "+91 944 657 9337";
$wa2 = "+91 977 848 3161";
$em = "roysman333@gmail.com";

$DR = $_SERVER["DOCUMENT_ROOT"];
$APP_NAME = "MINGLED";
$MAIN_NAME = "Mingled";
$PATH = "/mingled/";

$tdy = date ("Y-m-d");
$now = date_create()->format ("H:i:s");
// $TBL_ID = date ("y")."_".(date ("y") + 1);
$FinYrStart = (int) date ("Y04");
$FinYrEnd = (int) (date ("Y") + 1)."03";
$FinYrStartMonth = date ("Y-04");
$FinYrEndMonth = (date ("Y") + 1)."-03";
if (date ("Ym") < $FinYrStart) {
	// $TBL_ID = (date ("y") - 1)."_".date ("y");
	$FinYrStart = (int) (date ("Y") - 1)."04";
	$FinYrEnd = (int) date ("Y03");
	$FinYrStartMonth = (date ("Y") - 1)."-04";
	$FinYrEndMonth = date ("Y-03");
}
// google fcm api keys (console.firebase.google.com)
$pubKey = "BO384V8VcuvD3RLqzl7mNR419WpsOnbV98dOh88yoSfpZvT3GVMxGiMbPPCeBkTzY1zeEE7WMNZ9ilrSbEftG5U";
$priKey = "dn5DmziCU9jIhvzmZGXmpupuibcncVci2MI-nrDSRwM";
// // (web-push-codelab.glitch.me)
// $pubKey = "BNeWKu3updjLXtAiYu8DF7DPqFk9xtHdf_7WOC1hiGlmbAhCI2iJHicg6ep7d6Ca1VPCHt-3Nf_Dnf-ctPWtXjg";
// $priKey = "fgSnaYLHOh4a1BjJikJFHfELxieB0QGNkZWxnaU8h8M";


$TBL_ID = "24_25";


$MN_END = [
	31,		// jan
	28,		// feb
	31,		// mar
	
	30,		// apr
	31,		// may
	30,		// jun

	31,		// jul
	31,		// aug
	30,		// sep

	31,		// oct
	30,		// nov
	31,		// dec
];

function crt ($usr, $str) { return sha1 ($usr["name"] . "@mingled" . $str); }
function chk ($usr, $str) { return crt ($usr, $str) == $usr["password"]; }
function dispAmt ($a) { return preg_replace ("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $a); }
function validate (&$v) {
	$v = trim ($v);
	$v = stripslashes ($v);
	$v = htmlspecialchars ($v);
	$v = str_replace ("'", "", $v);
	$v = str_replace ('"', "", $v);
}
function x_n ($x, $n, $y = "") { return join ($y, array_fill (0, $n, $x)); }
?>
