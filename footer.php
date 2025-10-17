<footer>
	<div class="w3-padding-large w3-padding-64 w3-card-4 w3-text-black" style="background:linear-gradient(to bottom right, hsl(30, 14%, 90%), hsl(30, 14%, 65%));">
    	<div class="w3-row-padding w3-padding-large w3-text-black">
        	<div class="w3-third w3-padding-24 mt-n5">
        	    <img src="<?=$PATH;?>images/logo.jpeg" class="w3-image w3-round-xlarge" style="max-height:100px;"/>
        		<h5 class="pt-3"> <?=strtoupper ($regName);?> </h4>
        	</div>
        	<div class="w3-third w3-padding-24">
        		<h5 class="pb-3" style="text-underline-offset:5px;"> <u> SUPPORT </u> </h5>
<?
foreach ([
	"Cancellation_and_Refund",
	"Disclaimer_Policy",
	"Privacy_Policy",
	"Shipping_and_Delivery",
	"Terms_and_Conditions",
] as $lnk) echo "<a href='$PATH$lnk'>", str_replace ("_",  " ", $lnk), "</a> <br>";
?>			
        	</div>
        	<div class="w3-third w3-padding-24">
        		<h5 class="pb-3" style="text-underline-offset:5px;"> <u> CONTACT </u> </h5>
        		<p> <a href="tel:+91<?=str_replace (" ", "", $mob);?>" class="fa fa-phone"> +91 <?=$mob;?> </a> <br>
        			<a href="https://wa.me/91<?=str_replace (" ", "", $mob);?>" class="fa fa-whatsapp"> +91 <?=$mob;?> </a> <br>
        			<a href="mailto:<?=$emWeb;?>" class="fa fa-envelope"> <?=$emWeb;?> </a> <br>
        			<a href="mailto:<?=$emGm;?>" class="fa fa-envelope"> <?=$emGm;?> </a> <br>
        			<?=$addr;?>
        		</p>
        	</div>
    	</div>
	</div>
    <div class="w3-black w3-center w3-padding w3-small">
		All Rights Reserved
		<span class="w3-large w3-text-gray px-4"> | </span>
		Copyright © 2025 - <?=date("Y");?> <?=strtoupper ($regName);?>
		<span class="w3-large w3-text-gray px-4"> | </span>
		<a target="_blank" href="https://sman333.github.io/" class="w3-normal w3-text-white"> Developed by Suman </a>
	</div>
</footer>

