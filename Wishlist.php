<?
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "Admin/sysInfo.php";
    array_walk_recursive ($_POST, "validate");
    if ($_POST["wish"]??0) $_SESSION["WISH"] = htmlspecialchars_decode ($_POST["wish"]??"");
}else {
?>
<style>
    .pdt>div { aspect-ratio: 0.618; }
    .pdtImg {
        aspect-ratio: 1;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        border-radius: 14px 14px 0 0;
    }
</style>
<div class="py-3 px-md-5">
    <div class="px-2 py-md-3">
        <a href="<?=$PATH;?>Shop" class="w3-text-black p-2 py-md-4"> <i class="fa fa-chevron-left w3-small"> </i> Back to Shop </a>
        <h3 class="w3-serif w3-border-bottom w3-border-gray py-4 m-0"> Wish List </h3>
        <div class="w3-row wishItms">
<?
    foreach (json_decode ($_SESSION["WISH"]??"[]", true)??[] as $itm) {
        echo "<div class='w3-col s6 m4 l3 py-3 px-2 py-md-3 pdt'>";
        echo    "<div class='w3-border w3-round-xlarge'>";
        echo        "<p class='w3-light-gray mb-0 pdtImg' style='background-image:url(", $PATH, "images/products/", $pt_ct[$itm["id"]], "_", $itm["id"], ".jpeg);'>";
        echo            "<span class='w3-right w3-xlarge btn mt-n1' id='", $itm["id"], "' onclick='remove(", $itm["id"], ",`wish`);$(this).parent().parent().parent().slideUp();'> &times; </span>";
        echo        "</p> <br>";
        echo        "<b class='w3-button w3-block w3-left-align w3-border w3-border-black w3-hover-white p-2 py-md-3 px-md-4' onclick='move(", $itm["id"], ");$(this).parent().parent().slideUp();'>";
        echo            "Rs.", $pt_pr[$itm["id"]], "<p class='w3-right m-0'> <i class='fa fa-shopping-bag'> </i> Add to Cart </p> </b>";
        echo        "</b>";
        echo        "<p class='p-2 py-4'>";
        echo            "<b class='w3-small w3-text-gray'>", $FormFields["Pdt"]["select"]["catId"][$pt_ct[$itm["id"]]], "</b>";
        echo            "<br>", $pt_nm[$itm["id"]];
        echo        "</p>";
        echo    "</div>";
        echo "</div>";
    }
?>
	    </div>
	</div>
</div>

<script>
    $(document).ready (() => {
        $(`.pdtImg`).click (function() {
            if (!event.target.id.length) location.href = `Product/${$(this).find (`span.btn`)[0].id}`;
        });
    });
</script>
<?
}
?>

