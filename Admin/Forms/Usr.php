<form id="<?=$add ? "addUsrForm" : ("editUsr_" . $user["usr_id"]);?>"
    class="w3-card w3-round-xxlarge w3-border w3-topbar w3-padding w3-border-<?=$add ? "blue w3-animate-left mb-5 collapse" : "brown mx-1 usrEdit";?>">

<? if ($add) { ?>
<center> <h6> New User </h6> </center>
<? }else { ?>
    <p class="mb-0">
        <input type="hidden" name="usr_id" value="<?=$user["usr_id"];?>" required />
        <i class="fa fa-edit ml-2 mt-2 editUsr" id="<?=$user["usr_id"];?>" title="Edit User <?=$user["usr_id"];?> Details"> </i>
        <button type="submit" class="w3-btn w3-amber badge-pill p-1 px-3" style="display:none;"> SAVE </button>
        <i class="w3-btn w3-blue w3-hover-white badge-pill mx-1 p-1 px-3 cancelEdit" style="display:none;"> CANCEL </i>

        <i class="w3-serif w3-right" title="D.O.J."> <?=date_create ($user["doj"])->format ("d-M-Y");?> </i>
        <i class="w3-right w3-text-red w3-xlarge fa fa-trash btn py-0 my-1 mr-3 deleteUsr" onclick="deleteUsr(<?=$user["usr_id"];?>)" title="Delete User" style="display:none;"> </i>
    </p>
<? } ?>

<div class="w3-row-padding">
<? foreach ($fields as $name => $attr) { ?>
    <!-- <p class="w3-col mb-0" style="width:<?=$attr["col"]."%";?>;"> -->
    <p class="w3-col m6 l<?=$attr["col"];?> mb-0">
    <?=($attr["label"]??0) ? '<i class="w3-block w3-darkblue pl-2">'.$attr["label"].'</i>':"";?>

<?  if ($attr["type"] == "sel") { ?>
    	<select class="w3-input" name="<?=$name;?>" <?=$add ? "":"disabled";?> required>
<?      foreach ($FormFields["Usr"]["select"][$name] as $val) { ?> <option value="<?=$val;?>" <?=($user[$name]??"") == $val ? "selected":"";?>> <?=$val;?> </option> <? } ?>
        </select>
<?
    }else echo "<input type='", $attr["type"], "' class='w3-input'name='$name' value='", $user[$name]??"", "' ", $add ? "":"readonly", " required />";
?>
    </p>
<? } ?>
</div>
<!-- /* ________________________________ P E R M I S S I O N S ________________________________ P E R M I S S I O N S ________________________________ P E R M I S S I O N S ________________________________ */ -->
<p class="mt-4 pl-4"> Permissions </p>
<div class="w3-row">
<? foreach ($permFields as $name => $attr) { ?>
    <div class="w3-col m6 l<?=$attr["col"];?> px-4">
        <p class="w3-border w3-border-indigo w3-left-align custom-control custom-switch badge-pill btn pt-3" id="<?=$name,"_",$user["usr_id"]??0;?>">
            <input type="checkbox" class="custom-control-input" id="chk_<?=$name,"_",$user["usr_id"]??0;?>" name="<?=$name;?>" value="1" <?=($user[$name]??0) ? "checked":"";?> <?=$add ? "":"disabled";?> />
            <label class="w3-text-{{THEME ? 'brown' : 'aqua'}} custom-control-label btn pb-3 pt-0 ml-5 px-4" for="chk_<?=$name,"_",$user["usr_id"]??0;?>"> <?=$attr["label"];?> </label>
        </p>
    </div>
<? } ?>
</div>

<? if ($add) { ?> <button type="submit" class="w3-btn w3-green w3-wide badge-pill my-4 px-5 d-block mx-auto"> <b> ADD USER </b> </button> <? } ?>

</form>

