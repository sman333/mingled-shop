<form id="<?=$add ? "addPdtForm" : ("editPdt_" . $pdt["pdt_id"]);?>"
    class="w3-card w3-round-xlarge w3-border w3-topbar w3-border-<?=$add ? "blue py-2 collapsee mb-5" : "brown w3-row p-1 mx-1 my-1 pdtEdit";?>">
<?
if ($add) { ?>
    <h6 class="w3-center"> New Product </h6>
    <div class="w3-row mx-2">
<? }else { ?>
    <p class="mb-0">
        <input type="hidden" name="pdt_id" value="<?=$pdt["pdt_id"]??"";?>" required />
        <i class="fa fa-edit ml-2 mt-2 editPdt" title="Edit Product <?=$pdt["pdt_id"];?> Details" id="<?=$pdt["pdt_id"];?>"> </i>
        <button type="submit" class="w3-btn w3-amber badge-pill p-1 px-3" style="display:none;"> SAVE </button>
        <i class="w3-btn w3-blue w3-hover-white badge-pill mx-1 p-1 px-3 cancelEdit" style="display:none;"> CANCEL </i>
        <b class="w3-large w3-text-gray"> <i> &nbsp; <?=$pdt["pdt_id"];?> </i> </b>

        <i class="w3-right w3-text-red w3-xlarge fa fa-trash btn p-0 pr-2 my-1 deletePdt" onclick="deletePdt(<?=$pdt["pdt_id"];?>)" title="Delete Row" style="display:none;"> </i>
        <a target="_blank" href="/Product/<?=$pdt["pdt_id"];?>" class="w3-right w3-large w3-text-blue fa fa-external-link py-2 px-3" title="View in Shop"> </a>
        <i class="w3-right w3-button w3-large w3-text-green w3-round-large fa fa-share-alt" title="Share" onclick="shrLnk(<?=$pdt["pdt_id"];?>)"> </i>
        <i class="w3-right w3-button w3-large w3-text-brown w3-round-large fa fa-copy" title="Copy Link" onclick="cpyLnk(<?=$pdt["pdt_id"];?>)"> </i>
    </p>
<? }

echo "<p class='w3-col s4 l3 w3-center'>";
if (!$add) echo "<i class='w3-block w3-round-large pdtImg' style='background-image:url(", $PATH, "images/products/", $pdt["catId"], "_", $pdt["pdt_id"], ".jpeg);'> </i>";
echo    "<input type='file' class='w3-input w3-small p-1' name='photo' accept='image/jpeg'", $add ? "":" disabled ", "/>";
echo "</p>";

echo "<div class='w3-col s8 l9'>";
foreach ($fields as $name => $attr) {
    echo "<p class='w3-col l", $attr["col"], " m-0 px-1'> <i class='w3-block w3-small w3-darkblue pl-2'> ", $attr["label"], " </i>";

    if ($attr["type"] == "sel") {
?>
        <select class="w3-select p-1" name="<?=$name;?>" <?=$add ? "size='".count ($FormFields["Pdt"]["select"][$name])."'" : "disabled";?> required>
<?
        if ($name == "catId") foreach ($FormFields["Pdt"]["select"][$name] as $val => $txt) { ?> <option value="<?=$val;?>" <?=($pdt[$name]??"") == $val ? "selected":"";?>> <?=$txt;?> </option> <? }
        else foreach ($FormFields["Pdt"]["select"][$name] as $val) { ?> <option value="<?=$val;?>" <?=($pdt[$name]??"") == $val ? "selected":"";?>> <?=$val;?> </option> <? }
?>
        </select>
<?
    }else echo "<input type='", $attr["type"], "' class='w3-input p-1' name='$name' value='", $pdt[$name]??"", "' ", $add ? "":" readonly ", in_array ($name, ["pdtName", "price", "minQty"]) ? " required ":"", " />";

    echo "</p>";
}
echo "</div>";

// -------------------------------- colours -------------------------------- colours -------------------------------- colours  --------------------------------
if (!$add) {
    echo "<p class='w3-col s12 w3-small w3-dark-gray w3-round pl-2 mb-0' onclick='$(this).next().slideToggle().prev().find(`i.fa`).toggleClass(`fa-caret-right fa-caret-down`)'>", count ($clr[$pdt["pdt_id"]??""]??[]), " Colours <i class='fa fa-caret-right'> </i> </p>";
    echo "<div class='w3-col s12 collapse'>";
    foreach ($clrFields as $name => $attr) {
        echo "<p class='w3-col s", $attr["col"], " m-0 px-1'> <i class='w3-block w3-small w3-darkblue pl-2'> ", $attr["label"], " </i>";
        echo    "<input type='", $attr["type"], "' class='w3-input p-1 ", substr ($name, 0, 1) == "p" ? "clrLnk":"", "' name='$name' value='", $pdtClr[$pdt["pdt_id"]??""][$name]??"", "' readonly list='", substr ($name, 0, 1) == "c" ? "clr" : "pdtId", "List'/>";
        echo "</p>";
    }
    echo "</div>";
}

// -------------------------------- more photos -------------------------------- more photos -------------------------------- more photos  --------------------------------
echo "<p class='w3-col s12 w3-small w3-dark-gray w3-round pl-2 mb-0' onclick='$(this).next().slideToggle().prev().find(`i.fa`).toggleClass(`fa-caret-right fa-caret-down`)'> More Images <i class='fa fa-caret-right'> </i> </p>";
echo "<div class='w3-col s12 collapse ", $add ? "show":"", "'>";
for ($i = 1; $i < 5; $i++) {
    echo "<p class='w3-col s3 w3-center px-1'>";
    if (!$add) echo "<i class='w3-block w3-round-large pdtImgs'".(file_exists ("../images/products/".$pdt["catId"]."_".$pdt["pdt_id"]."_$i.jpeg") ? " style='background-image:url($PATH"."images/products/".$pdt["catId"]."_".$pdt["pdt_id"]."_$i.jpeg);'":"")."> </i>";
    echo    "<input type='file' class='w3-input w3-small p-1' name='photo$i' accept='image/jpeg'", $add ? "":" disabled ", "/>";
    if (!$add) echo file_exists ("../images/products/".$pdt["catId"]."_".$pdt["pdt_id"]."_$i.jpeg") ? "<i class='fa fa-close w3-text-red p-1' title='Delete image' onclick='deleteImg($i,".$pdt["pdt_id"].")'></i>":"";
    echo "</p>";
}
echo "</div>";

if ($add) { ?> </div>
<p class="w3-center mb-0">
    <button type="submit" class="w3-btn w3-green w3-wide badge-pill my-3 px-5 mr-4"> <b> ADD </b> </button>
</p>
<? } ?>
</form>

