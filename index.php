<? if (!in_array ($_GET["page"]??"", ["Cancellation_and_Refund", "Contact", "Disclaimer_Policy", "Privacy_Policy", "Shipping_and_Delivery", "Terms_and_Conditions",
        "Account", "Cart", "Checkout", "Delivery", "Product", "Shop", "SignIn", "Wishlist"])) $_GET["page"] = "Home";
require "info.php"; require "Admin/ConfigDb.php"; require "Admin/FormFields.php";
if ($_GET["pdt"]??0) {
    $sql = "SELECT * FROM Pdt WHERE status='$ACTIVE' AND pdt_id='".($_GET["pdt"]??"")."'";
    $pdt = mysqli_fetch_assoc (mysqli_query ($conn, $sql))??[];
}
if (in_array ($_GET["page"], ["Product", "Shop"])) {
    $clr = [];
    $clrCnt = 15;
    $sql = "SELECT * FROM Clr";
    foreach (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[] as $c)
        for ($i = 1; $i <= $clrCnt; $i++) {
            if ((!($c["p$i"]??0)) || !($pt_ct[$c["p$i"]]??0)) continue;
            $clr[$c["p$i"]] = [];
            for ($j = 1; $j <= $clrCnt; $j++) if ($i != $j && strlen ($c["c$j"]??"") && ($c["p$j"]??0) && ($pt_ct[$c["p$j"]]??0)) $clr[$c["p$i"]][$c["c$j"]] = $c["p$j"];
            ksort ($clr[$c["p$i"]]);
        }
}
?>
<!DOCTYPE html>
<html lang="en" style="scroll-behavior:smooth;">
<title> <?=$_GET["page"] == "Product" ? $pdt["pdtName"]??"" : "$regName ".$_GET["page"];?> </title>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta property="og:description" content="<?=$_GET["page"] == "Product" ? $FormFields["Pdt"]["select"]["catId"][$pdt["catId"]??0]??"" : $regDes;?>"/>
<meta property="og:image" content="https://<?=$_SERVER["HTTP_HOST"], $_GET["page"] == "Product" ? "/images/products/".($pdt["catId"]??1)."_".($pdt["pdt_id"]??1).".jpeg" : "/favicon.png";?>"/>
<meta property="og:site_name" content="<?=$regName;?>"/>
<meta property="og:title" content="<?=$_GET["page"] == "Product" ? $pdt["pdtName"]??"" : $regName;?>"/>
<meta property="og:url" content="<?=$_SERVER["SCRIPT_URI"];?>"/>
<link rel="icon" href="<?=$PATH, $_GET["page"] == "Product" ? "images/products/".($pdt["catId"]??1)."_".($pdt["pdt_id"]??1).".jpeg" : "favicon.png";?>" type="image/*"/>
<!-- <link rel="stylesheet" href="lib/w3.min.css"/>
<link rel="stylesheet" href="lib/bootstrap.min.css"/>
<link rel="stylesheet" href="lib/font-awesome-4.7.0/css/font-awesome.min.css"/>
<script src="lib/jquery-3.6.4.min.js"></script> -->
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
<link rel="stylesheet" href="https://db.onlinewebfonts.com/c/89b6f2d1098ac5bb207a808fb051ed53?family=Santral-Regular"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<style>
@font-face {
    font-family: "Santral";
    src: url("<?=$PATH;?>lib/santral_light.otf");
}
    body * {
        font-family: "Santral-Regular", "Santral", sans-serif;
        letter-spacing: 0.5px;
    }
    .noUl, .noUl:hover, .noUl a { text-decoration-line: none !important; }
    .noScrollBar::-webkit-scrollbar { display: none; }
    .noScrollBar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    footer a { color: black !important; }
    .themeBg {
        background: hsl(30, 14%, 67%);
        color: white !important;
    }
    .themeBorder { border: 2px solid hsl(30, 14%, 67%); }
	.circ {
		aspect-ratio: 1 !important;
		border-radius: 50% !important;
	}
	.w3-beige {
		background-color: beige !important;
		color: black !important;
	}
	input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button {
		-webkit-appearance: none;
    	-moz-appearance: none;
		appearance: none;
		display: none;
		margin: 0;
	}
	.thin {
        -webkit-text-fill-color: black;
        -webkit-text-stroke: 0.5px;
    }
	.bold {
        -webkit-text-fill-color: black;
        -webkit-text-stroke: 0.2px;
    }
	.bold:hover {
        -webkit-text-fill-color: inherit;
        -webkit-text-stroke: 0.2px;
    }
	.bolder {
        -webkit-text-fill-color: black;
        -webkit-text-stroke: 0.5px;
    }
	.bolder:hover {
        -webkit-text-fill-color: black;
        -webkit-text-stroke: 0.5px;
    }
	.w3-bold { font-weight: 800; }
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
	nav {
	    /*border-top: 4px solid;*/
	    /*border-bottom: 2px solid;*/
	}
    .cart,.wish {
        position: fixed;
        right: 0;
        /* top: 70px; */
        top: 32px;
    }
@media only screen and (max-width: 700px) {
    .cart,.wish { width: 60%; }
}
@media only screen and (min-width: 700px) {
    .cart,.wish { width: 300px; }
}
    /*.beat:hover { animation: beat 1s linear infinite; }*/
	@keyframes beat {
		0% { transform: scale(1,1); }
		10% { transform: scale(1.5,1.5); }
		20% { transform: scale(1,1); }
		30% { transform: scale(1.5,1.5); }
        60% { transform: scale(1,1); }
		100% { transform: scale(1,1); }
	}
</style>
<body>
<header class="sticky-top" style="z-index:5;">
    <nav class="w3-bar w3-card w3-center themeBg py-2">
        <span class="w3-bar-item w3-left btn pb-0 pt-2 menuBtn" onclick="menu(true)">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 50 50">
                <path d="M 0 9 L 0 11 L 50 11 L 50 9 Z M 0 24 L 0 26 L 50 26 L 50 24 Z M 0 39 L 0 41 L 50 41 L 50 39 Z"></path>
            </svg>
        </span>
        <a href="<?=$PATH;?>" class="w3-large w3-serif w3-text-white pb-0 pl-5 pt-1 noUl" style="display:inline-block;"> <?=strtoupper ($regName);?> </a>
        <a href="<?=$PATH;?>Cart" class="w3-bar-item w3-right w3-button w3-text-black w3-hover-none pb-0 pr-3 pt-1 cartBtn" title="Cart">
            <svg height="20" width="20" viewBox="0 0 50 50" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M8,14L4,49h42l-4-35H8z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"/><rect fill="none" height="50" width="50"/><path d="M34,19c0-1.241,0-6.759,0-8  c0-4.971-4.029-9-9-9s-9,4.029-9,9c0,1.241,0,6.759,0,8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"/><circle cx="34" cy="19" r="2"/><circle cx="16" cy="19" r="2"/></svg>
            <span class="w3-red badge rounded-circle mt-3 ml-n2" style="display:none; font-size:0.5em; position:fixed;"> 0 </span>
        </a>
        <a href="<?=$PATH;?>Wishlist" class="w3-bar-item w3-right w3-button w3-hover-none pb-0 pr-1 pt-2 wishBtn" title="Wishlist">
            <i class="fa fa-heart-o beat bolder" style="font-size:20px; color:hsl(30,14%,67%);"> </i>
            <span class="w3-red badge rounded-circle mt-2 ml-n2" style="display:none; font-size:0.5em; position:fixed;"> 0 </span>
        </a>
    </nav>
    <p class="collapse show mb-0 searchBox">
        <input type="search" class="w3-input py-2 pl-3" placeholder="Search" list="srchList"/>
        <datalist id="srchList">
        <? foreach ($pt_nm as $i => $p) echo "<option value='$p'/>"; ?>
        </datalist>        
    </p> 
</header>
<div class="w3-card-4 w3-bottombar w3-hide-small w3-hide-medium themeBorder w3-white w3-border w3-round-xlarge sticky-top collapse pt-3 wish">
    <h6 class="w3-center"> <b> Wishlist </b> </h6>
    <ol class="w3-margin-right wishList"> <i class="w3-text-gray w3-center"> -- Wish List Empty -- </i> </ol>
</div>
<div class="w3-card-4 w3-bottombar w3-hide-small w3-hide-medium themeBorder w3-white w3-border w3-round-xlarge sticky-top collapse pt-3 cart">
    <h6 class="w3-center"> <b> Cart </b> </h6>
    <ol class="w3-margin-right cartList"> <i class="w3-text-gray w3-center"> -- Cart Empty -- </i> </ol>
    <p class="w3-right w3-margin-right"> <b> <i> Total: <span class="cartTot"> </i> </b> </p>
    <a href="<?=$PATH;?>Cart" class="w3-button w3-block w3-text-green w3-hover-green chkout" style="border-radius:0 0 10px 10px; display:none;"> <b> Checkout <i class="fa fa-arrow-right"> </i> </b> </a>
</div>

<div class="w3-overlay" onclick="menu(false)" style="cursor:pointer;z-index:3;"> </div>    
<div class="w3-sidebar w3-bar-block w3-card themeBorder w3-animate-left" style="display:none;height:150vh;width:min(300px,80%);z-index:4;">
    <br> <br>
    <!-- <span class="w3-bar-item btn py-2" onclick="$(`.searchBox`).slideToggle().children().focus();menu(false);"> <i class="fa fa-search"> </i> Search </span> -->
<?
foreach ([
        ""              => "Home",
        "#Category"     => "Categories",
    ] as $lnk => $txt) echo "<a href='$PATH$lnk' class='w3-bar-item w3-button py-3' onclick='menu(false)'> - $txt </a>";
echo "<hr class='w3-gray'>";
foreach ([
        "Contact"   => ["phone",    "Contact us"],
        ($_SESSION["signedIn"]??0) ? "Account" : "LogIn"   => ["user",     "My Account"],
    ] as $lnk => $x) { $icn = $x[0]; $txt = $x[1];
        echo "<a href='$PATH$lnk' class='w3-bar-item w3-text-dark-gray w3-button py-3'> <i class='fa fa-$icn'> </i> $txt </a>";
    }
?>
</div>

<main class="w3-main">
<!--<br>-->
<? require $_GET["page"].".php"; ?>
<div class="<?=$debug ? "":"w3-hide d-none collapse";?> statusBar" <?=$debug ? "":"style='display:none;'";?>> </div>
</main>
<? require "footer.php"; ?>
<script>
    const O_L = $(`.w3-overlay`);
    const S_M = $(`.w3-sidebar`);
    const M_B = $(`.menuBtn`);
    function menu (state) {
        if (state) {
            O_L.show();
            S_M.show();
            M_B.attr (`onclick`, `menu(false)`);
            return;
        }
        O_L.hide();
        S_M.hide();
        M_B.attr (`onclick`, `menu(true)`);
    }
    const bt = $(`.beat`);
    bt.mouseover (function() { $(this).css (`animation`, `beat 1s linear infinite`); }).mouseout (function() { $(this).css (`animation`, ``); });
    document.ontouchmove = e => { bt.mouseout(); }
// /* =============================================================================================================================================================== */
    const CART_BTN = $(`.cartBtn`);
    const CART_CNT = $(`.cartBtn .badge`);
    const CART_DIV = $(`.cart`);
    const CART_LIST = $(`.cartList`);
    const CART_ITMS = $(`.cartItms`);
    const CART_TOT = $(`.cartTot`);
    const CHKOUT_BTN = $(`.chkout`);
    const REM_ITM_BTN = $(`.remBtn`);
    const S_B = $(`.statusBar`);
    const VIEW_WISH_LIST_BTN = $(`.viewWishlistBtn`);
    const WISH_BTN = $(`.wishBtn`);
    const WISH_CNT = $(`.wishBtn .badge`);
    const WISH_DIV = $(`.wish`);
    const WISH_LIST = $(`.wishList`);
    const WISH_ITMS = $(`.wishItms`);
    const WISH_ICN = $(`.wishIcn`);
    let CART = <?=$_SESSION["CART"]??"[]";?>;
    let WISH = <?=$_SESSION["WISH"]??"[]";?>;
    let ITEMS = [];
    let cartOpen = false, wishOpen = false;
    let mouse_over_at = 0, mouse_over;
<?
$sql = "SELECT * FROM Pdt WHERE status='$ACTIVE'";
foreach (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[] as $p) echo "ITEMS[", $p["pdt_id"], "] = {name:`", $p["pdtName"], "`,price:", $p["price"], "};\n";
?>

	$(document).ready (() => {
        CART_BTN.mouseenter (() => {
            mouse_over_at = Date.now();
            clearInterval (mouse_over);
            mouse_over = setInterval (() => toggleDiv (CART_DIV), 100);
        });
        CART_BTN.mouseleave (() => {
            mouse_over_at = false;
            clearInterval (mouse_over);
        });
        WISH_BTN.mouseenter (() => {
            mouse_over_at = Date.now();
            clearInterval (mouse_over);
            mouse_over = setInterval (() => toggleDiv (WISH_DIV), 100);
        });
        WISH_BTN.mouseleave (() => {
            mouse_over_at = false;
            clearInterval (mouse_over);
        });
        listItms (`wish`);
        listItms();
    })
    .mousemove (() => {
        if (event.target != CART_BTN[0] && event.target != CART_DIV[0] && !CART_BTN.find (event.target).length && !CART_DIV.find (event.target).length) { CART_DIV.hide(); cartOpen = false; };
        if (event.target != WISH_BTN[0] && event.target != WISH_DIV[0] && !WISH_BTN.find (event.target).length && !WISH_DIV.find (event.target).length) { WISH_DIV.hide(); cartOpen = false; };
    });
    function toggleDiv (div = false) {
        if (cartOpen && div == CART_DIV && wishOpen && div == WISH_DIV) return;
        if (mouse_over_at)
            if (500 < Date.now() - mouse_over_at) {
                CART_DIV.hide().css (`z-index`, 0);
                WISH_DIV.hide().css (`z-index`, 0);
                cartOpen = wishOpen = false;
                div.slideDown().css (`z-index`, 10);
                if (div == CART_DIV) cartOpen = true;
                else wishOpen = true;
                mouse_over_at = false;
                clearInterval (mouse_over);
            }
    }
	function postFormData (url, formData) {
		$.ajax ({
			type: `POST`,
			url: `<?=$PATH;?>${url}`,
			enctype: `multipart/form-data`,
			dataType: `text`,
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: () => S_B.html (`<i class="w3-xlarge fa fa-cog w3-spin"></i>`),
			success: resp => {
				$(`i.w3-spin`).remove();
				S_B.append (resp);
<?=($debug??0) ? "":"setTimeout (() => S_B.html (``), 5000);";?>
			},
			error: () => S_B.html (`<p class="w3-yellow"> Server error, please try again. </p>`)
		});
	}
    function add (id, to = ``) {
        if (to == `wish`) {
            if (WISH.find (itm => itm.id == id) == undefined) WISH.push ({id: id,});
        }else {
            let done = false;
            CART.forEach (itm => {
                if (itm.id == id) {
                    itm.qty++;
                    done = true;
                }
            });
            if (!done) CART.push ({id: id, qty: 1});
        }
        listItms (to);
    }
    function calcTot() {
        let tot = 0, cnt = 0;
        CART.forEach ((itm, i) => { tot += itm.qty * ITEMS[itm.id].price; cnt += itm.qty; });
        CART_TOT.text (tot);
        CART_CNT.text (cnt);
        if (cnt) CART_CNT.show();
        else CART_CNT.hide();
    }
    function listItms (x = ``) {
        if (x == `wish`) {
            WISH_LIST.html (``);
            WISH_ICN.removeClass (`wished`);
            WISH_ICN.each (function() { $(this).attr (`onclick`, $(this).attr (`onclick`).replace (`remove`, `add`)); });
            if (WISH.length) {
                WISH.forEach ((itm, i) => {
                    WISH_LIST.append (`<li> ${ITEMS[itm.id].name} <span class="w3-right"> ${(ITEMS[itm.id].price)} </span> <i class="fa fa-minus-circle w3-text-red w3-hover-red btn p-0" onclick="remove(${itm.id},'wish')"> </i> </li>`);
                    $(`.wishPdt${itm.id}`).addClass (`wished`).attr (`onclick`, $(`.wishPdt${itm.id}`).attr (`onclick`)?.replace (`add`, `remove`));
                });
                VIEW_WISH_LIST_BTN.show();
            }else {
                WISH_LIST.html (`<i class="w3-text-gray w3-center"> -- Wishlist Empty -- </i>`);
                VIEW_WISH_LIST_BTN.hide();
                WISH_ITMS.html (`<p class='w3-col s12 w3-large w3-center w3-text-gray p-3'> Wish list is empty </p>`);
            }
            WISH_CNT.text (WISH.length);
            if (WISH.length) WISH_CNT.show();
            else WISH_CNT.hide();
        }else {
            CART_LIST.html (``);
            REM_ITM_BTN.hide();
            if (CART.length) {
                CART.forEach ((itm, i) => {
                    CART_LIST.append (`<li> ${ITEMS[itm.id].name} <i> (x${itm.qty}) </i> <span class="w3-right"> ${(itm.qty * ITEMS[itm.id].price)} </span> <i class="fa fa-minus-circle w3-text-red w3-hover-red btn p-0" onclick="remove(${itm.id})"> </i> </li>`);
                    $(`.remItm${itm.id}`).show();
                });
                CHKOUT_BTN.show();
            }else {
                CART_LIST.html (`<i class="w3-text-gray w3-center"> -- Cart Empty -- </i>`);
                CHKOUT_BTN.hide();
                CART_ITMS.html (`<p class='w3-large w3-center w3-text-gray p-3'> Cart is empty </p>`);
            }
            calcTot();
        }
        save (x);
    }
    function move (id, to = ``) {
        add (id, to);
        if (to == `wish`) removeAll (id);
        else remove (id, `wish`);
    }
    function remove (id, from = ``) {
        let del = -1;
        if (from == `wish`) {
            WISH.forEach ((itm, i) => {
                if (itm.id == id) WISH.splice (i, 1);
            });
        }else {
            CART.forEach ((itm, i) => {
                if (itm.id == id) {
                    itm.qty--;
                    if (!itm.qty) del = i;
                }
            });
            if (del != -1) CART.splice (del, 1);
        }
        listItms (from);
    }
    function removeAll (id) {
        CART.forEach ((itm, i) => {
            if (itm.id == id) CART.splice (i, 1);
        });
        listItms();
    }
    function save (x = ``) {
        let formData = new FormData();
        formData.append (x == `wish` ? `wish` : `cart`, JSON.stringify (x == `wish` ? WISH : CART));
        formData.append (`tot`, parseInt (CART_TOT[0].innerText));
        postFormData (`${x == `wish` ? `Wishlist` : `Cart`}.php`, formData);
    }
</script>
</body>
</html>

