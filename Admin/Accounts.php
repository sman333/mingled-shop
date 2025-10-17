<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");
	unset ($FormFields["Ser"]["select"]["serHead"][0], $FormFields["Ser"]["select"]["serHead"][""]);

	$from = $_POST["rangeFrom"];
    $to = $_POST["rangeTo"];
	$payDone = [];
	$accounts = [];
	foreach ($FormFields["Ser"]["select"]["serHead"] as $h => $head) {
		if ($head == "ACCOUNTS") continue;

		$accounts[$head] = [];
		$sql = "SELECT * FROM Ser WHERE status='$ACTIVE' AND serHead='$h' ORDER BY serFor DESC,remarks + 0,serName";
		$sers = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC);
		foreach ($sers as $ser) {
			$sumColSerId = 0;
			switch ($ser["serName"]) {
				case "Marriage Fee": continue 2;

				case "Administrative Charges":
					$sumCol = "adminChrg";
					$sumColSerId = 45;
					break;
				case "CGST":
					$sumCol = "cGstAmt";
					$sumColSerId = 25;
					break;
				case "Hall Rent":
					$sumCol = "hallRent";
					$sumColSerId = 25;
					break;
				case "Church Choir":
					$sumCol = "choirFee";
					$sumColSerId = 45;
					break;
				case "Mission Monthly Offering":
					$sumCol = "mfAmt";
					$sumColSerId = "7,53";
					break;
				case "Monthly Offering - cover":
					$sumCol = "moAmt";
					$sumColSerId = 53;
					break;
				case "Poor Help":
					$sumCol = "poorFund";
					$sumColSerId = 45;
					break;
				case "Poor Marriage Help":
					$sumCol = "poorMarryFund";
					$sumColSerId = 45;
					break;
				case "SGST":
					$sumCol = "sGstAmt";
					$sumColSerId = 25;
					break;
				case "Security Charges":
					$sumCol = "security";
					$sumColSerId = 45;
					break;

				default :
					$sumCol = "amount";
					$sumColSerId = $ser["ser_id"];
			}
			
			if ($ser["serFor"] == "Rct") {
				$sql = "SELECT SUM($sumCol) as tot FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Cr' AND tDate BETWEEN '$from' AND '$to' AND payFor IN ($sumColSerId)";
				$accounts[$head][$ser["serName"]]["rTot"] = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["tot"]??0;
				if (in_array ($ser["serName"], ["Mission Monthly Offering", "Poor Help", "Poor Marriage Help", "Church Choir"])) {
					$sql = "SELECT SUM(amount) as tot FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Cr' AND tDate BETWEEN '$from' AND '$to' AND payFor='".$ser["ser_id"]."'";
					$accounts[$head][$ser["serName"]]["rTot"] += mysqli_fetch_assoc (mysqli_query ($conn, $sql))["tot"]??0;
				}
			}
			$payFor = array_search ($ser["serName"], $FormFields["Pay"]["select"]["payFor"]);
			if ($payFor && array_search ($ser["serName"], $payDone) === false) {
				$sql = "SELECT SUM(amount) as tot FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Dr' AND tDate BETWEEN '$from' AND '$to' AND payFor='$payFor'";
				$accounts[$head][$ser["serName"]]["pTot"] = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["tot"]??0;
				array_push ($payDone, $ser["serName"]);
			}
		}
	}

	$sql = "SELECT SUM(mfAmt) as tot FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Cr' AND tDate BETWEEN '$from' AND '$to' AND payFor='7'";
	$accounts["Offertory"]["Monthly Offering"]["rTot"] -= mysqli_fetch_assoc (mysqli_query ($conn, $sql))["tot"]??0;

	$accounts["Church Administration"]["Caution Deposit."]["rTot"] = ($accounts["Church Administration"]["Caution Deposit."]["rTot"]??0) + ($accounts["Marriage"]["Caution Deposit (Church)"]["rTot"]??0);
	$accounts["Church Administration"]["Caution Deposit."]["pTot"] = ($accounts["Church Administration"]["Caution Deposit."]["pTot"]??0) + ($accounts["Marriage"]["Caution Deposit (Church)"]["pTot"]??0);
	unset ($accounts["Marriage"]);

	$csv = [];
	array_push ($csv, "Head,Receipts,Payments");

	$rctTot = $payTot = 0;
	foreach ($accounts as $mainHead => $subHeads) {
		$rTot = $pTot = 0;
		$htm = "";
		$csvStr = [];
		foreach ($subHeads as $subHead => $sh)
			if (($sh["rTot"]??0) || ($sh["pTot"]??0)) {
				$htm .= "<tr>
							<td class='pl-5' title='Id ".($ser["ser_id"]??"")."'> $subHead </td>
							<td class='w3-right-align'> ".(($sh["rTot"]??0) ? dispAmt ($sh["rTot"]):"")." </td>
							<td class='w3-right-align'> ".(($sh["pTot"]??0) ? dispAmt ($sh["pTot"]):"")." </td>
						</tr>";
				$rTot += $sh["rTot"]??0;
				$pTot += $sh["pTot"]??0;

				array_push ($csvStr, join (",", [str_replace ("amp;", "", $subHead), (($sh["rTot"]??0) ? $sh["rTot"]:""), (($sh["pTot"]??0) ? $sh["pTot"]:"")]));
			}

		if ($htm != "") {
			echo "<tr>";
				echo "<td class='w3-text-blue'> <b> $mainHead </b> </td>";
				echo "<td> </td>";
				echo "<td> </td>";
			echo "</tr>";
			echo $htm;
			echo "<tr>";
				echo "<td> </td>";
				echo "<td class='w3-right-align w3-text-green'> <b> ", $rTot ? dispAmt ($rTot):"", " </b> </td>";
				echo "<td class='w3-right-align w3-text-red'> <b> ", $pTot ? dispAmt ($pTot):"", " </b> </td>";
			echo "</tr>";

			array_push ($csv, str_replace ("amp;", "", $mainHead).",,");
			foreach ($csvStr as $str) array_push ($csv, $str);
			array_push ($csv, ",".($rTot ? $rTot:"").",".($pTot ? $pTot:""));
		}
		$rctTot += $rTot;
		$payTot += $pTot;
	}
	echo "<tr>";
		echo "<td> <b> TOTAL </b> </td>";
		echo "<td class='w3-right-align'> <b> ".dispAmt ($rctTot)." </b> </td>";
		echo "<td class='w3-right-align'> <b> ".dispAmt ($payTot)." </b> </td>";
	echo "</tr>";

	array_push ($csv, join (",", ["TOTAL", $rctTot, $payTot]));
	$fileName = "Accounts_$from"."_$to";
	require "saveCsvFile.php";

}else {
	$maxDate = date("Y-m-d");
	$sql = "SELECT MIN(tDate) AS minDate FROM Tran$TBL_ID WHERE status='$ACTIVE'";
	$minDate = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["minDate"];
?>
<style>
    table,tr,th,td {
		border: 1px solid darkslategray;
		border-collapse: collapse;
	}
    th { font-weight: bold; }
    td,th {
        padding: 5px 20px;
        min-width: 200px;
    }
</style>
<div class="w3-card w3-border w3-border-blue w3-topbar w3-padding w3-round-xxlarge w3-{{THEME ? 'white' : 'black'}} mb-5">
	<h5 class="w3-row w3-center"> Receipt &amp; Payment Accounts
		<a href="#" target="_blank" class="w3-btn w3-border w3-border-blue w3-text-blue w3-hover-blue fa fa-print p-2 ml-2 circ printLnk" title="Print Accounts"> </a>
		<a href="#" target="_blank" class="w3-btn w3-border w3-border-green w3-text-green w3-hover-green fa fa-file-excel-o p-2 ml-2 circ excelLnk" download="" title="Download As Excel"> </a>
	</h5>
	<div class="w3-row w3-content w3-center">
		<div class="w3-half w3-padding">
			<div class="w3-row-padding">
				<p class="w3-col s6"> From <br> <input type="date" class="w3-input" id="rangeFrom" min="<?=$minDate;?>" max="<?=$maxDate;?>" /> </p>
				<p class="w3-col s6"> To <br> <input type="date" class="w3-input" id="rangeTo" min="<?=$minDate;?>" max="<?=$maxDate;?>" /> </p>
			</div>
		</div>
		<div class="w3-half w3-padding">
			<div class="w3-row-padding">
				<p class="w3-col s6"> Day <br> <input type="date" class="w3-input" id="day" min="<?=$minDate;?>" max="<?=$maxDate;?>" /> </p>
				<p class="w3-col s6"> Month <br> <input type="month" class="w3-input" min="<?=substr ($minDate, 0, 7);?>" max="<?=substr ($maxDate, 0, 7);?>" /> </p>
			</div>
		</div>
	</div>
</div>
<div class="w3-large pb-5">
	<table class="w3-{{THEME ? 'white' : 'black'}} mx-auto">
		<thead class="w3-center w3-text-gray"> <tr>
			<th> Head </th>
			<th> Receipts </th>
			<th> Payments </th>
		</tr> </thead>
		<tbody class="accs"> </tbody>
	</table>
</div>
<script>
    let from, to;
	$(document).ready (() => {
		$(`#day`)[0].valueAsDate = new Date();
        $(`#rangeFrom`).change (function() { $(`#rangeTo`)[0].min = this.value; });
        $(`#rangeFrom,#rangeTo`).change (function() {
			from = $(`#rangeFrom`).val();
			to = $(`#rangeTo`).val();
			$(`[type='month'],#day`).val (``);
			getAccs (`accs`);
        });
        $(`[type='month']`).change (function() {
			from = `${this.value}-01`;
			to = `${this.value}-${MN_END[parseInt (this.value.slice (-2))]}`;
			$(`#rangeFrom,#rangeTo,#day`).val (``);
			getAccs (`accs`);
        });
        $(`#day`).change (function() {
			from = to = this.value;
			$(`#rangeFrom,#rangeTo,[type='month']`).val (``);
			getAccs (`accs`);
        });
		setTimeout (() => $(`#day`).change(), 200);
	});
	function getAccs (eleId) {
		links();
		let formData = new FormData();
		formData.append (`rangeFrom`, from);
		formData.append (`rangeTo`, to);
		$.ajax ({
			type: `POST`,
			url: `/<?=$PATH;?>/Accounts.php`,
			enctype: `multipart/form-data`,
			dataType: `text`,
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: () => $(`.${eleId}`).html (`<i class="w3-xlarge w3-spin fa fa-cog"></i>`),
			success: resp => $(`.${eleId}`).html (resp),
			error: () => $(`.${eleId}`).html (`<p class="w3-yellow"> Server error, please try again. </p>`)
		});
	}
	function links() {
		$(`a.printLnk`)[0].href=`/<?=$PATH;?>/print_accounts/${from}_${to}/`;
		$(`a.excelLnk`)[0].download=`Accounts_${from}_${to}.csv`;
		$(`a.excelLnk`)[0].href=`http://${window.location.hostname}/<?=$PATH;?>/downloads/${$(`a.excelLnk`)[0].download}`;
	}
</script>
<? } ?>

