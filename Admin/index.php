<? require "sysInfo.php"; require "AuthenticateUser.php"; require "GlobalVals.php"; require "checkPerm.php"; ?>
<!DOCTYPE html>
<html lang="en" style="scroll-behavior:smooth;">
<title> <?=$MAIN_NAME;?> <?=$_GET["page"];?> </title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?=$PATH;?>favicon.png" type="image/x-icon"/>
<link rel="stylesheet" href="<?=$PATH;?>lib/w3.min.css"/>
<link rel="stylesheet" href="<?=$PATH;?>lib/bootstrap.min.css"/>
<link rel="stylesheet" href="<?=$PATH;?>lib/font-awesome-4.7.0/css/font-awesome.min.css"/>
<script src="<?=$PATH;?>lib/jquery-3.6.4.min.js"></script>
<script src="<?=$PATH;?>lib/bootstrap.min.js"></script>
<script src="<?=$PATH;?>lib/angular.min.js"></script>
<? require "PostFormData.php"; ?>
<body ng-app="" ng-init="THEME=<?=$usr["theme"];?>" class="w3-{{THEME ? 'light-gray' : 'dark'}}" style="background-color:<?=$usr["theme"] ? "beige" : "black";?>;">
<style>
	:root { /* --timeMin: 0; */ --timeSec: 0; }
	.rpb {
		--rpbProgDeg: calc(var(--timeSec) * 0.1deg);
		background-image: conic-gradient(from 0deg, deeppink var(--rpbProgDeg), {{THEME ? 'hsl(0,0%,80%)' : 'hsl(0,0%,20%)'}} var(--rpbProgDeg));
	}
    .themeBg {
        background-color: deeppink !important;
        color: black !important;
    }
	#statusBar {
		top: 50px;
		z-index: 3 !important;
	}
	[class$=Response] {
		border: 5px none;
		border-left-style: solid;
		border-radius: 5px;
		box-shadow: 0 4px 10px 0 rgba(0,0,0,0.2),0 4px 20px 0 rgba(0,0,0,0.19);
		color: black !important;
		margin: 5px;
		padding: 10px;
	}
	p.FailedResponse {
		background: lightsalmon;
		border-color: red;
	}
	p.SuccessResponse {
		background: palegreen;
		border-color: green;
	}
	a,a:hover { color: inherit; }
	select,option { font-weight: bold; }
	select,textarea {
		background-color: <?=$usr["theme"] ? "beige" : "black";?>;
		border-color: darkslategray;
		color: <?=$usr["theme"] ? "black" : "white";?>;
		background-color: {{THEME ? 'beige' : 'black'}} !important;
		border-color: {{THEME ? 'lightgray' : 'darkslategray'}} !important;
		color: {{THEME ? 'black' : 'white'}} !important;
	}
	form,input:not(#addFamForm [type='date'],.famEdit [type='date'],#famNoSearch,#nameSearch,#mobNoSearch) {
		background-color: <?=$usr["theme"] ? "beige" : "black";?>;
		border-color: darkslategray;
		color: <?=$usr["theme"] ? "black" : "white";?>;
		background-color: {{THEME ? 'beige' : 'black'}};
		border-color: {{THEME ? 'lightgray' : 'darkslategray'}};
		color: {{THEME ? 'black' : 'white'}};
	}
	:disabled {
		background-color: <?=$usr["theme"] ? "beige" : "black";?>;
		color: <?=$usr["theme"] ? "black" : "white";?>;
		background-color: {{THEME ? 'hsl(0,0%,95%)' : 'hsl(0,0%,5%)'}};
		color: {{THEME ? 'black' : 'white'}};
	}
	[value=''] { color: transparent; }
	input::-webkit-calendar-picker-indicator { filter: invert(1); }
	input[type=number] { text-align: right; }
	input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button {
		-webkit-appearance: none;
    	-moz-appearance: none;
		appearance: none;
		display: none;
		margin: 0;
	}
	mark,.highlight {
		background-color: yellow !important;
		color: black !important;
	}
	.circ {
		aspect-ratio: 1 !important;
		border-radius: 50% !important;
	}
	.flexCenter {
		align-items: center;
		display: flex;
		justify-content: center;
	}
	.qkLnk {
		aspect-ratio: 1;
		height: 15vw;
		margin-left: auto;
		margin-right: auto;
		margin-top: -5.5vw;
		position: relative;
		display: flex;
		justify-content: center;
		align-items: center;
	}
	.w3-hoverable-dark>*:hover,.w3-hover-dark:hover { background-color: {{THEME ? 'darkgray' : 'darkslategray'}} !important; }
	.w3-dark {
		background-color: hsl(0,0%,{{THEME ? '80' : '10'}}%) !important;
		color: {{THEME ? 'dimgray' : 'lightgray'}} !important;
	}
	.w3-beige {
		background-color: beige !important;
		color: black !important;
	}
	.w3-crimson {
		background-color: crimson !important;
		color: white !important;
	}
	.w3-darkblue {
		background-color: hsl(240,100%,{{THEME ? '80' : '10'}}%) !important;
		color: {{THEME ? 'navy' : 'dodgerblue'}} !important;
	}
	.w3-darkgreen {
		background-color: hsl(150,100%,{{THEME ? '70' : '10'}}%) !important;
		color: {{THEME ? 'green' : 'lime'}} !important;
	}
	.w3-darkred {
		background-color: hsl(0,100%,{{THEME ? '80' : '10'}}%) !important;
		color: {{THEME ? 'maroon' : 'tomato'}} !important;
	}
	.w3-darkpurple {
		background-color: hsl(300,100%,{{THEME ? '80' : '10'}}%) !important;
		color: {{THEME ? 'purple' : 'orchid'}} !important;
	}
	.w3-darkyellow {
		background-color: hsl(45,100%,{{THEME ? '80' : '10'}}%) !important;
		color: {{THEME ? 'brown' : 'yellow'}} !important;
	}
	.w3-darkcyan {
		background-color: hsl(180,100%,{{THEME ? '80' : '10'}}%) !important;
		color: {{THEME ? 'teal' : 'cyan'}} !important;
	}
	#newamount,[name='famNo'] { font-weight: bold; }
	.beat { animation: beat 200ms <?=time() - strtotime (substr ($_SESSION["inDateTime"], 11)) < 5 ? 50 : 0;?> alternate; }
	@keyframes beat {
		from { background-color: yellow; }
		to { background-color: orangered; }
	}
</style>
<script>
	const MN_END = [
		0,		// spacer
		31,		// jan
<?=date ("y") % 4 ? 28 : 29;?>,		// feb
		31,		// mar
		30,		// apr
		31,		// may
		30,		// jun
		31,		// jul
		31,		// aug
		30,		// sep
		31,		// oct
		30,		// nov
		31,		// dec
	];
</script>
<header class="w3-top w3-card w3-small themeBg" style="z-index:5;">
	<div class="w3-bar">
		<a href="<?=$PATH;?>Admin/Home" class="w3-bar-item w3-button w3-hide-small py-0 px-1" style="font-size:1.9em;" title="Home"> <i class="fa fa-home"> </i> </a>
		<b class="w3-bar-item"> <?=$APP_NAME;?> </b>

		<a href="#menu" class="w3-bar-item w3-button w3-hide-small w3-hide-medium py-0" onclick=menu() title="Menu" style="font-size:1.9em; position:absolute; top:0; left:48vw;"> <i class="fa fa-th-large"> </i> </a>
		<a href="#menu" class="w3-bar-item w3-button w3-hide-small w3-hide-large py-0" onclick=menu() title="Menu" style="font-size:1.9em;"> <i class="fa fa-th-large"> </i> </a>

		<div class="w3-dropdown-hover w3-right">
			<button type="button" class="w3-button"> <i class="fa fa-caret-down"> </i> </button>
			<div class="w3-dropdown-content w3-bar-block w3-round-xlarge w3-card-4" style="right:0; z-index:5;">
				<small class="w3-bar-item"> <i> Last Login: </i> <?=$lastLogin??"";?> </small> 
				<a href="<?=$PATH;?>Admin/Logout" class="w3-bar-item w3-button w3-hover-red w3-round-xlarge"> <i class="fa fa-power-off"> </i> Log Out </a>
			</div>
		</div>
		<a href="<?=$PATH;?>Admin/profile" class="w3-bar-item w3-right"> <?=($_SESSION["usertype"]??"")," ",($_SESSION["name"]??"");?> </a>
		<b class="w3-bar-item w3-button w3-right w3-hover-{{THEME ? 'black' : 'white'}} changeTheme" title="{{THEME ? 'Dark' : 'Light'}} Theme" ng-click="THEME=!THEME"> <i class="fa fa-adjust"> </i> </b>
<?
// if ($bookPendCnt) echo "<a href='$PATH", "Admin/ChurchHallBook/warning/' class='w3-bar-item w3-button w3-hover-purple w3-right mr-3 px-2 beat'> <i class='fa fa-warning w3-large' title='Church Hall Booking Payment Pending'> </i> </a>";
?>
	</div>
</header>
<? require "MainMenuItems.php"; require "MainMenu.php"; ?>
<br> <br>
<main class="w3-main p-1 px-md-3 w3-animate-<?=($_SESSION["prevMenuDepth"]??0) <= ($MMI["depth"][$_GET["page"]]??0) ? "right" : "left";?>" style="min-height:90vh;">
<? include (empty ($_GET["page"]) ? "Home" : $_GET["page"]).".php"; ?>
</main>
<div class="w3-bottom w3-row w3-large w3-center w3-topbar w3-border-pink w3-card-4 w3-hide-medium w3-hide-large w3-{{THEME ? 'white' : 'black'}}" style="border-radius: 20px 20px 0 0; z-index:5;">
<?
foreach ($MMI["qkLnk"] as $txt => $lnk) {
	echo "<div class='w3-col s3'>";
	echo 	"<a href='$PATH", "Admin/$lnk", "' style='text-decoration:none;'>";
	echo 		"<div class='", ($_GET["page"]??"") == $lnk ? "w3-circle w3-card-4 w3-crimson w3-animate-bottom qkLnk" : "w3-hover-text-pink", " mb-n2' style='font-size:", ($_GET["page"]??"") == $lnk ? 8 : 5, "vw;'>";
	echo 			"<i class='fa fa-", $MMI["icons"][$txt], "'> </i>";
	echo 		"</div>";
	echo 		($_GET["page"]??"") == $lnk ? "":"<b class='w3-tiny'> $txt </b>";
	echo 	"</a>";
	echo "</div>";
}
?>
</div>
<div id="ses" style="display:none;"></div>
<footer class="w3-black w3-wide w3-small w3-black w3-center" style="z-index:5;">
	<?=$APP_NAME;?> &nbsp; Shop Management System | Developed by <a target="_blank" href="https://sman333.github.io/" class="w3-black"> Suman </a>
	<p class="w3-black w3-hide-medium w3-hide-large" style="height:50px;"> </p>
</footer>
<script>
    const R_D = `<?=($_GET["page"]??0) ? "/".$_GET["page"]:"", ($_GET["catId"]??0) ? "/".$_GET["catId"]:"";?>`;
	// let THEME = !window.matchMedia (`(prefers-color-scheme: dark)`).matches;
	// window.matchMedia (`(prefers-color-scheme: dark)`).onchange = () => theme (!window.matchMedia (`(prefers-color-scheme: dark)`).matches);
	var ROOT;
	var usrLastActiveTime = new Date().getTime();
	$(document).ready (() => {
		document.onkeydown = e => { if (e.key == `Escape`) { $(`i.cancelEdit`).click(); window.close(); } };
		$(`form`).keydown (e => { if ([`Enter`, `NumpadEnter`].includes (e.key) && e.target.tagName != `TEXTAREA`) e.preventDefault(); });
		$(`[type='week'],[type='month']`).click (function() { this.showPicker(); });
		$(`[type='date']`).change (function() { this.style.color = this.value.length ? `${($(`body`).hasClass (`w3-dark`) ? `white` : `black`)}` : `transparent`; });
		$(`[name*='amount']`).attr (`min`, 0);
		ROOT = document.querySelector (`:root`);
		clock();
		$(`.changeTheme`).click (() => {
			$(`[type='date']`).change();
			var formData = new FormData();
			formData.append (`theme`, <?=$usr["theme"];?>);
			postFormData (`profile.php`, formData);
		});
	})
	.on (`mousemove keypress`, (() => {
		if (new Date().getTime() - usrLastActiveTime < 1000) return;
		let formData = new FormData();
		formData.append (`usrActive`, true);
		formData.append (`page`, `<?=$_GET["page"];?>`);
		$.ajax ({
			type: `POST`,
			url: `<?=$PATH;?>Admin/sesTout.php`,
			enctype: `multipart/form-data`,
			dataType: `text`,
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			success: resp => $(`#ses`).html (resp)
		});
		usrLastActiveTime = new Date().getTime();
	}));
	function clock() {
		// ROOT.style.setProperty (`--timeMin`, ((timeFormat (`%h`) % 12) * 60) + parseInt (timeFormat (`%m`)));
		ROOT.style.setProperty (`--timeSec`, (timeFormat (`%m`) * 60) + parseInt (timeFormat (`%s`)));
		$(`.clock>p`).html (timeFormat (`%d <br> <br> <b class="w3-xlarge w3-sans-serif w3-text-pink"> %h : %m : %s </b> <br> <br> %D %N <br> %Y`));
	}
	setInterval (clock, 1000);
	const S_B = $(`#statusBar`);
<?=($debug??0) ? "":"S_B.attr (`title`, `Click to close`).click (() => S_B.html (``));";?>
	const zeroPad = x => parseInt (x) < 10 ? `0${x}` : x;
	function timeFormat (format, t = new Date()) {
		const month = [`January`, `February`, `March`, `April`, `May`, `June`, `July`, `August`, `September`, `October`, `November`, `December`];
		const day = [`Sun`, `Mon`, `Tues`, `Wednes`, `Thurs`, `Fri`, `Satur`];
		format = format.replace (`%Y`, t.getFullYear());					//	year YYYY
		format = format.replace (`%M`, zeroPad (t.getMonth() + 1));			//	month MM
		format = format.replace (`%D`, t.getDate());						//	day DD
		format = format.replace (`%h`, zeroPad (t.getHours()));				//	hours hh
		format = format.replace (`%m`, zeroPad (t.getMinutes()));			//	minutes mm
		format = format.replace (`%s`, zeroPad (t.getSeconds()));			//	seconds ss
		format = format.replace (`%N`, month[t.getMonth()]);				//	month MMM
		format = format.replace (`%d`, `${day[t.getDay()]}day`);			//	day ddd
		return format;
	}
</script>
</body>
</html>
<? $_SESSION["prevMenuDepth"] = ($MMI["depth"][$_GET["page"]]??0); ?>

