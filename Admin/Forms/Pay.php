<form id="<?=$add ? "addPayForm" : ("Pay_".$trn["trn_id"]);?>"
    class="w3-card w3-round-xxlarge w3-border w3-topbar my-2 mx-1 w3-border-<?=$add ? "red px-5 py-4" : "brown row p-3";?>">
    <input type="hidden" name="trn_id" value="<?=$trn["trn_id"]??"";?>" required />
<?
$flds = $fields;
if ($add) { $trn["action"] = "Cr";
?>
    <center> <h6> <?=($_GET["srNo"]??0) ? "Edit" : "New";?> Payment </h6> </center>
    <div class="row">
<?
}else {
?>
    <p class="col-12">
        <a href="/<?="$PATH/Payments/".$trn["srNo"];?>/" class="fa fa-edit" title="Edit Payment <?=$trn["trn_id"];?>"> </a>
        <span> Vr.No. <b class="w3-text-red"> <?=$trn["srNo"];?> </b> </span>

        <i class="w3-center w3-brown badge-pill px-3 mx-5"> <?=$trn["createdBy"], $trn["createAt"] ? " &nbsp;&nbsp; ".date_create (explode (" ", $trn["createAt"])[0])->format ("d-m-Y")." &nbsp; ".explode (" ", $trn["createAt"])[1]:"";?> </i> 
        <?=$trn["modifyBy"] ? "<i class='w3-center w3-dark-gray badge-pill px-3 mx-5'>".$trn["modifyBy"].($trn["modifyAt"] ? " &nbsp;&nbsp; ".date_create (explode (" ", $trn["modifyAt"])[0])->format ("d-m-Y")." &nbsp; ".explode (" ", $trn["modifyAt"])[1]:"")."</i>":"";?>

    	<i class="w3-right w3-text-red w3-xlarge fa fa-trash btn p-0 mt-2" title="Delete Payment" onclick="deletePay(<?=$trn["trn_id"];?>)"> </i>
        <a href="/<?=$PATH;?>/print_payment/<?=$trn["srNo"];?>/" target="_blank" class="w3-btn w3-right w3-border w3-border-blue w3-hover-blue w3-text-blue w3-xlarge fa fa-print p-2 mr-3 circ" title="Print Payment"> </a>
    </p>
<?
    if (($trn["payMode"]??"") != "CHEQUE" && ($trn["payMode"]??"") != "ACC_TRA") unset ($flds["chequeDate"], $flds["chequeNo"], $flds["chequeBank"]);
    if (($trn["payMode"]??"") != "UPI") foreach (["upiTrnDate", "upiTrnId"] as $cq) unset ($flds[$cq]);
}
foreach ($flds as $name => $attr) { ?>
    <p class="col-<?=$attr["col"];?> mb-0 px-1">
        <?=($attr["label"]??0) ? '<i class="w3-block w3-darkred pl-2">'.$attr["label"].'</i>':"";?>

<?  if ($attr["type"] == "sel") { ?>
	<select class="w3-select" name="<?=$name;?>" <?=$add ? "id=new$name":"disabled";?> required>
<?      foreach ($FormFields["Pay"]["select"][$name] as $val => $label) { ?>
        <option <?=$name == "payFor" ? 'class="serHead'.$FormFields["Pay"]["select"]["serMap"][$val].'"':"";?> value="<?=$val;?>"
            <?=($name == "serHead" ? $FormFields["Pay"]["select"]["serMap"][$trn["payFor"]??""] : $trn[$name]??"") == $val ? "selected":"";?>> <?=$label;?> </option>
<?      } ?>
    </select>
<?
    }elseif ($attr["type"] == "area") echo "<textarea rows='2' class='w3-input' name='$name' id='new$name' ", $add ? "":"readonly", ">", $trn[$name]??"", "</textarea>";

    else echo "<input type='", $attr["type"], "' class='w3-input' name='$name' value='", $trn[$name]??"","' ", $add ? "id=new$name" : "readonly", 
            $attr["type"] == "date" ? " min='$FinYrStartMonth' max='$tdy'":"", 
            in_array ($name, ["srNo", "particulars", "commResoNo",  "commResoDate", "billInvNo", "billInvDate", "phone", "payAddress"]) ? "":" required", 
            $name == "particulars" ? " list='commonPartList'":"", "/>";
?>
    </p>
<? }
if ($add) { ?> </div>
    <button type="submit" class="w3-btn w3-<?=($_GET["srNo"]??0) ? "amber" : "green";?> badge-pill m-4 px-4 addPay"> <b> S A V E </b> </button>
    <?
    if ($_GET["srNo"]??0) {
?>
<a href="/<?=$PATH;?>/Payments" class="w3-btn w3-blue badge-pill px-4"> </i> <b> C A N C E L </b> </a>
<?
    }
} ?>

</form>

