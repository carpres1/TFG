<?php
	if (isset($_POST['Submit1'])) {
		$selected_radio1 = $_POST['favoritefood'];
		$selected_radio2 = $_POST['favoritefood'];
		if (!isset($_POST['alergy'])){
	    		$selected_radio3 ="null";
	    	}else{
			$selected_radio3 = $_POST['alergy'];
		}
		$selected_radio4 = $_POST['Cfriend'];
		$selected_radio5 = $_POST['Ffood1'];
		
	}
	for($i=1; $i<6; $i++){
		$answer='$selected_radio'.$i;
		echo $answer;
	}

	session_start();
	require_once __DIR__ . '/src/Facebook/autoload.php';
			$fb = new Facebook\Facebook([
			  'app_id' => '727092907434360',
			  'app_secret' => 'c67e09be0ca2199cf4da15486f074fd2',
			  'default_graph_version' => 'v2.5',
			]);
			$helper = $fb->getCanvasHelper();
			$permissions = ['email','publish_actions','user_friends','user_hometown','user_location', 'user_birthday']; // optionnal
			try {
				if (isset($_SESSION['facebook_access_token'])) {
				$accessToken = $_SESSION['facebook_access_token'];
				} else {
			  		$accessToken = $helper->getAccessToken();
				}
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
			 	// When Graph returns an error
			 	echo 'Graph returned an error: ' . $e->getMessage();
			  	exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
			 	// When validation fails or other local issues
				echo 'Facebook SDK returned an error: ' . $e->getMessage();
			  	exit;
			 }
			if (isset($accessToken)) {
				if (isset($_SESSION['facebook_access_token'])) {
					$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
				} else {
					$_SESSION['facebook_access_token'] = (string) $accessToken;
				  	// OAuth 2.0 client handler
					$oAuth2Client = $fb->getOAuth2Client();
					// Exchanges a short-lived access token for a long-lived one
					$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
					$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
					$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
				}
		try{
			$request = $fb->get('/me');

			$profile_request = $fb->get('/me?fields=name,first_name,last_name,email,gender,hometown,location');
			$profile_response = $profile_request->getGraphNode()->asArray();
			print_r($profile_response);
			//$post_message = ['link' => 'https://carpres1.herokuapp.com/'];
			//$post_request = $fb->post('/me/feed', $post_message);

		} catch(Facebook\Exceptions\FacebookResponseException $e) {
				// When Graph returns an error
				echo 'Graph returned an error: ' . $e->getMessage();
				unset($_SESSION['facebook_access_token']);
				echo "<script>window.top.location.href='https://apps.facebook.com/'</script>";
				exit;
			}
		/*// connect
			$m = new MongoClient();
			// select a database
			$db = $m->tfg; 
			// select a collection (analogous to a relational database's table)
			$collection = $db->users;
			$document = array( "code" =>"" "name" => "", "surname" => "", "gender" => "", "email" => "", "location" =>"", "hometown" =>"", "qfavorite" =>"", "qrestriction" =>"" , "qalergy" =>"", "qfriend" =>array(), "qfood1" =>array());
$collection->insert($document);
			*/

	} else {
				$helper = $fb->getRedirectLoginHelper();
				$loginUrl = $helper->getLoginUrl('https://apps.facebook.com/getting_meaty/', $permissions);
				echo "<script>window.top.location.href='".$loginUrl."'</script>";
			}
?>
