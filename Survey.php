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

echo 'worked';
echo $favoritefood;
echo $alergy;
}
$fb = new Facebook\Facebook([
		  'app_id' => '727092907434360',
		  'app_secret' => 'c67e09be0ca2199cf4da15486f074fd2',
		  'default_graph_version' => 'v2.5',
		]);
echo
$request = $fb->get('/me');
$post_message = ['link' => 'https://carpres1.herokuapp.com/'];
$post_request = $fb->post('/me/feed', $post_message);

?>
