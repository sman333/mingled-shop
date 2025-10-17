<form id="<?=$add ? "addCatForm" : ("editCat_" . $cat["cat_id"]);?>"
    class="w3-card w3-round-xlarge w3-border w3-topbar w3-border-<?=$add ? "purple py-2 collapsee" : "brown w3-row mx-1 catEdit";?>">
<?
if ($add) { ?>
    <h6 class="w3-center"> New Category </h6>
    <div class="w3-row mx-4">
<? }else { ?>
    <p class="mb-0">
        <input type="hidden" name="cat_id" value="<?=$cat["cat_id"]??"";?>" required />
        <i class="fa fa-edit ml-2 mt-1 editCat" title="Edit Category <?=$cat["cat_id"];?> Details" id="<?=$cat["cat_id"];?>"> </i>
        <button type="submit" class="w3-btn w3-amber badge-pill p-1 px-3" style="display:none;"> SAVE </button>
        <i class="w3-btn w3-blue w3-hover-white badge-pill mx-1 p-1 px-3 cancelEdit" style="display:none;"> CANCEL </i>
        <a href="Products/<?=$cat["cat_id"];?>/" class="w3-btn w3-large w3-text-blue w3-hover-blue px-2 mx-3"> <i class="fa fa-cube"> </i> <b> <?=$cat["pdtCnt"];?> </b> </a>

        <i class="w3-right w3-text-red w3-xlarge fa fa-trash btn py-0 my-1 deleteCat" onclick="deleteCat(", $cat["cat_id"], ")" title="Delete Row" style="display:none;"> </i>
        <a target="_blank" href="/Shop/<?=str_replace (" ", "_", $cat["catName"]);?>" class="w3-right w3-large w3-text-blue fa fa-external-link p-3" title="View in Shop"> </a>
        <i class="w3-right w3-button w3-large w3-text-green w3-round-large fa fa-share-alt p-3" title="Share" onclick="shrLnk(`<?=str_replace (" ", "_", $cat["catName"]);?>`)"> </i>
        <i class="w3-right w3-button w3-large w3-text-brown w3-round-large fa fa-copy p-3" title="Copy Link" onclick="cpyLnk(`<?=str_replace (" ", "_", $cat["catName"]);?>`)"> </i>
    </p>
<? }

echo "<div class='w3-col s8 l9'>";
foreach ($fields as $name => $attr) {
    echo "<p class='w3-col l", $attr["col"], " m-0 px-1'> <i class='w3-block w3-small w3-darkpurple pl-2'> ", $attr["label"], " </i>";

    if ($attr["type"] == "sel") {
?>
        <select class="w3-select p-1" name="<?=$name;?>" <?=$add ? "size='".count ($FormFields["Cat"]["select"][$name])."'" : "disabled";?> required>
<?      foreach ($FormFields["Cat"]["select"][$name] as $val) { ?> <option value="<?=$val;?>" <?=($cat[$name]??"") == $val ? "selected":"";?>> <?=$val;?> </option> <? } ?>
        </select>
<?
    }else echo "<input type='", $attr["type"], "' class='w3-input p-1' name='$name' value='", $cat[$name]??"", "' ", $add ? "":" readonly ", $name == "catName" ? " required ":"", " />";

    echo "</p>";
}
echo "</div>";

echo "<p class='w3-col s4 l3 w3-center'>";
if (!$add) echo "<i class='w3-block w3-round-large catImg' style='background-image:url(", $PATH, "images/category/", $cat["cat_id"], ".jpeg);'> </i>";
echo    "<input type='file' class='w3-input w3-small p-1' name='photo' accept='image/jpeg'", $add ? "":" disabled ", "/>";
echo "</p>";

if ($add) { ?> </div>
    <p class="w3-center mb-0">
        <button type="submit" class="w3-btn w3-green w3-wide badge-pill my-3 px-5 mr-4"> <b> ADD </b> </button>
    </p>
<? } ?>

</form>

