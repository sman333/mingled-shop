<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

$fields = $FormFields["Pdt"]["fields"];
foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields["keys"], $vals);
$clrFields = $FormFields["Pdt"]["clrFields"];
foreach ($clrFields as $key => $vals) $clrFields[$key] = array_combine ($FormFields["keys"], $vals);
$pdt["catId"] = ($_GET["catId"]??0);
$uniqueClr = [];
$clr = [];
$clrCnt = 16;
$sql = "SELECT * FROM Clr";
$clrs = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
foreach ($clrs as $c)
    for ($i = 1; $i <= $clrCnt; $i++) {
        if ((!($c["p$i"]??0)) || !($pt_ct[$c["p$i"]]??0)) continue;
        $clr[$c["p$i"]] = [];
        for ($j = 1; $j <= $clrCnt; $j++) if ($i != $j && strlen ($c["c$j"]??"") && ($c["p$j"]??0) && ($pt_ct[$c["p$j"]]??0)) $clr[$c["p$i"]][$c["c$j"]] = $c["p$j"];
        ksort ($clr[$c["p$i"]]);
        $n = 1;
        $pdtClr[$c["p$i"]] = [];
        foreach ($clr[$c["p$i"]] as $cn => $pn) {
            $pdtClr[$c["p$i"]]["c$n"] = $cn;
            $pdtClr[$c["p$i"]]["p$n"] = $pn;
            $n++;
        }
        $uniqueClr[$c["c$i"]] = "";
    }
?>
<style>
    .pdtImg,.pdtImgs {
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
    }
    .pdtImg { height: 150px; }
    .pdtImgs { height: 70px; }
</style>
<i class="w3-btn w3-green fa fa-plus badge-pill sticky-top" onclick="$(`#addPdtForm`).slideToggle()" style="top:35px; z-index:2;" title="New Product"> <span class="w3-hide-small"> New Product </span> </i>
<a href="<?=$PATH;?>Admin/Category" class="w3-btn w3-purple w3-hide-small badge-pill py-1 sticky-top ml-3" style="top:35px; z-index:2;" title="View Categories"> <i class="fa fa-sitemap"> </i> <b> View Categories </b> </a>
<? if ($_GET["catId"]??0) echo "<a href='..' class='w3-btn w3-blue w3-hide-small badge-pill py-1 sticky-top ml-3' style='top:35px; z-index:2;' title='View All Products'> <i class='fa fa-cube'> </i> <b> View All Products </b> </a>"; ?>
<h6 class="w3-center"> Products </h6>
<div id="statusBar" class="sticky-top"> </div>
<? $add = true; require "Forms/Pdt.php"; ?>
<br>
<?
$add = false; $sql = "SELECT * FROM Pdt WHERE status IN ('$ACTIVE', '$DISABLED') ".(($_GET["catId"]??0) ? "AND catId=".$_GET["catId"]:"")." ORDER BY pdt_id DESC,status,pdtName";
$pdts = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
echo "<p class='pl-4 m-0'>", count ($pdts), " ", ($_GET["catId"]??0) ? $FormFields["Pdt"]["select"]["catId"][$_GET["catId"]]:"Products", "</p>";
?>
<input type="search" class="w3-input w3-content badge-pill sticky-top pl-4" placeholder="Search / Filter" style="max-width:500px; top:35px; z-index:3;" list="pdtList"/> <br>
<div class="w3-row">
<?
foreach ($pdts as $pdt) {
    echo "<div class='w3-half'>";
    require "Forms/Pdt.php";
    echo "</div>";
}
?>
</div>

<datalist id="pdtList">
<? foreach ($pdts as $p) echo "<option value='", $p["pdtName"], "'>", $FormFields["Pdt"]["select"]["catId"][$p["catId"]]??"", ", Rs.", $p["price"], "</option>"; ?>
</datalist>
<datalist id="clrList">
<? foreach (array_keys ($uniqueClr) as $c) echo "<option value='$c'/>"; ?>
</datalist>
<datalist id="pdtIdList">
<? foreach ($pdts as $p) echo "<option value='", $p["pdt_id"], "'>",$p["pdtName"], " " ,$p["colour"], " - ", $FormFields["Pdt"]["select"]["catId"][$p["catId"]]??"", "</option>"; ?>
</datalist>

<script>
let editPdt = false;
$(document).ready (() => {
	$(`i.editPdt`).click (function() {
		if (editPdt) {
			if (editPdt != this.id) $(`form#editPdt_${editPdt} :submit`).click();
		}else {
			$(`i.cancelEdit`).click();
			$(`form#editPdt_${this.id} input`).attr (`readonly`, false);
			$(`form#editPdt_${this.id} select,form#editPdt_${this.id} :file`).attr (`disabled`, false);
			$(`form#editPdt_${this.id} button,form#editPdt_${this.id} .deletePdt,form#editPdt_${this.id} .cancelEdit`).show();
			editPdt = this.id;
		}
	});
	$(`i.cancelEdit`).click (function() {
		$(`form.pdtEdit input`).attr (`readonly`, true);
		$(`form.pdtEdit select,form.pdtEdit :file`).attr (`disabled`, true);
		$(`form.pdtEdit button,form.pdtEdit .deletePdt,.cancelEdit`).hide();
		editPdt = false;
	});

<? foreach (["Products_Add", "Products_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
	$(`form`).submit (function() {
		event.preventDefault();
		if (validateFiles ($(this))) $(this).find (`:file`).attr (`disabled`, true);
		else return;
		let formData = new FormData (this);
		postFormData (`FormValid/pdtDetailsValidation.php`, formData);
	});
	$(`:file`).change (function() {
        if (!this.value.trim().length) return;
		if (!validateFiles ($(this).parents (`form`))) return;
		let formData = new FormData();
		formData.append (this.name, this.files[0], this.files[0].name);
		postFormData (`FormValid/pdtDetailsValidation.php`, formData);
	});
<? 	break; } ?>

	$(`[list='clrList']`).on (`input change`, function() {
	    let ele = $(this).parent();
	    ele.next().find (`.clrLnk`).val (this.value.trim().length ? ele.parent().parent().find (`[name='pdtName']`).val() + " " + this.value:``);
	});
	$(`[type='search']`).on (`input change`, function() {
		if (!this.value) {
			$(`.pdtEdit`).show();
			return;
		}
		let x = this.value.toLowerCase();
		$(`.pdtEdit`).hide().find (`input`).each (function() {
			if (this.value.toLowerCase().indexOf (x) > -1) $(this).parent().parent().parent().show();
		});
	});
    $(`#addPdtForm [name='pdtName']`).focus();
    $(`#addPdtForm [name='status']`).val (`ACTIVE`);
    $(`#addPdtForm [name='price']`).val (0);
// 	$(`.clrLnk`).click (function() { FAM_NO_SRCH.val ($(this).parent().next().find (`input`).val()).change(); });
});
const FileIpNames = { photo : `Photo`, photo1 : `Image 1`, photo2 : `Image 2`, photo3 : `Image 3`, photo4 : `Image 4` };
function validateFiles (form) {
	let fs = form.find (`:file`);
	let noErr = true;
	fs.each (function() {
		if (this.value.length) {
			let fileName = this.value;
			let ext = fileName.slice (fileName.lastIndexOf (`.`));
			let allowedTypes = [``, `.jpg`, `.jpeg`, `.JPG`, `.JPEG`];
			if (!(allowedTypes.includes (ext))) {
				S_B.html (`<p class='FailedResponse'> ${FileIpNames[this.name]} --> file type (${ext}) not allowed. </p>`);
				noErr = false;
				return;
			}
// 			let maxSize = 1024 * 1024;
// 			if (this.files[0].size > maxSize) {
// 				S_B.html (`<p class='FailedResponse'> ${FileIpNames[this.name]} --> file size limit 1000 KB.<br> File size ${Math.round (this.files[0].size / 1024)} KB. </p>`);
// 				noErr = false;
// 				return;
// 			}
		}
	});
	return noErr;
}

<? foreach (["Products_Add", "Products_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
function deletePdt (id) {
	if (confirm ("Are you sure you want to delete this Product ?")) {
		let formData = new FormData();
		formData.append (`deletePdt`, id);
		postFormData (`FormValid/pdtDetailsValidation.php`, formData);
		editPdt = false;
	}
}
function deleteImg (imgNo, pId) {
	if (confirm ("Are you sure you want to delete this image ?")) {
    	let formData = new FormData();
    	formData.append (`deleteImg`, imgNo);
    	formData.append (`pId`, pId);
    	postFormData (`FormValid/pdtDetailsValidation.php`, formData);
	}
}
<? 	break; } ?>

    function cpyLnk (id) { navigator.clipboard.writeText (`${location.hostname}/Product/${id}`); }
    async function shrLnk (id) {
        try { await navigator.share ({ text: $(`#editPdt_${id}`).find (`[name='pdtName']`).val(), url: `/Product/${id}` });
        } catch (err) { console.log (`Error: ${err}`); }	        
    }
</script>

