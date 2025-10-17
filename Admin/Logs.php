<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";

$selSql = ["Chb", "Con", "Fam", "Pay", "Rct", "Ser", "Usr"];
$selSql = join ("','", $selSql);
if ($_GET["tblName"]??0) {
    $sql = "SELECT * FROM Log WHERE tblName='".($_GET["tblName"]??"")."' AND srNo='".($_GET["srNo"]??"")."'";
}else {
    $from = $_GET["from"]??$tdy;
    $to = $_GET["to"]??$tdy;
    $sql = "SELECT * FROM Log WHERE tblName IN ('$selSql') AND createDate BETWEEN '$from' AND '$to'";
}
$logs = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;

$sql = "SELECT usr_id,CONCAT(usertype,' ',name) AS user FROM Usr";
$usrs = array_column (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??[], "user", "usr_id");
?>
<center> <h5> User Activity Logs
    <a href="#" target="_blank" class="w3-btn w3-border w3-border-black w3-hover-black w3-text-black fa fa-print p-2 circ printLnk" title="Print Logs"> </a>
</h5> </center>

<div class="w3-row p-2">
    <p class="w3-col s2 w3-center p-2"> Page / Menu / Type <br>
        <select class="w3-input" id="tblName" onchange="location=`/<?=$PATH;?>/Logs${this.value.length ? `/${this.value}/${$(`#srNo`).val()}/`:``}`">
            <option value=""> </option>
<? foreach (["Con" => "Consignment", "Fam" => "Family", "Rct" => "Receipts", "Pay" => "Payments"] as $val => $label) echo "<option value='$val' ", ($_GET["tblName"]??"") == $val ? "selected":"", "> $label </option>"; ?>
        </select>
    </p>
    <p class="w3-col s2 w3-center p-2"> Fam. No. / Rt. No. / Vr. No. <br>
        <input type="number" class="w3-input" id="srNo" value="<?=$_GET["srNo"]??"";?>" onchange="location=`/<?=$PATH;?>/Logs${this.value.length ? `/${$(`#tblName`).val()}/${this.value}/`:``}`" />
    </p>
    
    <p class="w3-col s2 w3-center p-2"> Day <br>
        <input type="date" class="w3-input" value="<?=$_GET["from"]??"";?>" min="" max="<?=$tdy;?>" onchange="location=`/<?=$PATH;?>/Logs${this.value.length ? `/${this.value}/${this.value}/`:``}`" />
    </p>
    <p class="w3-col s2 w3-center p-2"> Month <br>
        <input type="month" class="w3-input" value="<?=$_GET["from"]??"";?>" min="" max="<?=$tdy;?>" onchange="location=`/<?=$PATH;?>/Logs${this.value.length ? `/${this.value}-01/${this.value}-${MN_END[parseInt (this.value.slice (-2))]}/`:``}`" />
    </p>
    <p class="w3-col s2 w3-center p-2"> From <br>
        <input type="date" class="w3-input" id="from" value="<?=$_GET["from"]??"";?>" min="" max="<?=$tdy;?>" onchange="location=`/<?=$PATH;?>/Logs${this.value.length ? `/${this.value}/${$(`#to`).val()}/`:``}`" />
    </p>
    <p class="w3-col s2 w3-center p-2"> To <br>
        <input type="date" class="w3-input" id="to" value="<?=$_GET["to"]??"";?>" min="" max="<?=$tdy;?>" onchange="location=`/<?=$PATH;?>/Logs${this.value.length ? `/${$(`#from`).val()}/${this.value}/`:``}`" />
    </p>
</div>

<style>
    table,tr,th,td {
		border: 1px solid;
		border-collapse: collapse;
	}
    td,th { white-space: nowrap; }
    td,th { padding: 5px; }
</style>
<div style="overflow-x:auto;">
<?
if ($logs) for ($i = 0; $i < count ($logs); $i++) {
    $log = $logs[$i];
    $log["query"] = urldecode ($log["query"]);

    switch ($log["tblName"]) {
        case "Con":
        case "Cov": $boxBorderColor = "yellow";
            break;
        case "Fam": $boxBorderColor = "purple";
            break;
        case "Pay": $boxBorderColor = "red";
            break;
        case "Rct": $boxBorderColor = "green";
            break;
        default: $boxBorderColor = "blue";
    }
    switch ($log["action"]) {
        case "Add": $action = "plus w3-text-green";
            break;
        case "Edit": $action = "edit w3-text-amber";
            break;
        case "Delete": $action = "trash w3-text-red";
            break;
    }
/* ___________________________ get query ___________________________ get query ___________________________ get query ___________________________ get query ___________________________ */
    if ($log["action"] == "Add") {
        $keys = explode (",", explode (")", explode ("(", $log["query"])[1]??"")[0]);
        $vals = explode ("','", explode ("')", explode ("VALUES(", $log["query"])[1]??"")[0]);
        if (count ($keys) != count ($vals)) {
            echo "<br> keys : ", join (",", $keys);
            echo "<br> vals : ", join (",", $vals);
            continue;
        }
        $query = array_combine ($keys, $vals);
    }
    if ($log["action"] == "Delete") {
        $tblName = in_array ($log["tblName"], ["Pay", "Rct"]) ? "Tran$TBL_ID" : $log["tblName"];
        $idRow = (in_array ($log["tblName"], ["Pay", "Rct"]) ? "trn" : strtolower ($log["tblName"]))."_id";
        $sql = "SELECT * FROM $tblName WHERE $idRow='".$log["rowId"]."'";
        $query = mysqli_fetch_assoc (mysqli_query ($conn, $sql));
        $keys = array_keys ($query);
    }
    if ($log["action"] == "Edit") {
        $i++;
        parse_str (str_replace (", ", "&", $log["query"]), $prevQuery);
        $keys = array_keys ($prevQuery);
        parse_str (str_replace (",", "&", explode (" WHERE", explode ("SET ", urldecode ($logs[$i]["query"]))[1]??"")[0]), $query);
    }

/* ------------------------ get form fields ------------------------ get form fields ------------------------ get form fields ------------------------ get form fields ------------------------ get form fields ------------------------ */
    $flds = $FormFields[$log["tblName"]]["fields"];
    if ($log["tblName"] == "Usr") $flds = array_merge ($flds, $FormFields["Usr_Prm"]["fields"]);

    if ($log["tblName"] == "Rct") {
        $unsetFields = ["famHead", "famNo", "ward", "oldFamNo", "serHead", "moFrom", "moTo"];
        $details = ["", "PledgeAmt", "PgNo", "PaidUpto", "TotPaid", "Arrear"];
        foreach (["head", "wife", "child1", "child2", "child3", "child4"] as $pre) foreach ($details as $detail) array_push ($unsetFields, $pre.$detail);
        foreach ($unsetFields as $uf) unset ($flds[$uf]);
    }
    if ($log["tblName"] == "Pay") unset ($flds["serHead"]);
    foreach ($flds as $name => $attr) $query[$name] = trim ($query[$name]??"", "'");

/* ------------------------ format query fields ------------------------------------------------ format query fields ------------------------------------------------ format query fields ------------------------ */
    if (in_array ($log["action"], ["Add", "Delete"])) foreach ($flds as $name => $attr) if (($query[$name]??"") == "" || ($query[$name]??"") == 0) unset ($flds[$name]);
    if ($log["action"] == "Edit") foreach ($flds as $name => $attr) {
        if (($prevQuery[$name]??"") == 0 || ($prevQuery[$name]??"") == "") $prevQuery[$name] = "";
        if (($query[$name]??"") == 0 || ($query[$name]??"") == "") $query[$name] = "";
        if (strcmp ($prevQuery[$name]??"", $query[$name]??"") == 0) unset ($flds[$name]);
    }

/* ==================================================== display ==================================================== display ==================================================== */
    echo "<div class='w3-card w3-black w3-round-xlarge w3-border w3-topbar w3-border-$boxBorderColor w3-hover-dark'>";
    echo    "<div class='w3-row-padding'>";
    echo        "<p class='w3-col s1'>", $log["createDate"], " ", $log["createTime"], "</p>";
    echo        "<p class='w3-col s1'>", $usrs[$log["createBy"]], "<br> <i class='w3-large fa w3-text-", $log["status"] ? "light-green fa-check" : "deep-orange fa-close", "'> </i> </p>";
    echo        "<p class='w3-col s1'>", $log["tblName"], " no. ", $log["srNo"], "<br> <i class='w3-large w3-left fa fa-$action'> </i>", "</p>";
    echo        "<div class='w3-col s9'>";
    echo            "<div class=''>";

    echo "<table class=''>";
    echo    "<tr>";
    foreach ($flds as $name => $attr) if (in_array ($name, $keys)) echo "<th>", $attr["2"] == "" ? $name : $attr["2"], "</th>";
    if (!count ($flds)) echo "<th class='w3-gray px-5'> <i> No changes </i> </th>";
    echo    "</tr>";
    
    if ($log["action"] == "Edit") {
        echo    "<tr>";
        foreach ($flds as $name => $attr) if (in_array ($name, $keys)) echo "<td>", $prevQuery[$name], "</td>";
        echo    "</tr>";
    }

    echo    "<tr>";
    foreach ($flds as $name => $attr) if (in_array ($name, $keys)) echo "<td>", $query[$name], "</td>";
    echo    "</tr>";
    echo "</table>";

    echo            "</div>";
    echo        "</div>";
    echo    "</div>";
    echo "</div>";
/* ==================================================== display ==================================================== display ==================================================== */
}
else echo "<h5 class='w3-large w3-center w3-dark-gray w3-text-black p-4 px-5'> <b> No logs found. </b> </h5>";
?>
</div>

