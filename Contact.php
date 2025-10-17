<? require "info.php"; ?>
<div class="w3-padding-64 w3-padding-large w3-center">
	<h1 class="w3-center pb-5"> Get in touch </h1>
	<a class="w3-block w3-text-black" href="mailto:<?=$emWeb;?>"> <i class="w3-xlarge w3-light-gray fa fa-envelope p-3 circ"> </i> <br> <br> <?=$emWeb;?> </a>
	<br>
	<br>
	<br>
	<p class="mb-0"> <i class="w3-xlarge w3-light-gray fa fa-map-marker py-2 px-3 circ"> </i> <br> <br> <?=$addr;?> </p>
	<br>
	<br>
	<br>
	<a class="w3-block w3-text-black" href="tel:+91<?=str_replace (" ", "", $mob);?>"> <i class="w3-xlarge w3-light-gray fa fa-phone pb-2 pt-3 px-3 circ"> </i> <br> <br> +91 <?=$mob;?> </a>
</div>

