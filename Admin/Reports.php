<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

$fs = [
	"Rct" => [
		"Sr" 			=> 4,
		"Date" 			=> 9,
		"Rt.No." 		=> 10,
		"Head" 			=> 20,
		"Fno." 			=> 5,
		"Name" 			=> 35,
		"Mode" 			=> 7,
		"Amount" 		=> 10,
	],
	"Pay" => [
		"Sr" 			=> 4,
		"Date" 			=> 9,
		"Vr.No." 		=> 10,
		"Head" 			=> 20,
		"Name" 			=> 40,
		"Mode" 			=> 7,
		"Amount" 		=> 10,
	]
];
$col = [];
foreach ($fs as $typ => $cols) $col[$typ] = array_values ($cols);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");

	$_POST["display"] = true;
	$from = $_POST["from"];
	$to = $_POST["to"];

	if ($_POST["trnTyp"] == "receipts") {		 /* ------------------------------------------------------------------------ receipts ------------------------------------ receipts ------------------------------------ receipts */
		$prefixes = ["head", "wife", "child1", "child2", "child3", "child4"];
		$sql = ["famNo"];
		foreach ($prefixes as $pre) array_push ($sql, "CONCAT(Fam.$pre"."Salut,' ',Fam.$pre) AS $pre", "Fam.$pre"."Status");
		$sql = join (",", $sql);

		$sql = "SELECT Tran$TBL_ID.*,$sql FROM Tran$TBL_ID,Fam WHERE payBy=fam_id AND Tran$TBL_ID.status='$ACTIVE' AND Fam.status IN ('$ACTIVE','$UTILITY') AND action='Cr' AND tDate BETWEEN '$from' AND '$to' ORDER BY tDate,srNo";
		$trns = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;
		$totCash = $totBank = 0;
		$sr = 1;
		$selHeads = [];
		switch ($_POST["payForRct"]) {
			case 6: 	/* Missionary Fund - Mission Monthly Offering */
				$selHeads = [7, 53]; 	/* monthly offer., monthly offer. - cover */
				break;
			// case 7: 	/* monthly offering */
				// $selHeads = [53]; 	/* monthly offer. - cover */
				// break;
			case 27: 	/* choir fees */
			case 31: 	/* security chrg. */
			case 43: 	/* admin. chrg. */
			case 67: 	/* poor marriage help / fund */
			case 69: 	/* poor help / fund */
				$selHeads = [45]; 	/* marriage fee */
				break;
			case 98: 	/* c gst */
			case 99: 	/* s gst */
				$selHeads = [25]; 	/* hall rent */
				break;
		}
		array_push ($selHeads, $_POST["payForRct"]);

		if (($_POST["typ"]??"") == "tbl") {		/* view as table ------------------------------------ view as table ------------------------------------ view as table ------------------------------------ */
ob_start();
			foreach ($trns as $trn) {		/* _____________________________________ transactions loop _____________________________________ transactions loop _____________________________________ transactions loop */
				if (($_POST["calc"]??0) && (($calcFrom == "2023-04-01" && in_array ($trn["tDate"], [$from, $to])) || $trn["tDate"] == $to)) continue;
				if (($_POST["payForRct"]??"all") != "all" && !in_array ($trn["payFor"], $selHeads)) continue;
				$payer = false;
				if ($trn["payBy"] == $FormFields["SYS_ACC"]["NON MEMBER"]) $payer = $trn["nonMem"];
				else {
					foreach ($prefixes as $pre) if ($trn[$pre."Amt"]) { /* check if all mem. amts. are empty */
						$payer = $trn[$pre];
						break;
					}
					if (!$payer) foreach ($prefixes as $pre) if (!$trn[$pre."Status"]) { /* find first active mem. */
							$payer = $trn[$pre];
							break;
						}
				}
				switch ($_POST["payForRct"]) {
					case 6: 	/* Missionary Fund - Mission Monthly Offering */
						if ($trn["payFor"] == 7) {		/* monthly offer. */
							$trn["amount"] = $trn["mfAmt"];
							foreach ($prefixes as $pre) $trn[$pre."Amt"] = 0;
						}
						if ($trn["payFor"] == 53) {		/* monthly offer. - cover */
							$trn["amount"] = $trn["mfAmt"];
							$trn["moAmt"] = 0;
						}
						break;
					case 7: 	/* monthly offer. */
						if ($trn["payFor"] == 7) {		/* monthly offer. */
							$trn["amount"] -= $trn["mfAmt"];
							$trn["mfAmt"] = 0;
						}
					case 53: 	/* monthly offer. - cover */
						if ($trn["payFor"] == 53) {		/* monthly offer. - cover */
							$trn["amount"] = $trn["moAmt"];
							$trn["mfAmt"] = 0;
						}
						break;
					case 25: 	/* hall rent */
					case 98: 	/* c gst */
					case 99: 	/* s gst */
						if ($trn["payFor"] == 25) {		/* hall rent */
							$trn["amount"] = $trn[[25 => "hallRent", 98 => "cGstAmt", 99 => "sGstAmt"][$_POST["payForRct"]]];
							$trn["hallRent"] = $trn["cGstAmt"] = $trn["sGstAmt"] = 0;
							$trn[[25 => "hallRent", 98 => "cGstAmt", 99 => "sGstAmt"][$_POST["payForRct"]]] = $trn["amount"];
						}
						break;
					case 27: 	/* marriage choir fee */
					case 31: 	/* security chrg. */
					case 43: 	/* admin. chrg. */
					case 67: 	/* poor marriage help */
					case 69: 	/* poor help */
						if ($trn["payFor"] == 45) {		/* marriage fee */
							$trn["amount"] = $trn[[27 => "choirFee", 31 => "security", 43 => "adminChrg", 67 => "poorMarryFund", 69 => "poorFund"][$_POST["payForRct"]]];
							foreach (["poorFund", "poorMarryFund", "adminChrg", "choirFee", "security"] as $x) $trn[$x] = 0;
							$trn[[27 => "choirFee", 31 => "security", 43 => "adminChrg", 67 => "poorMarryFund", 69 => "poorFund"][$_POST["payForRct"]]] = $trn["amount"];
						}
						break;
				}
				$trn["payMode"] == "CASH" ? ($totCash += $trn["amount"]) : ($totBank += $trn["amount"]);
				if ((!$trn["amount"]) || ($_POST["calc"]??0)) continue;
				if ($_POST["payMode"] == "bank" && $trn["payMode"] == "CASH") continue;


				echo "<tr>";
					// echo "<td class='w3-right-align'> ", $sr++, " </td>";
				echo "<td> ", date_create ($trn["tDate"])->format ("d-m-Y"), " </td>";
				echo "<td> ", $trn["srNo"], " </td>";
				echo "<td> ", $FormFields["Rct"]["select"]["payFor"][$trn["payFor"]], " </td>";
				echo "<td style='text-align:right;'>", $trn["payBy"] == $FormFields["SYS_ACC"]["NON MEMBER"] ? "NM" : $trn["famNo"], "</td>";

				echo "<td>";
				switch ($trn["payFor"]) {
					case 25:
						echo "$payer &nbsp; &nbsp; <i class='w3-right'>";
						foreach (["hallRent" => "Rent", "cGstAmt" => "CGST", "sGstAmt" => "SGST"] as $x => $y) echo $trn[$x] ? "$y <br>":"";
						echo "</i>";
						break;
					case 45:
						echo "$payer &nbsp; &nbsp; <i class='w3-right'> ";
						foreach (["poorFund" => "Poor Fund", "poorMarryFund" => "Poor Marriage Fund", "adminChrg" => "Admin. Chrg.", "choirFee" => "Choir Fee", "security" => "Security Chrg."] as $x => $y) echo $trn[$x] ? "$y <br>":"";
						echo "</i>";
						break;
					case 53:
						echo "$payer &nbsp; &nbsp; <i class='w3-right'> ", $trn["moAmt"] ? "Monthly Subscription <br>":"", $trn["mfAmt"] ? "Missionary Fund":"", "</i>";
						break;
					default:
						if ($trn["payBy"] == $FormFields["SYS_ACC"]["NON MEMBER"]) echo $trn["nonMem"];
						else {
							$i = 0;
							foreach ($prefixes as $pre) if ($trn[$pre."Amt"]) {
								echo $trn[$pre], $trn["payFor"] == 7 ? "&nbsp; &nbsp; <i class='w3-right'> ".date_create ($trn[$pre."From"])->format ("F")." - ".date_create ($trn[$pre."To"])->format ("F")."</i>":"", "<br>";
								$i++;
							}
							if (!$i) foreach ($prefixes as $pre) if (!$trn[$pre."Status"]) { /* find first active mem. */
									echo $trn[$pre];
									break;
								}
						}
						echo $trn["mfAmt"] ? "&nbsp; &nbsp; <i class='w3-right'> Missionary Fund </i>":"";
				}
				echo "</td>";

				echo "<td> <center>", $trn["payMode"]." ", ($trn["payMode"] == "CHEQUE" || $trn["payMode"] == "ACC_TRA") ? $trn["chequeNo"] : ($trn["payMode"] == "UPI" ? $trn["upiTrnId"]:""), "</center> </td>";

				echo "<td style='text-align:right;'>";
				if ($trn["payMode"] != "CASH") echo "</td> <td style='text-align:right;'>";
				switch ($trn["payFor"]) {
					case 4:		/* Offertory - Birthday Offering */
					case 7:		/* Offertory - Monthly Offering */
					case 42:	/* Offertory - Monthly Offering Arrear */
					case 234:	/* Offertory - Monthly Offering Arrear 23_24 */
					// case 6:		/* Missionary Fund - Mission Monthly Offering */
					case 83:	/* Missionary Fund - Board for Mission - SKD */
						echo $trn["payBy"] == $FormFields["SYS_ACC"]["NON MEMBER"] ? dispAmt ($trn["amount"]):"";
						foreach ($prefixes as $pre) echo $trn[$pre."Amt"] ? dispAmt ($trn[$pre."Amt"])."<br>":"";
						echo $trn["mfAmt"] ? dispAmt ($trn["mfAmt"]):"";
						break;
					case 25:
						foreach (["hallRent", "cGstAmt", "sGstAmt"] as $x) echo $trn[$x] ? dispAmt ($trn[$x])."<br>":"";
						break;
					case 45:
						foreach (["poorFund", "poorMarryFund", "adminChrg", "choirFee", "security"] as $x) echo $trn[$x] ? dispAmt ($trn[$x])."<br>":"";
						break;
					case 53:
						echo $trn["moAmt"] ? dispAmt ($trn["moAmt"])."<br>":"", $trn["mfAmt"] ? dispAmt ($trn["mfAmt"]):"";
						break;
					default:
						echo dispAmt ($trn["amount"]);
				}
				if ($trn["payMode"] == "CASH") echo "</td> <td>";
				echo "</td>";

				echo $_POST["payMode"] == "bank" ? "<td style='text-align:right;'> ".dispAmt ($totBank + $openBalBank)." </td>":"";

				echo "</tr>";
			}	/* _____________________________________ transactions loop _____________________________________ transactions loop _____________________________________ transactions loop */
$table = ob_get_clean();

			if (($_POST["payForRct"]??"all") != "all" && !($_POST["calc"]??0)) {
				$rctTbl = $table;
				$tblTotCash = $totCash;
				$tblTotBank = $totBank;
				$calcFrom = $_POST["to"] = $_POST["from"];
				$_POST["from"] = "2023-04-01";
				$_POST["calc"] = true;
				require "Reports.php";
				$_POST["calc"] = false;
			}
			if (($_POST["payForRct"]??"all") == "all") {
				echo $table;
				echo "<tr>",
						x_n ("<td> </td>", 4),
						"<td> <center> <b> Total </b> </center> </td>
						<td> </td>
						<td style='text-align:right;'> <b>", dispAmt ($totCash), "</b> </td>
						<td style='text-align:right;' class='bankAmt'> <b>", dispAmt ($totBank), "</b> </td>",
					"</tr>";
			
			}elseif (!$_POST["calc"]) {
				echo "<tr>
						<td> ", date_create ($date[0])->format ("d-m-Y"), " </td>
						<td> </td>
						<td> <center> <b> O B </b> ", $FormFields["Rct"]["select"]["payFor"][$_POST["payForRct"]]??"", "</center> </td>",
						x_n ("<td> </td>", 3),
						"<td style='text-align:right;'> <b class='openBalCash'> 0 </b> </td>
						<td style='text-align:right;'> <b class='openBalBank'> 0 </b> </td>
					</tr>";
				echo $rctTbl;
				echo "<tr>",
						x_n ("<td> </td>", 4),
						"<td> <center> <b> Total </b> </center> </td>
						<td> </td>
						<td style='text-align:right;'> <b>", dispAmt ($tblTotCash), "</b> </td>
						<td style='text-align:right;'> <b>", dispAmt ($tblTotBank), "</b> </td>",
					"</tr>";
echo "<script>
	setTimeout (() => {
			$(`.openBalCash`).html ((parseInt ($(`.openBalCash`).html().replace (/,/g, ``)) + $totCash).toLocaleString (`hi-IN`));
			$(`.openBalBank`).html ((parseInt ($(`.openBalBank`).html().replace (/,/g, ``)) + $totBank).toLocaleString (`hi-IN`));
			$(`.closeBalCash`).html ((parseInt ($(`.closeBalCash`).html().replace (/,/g, ``)) + ".($tblTotCash + $totCash).").toLocaleString (`hi-IN`));
			$(`.closeBalBank`).html ((parseInt ($(`.closeBalBank`).html().replace (/,/g, ``)) + ".($tblTotBank + $totBank).").toLocaleString (`hi-IN`));
		}, 1000);
	</script>";						

if ($_POST["payForPay"] == 0) {
					echo "<tr>
						<td> ", date_create ($date[1])->format ("d-m-Y"), " </td>
						<td> </td>
						<td> <center> <b> C B </b> ", $FormFields["Rct"]["select"]["payFor"][$_POST["payForRct"]]??"", "</center> </td>",
						x_n ("<td> </td>", 3),
						"<td style='text-align:right;'> <b> ", dispAmt ($tblTotCash + $totCash), " </b> </td>
						<td style='text-align:right;'> <b> ", dispAmt ($tblTotBank + $totBank), " </b> </td>
					</tr>";
}
			}
		}else {		 /* view as list ------------------------------------ view as list ------------------------------------ view as list ------------------------------------ */
ob_start();
			foreach ($trns as $trn) {		/* _____________________________________ transactions loop _____________________________________ transactions loop _____________________________________ transactions loop  */
				if (($_POST["calc"]??0) && (($calcFrom == "2023-04-01" && in_array ($trn["tDate"], [$from, $to])) || $trn["tDate"] == $to)) continue;
				if (($_POST["payForRct"]??"all") != "all" && !in_array ($trn["payFor"], $selHeads)) continue;
				$payer = false;
				if ($trn["payBy"] == $FormFields["SYS_ACC"]["NON MEMBER"]) $payer = $trn["nonMem"];
				else {
					foreach ($prefixes as $pre) if ($trn[$pre."Amt"]) { /* check if all mem. amts. are empty */
						$payer = $trn[$pre];
						break;
					}
					if (!$payer) foreach ($prefixes as $pre) if (!$trn[$pre."Status"]) { /* find first active mem. */
							$payer = $trn[$pre];
							break;
						}
				}
				switch ($_POST["payForRct"]) {
					case 6: 	/* Missionary Fund - Mission Monthly Offering */
						if ($trn["payFor"] == 7) {		/* monthly offer. */
							$trn["amount"] = $trn["mfAmt"];
							foreach ($prefixes as $pre) $trn[$pre."Amt"] = 0;
						}
						if ($trn["payFor"] == 53) {		/* monthly offer. - cover */
							$trn["amount"] = $trn["mfAmt"];
							$trn["moAmt"] = 0;
						}
						break;
					case 7: 	/* monthly offer. */
						if ($trn["payFor"] == 7) {		/* monthly offer. */
							$trn["amount"] -= $trn["mfAmt"];
							$trn["mfAmt"] = 0;
						}
					case 53: 	/* monthly offer. - cover */
						if ($trn["payFor"] == 53) {		/* monthly offer. - cover */
							$trn["amount"] = $trn["moAmt"];
							$trn["mfAmt"] = 0;
						}
						break;
					case 25: 	/* hall rent */
					case 98: 	/* c gst */
					case 99: 	/* s gst */
						if ($trn["payFor"] == 25) {		/* hall rent */
							$trn["amount"] = $trn[[25 => "hallRent", 98 => "cGstAmt", 99 => "sGstAmt"][$_POST["payForRct"]]];
							$trn["hallRent"] = $trn["cGstAmt"] = $trn["sGstAmt"] = 0;
							$trn[[25 => "hallRent", 98 => "cGstAmt", 99 => "sGstAmt"][$_POST["payForRct"]]] = $trn["amount"];
						}
						break;
					case 27: 	/* marriage choir fee */
					case 31: 	/* security chrg. */
					case 43: 	/* admin. chrg. */
					case 67: 	/* poor marriage help */
					case 69: 	/* poor help */
						if ($trn["payFor"] == 45) {		/* marriage fee */
							$trn["amount"] = $trn[[27 => "choirFee", 31 => "security", 43 => "adminChrg", 67 => "poorMarryFund", 69 => "poorFund"][$_POST["payForRct"]]];
							foreach (["poorFund", "poorMarryFund", "adminChrg", "choirFee", "security"] as $x) $trn[$x] = 0;
							$trn[[27 => "choirFee", 31 => "security", 43 => "adminChrg", 67 => "poorMarryFund", 69 => "poorFund"][$_POST["payForRct"]]] = $trn["amount"];
						}
						break;
				}
				if (($_POST["payForRct"]??"all") != "all" || !in_array ($trn["payFor"], [108, 109]))
					$trn["payMode"] == "CASH" ? ($totCash += $trn["amount"]) : ($totBank += $trn["amount"]);
				if ((!$trn["amount"]) || ($_POST["calc"]??0)) continue;
?>
<a target="_blank" href="/<?=$PATH;?>/Transactions/Rct/<?=$trn["srNo"];?>/" class="w3-row w3-block w3-card w3-round-xlarge w3-border w3-border-dark-gray py-1 <?=in_array ($trn["payFor"], [108, 109]) ? "w3-darkgreen":"";?>" style="max-width:80%;">
	<p style="width:<?=$col["Rct"][0];?>%;" class="w3-col w3-text-gray w3-small pl-2 pt-2"> <?=$sr++;?> </p>
	<p style="width:<?=$col["Rct"][1];?>%;" class="w3-col"> <?=date_create ($trn["tDate"])->format ("d-m-Y");?> </p>
	<b style="width:<?=$col["Rct"][2];?>%;" class="w3-col w3-text-green w3-center"> <?=$trn["srNo"];?> </b>
	<b style="width:<?=$col["Rct"][3];?>%;" class="w3-col"> <?=$FormFields["Rct"]["select"]["payFor"][$trn["payFor"]];?> </b>
	<p style="width:<?=$col["Rct"][4];?>%;" class="w3-col w3-text-blue w3-right-align"> <?=$trn["payBy"] == $FormFields["SYS_ACC"]["NON MEMBER"] ? "NM" : $trn["famNo"];?> </p>
	<p style="width:<?=$col["Rct"][5];?>%;" class="w3-col px-4">
<?
				switch ($trn["payFor"]) {
					case 25:
						echo "$payer <i class='w3-right w3-right-align'>";
						foreach (["hallRent" => "Rent", "cGstAmt" => "CGST", "sGstAmt" => "SGST"] as $x => $y) echo $trn[$x] ? "$y <br>":"";
						echo "</i>";
						break;
					case 45:
						echo "$payer <i class='w3-right w3-right-align'>";
						foreach (["poorFund" => "Poor Fund", "poorMarryFund" => "Poor Marriage Fund", "adminChrg" => "Admin. Chrg.", "choirFee" => "Choir Fee", "security" => "Security Chrg."] as $x => $y) echo $trn[$x] ? "$y <br>":"";
						echo "</i>";
						break;
					case 53:
						echo "$payer <i class='w3-right w3-right-align'>", $trn["moAmt"] ? "Monthly Subscription <br>":"", $trn["mfAmt"] ? "Missionary Fund":"", "</i>";
						break;
					default:
						if ($trn["payBy"] == $FormFields["SYS_ACC"]["NON MEMBER"]) echo "<span class='w3-left'>".(strpos ($trn["nonMem"], ",") ? substr_replace ($trn["nonMem"], "<br>", strpos ($trn["nonMem"], ",") + 1, 0) : $trn["nonMem"])."</span>";
						else {
							$i = 0;
							foreach ($prefixes as $pre) if ($trn[$pre."Amt"]) {
								echo $trn[$pre], $trn["payFor"] == 7 ? "<i class='w3-right w3-right-align'> ".date_create ($trn[$pre."From"])->format ("F")." - ".date_create ($trn[$pre."To"])->format ("F")."</i>":"", "<br>";
								$i++;
							}
							if (!$i) foreach ($prefixes as $pre) if (!$trn[$pre."Status"]) { /* find first active mem. */
									echo $trn[$pre];
									break;
								}
						}
						echo $trn["mfAmt"] ? "&nbsp; &nbsp; <i class='w3-right w3-right-align'> Missionary Fund </i>":"";
				}
?>
	</p>
	<p style="width:<?=$col["Rct"][6];?>%;" class="w3-col w3-center">
		<?=$trn["payMode"];?> <br>
		<?=($trn["payMode"] == "CHEQUE" || $trn["payMode"] == "ACC_TRA") ? $trn["chequeNo"]:"";?> 
		<?=$trn["payMode"] == "UPI" ? $trn["upiTrnId"]:"";?> 
	</p>
	<p style="width:<?=$col["Rct"][7];?>%;" class="w3-col w3-right-align pr-2">
		<b>
<?
				switch ($trn["payFor"]) {
					case 4:		/* Offertory - Birthday Offering */
					case 7:		/* Offertory - Monthly Offering */
					case 42:	/* Offertory - Monthly Offering Arrear */
					case 234:	/* Offertory - Monthly Offering Arrear 23_24 */
					// case 6:		/* Missionary Fund - Mission Monthly Offering */
					case 83:	/* Missionary Fund - Board for Mission - SKD */
						echo $trn["payBy"] == $FormFields["SYS_ACC"]["NON MEMBER"] ? dispAmt ($trn["amount"]):"";
						foreach ($prefixes as $pre) echo $trn[$pre."Amt"] ? dispAmt ($trn[$pre."Amt"])."<br>":"";
						echo $trn["mfAmt"] ? dispAmt ($trn["mfAmt"]):"";
						break;
					case 25:
						foreach (["hallRent", "cGstAmt", "sGstAmt"] as $x) echo $trn[$x] ? dispAmt ($trn[$x])."<br>":"";
						break;
					case 45:
						foreach (["poorFund", "poorMarryFund", "adminChrg", "choirFee", "security"] as $x) echo $trn[$x] ? dispAmt ($trn[$x])."<br>":"";
						break;
					case 53:
						echo $trn["moAmt"] ? dispAmt ($trn["moAmt"])."<br>":"", $trn["mfAmt"] ? dispAmt ($trn["mfAmt"]):"";
						break;
					default:
						echo dispAmt ($trn["amount"]);
				}
?>
		</b>
	</p>
</a>
<?			} /* _____________________________________ transactions loop _____________________________________ transactions loop _____________________________________ transactions loop  */
$list = ob_get_clean()."<script> $(`.rctCnt`).html (`".($sr - 1)."`); </script>";

			if (($_POST["payForRct"]??"all") != "all" && !($_POST["calc"]??0)) {
				$rctList = $list;
				$listTotCash = $totCash;
				$listTotBank = $totBank;
				$calcFrom = $_POST["to"] = $_POST["from"];
				$_POST["from"] = "2023-04-01";
				$_POST["calc"] = true;
				require "Reports.php";
				$_POST["calc"] = false;
			}
			if (($_POST["payForRct"]??"all") == "all") {
				$_POST["balOpenDate"] = $_POST["from"];
				$_POST["balCloseDate"] = $_POST["to"];
				require "getBal.php";
				echo $list, "<p class='w3-right-align py-3 pr-2 mb-0' style='max-width:80%;'> Total <b class='w3-text-green pl-5'> ", dispAmt ($totCash + $totBank), " </b> </p>";
			
			}elseif (!$_POST["calc"]) {
				$balHtm = "<script>
							$(`.openBalCash`).html ((parseInt ($(`.openBalCash`).html().replace (/,/g, ``)) + $totCash).toLocaleString (`hi-IN`));
							$(`.openBalBank`).html ((parseInt ($(`.openBalBank`).html().replace (/,/g, ``)) + $totBank).toLocaleString (`hi-IN`));
							$(`.closeBalCash`).html ((parseInt ($(`.closeBalCash`).html().replace (/,/g, ``)) + ".($listTotCash + $totCash).").toLocaleString (`hi-IN`));
							$(`.closeBalBank`).html ((parseInt ($(`.closeBalBank`).html().replace (/,/g, ``)) + ".($listTotBank + $totBank).").toLocaleString (`hi-IN`));
						</script>";
				echo $balHtm,
					$rctList,
					"<p class='w3-right-align py-3 pr-2 mb-0' style='max-width:80%;'> Total <b class='w3-text-green pl-5'> ", dispAmt ($listTotCash + $listTotBank), " </b> </p>";
			}
		}



	}else {		/* ------------------------------------------------------------------------ payments ------------------------------------ payments ------------------------------------ payments ------------------------------------ */
		$sql = "SELECT * FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Dr' AND tDate BETWEEN '$from' AND '$to' ORDER BY tDate,srNo";
		$trns = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;
		$totCash = $totBank = 0;
		$sr = 1;
		if (($_POST["typ"]??"") == "tbl") {		/* view as table ------------------------------------ view as table ------------------------------------ view as table ------------------------------------ */
ob_start();
			foreach ($trns as $i => $trn) {		/* _____________________________________ transactions loop _____________________________________ transactions loop _____________________________________ transactions loop */
				if (($_POST["calc"]??0) && (($calcFrom == "2023-04-01" && in_array ($trn["tDate"], [$from, $to])) || $trn["tDate"] == $to)) continue;
				if (($_POST["payForPay"]??"all") != "all" && $_POST["payForPay"] != $trn["payFor"]) continue;
				$trn["payMode"] == "CASH" ? ($totCash += $trn["amount"]) : ($totBank += $trn["amount"]);
				if ($_POST["calc"]??0) continue;
				if ($_POST["payMode"] == "bank" && $trn["payMode"] == "CASH") continue;

				if ($_POST["payMode"] == "bank") {
					$revSumBank = 0;
					for ($i+=1; $i < count ($trns); $i++) $trns[$i]["payMode"] == "CASH" ? "":($revSumBank += $trns[$i]["amount"]);
				}

				echo "<tr>";
					// echo "<td class='w3-right-align'>", $sr++, " </td>";
					echo "<td> ", date_create ($trn["tDate"])->format ("d-m-Y"), " </td>";
					echo "<td> ", $trn["srNo"], "</td>";
					echo "<td> ", $FormFields["Pay"]["select"]["payFor"][$trn["payFor"]], " </td>";
					echo "<td> </td>";
					echo "<td>", $trn["nonMem"], " --> ", $trn["particulars"], "</td>";
					echo "<td> <center>", $trn["payMode"]." ", ($trn["payMode"] == "CHEQUE" || $trn["payMode"] == "ACC_TRA") ? $trn["chequeNo"] : ($trn["payMode"] == "UPI" ? $trn["upiTrnId"]:""), "</center> </td>";
					echo "<td style='text-align:right;'>", $trn["payMode"] == "CASH" ? "":"</td> <td style='text-align:right;'>", dispAmt ($trn["amount"]), $trn["payMode"] == "CASH" ? "</td> <td>":"", " </td>";

					echo $_POST["payMode"] == "bank" ? "<td style='text-align:right;'> ".dispAmt ($revSumBank + $closeBalBank)." </td>":"";

				echo "</tr>";
			}	/* _____________________________________ transactions loop _____________________________________ transactions loop _____________________________________ transactions loop */
$table = ob_get_clean();

			if (($_POST["payForPay"]??"all") != "all" && !($_POST["calc"]??0)) {
				$payTbl = $table;
				$tblTotCash = $totCash;
				$tblTotBank = $totBank;
				$calcFrom = $_POST["to"] = $_POST["from"];
				$_POST["from"] = "2023-04-01";
				$_POST["calc"] = true;
				require "Reports.php";
				$_POST["calc"] = false;
			}
			if (($_POST["payForPay"]??"all") == "all") {
				echo $table;
				echo "<tr>",
						x_n ("<td> </td>", 4),
						"<td> <center> <b> Total </b> </center> </td>
						<td> </td>
						<td style='text-align:right;'> <b>", dispAmt ($totCash), "</b> </td>
						<td style='text-align:right;' class='bankAmt'> <b>", dispAmt ($totBank), "</b> </td>",
					"</tr>";
			
			}elseif (!$_POST["calc"]) {
if ($_POST["payForRct"] == 0) {
				echo "<tr>
						<td> ", date_create ($date[0])->format ("d-m-Y"), " </td>
						<td> </td>
						<td> <center> <b> O B </b> ", $FormFields["Pay"]["select"]["payFor"][$_POST["payForPay"]]??"", "</center> </td>",
						x_n ("<td> </td>", 3),
						"<td style='text-align:right;'> <b> ", dispAmt ($totCash), " </b> </td>
						<td style='text-align:right;'> <b> ", dispAmt ($totBank), " </b> </td>
					</tr>";
}
				echo $payTbl;
				echo "<tr>",
						x_n ("<td> </td>", 4),
						"<td> <center> <b> Total </b> </center> </td>
						<td> </td>
						<td style='text-align:right;'> <b>", dispAmt ($tblTotCash), "</b> </td>
						<td style='text-align:right;'> <b>", dispAmt ($tblTotBank), "</b> </td>",
					"</tr>
					<tr>
						<td> ", date_create ($date[1])->format ("d-m-Y"), " </td>
						<td> </td>
						<td> <center> <b> C B </b> ", $FormFields["Pay"]["select"]["payFor"][$_POST["payForPay"]]??"", "</center> </td>",
						x_n ("<td> </td>", 3),
						"<td style='text-align:right;'> <b class='closeBalCash'> 0 </b> </td>
						<td style='text-align:right;'> <b class='closeBalBank'> 0 </b> </td>
					</tr>";
echo "<script>
	setTimeout (() => {
			$(`.openBalCash`).html ((parseInt ($(`.openBalCash`).html().replace (/,/g, ``)) - $totCash).toLocaleString (`hi-IN`));
			$(`.openBalBank`).html ((parseInt ($(`.openBalBank`).html().replace (/,/g, ``)) - $totBank).toLocaleString (`hi-IN`));
			$(`.closeBalCash`).html ((parseInt ($(`.closeBalCash`).html().replace (/,/g, ``)) - ".($tblTotCash + $totCash).").toLocaleString (`hi-IN`));
			$(`.closeBalBank`).html ((parseInt ($(`.closeBalBank`).html().replace (/,/g, ``)) - ".($tblTotBank + $totBank).").toLocaleString (`hi-IN`));
		}, 1000);
	</script>";						
			}
		}else {		 /* view as list ------------------------------------ view as list ------------------------------------ view as list ------------------------------------ */
ob_start();
			foreach ($trns as $trn) {		/* _____________________________________ transactions loop _____________________________________ transactions loop _____________________________________ transactions loop  */
				if (($_POST["calc"]??0) && (($calcFrom == "2023-04-01" && in_array ($trn["tDate"], [$from, $to])) || $trn["tDate"] == $to)) continue;
				if (($_POST["payForPay"]??"all") != "all" && $_POST["payForPay"] != $trn["payFor"]) continue;
				if (($_POST["payForPay"]??"all") != "all" || !in_array ($trn["payFor"], [116, 117]))
					$trn["payMode"] == "CASH" ? ($totCash += $trn["amount"]) : ($totBank += $trn["amount"]);
				if ((!$trn["amount"]) || ($_POST["calc"]??0)) continue;
?>
<a target="_blank" href="/<?=$PATH;?>/Transactions/Pay/<?=$trn["srNo"];?>/" class="w3-row w3-block w3-card w3-round-xlarge w3-border w3-border-dark-gray py-1 ml-auto <?=in_array ($trn["payFor"], [116, 117]) ? "w3-darkred":"";?>" style="max-width:80%;">
	<p style="width:<?=$col["Pay"][0];?>%;" class="w3-col w3-text-gray w3-small pl-2 pt-2"> <?=$sr++;?> </p>
	<p style="width:<?=$col["Pay"][1];?>%;" class="w3-col"> <?=date_create ($trn["tDate"])->format ("d-m-Y");?> </p>
	<b style="width:<?=$col["Pay"][2];?>%;" class="w3-col w3-text-red w3-center"> <?=$trn["srNo"];?> </b>
	<b style="width:<?=$col["Pay"][3];?>%;" class="w3-col"> <?=$FormFields["Pay"]["select"]["payFor"][$trn["payFor"]];?> </b>
	<p style="width:<?=$col["Pay"][4];?>%;" class="w3-col"> <?=$trn["nonMem"], " --> ", $trn["particulars"];?> </p>
	<p style="width:<?=$col["Pay"][5];?>%;" class="w3-col w3-center">
		<?=$trn["payMode"];?> <br>
		<?=($trn["payMode"] == "CHEQUE" || $trn["payMode"] == "ACC_TRA") ? $trn["chequeNo"]:"";?>
	</p>
	<p style="width:<?=$col["Pay"][6];?>%;" class="w3-col w3-right-align pr-2"> <b> <?=dispAmt ($trn["amount"]);?> </b> </p>
</a>
<? 			}
$list = ob_get_clean()."<script> $(`.payCnt`).html (`".($sr - 1)."`); </script>";

			if (($_POST["payForPay"]??"all") != "all" && !($_POST["calc"]??0)) {
				$payList = $list;
				$listTotCash = $totCash;
				$listTotBank = $totBank;
				$calcFrom = $_POST["to"] = $_POST["from"];
				$_POST["from"] = "2023-04-01";
				$_POST["calc"] = true;
				require "Reports.php";
				$_POST["calc"] = false;
			}
			if (($_POST["payForPay"]??"all") == "all") {
				$_POST["balOpenDate"] = $_POST["from"];
				$_POST["balCloseDate"] = $_POST["to"];
				require "getBal.php";
				echo $list, "<p class='w3-right-align py-3 pr-2 mb-0 ml-auto' style='max-width:80%;'> Total <b class='w3-text-red pl-5'> ", dispAmt ($totCash + $totBank), " </b> </p>";
			
			}elseif (!$_POST["calc"]) {
				$balHtm = "<script>
							$(`.openBalCash`).html ((parseInt ($(`.openBalCash`).html().replace (/,/g, ``)) - $totCash).toLocaleString (`hi-IN`));
							$(`.openBalBank`).html ((parseInt ($(`.openBalBank`).html().replace (/,/g, ``)) - $totBank).toLocaleString (`hi-IN`));
							$(`.closeBalCash`).html ((parseInt ($(`.closeBalCash`).html().replace (/,/g, ``)) - ".($listTotCash + $totCash).").toLocaleString (`hi-IN`));
							$(`.closeBalBank`).html ((parseInt ($(`.closeBalBank`).html().replace (/,/g, ``)) - ".($listTotBank + $totBank).").toLocaleString (`hi-IN`));
						</script>";
				echo $balHtm,
					$payList,
					"<p class='w3-right-align py-3 pr-2 mb-0 ml-auto' style='max-width:80%;'> Total <b class='w3-text-red pl-5'> ", dispAmt ($listTotCash + $listTotBank), " </b> </p>";
			}
		}
	}


/* ================ not post request ================ not post request ================ not post request ================ not post request ================ not post request ================ */
}else {
	$maxDate = date("Y-m-d");
	$sql = "SELECT MIN(tDate) AS minDate FROM Tran$TBL_ID WHERE status='$ACTIVE'";
	$minDate = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["minDate"];
	$balHtm = "<i class='w3-row w3-block w3-card w3-center py-3' style='background-color:%COLOR%;'>
				<span class='w3-col s4'> Opening Balance Cash <b class='w3-text-aqua openBalCash'> </b> </span>
				<span class='w3-col s2'> Bank <b class='w3-text-aqua openBalBank'> </b> </span>
				<span class='w3-col s4'> Closing Balance Cash <b class='w3-text-amber closeBalCash'> </b> </span>
				<span class='w3-col s2'> Bank <b class='w3-text-amber closeBalBank'> </b> </span>
			</i>";
	$getDate = explode ("_", $_GET["date"]??"");
	if (in_array (strlen ($_GET["date"]??""), [11, 21])) {
		$date1 = $getDate[0];
		$date2 = $getDate[1];
	}elseif (strlen ($getDate[0]) == 10) $date = $getDate[0];
	if (strlen ($getDate[0]) == 7) $month = $getDate[0];
?>
<div class="w3-padding w3-card w3-round-xxlarge w3-border w3-border-blue w3-{{THEME ? 'white' : 'black'}}">
	<center> <h5 class="w3-blue w3-card w3-round-xxlarge p-2">
		<a href="#" target="_blank" class="w3-btn w3-border w3-border-black w3-hover-black w3-text-black w3-left fa fa-book p-2 circ printLedgerLnk" title="Print Ledger"> </a>
		Reports
		<a href="#" target="_blank" class="w3-btn w3-border w3-border-black w3-hover-black w3-text-black fa fa-print p-2 circ printLnk" title="Print Report"> </a>
		<a href="#" target="_blank" class="w3-btn w3-border w3-border-black w3-hover-black w3-text-black fa fa-file-excel-o p-2 ml-2 circ excelLnk" download="" title="Download As Excel"> </a>
		<a href="#" target="_blank" class="w3-btn w3-border w3-border-black w3-hover-black w3-text-black p-1 px-3 printLnkBank w3-aqua w3-right badge-pill" title="Print only Bank transactions"> <i class="fa fa-print"> </i> Bank Trans. Only </a>
	</h5> </center>

	<div class="w3-row w3-black w3-center pt-2 sticky-top" style="top:30px; z-index:2;">
		<div class="w3-col s4">
			<div class="w3-row">
				<p class="w3-col s6 mb-0 px-1"> From <br> <input type="date" class="w3-input" id="from" min="<?=$minDate;?>" max="<?=$maxDate;?>" value="<?=$date1??"";?>" /> </p>
				<p class="w3-col s6 mb-0 px-1"> To <br> <input type="date" class="w3-input" id="to" min="<?=$minDate;?>" max="<?=$maxDate;?>" value="<?=$date2??"";?>" /> </p>
			</div>
		</div>
		<p class="w3-col s2 mb-0 px-1"> Towards <br>
			<select class="w3-select" id="payForRct">
				<option value="all"> all </option>
<? unset ($FormFields["Rct"]["select"]["payFor"][""]); foreach ($FormFields["Rct"]["select"]["payFor"] as $val => $label) { ?> <option value="<?=$val;?>"> <?=$label;?> </option> <? } ?>
			</select>
		</p>
		<div class="w3-col s4">
			<div class="w3-row">
				<p class="w3-col s6 mb-0 px-1"> Day <br> <input type="date" class="w3-input" id="day" min="<?=$minDate;?>" max="<?=$maxDate;?>" value="<?=$date??"";?>" /> </p>
				<p class="w3-col s6 mb-0 px-1"> Month <br> <input type="month" class="w3-input" id="reportMonth" min="<?=substr ($minDate, 0, 7);?>" max="<?=substr ($maxDate, 0, 7);?>" value="<?=$month??"";?>" /> </p>
			</div>
		</div>
		<p class="w3-col s2 mb-0 px-1"> Towards <br>
			<select class="w3-select" id="payForPay">
				<option value="all"> all </option>
<? unset ($FormFields["Pay"]["select"]["payFor"][""]); foreach ($FormFields["Pay"]["select"]["payFor"] as $val => $label) { ?> <option value="<?=$val;?>"> <?=$label;?> </option> <? } ?>
			</select>
		</p>
	</div>

	<div class="w3-row">
		<div class="w3-half"> <b class="w3-center w3-padding w3-card w3-block w3-green w3-round-xxlarge"> <span class="rctCnt"> </span> RECEIPTS </b> </div>
		<a href="#middle" class="w3-half"> <b class="w3-center w3-padding w3-card w3-block w3-red w3-round-xxlarge"> <span class="payCnt"> </span> PAYMENTS </b> </a>
	</div>

	<?=str_replace ("%COLOR%", "darkgreen", $balHtm);?>
	<a href="#middle" class="w3-red w3-large w3-right fa fa-arrow-down sticky-top badge-pill py-1 px-3" style="top:40px; z-index:2;"> Payments </a>
	<a href="#top" class="w3-green w3-large fa fa-arrow-up sticky-top badge-pill py-1 px-3" style="top:40px;"> Receipts </a>

	<div class="w3-hoverable-dark" id="receipts_list"> </div>
	
	<?=str_replace ("%COLOR%", "darkgreen", $balHtm);?>
	<a href="#bottom" class="w3-red w3-large w3-right fa fa-arrow-down sticky-top badge-pill py-1 px-3" style="top:40px; z-index:5;"> Payments </a>
	<hr class="w3-blue my-5" style="height:2px;" id="middle">
	<br>
	<a href="#middle" class="w3-red w3-large fa fa-arrow-up sticky-top badge-pill py-1 px-3" style="top:40px;"> Payments </a>

	<h5 class="w3-text-red w3-center"> <span class="payCnt"> </span> PAYMENTS </h5>
	<?=str_replace ("%COLOR%", "darkred", $balHtm);?>
	<div class="w3-hoverable-dark" id="vouchers_list"> </div>
	<?=str_replace ("%COLOR%", "darkred", $balHtm);?>
	<i id="bottom"> </i>

</div>
<script>
	const FR = $(`#from`);
	const TO = $(`#to`);
	const DAY = $(`#day`);
	const MNTH = $(`#reportMonth`);
	const RCT_FOR = $(`#payForRct`);
	const PAY_FOR = $(`#payForPay`);

	let from, to, payForRct = `<?=$_GET["payForRct"]??"all";?>`, payForPay = `<?=$_GET["payForPay"]??"all";?>`;
	
	$(document).ready (() => {
		FR.change (function() { TO[0].min = this.value; });
		$(`#from,#to`).change (() => location = `/<?=$PATH;?>/Reports/${FR.val()}_${TO.val()}/${payForRct}/${payForPay}/`);
		DAY.change (function() { if (this.value) location = `/<?=$PATH;?>/Reports/${this.value}/${payForRct}/${payForPay}/`; });
		MNTH.change (() => location = `/<?=$PATH;?>/Reports/${MNTH.val()}/${payForRct}/${payForPay}/`);
		RCT_FOR.change (function() {
			payForRct = this.value;
			$(`#vouchers_list`).html (``);
			let vrHead = $(`#payForPay > :contains(${$(`#payForRct > [value='${this.value}']`).text()})`);
			payForPay = (this.value == 107) ? 106 : ((this.value == 127) ? 125 : (vrHead.val()??0));

			let formData = new FormData();
			formData.append (`trnTyp`, `receipts`);
			getReports (formData, `receipts_list`);

			PAY_FOR.val (payForPay);
			if (!vrHead.length) return;
			formData.append (`trnTyp`, `vouchers`);
			getReports (formData, `vouchers_list`);
		});
		PAY_FOR.change (function() {
			payForPay = this.value;
			$(`#receipts_list`).html (``);
			let rtHead = $(`#payForRct > :contains(${$(`#payForPay > [value='${this.value}']`).text()})`);
			payForRct = (this.value == 106) ? 107 : ((this.value == 125) ? 127 : (rtHead.val()??0));

			let formData = new FormData();
			formData.append (`trnTyp`, `vouchers`);
			getReports (formData, `vouchers_list`);
			
			RCT_FOR.val (payForRct);
			if (!rtHead.length) return;
			formData.append (`trnTyp`, `receipts`);
			getReports (formData, `receipts_list`);
		});
		RCT_FOR.val (payForRct);
		PAY_FOR.val (payForPay);
		// setTimeout (() => DAY.change(), 200);
<?  if (!($_GET["date"]??0)) {
?>
		DAY[0].valueAsDate = new Date();
		setTimeout (() => DAY.change(), 200);
<?  }
    if (in_array (strlen ($_GET["date"]??0), [11, 21])) {
?>
		from = FR.val();
		to = TO.val();
		DAY.val (``);
		MNTH.val (``);
		let formData = new FormData();
		formData.append (`trnTyp`, `receipts`);
		getReports (formData, `receipts_list`);
		formData.append (`trnTyp`, `vouchers`);
		getReports (formData, `vouchers_list`);
<?
    }elseif (strlen ($getDate[0]) == 10) {
?>
		from = to = DAY.val();
		FR.val (``);
		TO.val (``);
		MNTH.val (``);
		let formData = new FormData();
		formData.append (`trnTyp`, `receipts`);
		getReports (formData, `receipts_list`);
		formData.append (`trnTyp`, `vouchers`);
		getReports (formData, `vouchers_list`);
		setTimeout (() => DAY.focus(), 100);
<?
    }
    if (strlen ($getDate[0]) == 7) {
?>
		from = `${MNTH.val()}-01`;
		to = `${MNTH.val()}-${MN_END[parseInt (MNTH.val().slice (-2))]}`;
		FR.val (``);
		TO.val (``);
		DAY.val (``);
		let formData = new FormData();
		formData.append (`trnTyp`, `receipts`);
		getReports (formData, `receipts_list`);
		formData.append (`trnTyp`, `vouchers`);
		getReports (formData, `vouchers_list`);
		setTimeout (() => MNTH.focus(), 100);
<?
    }
?>		
	});
	function getReports (formData, eleId) {
		links();
		$(`[class*='Bal']`).html (0);
		$(`.rctCnt, .payCnt`).html (``);
		formData.append (`payForRct`, payForRct);
		formData.append (`payForPay`, payForPay);
		formData.append (`from`, from);
		formData.append (`to`, to);
		$.ajax ({
			type: `POST`,
			url: `/<?=$PATH;?>/Reports.php`,
			enctype: `multipart/form-data`,
			dataType: `text`,
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: () => $(`#${eleId}`).html (`<i class="w3-xlarge w3-spin fa fa-cog"></i>`),
			success: resp => $(`#${eleId}`).html (resp),
			error: () => $(`#${eleId}`).html (`<p class="w3-yellow"> Server error, please try again. </p>`)
		});
	}
	function links() {
		let lnk = `/<?=$PATH;?>/print_report/${from}_${to}/${payForRct}/${payForPay}/`;
		$(`a.printLedgerLnk`)[0].href = `/<?=$PATH;?>/print_ledger/${from}_${to}/${payForRct}/${payForPay}/`;
		$(`a.printLnk`)[0].href = `${lnk}all/`;
		$(`a.printLnkBank`)[0].href = `${lnk}bank/`;
		$(`a.excelLnk`)[0].download = `Report_${from}_${to}.csv`;
		$(`a.excelLnk`)[0].href = `http://${window.location.hostname}/<?=$PATH;?>/downloads/${$(`a.excelLnk`)[0].download}`;
	}
</script>
<? } ?>

