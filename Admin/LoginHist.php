<? require "AuthenticateUser.php"; require "FormFields.php"; require "checkPerm.php";
$from = $_GET["from"]??$tdy;
$to = $_GET["to"]??$tdy;
if ($from == $to) $sql = "SELECT * FROM LoginHist WHERE inDateTime LIKE '$from %'";
else $sql = "SELECT * FROM LoginHist WHERE inDateTime>'$from 00:00:00' AND inDateTime<'$to 00:00:00'";
$logs = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC)??0;
?>
<center> <h5> Login History
    <a href="#" target="_blank" class="w3-btn w3-border w3-border-black w3-hover-black w3-text-black fa fa-print p-2 circ printLnk" title="Print Login History"> </a>
</h5> </center>

<div class="w3-row p-2 w3-content">
    <p class="w3-col s3 w3-center p-2"> Day <br>
        <input type="date" class="w3-input" id="day" value="<?=$_GET["from"]??"";?>" min="" max="<?=$tdy;?>" onchange="location=`/<?=$PATH;?>/LoginHist${this.value.length ? `/${this.value}/${this.value}/`:``}`" />
    </p>
    <p class="w3-col s3 w3-center p-2"> Month <br>
        <input type="month" class="w3-input" id="" value="<?=$_GET["from"]??"";?>" min="" max="<?=$tdy;?>" onchange="location=`/<?=$PATH;?>/LoginHist${this.value.length ? `/${this.value}-01/${this.value}-${MN_END[parseInt (this.value.slice (-2))]}/`:``}`" />
    </p>
    <p class="w3-col s3 w3-center p-2"> From <br>
        <input type="date" class="w3-input" id="from" value="<?=$_GET["from"]??"";?>" min="" max="<?=$tdy;?>" onchange="location=`/<?=$PATH;?>/LoginHist${this.value.length ? `/${this.value}/${$(`#to`).val()}/`:``}`" />
    </p>
    <p class="w3-col s3 w3-center p-2"> To <br>
        <input type="date" class="w3-input" id="to" value="<?=$_GET["to"]??"";?>" min="" max="<?=$tdy;?>" onchange="location=`/<?=$PATH;?>/LoginHist${this.value.length ? `/${$(`#from`).val()}/${this.value}/`:``}`" />
    </p>
</div>

<p class="w3-black w3-row sticky-top" style="top:40px;">
<?
foreach (["Sign In Date &amp; Time" => "2 pl-2", "User" => "3 pl-5", "IP" => "2 pl-5", "Session Id" => "3 pl-5", "Sign Out Date &amp; Time" => "2"] as $t => $class) echo "<b class='w3-col s$class'> $t </b>"
?>
</p>

<div style="overflow-x:auto;">
<?
if ($logs) foreach ($logs as $log) {

    echo "<div class='w3-card w3-black w3-round-xlarge w3-border w3-topbar w3-border-brown w3-hover-dark'>";
    echo    "<div class='w3-row-padding'>";
    echo        "<p class='w3-col s2'>", $log["inDateTime"], "</p>";
    echo        "<p class='w3-col s3'> <i class='w3-large fa w3-text-", ($log["usertype"]??0) ? "light-green fa-check" : "deep-orange fa-close", "'> </i>", $log["usertype"], " ", $log["username"], "</p>";
    echo        "<p class='w3-col s2'>", $log["ip"], "</p>";
    echo        "<p class='w3-col s3'>", $log["sessionId"], "</p>";
    echo        "<p class='w3-col s2'>", $log["outDateTime"], "</p>";
    echo    "</div>";
    echo "</div>";
    
}
else echo "<h5 class='w3-large w3-center w3-dark-gray w3-text-black p-4 px-5'> <b> No logs found. </b> </h5>";
?>
</div>
<script>
    const DAY = $(`#day`);
    // const MNTH = $(`[type='month']`);
    const FR = $(`#from`);
    const TO = $(`#to`);
    $(document).ready (() => {
        if (FR.val() == TO.val()) setTimeout (() => DAY.focus(), 300);
        else setTimeout (() => FR.focus(), 300);
    });
</script>

