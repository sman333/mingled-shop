<?
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "Admin/sysInfo.php";
    array_walk_recursive ($_POST, "validate");
    if ($_POST["cart"]??0) $_SESSION["CART"] = htmlspecialchars_decode ($_POST["cart"]??"");
    if (isset ($_POST["tot"])) $_SESSION["tot"] = (int) $_POST["tot"];
}else {
?>
<div class="py-5 px-md-5">
    <div class="w3-row">
        <div class="w3-twothird px-2 pr-md-5 py-md-5">
            <div class="pr-md-5">
                <a href="<?=$PATH;?>Shop" class="w3-text-black p-2 py-md-4"> <i class="fa fa-chevron-left w3-small"> </i> Continue Shopping </a>
                <h3 class="w3-serif w3-border-bottom w3-border-gray py-4 m-0"> Shopping Cart </h3>
                <div class="cartItms">
<?
    $_SESSION["tot"] = $_SESSION["itmCnt"] = 0;
    foreach (json_decode ($_SESSION["CART"]??"[]", true)??[] as $itm) {
        $_SESSION["tot"] += $pt_pr[$itm["id"]] * $itm["qty"];
        $_SESSION["itmCnt"] += $itm["qty"];
		echo    "<div class='w3-row w3-border-bottom w3-border-gray pt-3 pb-4'>";
        echo        "<a href='$PATH", "Product/", $itm["id"], "' class='w3-col s5 m4 l3'>";
        echo            "<img src='images/products/", $pt_ct[$itm["id"]], "_", $itm["id"], ".jpeg' class='w3-image w3-round-large'/>";
        echo        "</a>";
        echo        "<div class='w3-col s7 m8 l9 px-3'>";
        echo            "<span class='w3-right w3-large w3-text-khaki btn pr-0 mt-n2' onclick='removeAll(", $itm["id"], ");$(this).parent().parent().slideUp();'> &times; </span>";
        echo            "<p>";
        echo                "<b class='w3-small w3-text-gray'>", $FormFields["Pdt"]["select"]["catId"][$pt_ct[$itm["id"]]], "</b> <br>";
        echo                "<span class='w3-bold'>", $pt_nm[$itm["id"]], "</span>";
        echo            "</p>";
        echo            "<p class='w3-row'>";
        echo                "<i class='w3-col s12 w3-small w3-text-gray'> Qty </i> <br>";
        echo                "<i class='w3-col s3 w3-button fa fa-minus qtyDec'> </i>";
        echo                "<input type='number' class='w3-col s6 w3-large w3-center w3-border w3-round qty' style='max-width:200px;' min='1' step='1' name='", $itm["id"], "' value='", $itm["qty"], "'/>";
        echo                "<i class='w3-col s3 w3-button fa fa-plus qtyInc'> </i>";
        echo            "</p>";
        echo            "<p class='w3-right-align itmTot'> Rs. ", $pt_pr[$itm["id"]] * $itm["qty"], "</p>";
        echo            "<p class='w3-bold w3-small w3-hover-text-pink btn m-0' onclick='move(", $itm["id"], ",`wish`);$(this).parent().parent().slideUp();'>";
        echo                "<i class='fa fa-heart-o beat'> </i> Save for Later";
        echo            "</p>";
        echo        "</div>";
		echo    "</div>";
    }
?>
                </div>
            </div>
        </div>
        <div class="w3-third px-md-2 py-md-5 sticky-top" style="z-index:2;">
            <br class="w3-hide-small">
            <div class="w3-light-gray p-3">
                <div class="p-3">
                    <b> Order Summary </b>
                    <br>
                    <br>
                    <b class="w3-small"> Subtotal <span class="w3-right"> Rs. <span class="cartTot"><?=$_SESSION["tot"]??0;?></span> </span> </b>
                    <br>
                    <br>
                    <b class="w3-block w3-border-top w3-border-gray py-3"> Total <span class="w3-right"> Rs. <span class="cartTot"><?=$_SESSION["tot"]??0;?></span> </span> </b>
                    <br>
                    <br>
<?="<a href='$PATH", (($_SESSION["tot"]??0) && ($_SESSION["itmCnt"]??0)) ? (($_SESSION["signedIn"]??0) ? "Delivery" : "SignIn"):"", "' class='w3-button w3-block w3-round w3-padding-16 w3-black chkout'> <b> Checkout </b> </a>";?>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
	$(document).ready (() => {
        $(`input.qty`).change (function() {
            CART.forEach (itm => { if (itm.id == this.name) itm.qty = parseInt (this.value); });
            listItms();
            $(this).parent().next().text (`Rs. ${ITEMS[this.name].price * this.value}`);
        });
        $(`.qtyDec`).click (function() {
            let x = $(this).next();
            if (x[0].value < 2) return;
            x[0].value--;
            x.change();
        });
        $(`.qtyInc`).click (function() {
            let x = $(this).prev();
            x[0].value++;
            x.change();
        });
    });
</script>
<?
}
?>

