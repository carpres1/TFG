<?php

session_start();
		require_once __DIR__ . '/src/Facebook/autoload.php';
		$fb = new Facebook\Facebook([
		  'app_id' => '727092907434360',
		  'app_secret' => 'c67e09be0ca2199cf4da15486f074fd2',
		  'default_graph_version' => 'v2.5',
		]);
		$helper = $fb->getCanvasHelper();
try {
$request_friends = $fb->get('/me/taggable_friends?fields=user&limit=5000');
$friends = $request_friends->getGraphEdge();
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
	exit;
}
//As the page of friends hasa maximumthis methods gets all the friends in the same array
if ($fb->next($friends)) {
	$allFriends = array();
	$friendsArray = $friends->asArray();
	while ($friends = $fb->next($friends)) {
		$friendsArray = $friends->asArray();
		$allFriends = array_merge($friendsArray, $allFriends);
	}
} else {
	$allFriends = $friends->asArray();
}
 echo $allFriends;			

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

?>
