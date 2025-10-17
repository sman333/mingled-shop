<form id="<?=$add ? "addRctForm" : ("Rct_".$trn["trn_id"]);?>"
    class="w3-card w3-round-xxlarge w3-border w3-topbar my-2 mx-1 w3-border-<?=$add ? "green px-5 py-4" : "brown row p-3";?>">
    <input type="hidden" name="trn_id" value="<?=$trn["trn_id"]??"";?>" required />
<?
$flds = $fields;
if ($add) { $trn["action"] = "Cr";
?>
    <center> <h6> <?=($_GET["srNo"]??0) ? "Edit" : "New";?> Receipt </h6> </center>
    <div class="row">
<?
}else {
?>
    <p class="col-12">
        <a href="/<?="$PATH/Receipts/".$trn["srNo"];?>/" class="fa fa-edit" title="Edit Receipt <?=$trn["trn_id"];?>"> </a>
        <span> Rt.No. <b class="w3-text-green"> <?=$trn["srNo"];?> </b> </span>

        <i class="w3-center w3-brown badge-pill px-3 mx-5"> <?=$trn["createdBy"], $trn["createAt"] ? " &nbsp;&nbsp; ".date_create (explode (" ", $trn["createAt"])[0])->format ("d-m-Y")." &nbsp; ".explode (" ", $trn["createAt"])[1]:"";?> </i> 
        <?=$trn["modifyBy"] ? "<i class='w3-center w3-dark-gray badge-pill px-3 mx-5'>".$trn["modifyBy"].($trn["modifyAt"] ? " &nbsp;&nbsp; ".date_create (explode (" ", $trn["modifyAt"])[0])->format ("d-m-Y")." &nbsp; ".explode (" ", $trn["modifyAt"])[1]:"")."</i>":"";?>

        <i class="w3-right w3-text-red w3-xlarge fa fa-trash btn p-0 mt-2" title="Delete Receipt" onclick="deleteRct(<?=$trn["trn_id"];?>)"> </i>
        <a target="_blank" href="/<?=$PATH;?>/print_receipt/<?=$trn["srNo"];?>/" class="w3-btn w3-right w3-border w3-border-blue w3-hover-blue w3-text-blue w3-xlarge fa fa-print p-2 mr-4 circ" title="Print Receipt"> </a>
<?  if ($trn["nonMem"] == "") { ?>
        <a target="_blank" href="/<?=$PATH;?>/MonthlySub/<?=$trn["famNo"];?>/" class="w3-btn w3-right w3-border w3-border-purple w3-hover-purple w3-text-purple w3-large fa fa-th-list p-2 mr-3 circ" title="Family Info"> </a>
        <a target="_blank" href="/<?=$PATH;?>/Family/<?=$trn["famNo"];?>/" class="w3-btn w3-right w3-border w3-border-purple w3-hover-purple w3-text-purple w3-large fa fa-users p-2 mr-3 circ" title="Edit Family"> </a>
<?  } ?>
<?  if ($trn["payFor"] == 53) { ?>
        <a href="/<?=$PATH;?>/Transactions/Rct/<?=$trn["srNo"];?>/" class="w3-btn w3-right w3-border w3-border-teal w3-hover-teal w3-text-teal w3-xlarge fa fa-envelope-open p-2 mx-3 circ" title="Cover Offering Details"> </a>
<?      if ($covCnt) { ?> <i class="w3-btn w3-right w3-border w3-border-blue w3-hover-blue w3-text-blue w3-xlarge fa fa-list p-2 circ" title="Cover Offering Report" onclick="COV_LIST_DIALOG.showModal()"> </i> <? } ?>
<?  } ?>
<?      if ($trn["payFor"] == 53) { ?> <b class="w3-right w3-text-teal w3-border w3-border-teal badge-pill mr-5 ml-auto"> <i> <?=$covCnt;?> Covers </i> </b> <? } ?>
    </p>
<?
    if ($trn["famNo"] == 30003) unset ($trn["famNo"], $trn["oldFamNo"]);
    switch ($trn["payFor"]??"") {
        case 45:    /* marriage - marriage fee */
            foreach (array_merge ($hallFields, $memFields, $bdmfFields, $moFields) as $f) unset ($flds[$f]);
            break;
        case 4:     /* offertory - birthday offering */
        case 42:    /* offertory - Monthly Offering Arrear */
        case 234:   /* offertory - Monthly Offering Arrear 23_24 */
        case 162:	/* Offertory - Harvest Festival - Donation */
        case 164:	/* Offertory - Harvest Festival - Bidding */
        case 6:     /* missionary fund - missionary monthly offering */
        case 83:    /* missionary fund - board for mission - sdk */
            foreach ($prefixes as $prefix)
                if (!$trn[$prefix."Amt"]) foreach (array_merge ($infos, ["", "Amt", "PgNo"]) as $info) unset ($flds[$prefix.$info]);
                else $flds[$prefix."PgNo"]["col"] = 6;
            foreach (array_merge ($hallFields, $marryFields, $moFields, $memFields) as $f) unset ($flds[$f]);
            break;
        case 7:     /* offertory - monthly subscription */
            foreach ($prefixes as $prefix) {
                if (!$trn[$prefix."Amt"]) foreach (array_merge ($infos, ["", "Amt", "PgNo"]) as $info) unset ($flds[$prefix.$info]);
                unset ($flds[$prefix."TotPaid"], $flds[$prefix."Arrear"]);
            }
            foreach (array_merge ($hallFields, $marryFields, ["particulars"]) as $f) unset ($flds[$f]);
            break;
        case 25:    /* blm hall - hall rent */
            foreach (array_merge ($marryFields, $memFields, $bdmfFields, $moFields) as $f) unset ($flds[$f]);
            break;
        default:
            foreach (array_merge ($hallFields, $marryFields, $memFields, $bdmfFields, $moFields) as $f) unset ($flds[$f]);
    }
    if (($trn["payMode"]??"") != "CHEQUE" && ($trn["payMode"]??"") != "ACC_TRA") unset ($flds["chequeDate"], $flds["chequeNo"], $flds["chequeBank"]);
    if (($trn["payMode"]??"") != "UPI") unset ($flds["upiTrnDate"], $flds["upiTrnId"]);
    if (!($trn["mfAmt"]??0)) unset ($flds["mfAmt"]);
    if (!($trn["moAmt"]??0)) unset ($flds["moAmt"]);
}
foreach ($flds as $name => $attr) { ?>
    <p class="col-<?=$attr["col"];?> mb-0 px-1">
        <?=($attr["label"]??0) ? '<i class="w3-block w3-darkgreen pl-2">'.$attr["label"].'</i>':"";?>
        <?=($add && $name == "famHead") ? "<span class='w3-red badge-pill collapse newFam'></span>":"";?>

<?  if ($attr["type"] == "sel") { ?>
	<select class="w3-select" name="<?=$name;?>" <?=$add ? "id=new$name" : "disabled",$name == "ward" ? "":" required";?>>
<?      foreach ($FormFields["Rct"]["select"][$name] as $val => $label) { ?>
        <option <?=$name == "payFor" ? 'class="serHead'.$FormFields["Rct"]["select"]["serMap"][$val].'"':"";?> value="<?=$val;?>"
            <?=($name == "serHead" ? $FormFields["Rct"]["select"]["serMap"][$trn["payFor"]??""] : $trn[$name]??"") == $val ? "selected":"";?>> <?=$label;?> </option>
<?      } ?>
    </select>
<?
    }elseif ($attr["type"] == "area") echo "<textarea rows='1' class='w3-input' name='$name' ", $add ? "id=new$name" : "readonly", ">", $trn[$name]??"", "</textarea>";

    else echo "<input type='", $attr["type"], "' class='w3-input' name='$name' value='", ($trn[$name]??"") ? ($trn[$name]??""):"", "' ", 
        $attr["type"] == "date" ? "max='$tdy'":"", 
        $attr["type"] == "month" ? "min='$FinYrStartMonth' max='$FinYrEndMonth'":"", 
        $add ? "id=new$name" : "readonly", $name == "famHead" ? " list='famList'":"", in_array ($name, $notReq) ? "":" required", " />";
?>
    </p>
<? }
if ($add) { ?> </div>
    <button type="submit" class="w3-btn w3-<?=($_GET["srNo"]??0) ? "amber" : "green";?> badge-pill m-4 px-4 addRct"> <b> S A V E </b> </button>
<?
    if ($_GET["srNo"]??0) {
?>
<a href="/<?=$PATH;?>/Receipts" class="w3-btn w3-blue badge-pill px-4"> </i> <b> C A N C E L </b> </a>
<?
    }
} ?>

</form>

