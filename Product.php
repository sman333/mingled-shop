<?
if (!($pdt["pdt_id"]??0)) exit ("<h1> No product selected. </h1>");
?>
<style>
    .imgZoom {
        background-repeat: no-repeat;
        cursor: crosshair;
    }
    .imgZoom img:hover { opacity: 0; }
</style>
<div class="py-3 px-md-5">
    <div class="px-3 py-md-3">
        <a href="<?=$PATH;?>Shop" class="w3-text-black p-2 py-md-4"> <i class="fa fa-chevron-left w3-small"> </i> Back to Shop </a>

        <div class="w3-row">
            <div class="w3-twothird p-2 p-md-3 p-lg-5">
                <div class="w3-row">
                    <div class="w3-col m2">
                        <div class="w3-row">
                            <img src="<?=$PATH, "images/products/", $pt_ct[$pdt["pdt_id"]], "_", $pdt["pdt_id"];?>.jpeg" class="w3-image w3-col s2 m10 w3-border w3-border-black p-2 tabs tab0" onclick="preview(0)"/>
<?
$imgCnt = 4;
for ($i = 1; $i <= $imgCnt; $i++) if (glob ("images/products/".$pt_ct[$pdt["pdt_id"]]."_".$pdt["pdt_id"]."_$i.jpeg"))
        echo "<img src='$PATH", "images/products/", $pt_ct[$pdt["pdt_id"]], "_", $pdt["pdt_id"], "_$i.jpeg' class='w3-image w3-col s2 m10 w3-border-black p-2 tabs tab$i' onclick='preview($i)'/>";
?>
                        </div>
                    </div>
                    
                    <div class="w3-col m10" style="overflow:hidden;">
                        <p class="imgZoom img0" style="background-image:url(<?=$PATH, "images/products/", $pt_ct[$pdt["pdt_id"]], "_", $pdt["pdt_id"];?>.jpeg);" onmousemove="zoom(event)">
                            <img src="<?=$PATH, "images/products/", $pt_ct[$pdt["pdt_id"]], "_", $pdt["pdt_id"];?>.jpeg" class="w3-image"/>
                        </p>
<?
for ($i = 1; $i <= $imgCnt; $i++) {
    if (!glob ("images/products/".$pt_ct[$pdt["pdt_id"]]."_".$pdt["pdt_id"]."_$i.jpeg")) continue;
    echo "<p class='w3-animate-right imgZoom img$i' style='background-image:url($PATH", "images/products/", $pt_ct[$pdt["pdt_id"]], "_", $pdt["pdt_id"], "_$i.jpeg); display:none;' onmousemove='zoom(event)'>";
    echo    "<img src='$PATH", "images/products/", $pt_ct[$pdt["pdt_id"]], "_", $pdt["pdt_id"], "_$i.jpeg' class='w3-image'/>";
    echo "</p>";
}
?>
                    </div>
                </div>
            </div>

            <div class="w3-third py-4 p-2 p-md-3 p-lg-5">
                <p class="mb-0"> <?=$FormFields["Pdt"]["select"]["catId"][$pdt["catId"]]??"";?> </p>
                <h2 class="w3-serif w3-border-bottom w3-border-dark-gray pb-4"> <?=$pdt["pdtName"], " ", $pdt["colour"];?> </h2>
<?
if ($clr[$pdt["pdt_id"]]??0) echo "<p class='w3-small pl-2 mb-0'> Colours </p>";
foreach ($clr[$pdt["pdt_id"]]??[] as $c => $p) {
    echo "<div class='p-2'>";
    echo    "<a href='$PATH", "Product/$p' class='w3-button w3-block w3-border w3-border-black w3-round-large p-0'>";
    echo        "<img src='$PATH", "images/products/", $pt_ct[$p], "_$p.jpeg' class='w3-col w3-image' style='height:50px;width:50px;'/>";
    echo        "<p class='w3-rest w3-left-align pl-2 pt-3 mb-0'> $c </p>";
    echo    "</a>";
    echo "</div>";
}
?>
                <br>
                <i class="w3-right w3-xlarge w3-button w3-round-large w3-text-dark-gray fa fa-share-alt" onclick="shrLnk()"> </i>
<?
$wsh = "<p class='w3-bold w3-hover-text-pink btn m-0' onclick='move(".$pdt["pdt_id"].",`wish`);location.reload();'> <i class='fa fa-heart-o beat'> </i> Save for Later </p>";
foreach (json_decode ($_SESSION["WISH"]??"[]", true)??[] as $itm)
    if (($pdt["pdt_id"]??0) == $itm["id"]) {
        $wsh = "<p class='w3-bold btn m-0' onclick='remove(".$pdt["pdt_id"].",`wish`);location.reload();'> &times; Remove from Saved List </p>";
        break;
    }
echo $wsh, "<br><br>";
foreach (json_decode ($_SESSION["CART"]??"[]", true)??[] as $itm)
    if (($pdt["pdt_id"]??0) == $itm["id"]) {
        echo "<p> added to cart </p>";
        break;
    }
echo "<b class='w3-button w3-block w3-left-align w3-round w3-padding-16 w3-black p-3' onclick='add(", $pdt["pdt_id"], ",`cart`)'>";
echo    "Rs.", $pt_pr[$pdt["pdt_id"]], "<p class='w3-right m-0'> <i class='fa fa-shopping-bag'> </i> Add to Cart </p> </b>";
echo "</b>";
?>
            </div>
        </div>



        <style>
            .pdt>div {
                aspect-ratio: 0.618;
                transform: scale(1,1);
                transition: all 200ms;
                /*width: 200px;*/
            }
            .pdt>div:hover { transform: scale(1.03,1.03); }
            .pdtImg {
                aspect-ratio: 1;
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
                border-radius: 14px 14px 0 0;
            }
            .pdtName {
                -webkit-box-orient: vertical;      
                -webkit-line-clamp: 2;
                display: -webkit-box;
                max-height: 3.5em;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .addBtn { border-radius: 0 0 14px 14px; }

            @media only screen and (max-width: 500px) {
                .d-flex { width: 280vw; }
                .pdt>div { width: 38vw; }
            }
            @media only screen and (min-width: 500px) {
                .d-flex { width: 260vw; }
                .pdt>div { width: 35vw; }
            }
            @media only screen and (min-width: 600px) {
                .d-flex { width: 220vw; }
                .pdt>div { width: 30vw; }
            }
            @media only screen and (min-width: 768px) {
                .d-flex { width: 180vw; }
                .pdt>div { width: 25vw; }
            }
            @media only screen and (min-width: 992px) {
                .d-flex { width: 160vw; }
                .pdt>div { width: 22vw; }
            }
            @media only screen and (min-width: 1200px) {
                .d-flex { width: 120vw; }
                .pdt>div { width: 16vw; }
            }
        </style>
        <br>
        <h5 class="w3-serif pt-5"> You May Also Like </h5>
        <div class="noScrollBar" style="max-width:100vw; overflow-x:auto;">
            <div class="d-flex">
<?
$sql = "SELECT * FROM Pdt WHERE status='ACTIVE' ORDER BY RAND() LIMIT 7";
foreach (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[] as $pdt) {
    echo "<div class='flex-fill p-2 p-md-4 pdt'>";
    echo    "<div class='w3-border w3-round-xlarge w3-hover-shadow w3-small'>";
    echo        "<a href='$PATH", "Product/", $pdt["pdt_id"], "'>";
    echo            "<p class='w3-light-gray mb-0 pdtImg' style='background-image:url($PATH", "images/products/", $pdt["catId"], "_", $pdt["pdt_id"], ".jpeg);'> </p>";
    echo        "</a>";
    echo        "<p class='p-2 mb-0 pdtName'>", $pdt["pdtName"], " ", $pdt["colour"], "</p>";
    echo        "<p class='w3-row px-4 mb-0'>";
    echo ($clr[$pdt["pdt_id"]]??0) ? "<img src='$PATH"."images/clr_wheel.png' class='w3-col s2 w3-image pr-1 pr-md-2 pr-xl-3'/>":"<b class='w3-col s2 mb-0'> <br> </b>";
    echo            "<span class='w3-col s10 w3-right-align'>";
    echo                "<i class='w3-text-red w3-hover-red fa fa-minus-circle btn py-1 remBtn remItm", $pdt["pdt_id"], "' onclick='remove(", $pdt["pdt_id"], ")'> </i> <br>";
    echo            "</span>";
    // echo            "<span class='w3-col s8 w3-right-align w3-text-gray py-1'> <small> Rs. </small>", $pdt["price"], "</span>";
    echo        "</p>";
    echo        "<p class='w3-display-bottommiddle w3-button w3-block w3-hover-black badge-pill py-1 mb-0 addBtn' onclick='add(", $pdt["pdt_id"], ",`cart`)'> <i class='fa fa-shopping-bag'> </i> <small> Rs. </small>", $pdt["price"], "</p>";
    echo    "</div>";
    echo "</div>";
}
?>
            </div>
        </div>
	</div>
</div>



<br> <br>
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
<div class="w3-center py-5 px-2 px-md-5">
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
    echo    "<p style='background-image:url($PATH", "images/category/$id.jpeg);' class='cat'> </p>";
    echo    $c, 20 < strlen ($c) ? "":"<br>", "<br>";
    echo "</a>";
    if ($i%2) echo "</div> </div>";
    $i++;
}
if ($i%2) echo "</div> </div>";
?>
    </div>
</div>



<script>
    async function shrLnk() {
        try { await navigator.share ({ text: document.title, url: location.href });
        } catch (err) { console.log (`Error: ${err}`); }	        
    }
    function zoom (e) {
        e.preventDefault();
        var zoomer = e.currentTarget;
        e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX;
        e.offsetY ? offsetY = e.offsetY : offsetY = e.touches[0].pageY;
        x = offsetX/zoomer.offsetWidth * 100;
        y = offsetY/zoomer.offsetHeight * 100;
        zoomer.style.backgroundPosition = x + '% ' + y + '%';
    }
    const TABS = $(`.tabs`);
    let curPrvw = 0;
    const IMGS = $(`.imgZoom`);
    function preview (i) {
        if (TABS.length < 2) return;
        TABS.removeClass (`w3-border`);
        $(`.tab${i}`).addClass (`w3-border`);
        IMGS.hide().removeClass (`w3-animate-left w3-animate-right`);
        $(`.img${i}`).addClass (`w3-animate-${i < curPrvw ? `left` : `right`}`).show();
        curPrvw = i;
    }
    let touchstartX = 0
    let touchendX = 0
    IMGS.on (`touchstart`, e => touchstartX = e.changedTouches[0].screenX);
    IMGS.on (`touchend`, e => {
        touchendX = e.changedTouches[0].screenX;
        swipe();
    });
    function swipe() {
        if (Math.abs (touchstartX - touchendX) < 100) return;
        if (touchendX < touchstartX) preview (curPrvw < (TABS.length - 1) ? (curPrvw + 1) : 0);     // swiped left
        if (touchendX > touchstartX) preview (0 < curPrvw ? (curPrvw - 1) : (TABS.length - 1));     // swiped right
    }
</script>

