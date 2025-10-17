<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");
    if (!($_POST["typ"]??0)) exit ("<center> <h4> 404 </h4> <p> Invalid Number! </p> </center>");

    $selSql = ["famNo", "ward", "oldFamNo"];
    $notReq = ["srNo", "famNo", "ward", "oldFamNo", "nonMem", "moFrom", "moTo", "particulars"];
    $prefixes = ["head", "wife", "child1", "child2", "child3", "child4"];
    $infos = ["PledgeAmt", "From", "To", "TotPaid", "Arrear"];
    $moFields = ["moFrom", "moTo"];
    $hallFields = ["hallRent", "cGstAmt", "sGstAmt"];
    $marryFields = ["poorFund", "poorMarryFund", "adminChrg", "choirFee", "security", "cautionDeposit"];
    $covNotDis = ["covtrn_id", "payBy", "famHead", "amount", "moTo", "mfAmt"];
    $memFields = $bdmfFields = [];
    foreach ($prefixes as $prefix) {
        foreach (["", "PledgeAmt", "PgNo"] as $info) array_push ($selSql, "Fam.$prefix$info");
        foreach (["", "Amt", "PgNo"] as $info) array_push ($bdmfFields, $prefix.$info);
        foreach ($infos as $info) array_push ($memFields, $prefix.$info);
        array_push ($covNotDis, $prefix."Amt", $prefix."To");
        array_push ($selSql, "Fam.$prefix"."Status");
    }
    $selSql = join (",", $selSql);
    $sql = "SELECT Tran$TBL_ID.*,head AS famHead,$selSql FROM Tran$TBL_ID,Fam WHERE Tran$TBL_ID.status='$ACTIVE' AND Fam.status IN ('$ACTIVE','$UTILITY') AND payBy=fam_id AND action='".($_POST["typ"] == "Rct" ? "Cr" : "Dr")."' AND srNo='".$_POST["srNo"]."'";
    $trns = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;
    if ($trns) { foreach ($trns as $trn) {
        $fields = $FormFields[$_POST["typ"]]["fields"];
        foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields[$_POST["typ"]]["keys"], $vals);
        if ($trn["payFor"] == 53) {
            $sql = "SELECT Tran$TBL_ID.*,CONCAT(head,' - ',famNo) AS famHead,$selSql FROM Tran$TBL_ID,Fam WHERE Tran$TBL_ID.status='$COVER' AND Fam.status IN ('$ACTIVE','$UTILITY') AND payBy=fam_id AND payFor=7 AND action='Cr' AND srNo='".$trn["trn_id"]."'";
            $covs = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;
            $covCnt = count ($covs);
        }
        $add = false; require "Forms/".$_POST["typ"].".php";
        if ($trn["payFor"] == 53) {
            $fields = $FormFields["Cov"]["fields"];
            foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields["Cov"]["keys"], $vals);
?>
<div class="w3-row-padding sticky-top" style="top:40px; z-index:1;">
    <p class="w3-col w3-indigo w3-border w3-border-brown w3-topbar w3-round-xlarge px-3 pb-2" style="max-width:180px;"> Balance
        <br> <input type="number" class="w3-input w3-large" id="moRem" value="<?=$trn["moAmt"];?>" max=<?=$trn["moAmt"];?> min=0 readonly /> </p>
    <p class="w3-col w3-green w3-border w3-border-teal w3-topbar w3-round-xlarge px-3 pb-2 mx-3" style="max-width:180px;"> Entered
        <br> <input type="number" class="w3-input w3-large" id="moSum" value="0" max=<?=$trn["moAmt"];?> min=0 readonly /> </p>

    <p class="w3-col w3-brown w3-border w3-border-indigo w3-topbar w3-round-xlarge px-3 pb-2 <?=$trn["mfAmt"] ? "":"collapse";?>" style="max-width:180px;"> Balance Mission
        <br> <input type="number" class="w3-input w3-large" id="mfRem" value="<?=$trn["mfAmt"];?>" max=<?=$trn["mfAmt"];?> min=0 readonly /> </p>
    <p class="w3-col w3-teal w3-border w3-border-green w3-topbar w3-round-xlarge px-3 pb-2 mx-3 <?=$trn["mfAmt"] ? "":"collapse";?>" style="max-width:180px;"> Entered Mission
        <br> <input type="number" class="w3-input w3-large" id="mfSum" value="0" max=<?=$trn["mfAmt"];?> min=0 readonly /> </p>

    <p class="w3-col s3 w3-right w3-right-align py-4">
<?  if ($covCnt) { ?>
        <i class="w3-btn w3-black w3-border w3-border-blue w3-text-blue w3-hover-blue w3-xlarge fa fa-list mr-4 p-2 circ" title="Cover Offering Report" onclick="COV_LIST_DIALOG.showModal()"> </i>
        <a href="/<?=$PATH;?>/downloads/Monthly_Sub_Cover_List_<?=$trn["srNo"];?>.csv" target="_blank" class="w3-btn w3-black w3-border w3-border-green w3-text-green w3-hover-green w3-xlarge fa fa-file-excel-o p-2 mr-2 circ excelLnk" download="Monthly_Sub_Cover_List_<?=$trn["srNo"];?>.csv" title="Download As Excel"> </a>
        <a href="/<?=$PATH;?>/print_covers/pdf/<?=$trn["srNo"];?>/" target="_blank" class="w3-btn w3-black w3-border w3-border-blue w3-text-blue w3-hover-blue w3-xlarge fa fa-print p-2 mr-2 circ printLnk" title="Print Covers"> </a>
<?  } ?>
        <button type="button" class="w3-btn w3-green w3-wide badge-pill saveBtn" onclick="$(`#covForm :submit`).click()"> <b class="px-3"> SAVE </b> </button>
    </p>
    <p class="w3-col w3-right w3-black w3-border w3-border-teal w3-topbar w3-round-xlarge px-3 pb-2" style="max-width:130px;"> <i> Covers Added </i> <br> <span class="w3-xlarge covCnt"> <?=$covCnt;?> </span> </p>
</div>
<form id="covForm" class="w3-card w3-round-xxlarge w3-border w3-border-blue w3-topbar py-4">
    <center>
        <h5> Monthly Subscription - Cover Details
            <i class="w3-btn fa fa-edit ml-2 editCovs" title="Edit Cover Details"> </i> 
            <i class="w3-blue w3-hover-white btn btn-sm badge-pill mx-1 cancelEdit" style="display:none;"> CANCEL </i>
        </h5>
        <!-- <i class="w3-small w3-wide w3-text-indigo w3-border w3-border-indigo badge-pill px-1 py-0"> βeta </i> -->
    </center> <br>
    <input type="hidden" name="trn_id" value="<?=$trn["trn_id"];?>" required />
    <div class="w3-border w3-border-teal w3-topbar w3-round-xxlarge w3-padding-large row mx-4">
        <p class="col-12 w3-center"> <b class="w3-text-teal mx-4"> <i> Cover Number <?=$covCnt + 1;?> </i> </b> </p>
        <p class="col-12 covStsBar"> </p>
<?
    foreach ($fields as $name => $attr) {
        echo "<p class='col-", $attr["col"], " m-0 p-0 px-1'> <i class='w3-block w3-small w3-darkcyan pl-2'>", $attr["label"], "</i>";
        echo    "<input type='", $attr["type"], "' class='w3-input' name='$name' ", $attr["type"] == "month" ? "min='$FinYrStartMonth' max='$FinYrEndMonth'":"",
                    $name == "famHead" ? " list='famList' required":"", in_array ($name, $covNotDis) ? "":" disabled", "/>";
        echo "</p>";
    }
?>
    </div>
<?
    $totAmount = $totMfAmt = 0;
    for ($i = $covCnt; $i > 0; $i--) {
        foreach ($prefixes as $prefix) {
            if (!($covs[$i - 1][$prefix]??0)) continue;
            $sql = "SELECT SUM($prefix"."Amt) AS totPaid FROM Tran$TBL_ID WHERE status IN ('$ACTIVE','$COVER') AND payBy='".$covs[$i - 1]["payBy"]."' AND payFor='7' AND action='Cr' AND $prefix"."To>='$FinYrStartMonth'";
            $covs[$i - 1][$prefix."TotPaid"] = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["totPaid"]??0;
            $covs[$i - 1][$prefix."Arrear"] = 0;
        }
        $totAmount += $covs[$i - 1]["amount"];
        $totMfAmt += $covs[$i - 1]["mfAmt"];
?>
    <div class="w3-border w3-border-teal w3-topbar w3-round-xxlarge w3-padding-large row mx-4 cov<?=$i;?>">
        <p class="col-12 w3-center px-0">
            <i class="w3-btn w3-left fa fa-edit editCov" title="Edit Cover Details <?=$covs[$i - 1]["trn_id"];?>"> </i>
            <i class="w3-btn w3-orange w3-hover-yellow badge-pill py-0 mr-4 clearCov" onclick="$(`.cov<?=$i;?> :input`).attr(`required`,false).val(``);" title="Clear Cover" style="display:none;"> CLEAR </i>
            <b class="w3-text-teal mx-4 covNo" style="display:none"> <i> Cover Number <?=$i;?> </i> </b>
            <i class="w3-text-red w3-large fa fa-trash btn p-0 ml-4 deleteCov" onclick='deleteCov(<?=$covs[$i - 1]["trn_id"], ",$i";?>)' title="Delete Cover" style="display:none;"> </i>
        </p>
<?
        $flds = $fields;
        for ($c = 1; $c < 5; $c++) if ($covs[$i - 1]??0) if (empty ($covs[$i - 1]["child$c"])) foreach (array_merge ($infos ,["", "Amt", "PgNo"]) as $info) unset ($flds["child".$c.$info]);
        foreach ($flds as $name => $attr) {
            if ($attr["type"] == "hidden") continue;
            if (($covs[$i - 1][$name]??0) == 0) $covs[$i - 1][$name] = "";
            echo "<p class='col-", $attr["col"], " m-0 p-0 px-1'> <i class='w3-block w3-small w3-darkcyan pl-2'>", $attr["label"], "</i> <b class='w3-block w3-border-bottom w3-border-dark-gray px-2 ",
                    $attr["type"] == "number" ? "w3-right-align":"", "'>", $attr["type"] == "month" ? (($covs[$i - 1][$name]??0) ? date_create ($covs[$i - 1][$name])->format ("F Y"):"") : ($covs[$i - 1][$name]??""), "<br> </b> </p>";
        }
?>
    </div>
<?  }
    echo "<input type='hidden' class='totAmount' value='", $totAmount, "' />";
    echo "<input type='hidden' class='totMfAmt' value='", $totMfAmt, "' />";
?>
    <button type="submit" class="w3-btn w3-green w3-wide badge-pill m-4 px-4"> <b> SAVE </b> </button>
</form>
<script>
    const TRN_ID = <?=$trn["trn_id"]??0;?>;
    const COVS = [
<? 
foreach ($covs as $cov) {
    echo "{";
    foreach ($cov as $key => $val) echo "$key : `$val`, ";
    echo "},";
}
?>
        ];
</script>
<datalist id="famList">
<? $sql = "SELECT famNo,oldFamNo,ward,".join (",", $prefixes)." FROM Fam WHERE status='$ACTIVE' ORDER BY ward,famNo,oldFamNo,head";
$fams = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;
foreach ($fams as $fam) { ?> <option value="<?=$fam["head"]," - ",$fam["famNo"];?>"> <?=$fam["famNo"]," - (",$fam["oldFamNo"],") - ",$fam["ward"]," ",$fam["wife"],",",$fam["child1"],",",$fam["child2"],",",$fam["child3"],",",$fam["child4"],",";?> </option> <? } ?>
</datalist>
<?
require "coversList.php";
        }
    }}else { ?> <br> <br> <center> <h4> Invalid Number </h4> <p> This <i> <?=$_POST["typ"] == "Rct" ? "receipt" : "payment";?> number </i> does not exist! </p> </center> <? }
}else {
    $sql = "SELECT MIN(srNo) AS minNo, MAX(srNo) AS maxNo FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Cr'";
    $rctNo = mysqli_fetch_assoc (mysqli_query ($conn, $sql));
    $sql = "SELECT MIN(srNo) AS minNo, MAX(srNo) AS maxNo FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Dr'";
    $payNo = mysqli_fetch_assoc (mysqli_query ($conn, $sql));
?>
<style> [name='amount'],[name='famNo'] { font-weight: bold; } </style>
<center> <h6> Transactions </h6> </center>
<div class="w3-row">
    <div class="w3-col s6">
        <h5 class="w3-center w3-text-green"> Receipt </h5>
        <p class="w3-row">
            <input type="number" class="w3-col s6 w3-input w3-card badge-pill px-4" id="searchRct1" list="rtNoList1" min="<?=$rctNo["minNo"]??0;?>" max="<?=$rctNo["maxNo"];?>" placeholder=" Search... receipt number" />
            <input type="number" class="w3-col s6 w3-input w3-card badge-pill px-1" id="searchRct" list="rtNoList" value="<?=($_GET["typ"]??0) == "Rct" ? $_GET["srNo"]:"";?>" min="<?=$rctNo["minNo"]??0;?>" max="<?=$rctNo["maxNo"];?>" placeholder=" Search... receipt number" />
        </p>
    </div>
    <div class="w3-col s6 px-5">
        <h5 class="w3-center w3-text-red"> Payment </h5> </center>
        <p class="w3-row">
            <input type="number" class="w3-col s6 w3-input w3-card badge-pill px-4" id="searchPay1" list="vrNoList1" min="<?=$payNo["minNo"]??0;?>" max="<?=$payNo["maxNo"];?>" placeholder=" Search... voucher number" />
            <input type="number" class="w3-col s6 w3-input w3-card badge-pill px-1" id="searchPay" list="vrNoList" value="<?=($_GET["typ"]??0) == "Pay" ? $_GET["srNo"]:"";?>" min="<?=$payNo["minNo"]??0;?>" max="<?=$payNo["maxNo"];?>" placeholder=" Search... voucher number" />
        </p>
    </div>
</div>

<datalist id="rtNoList">
<? $sql = "SELECT srNo FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Cr' ORDER BY srNo DESC LIMIT 512";
$rcts = array_reverse (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[]);
foreach ($rcts as $rct) { ?> <option value="<?=$rct["srNo"];?>" /> <? } ?>
</datalist>
<datalist id="rtNoList1">
<? $sql = "SELECT srNo FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Cr' ORDER BY srNo DESC LIMIT 512 OFFSET 512";
$rcts = array_reverse (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[]);
foreach ($rcts as $rct) { ?> <option value="<?=$rct["srNo"];?>" /> <? } ?>
</datalist>

<datalist id="vrNoList">
<? $sql = "SELECT srNo FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Dr' ORDER BY srNo DESC LIMIT 512";
$pays = array_reverse (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[]);
foreach ($pays as $pay) { ?> <option value="<?=$pay["srNo"];?>" /> <? } ?>
</datalist>
<datalist id="vrNoList1">
<? $sql = "SELECT srNo FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='Dr' ORDER BY srNo DESC LIMIT 512 OFFSET 512";
$pays = array_reverse (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[]);
foreach ($pays as $pay) { ?> <option value="<?=$pay["srNo"];?>" /> <? } ?>
</datalist>

<div id="statusBar" class="sticky-top" style="top:150px;"> </div>
<div class="w3-padding-large" id="transaction"> </div>
<script>
    const SRCH_RCT = $(`#searchRct`);
    const SRCH_RCT1 = $(`#searchRct1`);
    const SRCH_PAY = $(`#searchPay`);
    const SRCH_PAY1 = $(`#searchPay1`);
	$(document).ready (() => {
        SRCH_RCT.focus()
            .on (`input`, function() {
                if (!this.value) return;
                SRCH_RCT1.val (``);
                SRCH_PAY.val (``);
                SRCH_PAY1.val (``);
                let formData = new FormData();
                formData.append (`typ`, `Rct`);
                formData.append (`srNo`, this.value);
                getTrn (formData, `transaction`);
            });
        SRCH_RCT1.on (`input`, function() {
            if (!this.value) return;
            SRCH_RCT.val (``);
            SRCH_PAY.val (``);
            SRCH_PAY1.val (``);
            let formData = new FormData();
            formData.append (`typ`, `Rct`);
            formData.append (`srNo`, this.value);
            getTrn (formData, `transaction`);
        });
        SRCH_PAY.on (`input`, function() {
            if (!this.value) return;
            SRCH_RCT.val (``);
            SRCH_RCT1.val (``);
            SRCH_PAY1.val (``);
            let formData = new FormData();
            formData.append (`typ`, `Pay`);
            formData.append (`srNo`, this.value);
            getTrn (formData, `transaction`);
        });
        SRCH_PAY1.on (`input`, function() {
            if (!this.value) return;
            SRCH_RCT.val (``);
            SRCH_RCT1.val (``);
            SRCH_PAY.val (``);
            let formData = new FormData();
            formData.append (`typ`, `Pay`);
            formData.append (`srNo`, this.value);
            getTrn (formData, `transaction`);
        });
        $(`#searchRct,#searchPay`).trigger (`input`)
            .keydown (function (e) {
                if (e.key == `ArrowUp`) {
                    e.preventDefault();
                    if (this.min < this.value) this.value--;
                    else return;
                    $(this).trigger (`input`);
                }
                if (e.key == `ArrowDown`) {
                    e.preventDefault();
                    if (this.value < this.max) this.value++;
                    else return;
                    $(this).trigger (`input`);
                }
            });
	});
    function docReady() {
        let CovEdit = 0;
		$(`[type='date'],[type='week'],[type='month']`).click (function() { this.showPicker(); });
        $(`i.cancelEdit`).click (function() {
            $(`form#covForm input`).attr (`readonly`, true);
            $(`form#covForm button,form#covForm .cancelEdit,.deleteCov,.clearCov,.covNo`).fadeOut();
        });
        $(`i.editCovs`).click (function() {
            CovEdit = 1;
            $(`form#covForm input`).attr (`readonly`, false);
            $(`form#covForm button,form#covForm .cancelEdit,.deleteCov,.clearCov,.covNo`).show();
        });

        const RCT_MO_AMT = $(`[name='moAmt']`).first()[0];
        const RCT_MF_AMT = $(`[name='mfAmt']`).first()[0];
        const MO_REM = $(`#moRem`)[0];
        const MO_SUM = $(`#moSum`)[0];
        const MF_REM = $(`#mfRem`)[0];
        const MF_SUM = $(`#mfSum`)[0];
        const COV_AMOUNT = $(`#covForm [name='amount']`);
        const COV_FROMS = $(`#covForm [name$='From']`);
        const COV_AMTS = $(`#covForm [name$='Amt']:not(:disabled,[name='mfAmt'])`);
        const COV_PLDGS = $(`#covForm [name$='PledgeAmt']`);
        const COV_MF_AMT = $(`#covForm [name='mfAmt']`);
        const TOT_AMOUNT = $(`.totAmount`)[0];
        const TOT_MF_AMT = $(`.totMfAmt`)[0];
        const COV_STS_BAR = $(`.covStsBar`);
        let prefixes = [`head`, `wife`, `child1`, `child2`, `child3`, `child4`], info = [``, `PledgeAmt`, `PgNo`, `PaidUpto`, `From`, `To`, `TotPaid`, `Arrear`], infos = [`famNo`, `ward`, `oldFamNo`];
        let payToStr = [];
        prefixes.forEach (prefix => {
            info.forEach (inf =>  infos.push (`${prefix}${inf}`));
            let pre = `#new${prefix}`;
            // moInfo.forEach (inf =>  memStr.push (`${pre}${inf}`));
            // amStr.push (`${pre}Amt`);
            // pgStr.push (`${pre}PgNo`);
            // arStr.push (`${pre}Arrear`);
            // pdFrStr.push (`${pre}From`);
            payToStr.push (`${pre}To`);
            // totPdStr.push (`${pre}TotPaid`);
            // plgAmStr.push (`${pre}PledgeAmt`);
        });

<? foreach (["Receipts_Add", "Receipts_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
        $(`form#covForm`).submit (function() {
            event.preventDefault();
            if (parseInt (MO_REM.value) < 0 || parseInt (MF_REM.value) < 0) {
                COV_STS_BAR.html (`<p class="w3-large w3-yellow w3-padding"> <b> Cover amount must be less than Remaining amount. </b> </p>`);
                return;
            }
            let formData = new FormData ($(this)[0]);
            formData.append (`covCnt`, parseInt ($(`.covCnt`).text()) + 1);
            formData.append (`edit`, CovEdit);
            postFormData (`FormValid/covDetailsValidation.php`, formData);
        });
<? 	break; } ?>

        $(`[name='famHead']`).on (`input`, function() {
            COV_STS_BAR.html (``);
            let covNo = this.name.slice (7);
			if (this.value.length > 3) {
				let formData = new FormData();
				formData.append (`getFamInfo`, true);
				formData.append (`famHeadNo`, this.value);
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
						if (resp.newFam) {
							COV_STS_BAR.html (`This name does not exist. <a class="w3-text-red btn" href="/<?=$PATH;?>/Family"> ADD NEW FAMILY </a>`);
						}else {
							COV_STS_BAR.html (``);
							infos.map (info => { $(`[name='${info}${covNo}']`).val (resp[info]); });
							$(`[name='payBy${covNo}']`).val (resp[`fam_id`]);
							$(`[name='moTo${covNo}']`).val (``);
							$(`[name='moTo${covNo}']`)[0].showPicker();
							prefixes.forEach (pre => {
                                $(`[name='${pre}Amt${covNo}']`).attr (`min`, $(`[name='${pre}PledgeAmt${covNo}']`).val());
                                $(`[name='${pre}To${covNo}']`).attr (`min`, $(`[name='${pre}From${covNo}']`).val());
                                $(`[name='${pre}Amt${covNo}'],[name='${pre}To${covNo}']`).prop (`disabled`, resp[`${pre}Status`].length);
                            });
						}
					},
					error: () => S_B.html(`<p class="w3-yellow"> Server error, please try again. </p>`)
				});
			}else $(`[name='payBy${covNo}']`).val (``);
		});

        COV_AMTS.attr (`min`, 0)
            .on (`input`, function() {
                COV_AMOUNT.val (0);
                COV_AMTS.each (function() {
                    if (this.value) COV_AMOUNT.val (parseInt (COV_AMOUNT.val()) + parseInt (this.value));
                    this.required = 0 < this.value;
                    $(`[name='${this.name.slice (0, this.name.indexOf (`A`))}To${this.name.slice (this.name.indexOf (`t`) + 1)}']`).prop (`required`, this.required);
                });
                COV_AMOUNT.trigger (`input`);
            }).trigger (`input`);
        COV_AMOUNT.attr (`min`, 0)
            .on (`input`, function() {
                MO_SUM.value = TOT_AMOUNT.value;
                if ($(`[name='covtrn_id']`).val()) {
                    let cov = COVS.find (cv => cv.trn_id == $(`[name='covtrn_id']`).val());
                    if (cov.amount == "") cov.amount = 0;
                    MO_SUM.value = parseInt (MO_SUM.value) - parseInt (cov.amount);
                }
                if (this.value) MO_SUM.value = parseInt (MO_SUM.value) + parseInt (this.value);
                MO_REM.value = parseInt (RCT_MO_AMT.value) - parseInt (MO_SUM.value);
                if (parseInt (MO_REM.value) < 0 || parseInt (MF_REM.value) < 0) COV_STS_BAR.html (`<p class="w3-large w3-yellow w3-padding"> <b> Cover amount must be less than Remaining amount. </b> </p>`);
                else COV_STS_BAR.html (``);
            }).trigger (`input`);
        COV_MF_AMT.attr (`min`, 0)
            .on (`input`, function() {
                MF_SUM.value = TOT_MF_AMT.value;
                if ($(`[name='covtrn_id']`).val()) {
                    let cov = COVS.find (cv => cv.trn_id == $(`[name='covtrn_id']`).val());
                    if (cov.mfAmt == "") cov.mfAmt = 0;
                    MF_SUM.value = parseInt (MF_SUM.value) - parseInt (cov.mfAmt);
                }
                if (this.value) MF_SUM.value = parseInt (MF_SUM.value) + parseInt (this.value);
                MF_REM.value = parseInt (RCT_MF_AMT.value) - parseInt (MF_SUM.value);
                if (parseInt (MF_REM.value) < 0 || parseInt (MO_REM.value) < 0) COV_STS_BAR.html (`<p class="w3-large w3-yellow w3-padding"> <b> Missionary Fund amount must be less than Mission Remaining amount. </b> </p>`);
                else COV_STS_BAR.html (``);
            }).trigger (`input`);

        $(`.editCov`).click (function() {
            covtrn_id = this.title.split (` `)[3];
            $(`[name='covtrn_id']`).val (covtrn_id);
            // $(`#covForm [name='famHead']`).val ($(this).parent().next().children (`b`).text()).trigger (`input`);
            let cov = COVS.find (cv => cv.trn_id == covtrn_id);
            for (info in cov) $(`#covForm [name='${info}']`).val (cov[info]);
            COV_AMOUNT.trigger (`input`);
            COV_MF_AMT.trigger (`input`);
            $(`#covForm [name='covtrn_id']`).val (cov["trn_id"]);
            $(`#covForm [name='trn_id']`).val (TRN_ID);
            $(`#covForm`)[0].scrollIntoView();
        });

        $(`[name^='moTo']`).change (function() { $(`[name$='To${this.name.slice (4)}']`).val (this.value); });
		$(`[name$='To']:not([name^='moTo'])`).change (function() { $(`[name='moTo${this.name.slice (this.name.indexOf (`o`) + 1)}']`).val (``); });
        COV_PLDGS.each (function() { if (this.value) $(`[name='${this.name.slice (0, this.name.indexOf (`P`))}Amt${this.name.slice (this.name.indexOf (`t`) + 1)}']`).attr (`min`, this.value); });
        COV_FROMS.each (function() { if (this.value) $(`[name='${this.name.slice (0, this.name.indexOf (`F`))}To${this.name.slice (this.name.indexOf (`m`) + 1)}']`).attr (`min`, this.value); });
        $(`.excelLnk`).each ((i, ele) => ele.href = `${ele.href}`);
    }

<? foreach (["Receipts_Add", "Receipts_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
	function deleteCov (trn_id, covNo) {
		let formData = new FormData();
		formData.append (`deleteCov`, trn_id);
		formData.append (`covNo`, covNo);
		postFormData (`FormValid/covDetailsValidation.php`, formData);
	}
	function deleteRct (trn_id) {
		if (confirm ("Are you sure you want to delete this receipt ?")) {
			let formData = new FormData();
			formData.append (`deleteRct`, trn_id);
			postFormData (`FormValid/rctDetailsValidation.php`, formData);
		}
	}
<? 	break; } ?>

<? foreach (["Payments_Add", "Payments_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
	function deletePay (trn_id) {
		if (confirm ("Are you sure you want to delete this voucher ?")) {
			let formData = new FormData();
			formData.append (`deletePay`, trn_id);
			postFormData (`FormValid/payDetailsValidation.php`, formData);
		}
	}
<? 	break; } ?>

	function getTrn (formData, eleId) {
		$.ajax ({
			type: `POST`,
			url: `/<?=$PATH;?>/Transactions.php`,
			enctype: `multipart/form-data`,
			dataType: `text`,
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: () => $(`#${eleId}`).html (`<center><i class="w3-xlarge w3-spin fa fa-cog"></i></center>`),
			success: resp => { $(`#${eleId}`).html (resp); docReady(); },
			error: () => $(`#${eleId}`).html (`<p class="w3-yellow"> Server error, please try again. </p>`)
		});
	}
	function hideCov (n) { $(`.cov${n}`).html (``); }
    function showError (err) {
        const COV_STS_BAR = $(`.covStsBar`);
        COV_STS_BAR.html (``);
        COV_STS_BAR.html (err);
        COV_STS_BAR.parent()[0].scrollIntoView (false);
    }
</script>
<? } ?>

