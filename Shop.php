<div class="w3-center p-5">
    <h4> Most Popular Jewelry </h4>
    <p> Our most desired designs balance artistic vision with exemplary craftsmanship. </p>
</div>
<div class="p-md-2 px-lg-4"> <hr class="w3-light-gray my-0"> </div>

<style>
    .pdt>div {
        aspect-ratio: 0.618;
        transform: scale(1,1);
        transition: all 200ms;
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
</style>
<div class="px-md-3 px-lg-5">
    <p class="w3-right-align px-4 pt-2" style="max-width:1200px;"> Sort by 
        <select class="p-1" onchange="location=`<?=$PATH;?>Shop/<?=($_GET["cat"]??0) ? $_GET["cat"]."/":"";?>${this.value}`;">
<?
foreach ([
        "date_desc" => "Latest",
        "date_asc" => "Oldest",
        "price_desc" => "Price Highest",
        "price_asc" => "Price Lowest"
    ] as $lnk => $txt)
    echo "<option value='$lnk' ", ($_GET["sort"]??"") == $lnk ? "selected":"", "> $txt </option>";
?>
        </select>
    </p>

    <div class="w3-row-padding w3-content" style="max-width:1200px;">
<?
if ($_GET["cat"]??0) foreach ($FormFields["Pdt"]["select"]["catId"] as $id => $c) if (str_replace ("_", " ", $_GET["cat"]??"") == $c) $fltr = $id;
$sort = "";
if ($_GET["sort"]??0) {
    $sort = explode ("_", $_GET["sort"]);
    $sort = (stristr ($sort[0], "price") ? "price" : "pdt_id")." ".strtoupper ($sort[1]).",";
}
$sql = "SELECT * FROM Pdt WHERE status='$ACTIVE' ORDER BY $sort pdt_id DESC";
foreach (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[] as $pdt) { if ($fltr??0) if ($pdt["catId"] != $fltr) continue;
    echo "<div class='w3-col s6 m4 l3 py-2 px-1 p-md-4 pdt'>";
    echo    "<div class='w3-border w3-round-xlarge w3-hover-shadow'>";
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

<datalist id="pdtList">
<? foreach ($pt_nm as $i => $nm) echo "<option value='$nm'/>"; ?>
</datalist>

<script>
	$(document).ready (() => {
        $(`[type=search]`).on (`input change`, function() {
            if (!this.value) {
                $(`.pdt`).show();
                return;
            }
            let x = this.value.toLowerCase();
            $(`.pdt`).hide();
            $(`.pdtName`).each (function() {
                if ($(this).text().toLowerCase().indexOf (x) > -1) $(this).parent().parent().show();
            });
        });
    });
</script>

