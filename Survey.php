<?php

$favoritefood = 0;
$alergy =0;

if (isset($_POST['Submit1'])) {
if (!isset($_POST['favoritefood'])){
    		echo "No Language gotten";
    	return;
				}
	$selected_radio = $_POST['favoritefood'];
	$selected_radio2 = $_POST['alergy'];

	if ($selected_radio == '1') {

		$favoritefood = '1';

	}
	if ($selected_radio2 == '2') {

		$alergy = 2;
		

	}
$conexion = new MongoDB\DriverMongoDB\Client("mongodb://192.168.1.103");
echo 'worked';
echo $favoritefood;
echo $alergy;
}

?>
