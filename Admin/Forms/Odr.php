<div id="odr_<?=$odr["odr_id"];?>" class="w3-card w3-<?=$usr["theme"] ? "white" : "black";?> w3-round-xlarge w3-border w3-topbar w3-border-<?=$x[0];?> mx-1">
    <p class="m-0">
<?
if ($odr["status"] == $PAID) echo "<b class='w3-btn w3-blue badge-pill px-3 py-1 ml-1 ml-md-3 shippedOdr' onclick='shippedOdr(", $odr["odr_id"], ")'> <i class='fa fa-truck'> </i> Shipped </b>";
if ($odr["status"] == $SHIPPED) echo "<b class='w3-btn w3-green badge-pill px-3 py-1 ml-1 ml-md-3 completedOdr' onclick='deliveredOdr(", $odr["odr_id"], ")'> <i class='fa fa-check'> </i> Delivered </b>";
?>
        <i class="w3-left w3- w3-<?=$x[0];?> px-2 ml-3" title="created on"> <?=date_create ($odr["createAt"])->format ("d M Y H:i:s");?> </i>
<?
if ($odr["status"] == $PAID) echo "<i class='w3-left w3- w3-", $x[0], " px-2 ml-3' title='paid on'> Paid: ", date_create ($odr["modifyAt"])->format ("d M Y H:i:s"), " </i>";
?>
        <i class="w3-right w3-text-red w3-xlarge fa fa-trash btn py-0 my-1" onclick="deleteOdr(<?=$odr["odr_id"];?>)" title="Delete Order"> </i>
    </p>
    <div class="w3-row mx-4 pb-2">
        <div class="w3-third">
<?
foreach (["cusName", "mob", "mob2", "email"] as $name) echo "<p class='m-0 px-1' title='", $fields[$name]["label"], "'>", $odr[$name]??"", "<br> </p>";
?>
        </div>
        <div class="w3-third">
<?
foreach (["street1", "street2", "landmark", "city_pin", "state"] as $name) echo "<p class='m-0 px-1' title='", $fields[$name]["label"], "'>", $odr[$name]??"", "<br> </p>";
?>
        </div>
        <div class="w3-third w3-right-align">
<?
$odr["discount"] == 0 ? $odr["discount"] = "":"";
foreach (["discount", "remarks", "pg", "amount"] as $name) echo "<p class='m-0 px-1' title='", $fields[$name]["label"], "'>", $odr[$name]??"", "<br> </p>";
?>
        </div>
    </div>
    <div class="w3-row w3-border-top w3-border-brown px-3 py-2">
<?
foreach (json_decode (htmlspecialchars_decode ($odr["itmsJson"]??"")??"[]", true)??[] as $itm) {
    echo "<div class='w3-col m6 l3 pb-1'>";
    echo    "<div class='w3-row-padding'>";
    echo        "<i class='w3-col s4'> <img src='../images/products/", $pdts[$itm["id"]]["catId"], "_", $itm["id"], ".jpeg' class='w3-image w3-round-large'/> </i>";
    echo        "<div class='w3-col s8'>";
    echo            "<p class='m-0'> <b class='w3-small w3-text-gray'>", $cats[$pdts[$itm["id"]]["catId"]]["catName"], "</b> <br>", $pdts[$itm["id"]]["pdtName"], " (<i>Rs.", $pdts[$itm["id"]]["price"], "</i>) </p>";
    echo            " x <b class='w3-large'> ", $itm["qty"], "</b>";
    echo        "</div>";
    echo    "</div>";
    echo "</div>";
}
?>
    </div>
</div>

