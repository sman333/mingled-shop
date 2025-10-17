<?
$logSql = "INSERT INTO Log(
		createDate,
		createTime,
		createBy,
		tblName,
		action,
		srNo,
		rowId,
		status,
		query
	) VALUES(
		'$tdy',
		'$now',
		'".$usr["usr_id"]."',
		'$tblName',
		'".($log_action??"")."',
		'".($log_srNo??"")."',
		'".($log_rowId??0)."',
		'$log_status',
		'".str_replace ("'", "", urlencode ($data??""))."'
	)";
mysqli_query ($conn, $logSql);
?>

