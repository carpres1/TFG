<?php

$favoritefood = 0;

if (isset($_POST['Submit1'])) {
if (!isset($_POST['favoritefood'])){
    		echo "No Language gotten";
    	return;
				}
	$selected_radio = $_POST['favoritefood'];

	if ($selected_radio == '1') {

		$favoritefood = '1';

	}
	else if ($selected_radio == '2') {

		$favoritefood = 2;
		

	}
echo 'worked';
echo $favoritefood;
}

?>
