<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

$fields = $FormFields["Rct"]["fields"];
foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields["Rct"]["keys"], $vals);

$selSql = ["famNo", "oldFamNo", "ward"];
$notReq = ["famNo", "ward", "oldFamNo", "nonMem", "moFrom", "moTo", "particulars", "mfAmt"];
$prefixes = ["head", "wife", "child1", "child2", "child3", "child4"];
$infos = ["PledgeAmt", "From", "To", "TotPaid", "Arrear"];
$moFields = ["moFrom", "moTo"];
$hallFields = ["hallRent", "cGstAmt", "sGstAmt"];
$marryFields = ["poorFund", "poorMarryFund", "adminChrg", "choirFee", "security"];
$memFields = $bdmfFields = [];
foreach ($prefixes as $prefix) {
	foreach (["", "PledgeAmt", "PgNo"] as $info) array_push ($selSql, "Fam.$prefix$info");
	foreach (["", "Amt", "PgNo"] as $info) array_push ($bdmfFields, $prefix.$info);
	foreach ($infos as $info) array_push ($memFields, $prefix.$info);
	array_push ($selSql, "Fam.$prefix"."Status");
}
$selSql = join (",", $selSql);
?>
<style>
	[name='amount'] { color: deeppink !important; }
	[name$='Arrear'] { font-weight: bold; }
</style>
<center> <h6> Receipts </h6> </center>
<div id="statusBar" class="sticky-top"> </div>
<div class="p-2" style="overflow-x:auto;">
    <div style="min-width:1080px;">
<? $add = true; $sql = "SELECT Tran$TBL_ID.*,CONCAT(head,' - ',famNo) AS famHead,$selSql FROM Tran$TBL_ID,Fam WHERE Tran$TBL_ID.status='$ACTIVE' AND Fam.status IN ('$ACTIVE','$UTILITY') AND fam_id=payBy AND action='Cr' AND srNo='".($_GET["srNo"]??"")."'";
$trn = mysqli_fetch_assoc (mysqli_query ($conn, $sql));
if (!($trn??0)) {
	$sql = "SELECT srNo,tDate,payFor FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Cr' ORDER BY trn_id DESC LIMIT 1";
	$trn = mysqli_fetch_assoc (mysqli_query ($conn, $sql));
	$trn["srNo"] = $trn["srNo"] + 1;
}
$rctEdit = $trn["trn_id"]??0;
if (($trn["famHead"]??0) == "NON MEMBER - 3") $trn["famHead"] = "NON MEMBER";
if (($_GET["mode"]??"") == "bdr") {
	$sql = "SELECT * FROM Itm WHERE status='$ACTIVE' AND remarks<>'Paid' AND bidderNo='".($_GET["srNo"]??"")."'";
	$bids = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
	$trn["famHead"] = ($bids[0]["bidder"]??"")." ";
	$trn["payFor"] = 203;
	$trn["amount"] = 0;
	$trn["particulars"] = "";
	foreach ($bids as $b) {
		$trn["amount"] += $b["amt"];
		$trn["particulars"] .= $b["name"].", ";
	}
	$trn["particulars"] = rtrim ($trn["particulars"], ", ");
}
// if (($_GET["mode"]??"") == "chb") {
// 	$sql = "SELECT * FROM Chb WHERE status='$ACTIVE' AND chb_id='".($_GET["srNo"]??"")."'";
// 	$chb = mysqli_fetch_assoc (mysqli_query ($conn, $sql))??0;
// 	if ($chb) {
// 		$trn["nonMem"] = ($chb["name"]??"")." ";
// 		$trn["famHead"] = "NON MEMBER";
// 		$trn["payFor"] = 25;
// 		$trn["hallRent"] = $chb["paid"];
// 		$trn["particulars"] = $chb["part"]." ".$chb["progDate"]." (".(($chb["church"]??0) ? "Church":"")." ".($chb["hall"]??"")." ".(($chb["kitchen"]??0) ? "Kitchen":"").")";
// 	}
// }
require "Forms/Rct.php"; ?>
<br>
<p> Recent </p>
<? $add = false; $sql = "SELECT Tran$TBL_ID.*,head AS famHead,$selSql FROM Tran$TBL_ID,Fam WHERE Tran$TBL_ID.status='$ACTIVE' AND Fam.status IN ('$ACTIVE','$UTILITY') AND fam_id=payBy AND action='Cr' ORDER BY trn_id DESC LIMIT 5";
$trns = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;
if ($trns) foreach ($trns as $trn) {
	if ($trn["payFor"] == 53) {
		$sql = "SELECT COUNT(*) AS covCnt FROM Tran$TBL_ID WHERE status='$COVER' AND payFor=7 AND action='Cr' AND srNo='".$trn["trn_id"]."'";
		$covCnt = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["covCnt"]??0;
	}
	require "Forms/Rct.php";
}
else { ?> <h5 class="w3-center w3-card w3-padding-large w3-black w3-opacity"> no recent activity </h5> <? } ?>
    </div>
</div>

<datalist id="famList">
<?
	$sql = "SELECT $selSql,".join (",", $prefixes)." FROM Fam WHERE status='$ACTIVE' ORDER BY ward,famNo,oldFamNo,head";
	$fams = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;
	foreach ($fams as $fam) {
		foreach ($prefixes as $pre) if ($fam[$pre."Status"] == "") break; 	/* find first active mem. */
		echo "<option value='", $fam[$pre], " - ", $fam["famNo"], "'>", $fam["ward"], " - ", $fam["head"], ",", $fam["wife"], ",", $fam["child1"], ",", $fam["child2"], ",", $fam["child3"], ",", $fam["child4"], "</option>";
	}
?>
</datalist>
<datalist id="commonPartList">
<?
	$sql = "SELECT DISTINCT particulars,COUNT(*) FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Cr' GROUP BY particulars HAVING COUNT(*) > 5";
	foreach (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[] as $p) echo "<option value='", $p["particulars"], "'/>";
?>
</datalist>

<script>
	const RctEdit = <?=($_GET["srNo"]??0) ? $rctEdit : "false";?>;
	const AMOUNT = $(`#addRctForm [name='amount']`);
	const ARREARS = $(`#addRctForm [name$='Arrear']`);
	const HEAD_AMT = $(`#addRctForm [name='headAmt']`);
	const FAMHEAD = $(`#addRctForm [name='famHead']`);
	const FAM_NO = $(`#addRctForm [name='famNo']`);
	const MO_AMT = $(`#addRctForm [name='moAmt']`);
	const MF_AMT = $(`#addRctForm [name='mfAmt']`);
	const NONMEM = $(`#addRctForm [name='nonMem']`);
	const PART = $(`#newparticulars`);
	const PAYFOR = $(`#addRctForm [name='payFor']`);
	const PAYMODE = $(`#addRctForm [name='payMode']`);
	const SERHEAD = $(`#addRctForm [name='serHead']`);
	const SR_NO = $(`#addRctForm [name='srNo']`);
	const T_DATE = $(`#addRctForm [name='tDate']`);
	let prefixes = [`head`, `wife`, `child1`, `child2`, `child3`, `child4`], info = [``, `PledgeAmt`, `PgNo`, `From`, `To`, `TotPaid`, `Arrear`], infos = [`famNo`, `ward`, `oldFamNo`];
	let moStr = `#newmoFrom,#newmoTo`, cqStr = `#newchequeDate,#newchequeNo,#newchequeBank`, upiStr = `#newupiTrnId,#newupiTrnDate`, hallStr = `#newhallRent,#newcGstAmt,#newsGstAmt`, marryStr = `#newpoorFund,#newpoorMarryFund,#newadminChrg,#newchoirFee,#newsecurity`;
	let dsblStr = [], moInfo = info.concat (`Amt`), memStr = [], amStr = [], pgStr = [], pdFrStr = [], pdToStr = [], arStr = [], plgAmStr = [], totPdStr = [], skipOnEdit = [];
	infos.forEach (inf => dsblStr.push (`[name='${inf}']`));
	prefixes.forEach (prefix => {
		info.forEach (inf => infos.push (`${prefix}${inf}`));
		let pre = `#new${prefix}`;
		moInfo.forEach (inf => memStr.push (`${pre}${inf}`));
		amStr.push (`${pre}Amt`);
		pgStr.push (`${pre}PgNo`);
		arStr.push (`${pre}Arrear`);
		pdFrStr.push (`${pre}From`);
		pdToStr.push (`${pre}To`);
		totPdStr.push (`${pre}TotPaid`);
		plgAmStr.push (`${pre}PledgeAmt`);
	});
	dsblStr = dsblStr.toString();
	amStr = amStr.toString();
	const AMTS = $(amStr);
	arStr = arStr.toString();
	pgStr = pgStr.toString();
	memStr = memStr.toString();
	pdFrStr = pdFrStr.toString();
	const PD_FR = $(pdFrStr);
	skipOnEdit = pdToStr;
	pdToStr = pdToStr.toString();
	plgAmStr = plgAmStr.toString();
	totPdStr = totPdStr.toString();
	$(document).ready (() => {

<? foreach (["Receipts_Add", "Receipts_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
		$(`form`).submit (function() {
				event.preventDefault();
				let formData = new FormData ($(this)[0]);
				if (RctEdit) {
					formData.append (`trn_id`, RctEdit);
					prefixes.forEach (pre => formData.append (`${pre}From`, $(`[name='${pre}From']`).val()));
				}
				if (PAYFOR.val() == `7`) {
					let Arrear = 0;
					ARREARS.each (function() { if (this.value.length) if (this.value != `0`) Arrear++; });
					if (Arrear) if (!confirm (`Ignore arrear and save Receipt ?`)) return;
				}
				postFormData (`FormValid/rctDetailsValidation.php`, formData);
<? if (($_GET["mode"]??"") == "bdr") { ?>
				formData = new FormData();
				formData.append (`biddingPayment`, true);
				formData.append (`bidderNo`, <?=$_GET["srNo"];?>);
				postFormData (`FormValid/conDetailsValidation.php`, formData);
<? } ?>
			});
<? 	break; } ?>

		PAYFOR.blur (function() { if (this.value.length) this.size = 1; })
			.change (function() {
				switch (this.value) {
					case `7`:	/* Offertory - Monthly Offering */
						if (FAMHEAD.val() != `NON MEMBER`) {
							$(pgStr).parent().addClass (`col-1`).removeClass (`col-6`);
							$(`#newparticulars,#newmoAmt,${hallStr},${marryStr}`).attr (`disabled`, true).parent().hide();
							$(`${moStr},#newmfAmt,${amStr},${pdToStr}`).attr (`disabled`, false).parent().show();
							$(`${arStr},${memStr},${pdToStr},${plgAmStr},${totPdStr}`).parent().show();
							FAMHEAD.trigger (`input`);
						}
						break;
					case `25`:	/* BLM Hall - Hall Rent */
						$(`${marryStr},${memStr},${moStr},#newmoAmt,#newmfAmt`).attr (`disabled`, true).parent().hide();
						$(`#newparticulars,${hallStr}`).attr (`disabled`, false).parent().show();
						AMOUNT.trigger (`input`);
						break;
					case `45`:	/* Marriage - Marriage Fee */
						$(`${hallStr},${memStr},${moStr},#newmoAmt,#newmfAmt`).attr (`disabled`, true).parent().hide();
						$(`#newparticulars,${marryStr}`).attr (`disabled`, false).parent().show();
						break;
					case `53`:	/* Offertory - Monthly Offering - cover */
						$(`${hallStr},${marryStr},${memStr},${moStr}`).attr (`disabled`, true).parent().hide();
						$(`#newparticulars,#newmoAmt,#newmfAmt`).attr (`disabled`, false).parent().show();
						break;
					default:
						if (FAMHEAD.val() != `NON MEMBER`) {
							$(pgStr).parent().addClass (`col-6`).removeClass (`col-1`);
							$(memStr).parent().show();
							$(`${arStr},${hallStr},${marryStr},${moStr},#newmoAmt,#newmfAmt,${pdFrStr},${pdToStr},${plgAmStr},${totPdStr}`).attr (`disabled`, true).parent().hide();
							$(`#newparticulars,${amStr}`).attr (`disabled`, false).parent().show();
							FAMHEAD.trigger (`input`);
						}
						// $(`${hallStr},${marryStr},${memStr},${moStr},#newmoAmt,#newmfAmt`).attr (`disabled`, true).parent().hide();
						// PART.attr (`disabled`, false).parent().show();
				}
			})
			.children().hover (function() { PAYFOR.val (this.value).change(); })
			.click (function() {
				PAYFOR[0].size = 1;
				SERHEAD[0].size = 1;
				if (![`7`, `25`, `45`, `53`].includes (this.value)) {
					if (NONMEM.val()) AMOUNT.focus();
					else HEAD_AMT.focus();
				}
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
		$(`#newmoFrom`).change (function() {
				PD_FR.val (this.value);
				$(pdToStr).prop (`min`, this.value);
				$(`#newmoTo`)[0].min = this.value;
				$(`#newmoTo`)[0].showPicker();
			});
		$(pdToStr).change (() => $(`#newmoTo`).val (``));
		$(`#newmoTo`).change (function() {
				$(pdToStr).val (this.value);
				prefixes.forEach (pre => $(`#new${pre}To`).css (`color`, `${$(`#new${pre}`).val().length ? `white` : `transparent`}`));
			});
		PD_FR.change (function() { $(`#${this.id.slice (0, -4)}To`)[0].min = this.value; });
		$(`#newhead`).on (`input`, function() { FAMHEAD.val (this.value).trigger (`input`); });
		PAYMODE.change (function() {
				if (this.value == `CHEQUE` || this.value == `ACC_TRA`) {
					$(cqStr).attr (`disabled`, false).parent().show();
					setTimeout (() => $(`#newchequeDate`)[0].showPicker(), 200);
					$(upiStr).attr (`disabled`, true).parent().hide();
				}else if (this.value == `UPI`) {
					$(upiStr).attr (`disabled`, false).parent().show();
					setTimeout (() => $(`#newupiTrnDate`)[0].showPicker(), 200);
					// $(`#newupiTrnId`).focus();
					$(cqStr).attr (`disabled`, true).parent().hide();
				}else {
					$(cqStr).attr (`disabled`, true).parent().hide();
					$(upiStr).attr (`disabled`, true).parent().hide();
				}
			}).change();
		$(`#newchequeDate`).change (() => $(`#newchequeNo`).focus()).attr (`max`, ``);
		$(`#newupiTrnDate`).change (() => $(`#newupiTrnId`).focus());
		FAMHEAD.blur (() => $(`.newFam`).text().length ? $(`.newFam`).show():``)
			.focus (() => $(`.newFam`).hide())
			.keydown (function() {
				if (event != undefined) {
					if ([`Enter`, `NumpadEnter`].includes (event.key)) {
						$(this).val ($(`#famList > [value$=' - ${this.value}']`).val()).trigger (`input`);
						FAMHEAD.blur();
						// SERHEAD.focus();
					}
				}
			})
			.on (`input`, function() {
				// if (this.value == `NON MEMBER`) NONMEM.focus();
				if (this.value.length < 4) return;
				let formData = new FormData();
				formData.append (`getFamInfo`, true);
				formData.append (`famHeadNo`, this.value);
				if (RctEdit) formData.append (`rctEdit`, RctEdit);
				$.ajax ({
					type: `POST`,
					url: `/<?=$PATH;?>/FormValid/famDetailsValidation.php`,
					enctype: `multipart/form-data`,
					dataType: `text`,
					data: formData,
					contentType: false,
					cache: false,
					processData: false,
					success: resp => { if (resp.length < 5) return;
						resp = JSON.parse (resp);
						if (resp.newFam) $(`.newFam`).html (`This name does not exist. <a class="w3-text-white btn" href="/<?=$PATH;?>/Family"> ADD NEW FAMILY </a>`);
						else {
							$(`.newFam`).hide().html (``);
							infos.forEach (info => { if (!RctEdit && !skipOnEdit.includes (info)) $(`#new${info}`).val (resp[info]).change(); });
							$(`#newpayBy`).val (resp[`fam_id`]);
							$(moStr).val (``);
							prefixes.forEach (pref => {
								pre = `#new${pref}`;
								$(`${pre}Amt`).attr (`min`, PAYFOR.val() == `7` ? $(`${pre}PledgeAmt`).val():``);
								$(`${pre}Amt,${pre}To`).prop (`disabled`, resp[`${pref}Status`].length);
								moInfo.forEach (inf => $(`${pre}${inf}`).css (`color`, `${$(`${pre}`).val().length ? `white` : `transparent`}`));
							});
							if (!RctEdit) $(`[type='month']`).change();
							ARREARS.change();
						}
					},
					error: () => S_B.html(`<p class="w3-yellow"> Server error, please try again. </p>`)
				});
			});
		AMTS.on (`input`, function() {
				AMOUNT.val (0);
				AMTS.each (function() { if (this.value) AMOUNT.val (parseInt (AMOUNT.val()) + parseInt (this.value)); });
				this.required = 0 < this.value;
				// $(`#${this.id.slice (0, -3)}To`)[0].required = (PAYFOR.val() == `7` ? 0 < this.value : false);
				$(`#${this.id.slice (0, -3)}To`)[0].required = (PAYFOR.val() == `7` ? ($(`#${this.id.slice (0, -3)}From`).val() == `<?=substr ($FinYrEndMonth, 0, 6);?>4` ? false : 0 < this.value) : false);
				if (PAYFOR.val() == `7`) AMOUNT.val (parseInt (AMOUNT.val()) + parseInt (MF_AMT.val() == `` ? 0 : MF_AMT.val()));
			});
		$(marryStr).on (`input`, function() {
				AMOUNT.val (0);
				$(marryStr).each (function() { if (this.value) AMOUNT.val (parseInt (AMOUNT.val()) + parseInt (this.value)); });
			});
		MF_AMT.on (`input`, () => {
				if (PAYFOR.val() == `7`) AMTS.trigger (`input`);
				else AMOUNT.val (parseInt (MF_AMT.val() == `` ? 0 : MF_AMT.val()) + parseInt (MO_AMT.val() == `` ? 0 : MO_AMT.val()));
			});
		MO_AMT.on (`input`, () => AMOUNT.val (parseInt (MO_AMT.val() == `` ? 0 : MO_AMT.val()) + parseInt (MF_AMT.val() == `` ? 0 : MF_AMT.val())));
		NONMEM.blur (function() {
				if (this.value.length) {
					FAMHEAD.val (`NON MEMBER`).trigger (`input`);
					PAYFOR.change();
				}
				HEAD_AMT[0].disabled = this.value.length;
			});
		$(`#addRctForm [name='hallRent']`).on (`input`, function() {
				$(`#newcGstAmt,#newsGstAmt`).val (Math.ceil (this.value * 0.09));
				AMOUNT.val (0);
				$(hallStr).each (function() { AMOUNT.val (parseInt (AMOUNT.val()) + parseInt (this.value)); });
			});
		$(`#newcGstAmt,#newsGstAmt`).on (`input`, function() { $(`#newcGstAmt,#newsGstAmt`).val (this.value); });

/* ______________________________________________________________________________________________________________ set form load values ____________________________________________ set form load values ______________________ */

		// T_DATE[0].valueAsDate = new Date();
		T_DATE.attr (`max`, `<?=$tdy;?>`)
			.change (function() { SR_NO.val (`${(this.value.slice (0, 7)).replace (`-`, ``)}`).focus(); });
			// .change();

		$(`[type='month']`).change (function() { this.style.color = this.value.length ? `white` : `transparent`; });
		if (!RctEdit) $(`[type='month']`).change();
		else $(`[type='month']`).each (function() { this.style.color = this.value.length ? `white` : `transparent`; });

		$(`[type='date']`).click (function() { this.showPicker(); });
		$(`${memStr},${hallStr},${marryStr},${moStr},#newmoAmt,#newmfAmt,${dsblStr}`).attr ({required:false, disabled:true});

		ARREARS.change (function() { this.style.color = this.value == 0 ? `white` : `red`; this.style.backgroundColor = this.value == 0 ? `` : `yellow`; });
		
		SR_NO.prop (`disabled`, !RctEdit);
		setTimeout (() => FAMHEAD.focus(), 500);
		setTimeout (() => PAYFOR.change(), 500);

		FAM_NO.attr ({disabled:false, readonly:true})
			.parent().click (() => window.open (`/<?=$PATH;?>/Family/${FAM_NO.val()}/`, `_blank`).focus());
	});

<? foreach (["Receipts_Add", "Receipts_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
	function deleteRct (trn_id) {
		if (confirm (`Are you sure you want to delete this receipt ?`)) {
			let formData = new FormData();
			formData.append (`deleteRct`, trn_id);
			postFormData (`FormValid/rctDetailsValidation.php`, formData);
		}
	}
<? 	break; } ?>
/* ______________________________________________________________________________________________________________ contra entry ____________________________________________ contra entry ______________________ */
	$(`#addRctForm option:contains(Encashment)`).click (() => {
		T_DATE[0].valueAsDate = new Date();
		NONMEM.val (`MMC`).blur();
		PAYMODE.val (`CHEQUE`).change();
		$(`#addRctForm [name='chequeBank']`).val (`Federal Bank`);
		PART.val (`Encashment`);
		SERHEAD.val (115).children().click();
		PAYFOR.val (108).change();
	});
	$(`#addRctForm option:contains(Remit. to Bank)`).click (() => {
		T_DATE[0].valueAsDate = new Date();
		NONMEM.val (`MMC`).blur();
		PAYMODE.val (`CASH`).change();
		PART.val (`Remittance to Bank`);
		SERHEAD.val (115).children().click();
		PAYFOR.val (109).change();
	});
</script>

