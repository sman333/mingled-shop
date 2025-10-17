<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	array_walk_recursive ($_POST, "validate");

$CASH_OPEN_BAL = 85189;
$BANK_OPEN_BAL = 2099053;

    $tot = $bal = [];
    foreach ($FormFields["Rct"]["select"]["payMode"] as $pM => $x) {
        foreach (["Rct" => "Cr", "Pay" => "Dr"] as $typ => $act) {
            $sql = "SELECT SUM(amount) AS tot FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='$act' AND payMode='$pM' AND tDate<'".$_POST["balOpenDate"]."'";
            $tot[$typ."_open_$pM"] = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["tot"]??0;

            $sql = "SELECT SUM(amount) AS tot FROM Tran$TBL_ID WHERE status='$ACTIVE' AND action='$act' AND payMode='$pM' AND tDate<='".$_POST["balCloseDate"]."'";
            $tot[$typ."_close_$pM"] = mysqli_fetch_assoc (mysqli_query ($conn, $sql))["tot"]??0;
        }
        $bal["open_$pM"] = $tot["Rct_open_$pM"] - ($tot["Pay_open_$pM"]??0);
        $bal["close_$pM"] = $tot["Rct_close_$pM"] - ($tot["Pay_close_$pM"]??0);
    }
    $bal["open_CASH"] += $CASH_OPEN_BAL;
    $bal["close_CASH"] += $CASH_OPEN_BAL;

    $openBalBank = $closeBalBank = $BANK_OPEN_BAL;
    foreach (["CHEQUE", "UPI", "ACC_TRA"] as $pM) {
        $openBalBank += $bal["open_$pM"];
        $closeBalBank += $bal["close_$pM"];
    }

    if ($_POST["display"]??0) {
        if ($_POST["typ"]??"" == "tbl")		/* view as table ------------------------------------ view as table ------------------------------------ view as table ------------------------------------ */
                echo "<tr>
                        <td> ", date_create (($_POST["bal"]??"") == "open" ? $_POST["balOpenDate"] : $_POST["balCloseDate"])->format ("d-m-Y"), " </td>
                        <td> </td>
                        <td style='text-align:center;'> <b> ", ($_POST["bal"]??"") == "open" ? "O" : "C", " B </b> </td>",
                        x_n ("<td> </td>", 3),
                        "<td style='text-align:right'> <b> ", dispAmt (($_POST["bal"]??"") == "open" ? $bal["open_CASH"] : $bal["close_CASH"]), " </b> </td>
                        <td style='text-align:right'> <b> ", dispAmt (($_POST["bal"]??"") == "open" ? $openBalBank : $closeBalBank), " </b> </td>
                        ", ($_POST["typ"] == "bank" ? "<td> ".dispAmt (($_POST["bal"]??"") == "open" ? $openBalBank : $closeBalBank)." </td>" : ""), "
                    </tr>";

        else 		 /* view as list ------------------------------------ view as list ------------------------------------ view as list ------------------------------------ */
            echo "<script>
                    $(`.openBalCash`).html (`", dispAmt ($bal["open_CASH"]), "`);
                    $(`.openBalBank`).html (`", dispAmt ($openBalBank), "`);
                    $(`.closeBalCash`).html (`", dispAmt ($bal["close_CASH"]), "`);
                    $(`.closeBalBank`).html (`", dispAmt ($closeBalBank), "`);
                </script>";
    }
}
?>

