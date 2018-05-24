<?php
	require("common.php");

	try {
		$mysqli = create_connection();
		require("prepare_info.php");
	} finally {
		$mysqli->close();
	}

	$start_date = strftime('%Y%m%d', $stand_s);
	$start_hour = strftime('%H', $stand_s);
	$start_min = strftime('%M', $stand_s);
	$start_date_msg = "";

	$end_date = strftime('%Y%m%d', $stand_e);
	$end_hour = strftime('%H', $stand_e);
	$end_min = strftime('%M', $stand_e);
	$end_date_msg = "";

	$network_id = "";
	$network_id_msg = "";

	$room_id = "";
	$room_id_msg = "";

	$applicant = "";
	$applicant_msg = "";

	$purpose = "";
	$purpose_msg = "";

	$error_message = "";

	session_start();
	if (isset($_SESSION['error_data'])) {
		$error_data = $_SESSION['error_data'];

		$start_date = $error_data['start_date'];
		$start_hour = $error_data['start_hour'];
		$start_min = $error_data['start_min'];
		$start_date_msg = $error_data['start_date_msg'];

		$end_date = $error_data['end_date'];
		$end_hour = $error_data['end_hour'];
		$end_min = $error_data['end_min'];
		$end_date_msg = $error_data['end_date_msg'];

		$network_id = $error_data['network_id'];
		$network_id_msg = $error_data['network_id_msg'];

		$room_id = $error_data['room_id'];
		$room_id_msg = $error_data['room_id_msg'];

		$applicant = $error_data['applicant'];
		$applicant_msg = $error_data['applicant_msg'];

		$purpose = $error_data['purpose'];
		$purpose_msg = $error_data['purpose_msg'];

		$error_message = $error_data['error_message'];

		unset($_SESSION['error_data']);
	}
    
    
    require_once 'vendor\autoload.php';

    use WindowsAzure\Common\ServicesBuilder;
    use WindowsAzure\Common\ServiceException;

    // Create blob REST proxy.
    $connectionString = "DefaultEndpointsProtocol=https;AccountName=storagesoracom;AccountKey=fU9GepJPZu7/w3BpZn4O99Bj5AsE7KLfxN4qdZskTljcqxG8FX9DSZRtHo2CTNz3g3QV+52z9aJse/d9ww1ftQ==;EndpointSuffix=core.windows.net";
    $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);

    try {
        // List blobs.
        $blob_list = $blobRestProxy->listBlobs("image");
        $blobs = $blob_list->getBlobs();

        foreach($blobs as $blob)
        {
            echo $blob->getName().": ".$blob->getUrl()."<br />";
            echo "<img src='".$blob->getUrl()."'><br />";
        }
    }
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here: 
        // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
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

<hr class="hr-text" data-content="新規登録" />

<form method="post" action="./do_regist.php">
<?php
	require("input_form.php");
?>
<input type="submit" value="登録" id="regist" />
</form>

<br />
<a href="./history.php">履歴</a>

<br />
<hr class="hr-text" data-content="接続状態" />
<div id="connections" align="center"></div>
<div align="center" id="date"></div>

<br />
<hr class="hr-text" data-content="スケジュール" />
<div id="schedules"></div>


</body>
</html>
