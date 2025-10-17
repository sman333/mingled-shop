<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

$fields = $FormFields["Cat"]["fields"];
foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields["keys"], $vals);
?>
<style>
    .catImg {
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        height: 150px;
    }
</style>
<i class="w3-btn w3-green fa fa-plus badge-pill my-md-n5 sticky-top" onclick="$(`#addCatForm`).slideToggle()" style="top:35px; z-index:2;" title="New Category"> <span class="w3-hide-small"> New Category </span> </i>
<a href="Products/" class="w3-btn w3-blue w3-hide-small badge-pill py-1 sticky-top ml-3" style="top:35px; z-index:2;" title="View All Products"> <i class="fa fa-cube"> </i> <b> View All Products </b> </a>
<h6 class="w3-center"> Categories </h6>
<div id="statusBar" class="sticky-top"> </div>
<div class="w3-padding-small">
<? $add = true; require "Forms/Cat.php"; ?>
<br> <br>
<i class="w3-blue w3-hover-white btn btn-sm badge-pill ml-4 cancelEdit" style="display:none;"> CANCEL </i>
<?
$add = false; $sql = "SELECT * FROM Cat WHERE status IN ('$ACTIVE', '$DISABLED') ORDER BY catName";
$cats = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[];
echo "<p class='pl-4'>", count ($cats), " Categories </p>";
foreach ($cats as $cat) {
	$sql = "SELECT COUNT(*) AS pdtCnt FROM Pdt WHERE status='$ACTIVE' AND catId='".$cat["cat_id"]."'";
	$cat["pdtCnt"] = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["pdtCnt"]??0;
	$cat["pdtCnt"] ? "":$cat["pdtCnt"] = "-";
	require "Forms/Cat.php";
}
?>
</div>
<script>
let editCat = false;
$(document).ready (() => {
	$(`i.editCat`).click (function() {
		if (editCat) {
			if (editCat != this.id) $(`form#editCat_${editCat} :submit`).click();
		}else {
			$(`i.cancelEdit`).click();
			$(`form#editCat_${this.id} input`).attr (`readonly`, false);
			$(`form#editCat_${this.id} select,form#editCat_${this.id} :file`).attr (`disabled`, false);
			$(`form#editCat_${this.id} button,form#editCat_${this.id} .deleteCat,form#editCat_${this.id} .cancelEdit`).show();
			editCat = this.id;
		}
	});
	$(`i.cancelEdit`).click (function() {
		$(`form.catEdit input`).attr (`readonly`, true);
		$(`form.catEdit select,form.catEdit :file`).attr (`disabled`, true);
		$(`form.catEdit button,form.catEdit .deleteCat,.cancelEdit`).hide();
		editCat = false;
	});

<? foreach (["Category_Add", "Category_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
	$(`form`).submit (function() {
		event.preventDefault();
		if (!validateFiles ($(this))) return;
		let formData = new FormData (this);
		postFormData (`FormValid/catDetailsValidation.php`, formData);
	});
<? 	break; } ?>
});

function validateFiles (form) {
	let fs = form.find (`:file`);
	let noErr = true;
	fs.each (function() {
		if (this.value.length) {
			let fileName = this.value;
			let ext = fileName.slice (fileName.lastIndexOf (`.`));
			let allowedTypes = [``, `.jpg`, `.jpeg`, `.JPG`, `.JPEG`];
			if (!(allowedTypes.includes (ext))) {
				S_B.html (`<p class='FailedResponse'> ${DocNames[this.name]} --> file type (${ext}) not allowed. </p>`);
				noErr = false;
				return;
			}
			let maxSize = 1024 * 1024;
			if (this.files.size > maxSize) {
				S_B.html (`<p class='FailedResponse'> ${DocNames[this.name]} --> file size limit 1000 KB.<br> File size ${this.files[0].size / 1024} KB. </p>`);
				noErr = false;
				return;
			}
		}
	});
	return noErr;
}

<? foreach (["Category_Add", "Category_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
function deleteCat (id) {
	if (confirm ("Are you sure you want to delete this Category ?")) {
		let formData = new FormData();
		formData.append (`deleteCat`, id);
		postFormData (`FormValid/catDetailsValidation.php`, formData);
		editCat = false;
	}
}
<? 	break; } ?>

    function cpyLnk (name) { navigator.clipboard.writeText (`${location.hostname}/Shop/${name}`); }
    async function shrLnk (name) {
        try { await navigator.share ({ text: name, url: `/Shop/${name}` });
        } catch (err) { console.log (`Error: ${err}`); }	        
    }
</script>

