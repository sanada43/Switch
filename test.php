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