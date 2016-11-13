<?php
	isset($_POST['id_foto']) or die('Kurang Parameter');
	//membentuk koneksi kedatabase
mysql_connect("localhost","root","prayan17");
mysql_select_db("cropphoto");
	
	$ID = $_POST['id_foto'];
	$upload_path_normal = "upload_pic/normal/";
	$upload_path_medium = "upload_pic/medium/";
	$upload_path_small = "upload_pic/small/";
	$r = mysql_fetch_array(mysql_query("SELECT * FROM gambar WHERE idgambar = '".$ID."'"));
	
	$normal = $upload_path_normal.$r['gambar'];
	$medium = $upload_path_medium.$r['gambar'];
	$small = $upload_path_small.$r['gambar'];
	if (file_exists($normal) || file_exists($medium) || file_exists($small)) {
		//menghapus file
		mysql_query("DELETE FROM gambar WHERE idgambar = '".$ID."'") or die ("failed");
		unlink("".$normal."");
		unlink("".$medium."");
		unlink("".$small."");
	} else {
		mysql_query("DELETE FROM gambar WHERE idgambar = '".$ID."'") or die ("failed");	
	}
	echo 'ok';	
?>