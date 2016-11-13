<?php
//membentuk koneksi kedatabase
mysql_connect("localhost","root","");
mysql_select_db("cropphoto");

//menentukan lokasi penyimpanan
$upload_path_normal = "upload_pic/normal/";
$upload_path_medium = "upload_pic/medium/";
$upload_path_small = "upload_pic/small/";

//file yang diupload, dijadikan sebagai dasar cropping
$large_image_name = "resized_pic.jpg";

//penamaan data setelah dicrop
$image_name = date("dHisY").".jpg";

//menentukan ukuran dan besar file MAX
$max_file = "1148576";//1MB
$max_width = "700";

//menentukan ukuran cropping
$med_width = "290";
$med_height = "200";
$full_width = "650";
$full_height = "600";
$thumb_width = "95";
$thumb_height = "65";

//lokasi watemark
$image_pathSmall = "images/logoPrintSmall.png";
$image_pathMedium = "images/logoPrintMedium.png";
$image_pathBig = "images/logoPrintBig.png";

//fungsi merubah ukuran / croping
function resizeImage($image,$width,$height,$scale) {
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	$source = imagecreatefromjpeg($image);
	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
	imagejpeg($newImage,$image,90);
	chmod($image, 0777);
	return $image;
}
function resizeThumbnailImage($image_name, $image, $width, $height, $start_width, $start_height, $scale){
	global $image_pathSmall;
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	$source = imagecreatefromjpeg($image);
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	$watermark = imagecreatefrompng($image_pathSmall);
    list($w_width, $w_height) = getimagesize($image_pathSmall);
	$pos_x = 20; 
    $pos_y = 20;
    imagecopy($newImage, $watermark, $pos_x, $pos_y, 0, 0, $w_width, $w_height);
	imagejpeg($newImage,$image_name,90);
	imagedestroy($newImage);
	chmod($image_name, 0777);
	return $image_name;
}
function resizeMediumImage($image_name, $image, $width, $height, $start_width, $start_height, $scale){
	global $image_pathMedium;
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	$source = imagecreatefromjpeg($image);
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	$watermark = imagecreatefrompng($image_pathMedium);
    list($w_width, $w_height) = getimagesize($image_pathMedium);
	$pos_x = 60; 
    $pos_y = 80;
    imagecopy($newImage, $watermark, $pos_x, $pos_y, 0, 0, $w_width, $w_height);
	imagejpeg($newImage,$image_name,90);
	imagedestroy($newImage);
	chmod($image_name, 0777);
	return $image_name;
}
function resizeFullImage($image_name, $image, $width, $height, $start_width, $start_height, $scale){
	global $image_pathBig;
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
	$source = imagecreatefromjpeg($image);
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
	$watermark = imagecreatefrompng($image_pathBig);
    list($w_width, $w_height) = getimagesize($image_pathBig);
	$pos_x = 200; 
    $pos_y = 200;
    imagecopy($newImage, $watermark, $pos_x, $pos_y, 0, 0, $w_width, $w_height);
	imagejpeg($newImage,$image_name,90);
	imagedestroy($newImage);
	chmod($image_name, 0777);
	return $image_name;
}
//fungsi untuk mendapatkan tinggi gambar
function getHeight($image) {
	$sizes = getimagesize($image);
	$height = $sizes[1];
	return $height;
}
//fungsi untuk mendapatkan lebar gambar
function getWidth($image) {
	$sizes = getimagesize($image);
	$width = $sizes[0];
	return $width;
}

//mengabungkan lokasi gambar dengan nama gambar
$large_image_location = $upload_path_normal.$large_image_name;
$thumb_image_location = $upload_path_small.$image_name;
$med_image_location = $upload_path_medium.$image_name;
$full_image_location = $upload_path_normal.$image_name;


//mengecek apakah ada gambar yang bernama sama di folder tersebut
if (file_exists($large_image_location)){
	if(file_exists($thumb_image_location)){
		$thumb_photo_exists = "<img src=\"".$upload_path_small.$image_name."\" alt=\"Thumbnail Image\"/>";
	}else{
		$thumb_photo_exists = "";
	}
	if(file_exists($med_image_location)){
		$med_photo_exists = "<img src=\"".$upload_path_medium.$image_name."\" alt=\"Thumbnail Image\"/>";
	}else{
		$med_photo_exists = "";
	}
	if(file_exists($full_image_location)){
		$full_photo_exists = "<img src=\"".$upload_path_normal.$image_name."\" alt=\"Thumbnail Image\"/>";
	}else{
		$full_photo_exists = "";
	}
   	$large_photo_exists = "<img src=\"".$upload_path_normal.$large_image_name ."\" alt=\"Large Image\"/>";
} else {
   	$large_photo_exists = "";
	$thumb_photo_exists = "";
	$med_photo_exists = "";
	$full_photo_exists = "";
}
?>