<?
$csvFile = fopen ("downloads/".$fileName.".csv", "w");
fwrite ($csvFile, join ("\n", $csv));
fclose ($csvFile);
?>