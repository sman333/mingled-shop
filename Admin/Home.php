<? require "AuthenticateUser.php"; require "checkPerm.php"; ?>
<div class="w3-display-container w3-hide-small <?=($menuDialog??1) ? "":("w3-animate-".(($_SESSION["prevMenuDepth"]??0) <= ($MMI["depth"][$_GET["page"]]??0) ? "right" : "left"));?> mt-n4" style="min-height:95vh;">

	<div class="w3-display-middle w3-card-4 circ flexCenter rpb" style="height:30vh; z-index:1;" title="System Clock Date & Time">
		<div class="circ flexCenter clock w3-{{THEME ? 'white' : 'black'}}" style="height:29vh;"> <p class="w3-serif w3-center mb-0"> <i class="w3-jumbo fa fa-clock-o"> </i> </p> </div>
	</div>

	<nav class="w3-display-middle w3-row" style="top:48vh; width:100%;">
<?
foreach ($MMI["Quad"] as $q => $items) {
?>
		<div class="w3-col m6" style="padding:5vh;">
			<div class="w3-row w3-card w3-border w3-border-<?=$MMI["color"][$q];?> w3-{{THEME ? 'white' : 'black'}} w3-topbar w3-round-xxlarge quad p-3" style="min-height:40vh;">
<?
	foreach ($items as $txt => $lnk) {
		if (!$usr[$FormFields["permissions"][explode ("#", $lnk)[0]]]) continue;
?>
		<a href="<?=$PATH, "Admin/$lnk";?>"
			class="w3-col s6 l4 w3-btn w3-border w3-bottombar w3-round-xlarge w3-border-{{THEME ? 'white' : 'black'}} mb-4 
				w3-hover-border-<?=$MMI["color"][$q], " ", $_GET["page"] == explode ("/", $lnk)[0] ? " w3-".$MMI["color"][$q]." w3-text-":"";?>{{THEME ? 'white' : 'black'}}">
			<p class="mb-0 w3-large">
				<i class="w3-xlarge <?=$_GET["page"] == explode ("/", $lnk)[0] ? " w3-text-":"";?>{{THEME ? 'white' : 'black'}} w3-text-<?=$MMI["color"][$q];?> fa fa-<?=$MMI["icons"][$txt];?>"> </i>
				<br> <?=$txt;?>
				<? /* =in_array ($txt, $MMI["beta"]) ? '<br> <i class="w3-text-indigo w3-border w3-border-indigo w3-small badge-pill px-1 py-0"> βeta </i>':''; */?>
			</p>
		</a>
<?
	}
?>
			</div>
		</div>
<?
}
?>
	</nav>
</div>

<div class="w3-hide-medium w3-hide-large <?=($menuDialog??1) ? "":("w3-animate-".(($_SESSION["prevMenuDepth"]??0) <= ($MMI["depth"][$_GET["page"]]??0) ? "right" : "left"));?>">

	<div class="w3-card-4 mx-auto my-5 circ flexCenter rpb" style="height:30vh; z-index:1;" title="System Clock Date & Time">
		<div class="circ flexCenter clock w3-{{THEME ? 'white' : 'black'}}" style="height:29vh;"> <p class="w3-serif w3-center mb-0"> <i class="w3-jumbo fa fa-clock-o"> </i> </p> </div>
	</div>

	<nav class="w3-row">
<?
foreach ($MMI["Quad"] as $q => $items) {
?>
		<div class="w3-col m6 py-1">
			<div class="w3-row w3-card w3-border w3-border-<?=$MMI["color"][$q];?> w3-{{THEME ? 'white' : 'black'}} w3-topbar w3-round-xxlarge quad p-3" style="min-height:40vh;">
<?
	foreach ($items as $txt => $lnk) {
		if (!$usr[$FormFields["permissions"][explode ("#", $lnk)[0]]]) continue;
?>
		<a href="<?=$PATH, "Admin/$lnk";?>"
			class="w3-col s6 l4 w3-btn w3-border w3-bottombar w3-round-xlarge w3-border-{{THEME ? 'white' : 'black'}} mb-4 
				w3-hover-border-<?=$MMI["color"][$q], " ", $_GET["page"] == explode ("/", $lnk)[0] ? " w3-".$MMI["color"][$q]." w3-text-":"";?>{{THEME ? 'white' : 'black'}}">
			<p class="mb-0 w3-large">
				<i class="w3-xlarge <?=$_GET["page"] == explode ("/", $lnk)[0] ? " w3-text-":"";?>{{THEME ? 'white' : 'black'}} w3-text-<?=$MMI["color"][$q];?> fa fa-<?=$MMI["icons"][$txt];?>"> </i>
				<br> <?=$txt;?>
				<? /* =in_array ($txt, $MMI["beta"]) ? '<br> <i class="w3-text-indigo w3-border w3-border-indigo w3-small badge-pill px-1 py-0"> βeta </i>':''; */?>
			</p>
		</a>
<?
	}
?>
			</div>
		</div>
<?
}
?>
	</nav>
</div>

