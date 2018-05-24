<?php
    require_once 'vendor/autoload.php';
     
    use WindowsAzure\Common\ServicesBuilder;
    use WindowsAzure\Common\ServiceException;
     
    $primaryAccessKey = "fU9GepJPZu7/w3BpZn4O99Bj5AsE7KLfxN4qdZskTljcqxG8FX9DSZRtHo2CTNz3g3QV+52z9aJse/d9ww1ftQ==";
     
    $blobAccountName = "storagesoracom";
    $containerName = "public3";
     
    $connectionString = "DefaultEndpointsProtocol=https;AccountName=".$blobAccountName.";AccountKey=".$primaryAccessKey;
    $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
    
    try {
    // List blobs.
        $blob_list = $blobRestProxy->listBlobs($containerName);
        $blobs = $blob_list->getBlobs();
     
        foreach($blobs as $blob)
            {
                echo $blob->getName().": ".$blob->getUrl()."\n";
            }
        
    catch(ServiceException $e){
        // Error codes and messages are here: 
        // http://msdn.microsoft.com/ja-jp/library/windowsazure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script src="./jquery-3.2.1.min.js"></script>
	<script src="./confirm.js"></script>
	<script src="./list_sched.js"></script>
    <link rel="stylesheet" type="text/css" href="./style.css" />  
</head>
<body>

<p>てすと</p>


</body>
</html>