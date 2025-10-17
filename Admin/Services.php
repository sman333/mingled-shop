<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

$fields = $FormFields["Ser"]["fields"];
foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields["Ser"]["keys"], $vals);
?>
<center> <h6> Services </h6> </center>
<div id="statusBar" class="sticky-top"> </div>
<div class="w3-padding-large">
<? $add = true; require "Forms/Ser.php"; ?>
</div>
<div class="w3-padding">
	<div class="w3-padding-small" style="overflow-x:scroll;">
		<div style="min-width:500px;">
			<div class="row w3-black w3-center">
				<i class="w3-blue w3-hover-white btn btn-sm badge-pill ml-4 cancelEdit" style="display:none;"> CANCEL </i>
<? foreach ($fields as $x => $attr) { ?> <b class="col-<?=$attr["col"];?> mx-auto"> <?=$attr["label"];?> </b> <? } ?>
				<!-- <b class="col-1 mx-auto"> Mod. On </b> -->
			</div>
<? $add = false; 
unset ($FormFields["Ser"]["select"]["serHead"][""]);
foreach ($FormFields["Ser"]["select"]["serHead"] as $i => $x) { ?>
<p class="w3-text-{{THEME ? 'indigo' : 'blue'}} m-0 pt-4"> <b> <?=$x;?> </b> </p>
<?	$sql = "SELECT * FROM Ser WHERE status='$ACTIVE' AND serHead='$i' ORDER BY serFor DESC,remarks + 0";
	$sers = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC);
	foreach ($sers as $ser) { require "Forms/Ser.php"; }
} ?>
		</div>
	</div>
</div>
<datalist id="serList">
<? $sql = "SELECT * FROM Ser WHERE status='$ACTIVE' ORDER BY serName";
$sers = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;
foreach ($sers as $ser) { ?> <option value="<?=$ser["serName"];?>"> <?=$FormFields["Ser"]["select"]["serFor"][$ser["serFor"]];?> </option> <? } ?>
</datalist>
<script>
	$(document).ready (() => {
		$(`i.editSer`).click (function() {
			$(`i.cancelEdit`).click();
			$(`form#editSer_${this.id} input,form#editSer_${this.id} textarea`).attr (`readonly`, false);
			$(`form#editSer_${this.id} select`).attr (`disabled`, false);
			$(`form#editSer_${this.id} button,form#editSer_${this.id} .deleteSer,.cancelEdit`).show();
		});
		$(`i.cancelEdit`).click (function() {
			$(`form.serEdit input,form.serEdit textarea`).attr (`readonly`, true);
			$(`form.serEdit select`).attr (`disabled`, true);
			$(`form.serEdit button,form.serEdit .deleteSer,.cancelEdit`).hide();
		});

<? foreach (["Services_Add", "Services_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
		$(`form`).submit (function() {
			event.preventDefault();
			let formData = new FormData ($(this)[0]);
			postFormData (`FormValid/serDetailsValidation.php`, formData);
		});
<? 	break; } ?>

	});

<? foreach (["Services_Add", "Services_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
	function deleteSer (id) {
		let formData = new FormData();
		formData.append (`deleteSer`, id);
		postFormData (`FormValid/serDetailsValidation.php`, formData);
	}
<? 	break; } ?>

</script>
