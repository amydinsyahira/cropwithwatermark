<?php
include_once("func.php");
	$normal = $upload_path_normal.$_GET['g'];
	$medium = $upload_path_medium.$_GET['g'];
	$small = $upload_path_small.$_GET['g'];
	if (file_exists($normal) || file_exists($medium) || file_exists($small)) {
		//menghapus file
		mysql_query("DELETE FROM gambar WHERE gambar = '".$_GET['g']."'");
		unlink("".$normal."");
		unlink("".$medium."");
		unlink("".$small."");
	} else {
		mysql_query("DELETE FROM gambar WHERE gambar = '".$_GET['g']."'");	
	}
	header("location:".$_SERVER["PHP_SELF"]);
	exit(); 
?>