<style>
	.mainSlides  {
        background-position: bottom;
        background-repeat: no-repeat;
        background-size: cover;
	}
@media only screen and (max-width: 700px) {
    .mainSlides { height: 100vw; }
}
@media only screen and (min-width: 700px) {
    .mainSlides { height: 66.67vw; }
}
    .w3-left, .w3-right, .w3-badge { cursor: pointer; }
    .w3-badge { height:13px; width:13px; padding:0; }
</style>
<div class="w3-display-container w3-light-gray">
<?
$ssImgMaxCnt = 3;
for ($i = 0; $i < $ssImgMaxCnt; $i++) echo "<p class='w3-animate-right mainSlides' style='background-image:url(images/ss/", $i+1, ".jpeg?$hardRefresh);'> </p>";
?>
    <div class="w3-center w3-container w3-section w3-large w3-text-white w3-display-bottommiddle" style="width:100%">
        <div class="w3-left w3-hover-text-khaki" onclick="plusDivs(-1)"> &#10094; </div>
        <div class="w3-right w3-hover-text-khaki" onclick="plusDivs(1)"> &#10095; </div>
<? for ($i = 0; $i < $ssImgMaxCnt; $i++) echo "<span class='w3-badge w3-border w3-transparent w3-hover-white indicator' onclick='currentDiv(", $i+1, ")'> </span>"; ?>
    </div>
</div>
<script>
    var slideIndex = 1, caro;
    showSlide (slideIndex);
    function plusDivs (n) { showSlide (slideIndex += n); }
    function currentDiv (n) { showSlide (slideIndex = n); }
    function showSlide (n) {
        var i;
        var slides = $(`.mainSlides`);
        var dots = $(`.indicator`);
        if (n > slides.length) slideIndex = 1;
        if (n < 1) slideIndex = slides.length;
    	slides.hide();
    	dots.removeClass (`w3-white`);
        slides.eq (slideIndex-1).show();
        dots.eq (slideIndex-1).addClass (`w3-white`);
        window.clearTimeout (caro);
        caro = setTimeout (() => { plusDivs (1); }, 4000);
    }
</script>
<div class="w3-center pt-4 pb-5">
    <h5 class="w3-text-black pt-4 noUl"> <b> Hand Crafted Earings by Mingled </b> </h5>
	<a href="<?=$PATH;?>Shop" class="w3-button w3-white themeBorder px-5"> <h6 class="m-0 px-5"> Shop Now </h6> </a>
	<br> <br>
</div>



<style>
    .highlights {
        aspect-ratio: 1;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        transition: all 400ms;
    }
    /*.highlights:hover { transform: scale(1.1,1.1); }*/
@media only screen and (max-width: 500px) {
    .d-flex { width: 500vw; }
}
@media only screen and (min-width: 500px) {
    .d-flex { width: 450vw; }
}
@media only screen and (min-width: 600px) {
    .d-flex { width: 350vw; }
}
@media only screen and (min-width: 768px) {
    .d-flex { width: 325vw; }
}
@media only screen and (min-width: 992px) {
    .d-flex { width: 275vw; }
}
@media only screen and (min-width: 1200px) {
    .d-flex { width: 250vw; }
}
    .wishIcn {
        -webkit-text-fill-color: rgba(255,255,255,0.8);
        -webkit-text-stroke: 1px;
    }
    /*.wishIcn:hover {*/
    /*    -webkit-text-fill-color: deeppink;*/
    /*    -webkit-text-stroke: 2px;*/
    /*}*/
    .wished { -webkit-text-fill-color: deeppink !important; }
</style>
<div class="py-5 noScrollBar" style="max-width:100vw; overflow-x:auto;">
    <div class="d-flex">
<?
$sql = "SELECT * FROM Pdt WHERE status='ACTIVE' ORDER BY RAND() LIMIT 12";
foreach (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[] as $x) { $y = $x["pdt_id"]; $x = $x["catId"]."_$y";
    echo "<i class='flex-fill px-2'>";
    echo    "<p style='background-image:url(images/products/$x.jpeg);' class='w3-content w3-round-large w3-card highlights'>";
    echo        "<i class='w3-right w3-large fa fa-heart p-2 wishPdt$y wishIcn beat' id='$y' onclick='add($y,`wish`)'> </i>";
    echo    "</p>";
    echo "</i>";
}
?>
    </div>
</div>
<script>
    const hls = $(`.highlights`);
    const wi = $(`.wishIcn`);
    $(document).ready (() => {
        hls.click (function() { if (!event.target.id.length) location.href = `Product/${$(this).find (`.wishIcn`)[0].id}`; });
        hls.mouseover (function() { $(this).css (`transform`, `scale(1.1,1.1)`); }).mouseout (function() { $(this).css (`transform`, ``); });
        wi.mouseover (function() { $(this).css ({"-webkit-text-fill-color":"deeppink", "-webkit-text-stroke":"2px;"}); }).mouseout (function() { $(this).css ({"-webkit-text-fill-color":"rgba(255,255,255,0.8)", "-webkit-text-stroke":"1px;"}); });
        document.ontouchmove = e => { hls.mouseout(); wi.mouseout(); }
    });
</script>



<style>
    .cat {
        aspect-ratio: 1;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        transition: all 400ms;
    }
    .cat:hover { transform: scale(1.1,1.1); }
</style>
<div class="w3-center py-5 px-2 px-md-5" id="Category">
    <h4> <b> Shop by Category </b> </h4>
    <p class="w3-text-white pt-3 thin"> Mingled and designs celebrate the artistry of heritage with intricately crafted German silver, Afghani back-polished earrings, statement chokers, and more—bold, boho, and beautifully timeless, made to turn every glance into admiration. </p>
    <div class="w3-row py-4">
<?
$i = 0;
foreach ($FormFields["Pdt"]["select"]["catId"] as $id => $c) {
    $sql = "SELECT COUNT(*) AS pdtCnt FROM Pdt WHERE status='ACTIVE' AND catId=$id";
    $pdtCnt = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["pdtCnt"]??0;
    if (!$pdtCnt) continue;
    if (!($i%2)) echo "<div class='w3-col m6 l4'> <div class='w3-row'>";
    echo "<a href='$PATH", "Shop/", str_replace (" ", "_", $c), "' class='w3-col s6 w3-text-black p-2'>";
    echo    "<p style='background-image:url(images/category/$id.jpeg);' class='cat'> </p>";
    echo    $c, 20 < strlen ($c) ? "":"<br>", "<br>";
    echo "</a>";
    if ($i%2) echo "</div> </div>";
    $i++;
}
if ($i%2) echo "</div> </div>";
?>
    </div>
</div>



<div class="w3-center w3-light-gray px-4 py-5 p-md-5">
    <h4 class="w3-bold"> The <?=$regName;?> Experience </h4>
    <br> <br>
    <div class="w3-row-padding ">
<?
foreach ([
        "Secure Payment Options"        => ["lock",             "UPI, Card Payments & Net Banking available.",          "Learn More",   "Privacy_Policy"],
        "Fast Delivery"                 => ["plane",            "We deliver your order within 3-10 Days.",               "Contact Us",   "Contact"],
        "All India Shiping"             => ["truck",            "We ship to all locations in India.",                   "Learn More",   "Shipping_and_Delivery"],
        "$regName At Your Service"      => ["question-circle",  "Our client care experts are always here to help.",     "Contact Us",   "Contact"]
    ] as $ttl => $des) {
    echo "<div class='w3-quarter p-2 px-md-5'>";
    echo    "<i class='fa fa-", $des[0], " w3-xxlarge w3-text-gray py-3'> </i>";
    echo    "<h6 class='w3-bold'> $ttl <h6>";
    echo    "<p class='w3-text-dark-gray pt-2'>", $des[1], "</p>";
    echo    "<a href='", $des[3], "' class='w3-small w3-bold w3-text-black w3-hover-text-blue'>", $des[2], " &nbsp; <i class='fa fa-angle-right w3-medium w3-text-dark-gray'> </i> </a>";
    echo "</div>";
}
?>
    </div>
</div>

