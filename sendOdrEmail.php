<?
if ($odr["email"]??0) {
    $to = $odr["email"]??"";
	$sub = "Mingled - Payment received & Order confirmed.";

	$itms = "";
	foreach (json_decode ($odr["itmsJson"]??"[]", true)??[] as $itm)
	    $itms.= "<div style='padding:10px;'>
                    <a target='_blank' href='https://mingled.in/Product/".$itm["id"]."'>
                        <img src='https://mingled.in/images/products/".$pt_ct[$itm["id"]]."_".$itm["id"].".jpeg' style='float:left; margin-right:10px; width:70px;'/>
                    </a>
                    <div style='height:70px;'>
                        <p style='color:dimgray; font-size:1em; margin:0;'> ".$FormFields["Pdt"]["select"]["catId"][$pt_ct[$itm["id"]]]." </p>
                        <p style='font-size:1.5em; margin:0;'> ".$pt_nm[$itm["id"]]." <br> <span style='float:right;'> Rs.".$pt_pr[$itm["id"]]." </span> </p>
                        <p style='color:dimgray; font-size:1.5em; margin:0;'> &nbsp; &nbsp; x ".$itm["qty"]." </p>
                    </div>
                </div>";

	$msg = "<div style='background:lightgray; font-family:Arial,Helvetica,sans-serif;'>
                <div style='background:white; margin:auto; max-width:400px;'>
                    <h1 style='background:hsl(30, 14%, 67%); padding:20px; text-align:center;'> MINGLED </h1>
                    
                    <div style='padding:0 20px;'>
                        <h1 style='color:darkslategray; text-align:center;'>
                            <img style='height:95px; margin:auto; width:100px;' src='https://mingled.in/images/check_circ.png'/>
                            <br> Order Confirmed
                        </h1>
                        <h2 style='text-align:center;'> ".$odr["odr_id"]." </h2>
                        <br>
                        <div style='background:whitesmoke; border-radius:10px; padding:10px;'>
                            $itms
                        </div>
                        <br> <br>
                        <div style='background:whitesmoke; border-radius:10px; padding:10px;'>
                            <div style='color:darkslategray; padding:10px;'>
                                <p style='color:black; font-size:1.5em; margin:0;'> ".$odr["cusName"]." <span style='float:right;'> ".$odr["mob"]." </span> </p>
                                <h3 style='margin:0;'>".$odr["street1"]."</h3>
                                <h3 style='margin:0;'>".$odr["street2"]."</h3>
                                <h3 style='margin:0;'>".$odr["landmark"]."</h3>
                                <h3 style='margin:0;'>".$odr["city"]."</h3>
                                <h3 style='margin:0;'>".$odr["state"]." - ".$odr["pincode"]."</h3>
                                <br>
                                <p style='color:black; font-size:1.5em; margin:0;'> Shipping Charges <span style='float:right;'> Rs.".($_SESSION["shipAmt"]??80)." </span> </p>
                            </div>
                        </div>
                        <h2 style='padding:20px;'>
                            <b style='background:lime; border-radius:20px; color:black; padding:5px 40px;'> PAID </b>
                            <b style='float:right;'> Rs.".$odr["amount"]." </b>
                        </h2>
                        <br>
                    </div>
                    
                    <div style='background:black; padding:15px 25px;'>
                        <img src='https://mingled.in/images/logo.jpeg' style='float:left; width:40px;'/>
                        <p style='height:40px; margin:0; text-align:right;'>
                            <a target='_blank' style='color:white; line-height:20px; text-decoration:none;' href='https://mingled.in/Terms_and_Conditions'> Terms and Conditions </a>
                            <br>
                            <a target='_blank' style='color:white; line-height:20px; text-decoration:none;' href='https://mingled.in/Privacy_Policy'> Privacy Policy </a>
                        </p>
                    </div>
                </div>
            </div>";

	require "Admin/mail/mailSend.php";
	mailSend ($to, $sub, $msg);
	mailSend ("gayatrivm00@gmail.com", $sub, $msg);
}
?>

