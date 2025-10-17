<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

$fields = $FormFields["Usr"]["fields"];
foreach ($fields as $key => $vals) $fields[$key] = array_combine ($FormFields["keys"], $vals);
$permFields = $FormFields["Usr_Prm"]["fields"];
foreach ($permFields as $key => $vals) $permFields[$key] = array_combine ($FormFields["keys"], $vals);
?>
<i class="w3-btn w3-green w3-round-xxlarge fa fa-plus mb-4" onclick="$(`#addUsrForm`).toggle()"> New User </i>
<center> <h6> Users </h6> </center>
<div id="statusBar" class="sticky-top"> </div>
<style>
	p.custom-control.custom-switch:has(input.custom-control-input[type='checkbox']:checked) { background-color: hsl(150,100%,{{THEME ? '70' : '10'}}%); }
	p.custom-control.custom-switch:has(input.custom-control-input[type='checkbox']) { background-color: hsl(0,100%,{{THEME ? '80' : '10'}}%); }
	.custom-control-label { font-weight: bold !important; }
</style>
<div class="w3-padding-small">
<? $add = true; require "Forms/Usr.php";
$add = false; $sql = "SELECT * FROM Usr WHERE status='$ACTIVE' ORDER BY usr_id";
$usrs = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC);
foreach ($usrs as $user) { require "Forms/Usr.php"; } ?>
</div>
<script>
    $(document).ready (() => {
        $(`i.editUsr`).click (function() {
			$(`i.cancelEdit`).click();
			$(`form#editUsr_${this.id} input`).attr (`readonly`, false);
			$(`form#editUsr_${this.id} select,form#editUsr_${this.id} :checkbox`).attr (`disabled`, false);
			$(`form#editUsr_${this.id} button,form#editUsr_${this.id} .deleteUsr,form#editUsr_${this.id} .cancelEdit`).show();
		});
		$(`i.cancelEdit`).click (function() {
			$(`form.usrEdit input`).attr (`readonly`, true);
			$(`form.usrEdit select,form.usrEdit :checkbox`).attr (`disabled`, true);
			$(`form.usrEdit button,form.usrEdit .deleteUsr,.cancelEdit`).hide();
		});

<? foreach (["Users_Add", "Users_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
		$(`form`).submit (function() {
			event.preventDefault();
			let formData = new FormData (this);
			postFormData (`FormValid/usrDetailsValidation.php`, formData);
		});
<? 	break; } ?>

		$(`.custom-switch`).click (function() {
			let chk = $(`#chk_${this.id}`);
			if (chk[0].disabled) return;
			chk[0].checked = !$(`#chk_${this.id}`)[0].checked;
			chk.change();
		});
	});

<? foreach (["Users_Add", "Users_Edit"] as $p) if ($usr[$FormFields["permissions"][$p]??""]??0) { ?>
	function deleteUsr (Usr_id) {
    	if (confirm ("Are you sure you want to delete this User ?")) {
			let formData = new FormData();
			formData.append (`deleteUsr`, Usr_id);
			postFormData (`FormValid/usrDetailsValidation.php`, formData);
	    }
	}
<? 	break; } ?>
</script>

