<?php if (!empty($_POST)): 
    $file = '../config.php';
    
	$contents = "<?php \n";
    $contents .= "//Database details \n";
    $contents .= "define ('DB_HOST', '".$_POST['dbHost']."'); \n";
    $contents .= "//Username \n";
    $contents .= "define ('DB_USERNAME', '".$_POST['dbUserName']."'); \n";
    $contents .= "//Pass \n";
    $contents .= "define ('DB_PASS', '".$_POST['dbPass']."'); \n";
    $contents .= "//Database Name \n";
	$contents .= "define ('DB_NAME', '".$_POST['dbName']."'); \n";
	$contents .= "//Base URL \n";
	$contents .= "define ('BASE_URL', 'http://".$_POST['siteURL']."'); \n";
	$contents .= "//Email/Cookie URL \n";
	$contents .= "define ('EMAIL_URL', '".$_POST['siteURL']."'); \n";
	$contents .= "?>";	

file_put_contents($file, $contents);
?>