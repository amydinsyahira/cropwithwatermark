<?php
include_once("func.php");
//jika tombol upload diklik
if (isset($_POST["upload"])) { 
	//mendapatkan informasi file
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
	$userfile_size = $_FILES['image']['size'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = substr($filename, strrpos($filename, '.') + 1);
	
	//mengecek dan membatasi hanya file bertipe JPEG dan berkapasitas max 1MB
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
		if (($file_ext!="jpg") && ($userfile_size > $max_file)) {
			$error= "Hanya file bertipe JPEG dan berkapasitas max 1MB";
		}
	}else{
		$error= "Pilih dahulu Gambar bertipe jpg untuk diupload";
	}
	//jika semuanya sudah ok, tidak ada error, maka halaman cropping muncul.
	if (strlen($error)==0){
		
		if (isset($_FILES['image']['name'])){
			//memindahkan file upload(sebelum dicrop) ke folder medium.
			move_uploaded_file($userfile_tmp, $large_image_location);
			//memberikan hak akses kepada folder
			chmod($large_image_location, 0777);
			
			//memberikan parameter untuk fungsi tinggi dan lebar
			$width = getWidth($large_image_location);
			$height = getHeight($large_image_location);
			
			//mengecilkan ukuran gambar yang akan dicrop, jika gambar terlalu lebar
			if ($width > $max_width){
				$scale = $max_width/$width;
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}else{
				$scale = 1;
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}
			//menghapus file yang terlalu lebar dan menggantinya dengan yang baru
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}
		//Refresh halaman untuk menampilkan gambar yang siap untuk dicrop
		header("location:".$_SERVER["PHP_SELF"]);
		exit();
	}
}
//jika terdapat aksi upload hasil croppingan
if (isset($_POST["upload_thumbnail"]) && strlen($large_photo_exists)>0) {
	//mendapatkan koordinat gambar yang akan dicrop.
	$x1 = $_POST["x1"];
	$y1 = $_POST["y1"];
	$x2 = $_POST["x2"];
	$y2 = $_POST["y2"];
	$w = $_POST["w"];
	$h = $_POST["h"];
	//mengubah ukuran/croping sesuai lebar dan tinggi yang ditentukan
	$scale = $thumb_width/$w;
	$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
	//mengubah ukuran/croping sesuai lebar dan tinggi yang ditentukan untuk medium
	$scale2 = $med_width/$w;
	$cropped2 = resizeMediumImage($med_image_location, $large_image_location,$w,$h,$x1,$y1,$scale2);
	//mengubah ukuran/croping sesuai lebar dan tinggi yang ditentukan untuk gambar full
	$scale3 = $full_width/$w;
	$cropped3 = resizeFullImage($full_image_location, $large_image_location,$w,$h,$x1,$y1,$scale3);
	//memasukan kedalam database
	mysql_query("INSERT INTO gambar(gambar) VALUES('".$image_name."')");
	//menghapus image induk croppingan
	unlink($large_image_location);
	//Refres halaman untuk menampilkan hasil croppingan
	header("location:".$_SERVER["PHP_SELF"]);
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Cropping Gambar</title>
</head>
<body>
	<script type="text/javascript" src="js/jquery-pack.js.php"></script>
    <script type="text/javascript" src="js/colorbox.js.php"></script>
    <script type="text/javascript">
	function hapusfoto(ID){ 
	   $.post(
	      'action_del_foto.php',
		  {id_foto: ID},
		  function(response){
			if(response == 'ok')
			  $('#foto'+ID).fadeOut();
			else 
			  alert('Proses Hapus Foto gagal');
		  }
	   );
	}
	</script>
    <link rel="stylesheet" href="colorbox.css" />
	<script type="text/javascript" src="js/jquery.imgareaselect-0.3.min.js.php"></script>
<?php
//javascript aktif jika mendapatkan aksi upload gambar yang akan dicrop
if(strlen($large_photo_exists)>0){
	$current_large_image_width = getWidth($large_image_location);
	$current_large_image_height = getHeight($large_image_location);?>
<script type="text/javascript">
function preview(img, selection) { 
	var scaleX = <?php echo $med_width;?> / selection.width; 
	var scaleY = <?php echo $med_height;?> / selection.height; 
	
	$('#thumbnail + div > img').css({ 
		width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
		height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
} 

$(document).ready(function () { 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("Seleksi area yang akan dicrop");
			return false;
		}else{
			return true;
		}
	});	
}); 
$(window).load(function () { 
	$('#thumbnail').imgAreaSelect({ aspectRatio: '1.5:1', onSelectChange: preview }); 
});
</script>
<?php } else {?>
<script type="text/javascript">
$(document).ready(function () { 
	//slideshow
	$(".group4").colorbox({rel:'group4', slideshow:true});	
});
</script>
<?php
}
//jika ada gambar induk difolder normal
if(strlen($large_photo_exists)>0){?>
	<h2>Cropping gambar</h2>
	<div align="center">
		<img src="<?php echo $upload_path_normal.$large_image_name;?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
		<div style="float:left; position:relative; overflow:hidden; width:<?php echo $med_width;?>px; height:<?php echo $med_height;?>px;">
			<img src="<?php echo $upload_path_normal.$large_image_name;?>" style="position: relative;" alt="Thumbnail Preview" />
		</div>
		<br style="clear:both;"/>
		<form name="thumbnail" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
			<input type="hidden" name="x1" value="" id="x1" />
			<input type="hidden" name="y1" value="" id="y1" />
			<input type="hidden" name="x2" value="" id="x2" />
			<input type="hidden" name="y2" value="" id="y2" />
			<input type="hidden" name="w" value="" id="w" />
			<input type="hidden" name="h" value="" id="h" />
			<input type="submit" name="upload_thumbnail" value="Simpan hasil cropingan" id="save_thumb" />
		</form>
	</div>
<?php 	} else { ?>
<h2>Upload Gambar</h2>
<form name="photo" id="imageform" target="_parent" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
	Gambar <input type="file" name="image" size="30" /> <input type="submit" name="upload" value="Upload" />
</form>
<hr />
<?php
//Menampilakan pesan error, jika terdapat kesalahan gambar
if(strlen($error)>0){
	echo "<ul><li><strong>Error!</strong></li><li>".$error."</li></ul>";
}
//memanggil database
$ambil_gambar = mysql_query("SELECT * FROM gambar ORDER BY idgambar DESC");
$ada = mysql_num_rows($ambil_gambar);
$lokasi_gambar = $upload_path_normal.$gambar['gambar'];
	if($ada > 0){
		while($gambar = mysql_fetch_array($ambil_gambar)){
			if(strlen($lokasi_gambar )>0){
			//menampilkan link hapus gambar, jika terdapat gambar difolder tersebut
				echo '<div style="float:left; margin-right:10px;" id="foto'.$gambar['idgambar'].'">';
				echo '<p><a class="group4" href="'.$upload_path_normal.$gambar['gambar'].'"><img src="'.$upload_path_medium.$gambar['gambar'].'" /></a></p>';
				echo '<p><a href="javascript:hapusfoto('.$gambar['idgambar'].')" onclick="confirm(\'anda akan menghapus file '.$gambar['idgambar'].'\')" id="delfoto">Hapus gambar</a></p>';
				echo '</div>';
			}else echo 'file dengan nama '.$gambar['gambar'].', tidak ada';	
		} 
	}
}
?>
</body>
</html>