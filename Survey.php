<?php

require_once __DIR__ . '/src/Facebook/autoload.php';
		$fb = new Facebook\Facebook([
		  'app_id' => '727092907434360',
		  'app_secret' => 'c67e09be0ca2199cf4da15486f074fd2',
		  'default_graph_version' => 'v2.5',
		]);
$accessToken = $helper->getAccessToken();
echo "here";
$request = $fb->get('/me');
$post_message = ['link' => 'https://carpres1.herokuapp.com/'];
$post_request = $fb->post('/me/feed', $post_message);

?>
