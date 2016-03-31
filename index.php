<html>
	<body>
		<?php
		session_start();
		require_once __DIR__ . '/src/Facebook/autoload.php';
		$fb = new Facebook\Facebook([
		  'app_id' => '727092907434360',
		  'app_secret' => 'c67e09be0ca2199cf4da15486f074fd2',
		  'default_graph_version' => 'v2.5',
		]);
		$helper = $fb->getCanvasHelper();
		$permissions = ['email','publish_actions','user_friends','user_hometown','user_location']; // optionnal
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
			// validating the access token
			try {
				$request = $fb->get('/me');
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				// When Graph returns an error
				if ($e->getCode() == 190) {
					unset($_SESSION['facebook_access_token']);
					$helper = $fb->getRedirectLoginHelper();
					$loginUrl = $helper->getLoginUrl('https://apps.facebook.com/getting_meaty/', $permissions);
					echo "<script>window.top.location.href='".$loginUrl."'</script>";
					exit;
				}
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				// When validation fails or other local issues
				echo 'Facebook SDK returned an error: ' . $e->getMessage();
				exit;
			}
			// getting basic info about user
			try {
				$profile_request = $fb->get('/me?fields=name,first_name,last_name,email,gender,hometown,location');
				$profile_response = $profile_request->getGraphNode()->asArray();

				$request_friends = $fb->get('/me/taggable_friends?fields=name,id&limit=5000');
				$friends = $request_friends->getGraphEdge();

				$post_message = ['link' => 'https://carpres1.herokuapp.com/'];
				$post_request = $fb->post('/me/feed', $post_message);

			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				// When Graph returns an error
				echo 'Graph returned an error: ' . $e->getMessage();
				unset($_SESSION['facebook_access_token']);
				echo "<script>window.top.location.href='https://apps.facebook.com/'</script>";
				exit;
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

			$favoritefood= $timesout= $allergies= $fishtimes= 0;
			$favoritefoodErr=""; 

			if (empty($_POST["¿Cuál de las siguientes opciones es tu comida favorita?"])) {
    				 $favoritefoodErr = "Has de responder cual es tu comida favorita";
   			} else {
     				$favoritefood = test_input($_POST["¿Cuál de las siguientes opciones es tu comida favorita?"]);
   			}
			echo count($allFriends);
			

			// priting basic info about user on the screen
			print_r($profile_response);
		  	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
		} else {
			$helper = $fb->getRedirectLoginHelper();
			$loginUrl = $helper->getLoginUrl('https://apps.facebook.com/getting_meaty/', $permissions);
			echo "<script>window.top.location.href='".$loginUrl."'</script>";
		}
		?>
		<p>¿Cuál de las siguientes opciones es tu comida favorita?</p>
		<input type="radio" name="gender" <?php if (isset($favoritefood) && $favoritefood=="1") echo "checked";?>  value="female">Italiana
		<br></br>
		<input type="radio" name="gender" <?php if (isset($favoritefood) && $favoritefood=="2") echo "checked";?>  value="female">Española
		<br></br>
		<input type="radio" name="gender" <?php if (isset($favoritefood) && $favoritefood=="3") echo "checked";?>  value="female">Asiática
		<br></br>
   		<input type="radio" name="gender" <?php if (isset($favoritefood) && $favoritefood=="4") echo "checked";?>  value="male">Fast Food
		<br></br>
		<input type="submit" name="Finalizar" value="Submit"> 
		<?php
			if($favoritefood!=0){
				echo $favoritefood;
			}else{echo $favoritefoodErr;}
		?>
	</body>
</html>
