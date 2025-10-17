<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

$fields = $FormFields["Pay"]["fields"];
foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields["Pay"]["keys"], $vals);
?>
<center> <h6> Payments </h6> </center>
<div id="statusBar" class="sticky-top"> </div>
<div class="p-2" style="overflow-x:auto;">
	<div style="min-width:1080px;">
<? $add = true; $sql = "SELECT * FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Dr' AND srNo='".($_GET["srNo"]??"")."'";
$trn = mysqli_fetch_assoc (mysqli_query ($conn, $sql));
if (!($trn??0)) {
	$sql = "SELECT srNo,tDate FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Dr' ORDER BY trn_id DESC LIMIT 1";
	$trn = mysqli_fetch_assoc (mysqli_query ($conn, $sql));
	$trn["srNo"] = $trn["srNo"] + 1;
}
$payEdit = $trn["trn_id"]??0;
if ($_GET["mode"]??0) {
	$sql = "SELECT * FROM Chb WHERE status='$ACTIVE' AND chb_id='".($_GET["srNo"]??"")."'";
	$chb = mysqli_fetch_assoc (mysqli_query ($conn, $sql))??0;
	if ($chb) {
		if (($_GET["mode"]??"") == "church") $trn["payFor"] = 81;
		if (($_GET["mode"]??"") == "hall") $trn["payFor"] = 38;
		$trn["nonMem"] = ($chb["name"]??"")." ";
		$trn["particulars"] = $chb["part"]." ".$chb["progDate"]." (".(($chb["church"]??0) ? "Church":"")." ".($chb["hall"]??"")." ".(($chb["kitchen"]??0) ? "Kitchen":"").")";
	}
}
require "Forms/Pay.php"; ?>
<br>
<p> Recent </p>
<? $add = false; $sql = "SELECT * FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Dr' ORDER BY tDate DESC,trn_id DESC LIMIT 5";
$trns = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;
if ($trns) foreach ($trns as $trn) { require "Forms/Pay.php"; }
else { ?> <h5 class="w3-center w3-card w3-padding-large w3-black w3-opacity"> no recent activity </h5> <? } ?>
    </div>
</div>

<datalist id="commonPartList">
<?
	$sql = "SELECT DISTINCT particulars,COUNT(*) FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Dr' GROUP BY particulars HAVING COUNT(*) > 5";
	foreach (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[] as $p) echo "<option value='", $p["particulars"], "'/>";
?>
</datalist>

<script>
	const PayEdit = <?=($_GET["srNo"]??0) ? $payEdit : "false";?>;
	const AMOUNT = $(`#addPayForm [name='amount']`);
	const BILL = $(`#newbillInvNo,#newbillInvDate`);
	const COMM = $(`#newcommResoNo,#newcommResoDate`);
	const PAYFOR = $(`#newpayFor`);
	const SERHEAD = $(`#newserHead`);
	const SR_NO = $(`#addPayForm [name='srNo']`);
	const T_DATE = $(`#addPayForm [name='tDate']`);
	let cqStr = `#newchequeDate,#newchequeNo,#newchequeBank`;
	$(document).ready (() => {
<? foreach (["Payments_Add", "Payments_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
		$(`form`).submit (function() {
				event.preventDefault();
				let formData = new FormData ($(this)[0]);
				if (PayEdit) formData.append (`trn_id`, PayEdit);
<? if ($_GET["mode"]??0) echo "formData.append (`chbId`, ", $_GET["srNo"], ");"; ?>
				postFormData (`FormValid/payDetailsValidation.php`, formData);
			});
<? 	break; } ?>
		$(`[type='date']`).click (function() { this.showPicker(); });
		PAYFOR.blur (function() { if (this.value.length) this.size = 1; })
			.change (function() { PayEdit ? ``:SERHEAD.val() == `14` ? $(`#newnonMem`).val ($(`#newpayFor option[value='${this.value}']`).text()):``; })
			.children().hover (function() { PAYFOR.val (this.value).change(); })
			.click (() => {
				AMOUNT.focus();
				PAYFOR.attr (`size`, 1);
				SERHEAD.attr (`size`, 1);
			});
		SERHEAD.attr (`size`, (SERHEAD.val() == `` ? SERHEAD.children().length : 1))
			.focus (function() { this.size = SERHEAD.children().length; })
			.blur (function() { this.size = 1; })
			.change (function() {
				// PAYFOR.val (``).change().attr (`size`, $(`#newpayFor .serHead,#newpayFor .serHead${this.value}`).length);
				PAYFOR[0].size = $(`#newpayFor .serHead,#newpayFor .serHead${this.value}`).length;
				$(`[class^='serHead']`).hide();
				$(`.serHead,.serHead${this.value}`).show();
			})
			.children().hover (function() { SERHEAD.val (this.value).change(); })
			.click (() => {
				SERHEAD[0].size = 1;
				PAYFOR.focus();
			});
		$(`#newpayMode`).change (function() {
				if (this.value == `CHEQUE` || this.value == `ACC_TRA`) {
					$(cqStr).attr (`disabled`, false).parent().show();
					setTimeout (() => $(`#newchequeDate`)[0].showPicker(), 200);
				}else {
					$(cqStr).attr (`disabled`, true).parent().hide();
				}
			}).change();
		$(`#newchequeDate`).change (() => $(`#newchequeNo`).focus()).attr (`max`, ``);

		COMM.on (`input`, function() { COMM.prop (`required`, this.value.length); });
		BILL.on (`input`, function() { BILL.prop (`required`, this.value.length); });

/* ______________________________________________________________________________________________________________ set form load values ____________________________________________ set form load values ______________________ */

		// $(`#newtDate`)[0].valueAsDate = new Date();
		T_DATE.attr (`max`, `<?=$tdy;?>`)
			.change (function() { SR_NO.val (`${(this.value.slice (0, 7)).replace (`-`, ``)}`).focus(); });
			// .change();

		$(`[type='date']`).click (function() { this.showPicker(); });
		setTimeout (() => SR_NO.focus(), 500);
		setTimeout (() => PAYFOR.change(), 500);
	});
	
<? foreach (["Payments_Add", "Payments_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
	function deletePay (trn_id) {
		if (confirm ("Are you sure you want to delete this voucher ?")) {
			let formData = new FormData();
			formData.append (`deletePay`, trn_id);
			postFormData (`FormValid/payDetailsValidation.php`, formData);
		}
	}
<? 	break; } ?>

<? if ($_GET["mode"]??0) { ?>
	function refundChb (vrNo) {
		formData = new FormData();
		formData.append (`chb_id`, <?=$_GET["srNo"];?>);
		formData.append (`refund`, vrNo);
		formData.append (`refundMode`, `<?=$_GET["mode"];?>`);
		postFormData (`FormValid/chbDetailsValidation.php`, formData);
	}
<? } ?>

</script>

