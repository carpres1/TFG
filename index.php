<html>
	<head>
		<style>
			.error {color: #FF0000;}
		</style>
	</head>
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

			// The survey itself starts here with the radioButtons
			$favoritefood= $timesout= $allergies= $fishtimes= 0;
			$favoritefoodErr=""; 

						
			// define variables and set to empty values
			$nameErr = $emailErr = $genderErr = $websiteErr = "";
			$name = $email = $gender = $comment = $website = "";

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
			   if (empty($_POST["name"])) {
			     $nameErr = "Name is required";
			   } else {
			     $name = test_input($_POST["name"]);
			     // check if name only contains letters and whitespace
			     if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
			       $nameErr = "Only letters and white space allowed";
			     }
			   }
			  
			   if (empty($_POST["email"])) {
			     $emailErr = "Email is required";
			   } else {
			     $email = test_input($_POST["email"]);
			     // check if e-mail address is well-formed
			     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			       $emailErr = "Invalid email format";
			     }
			   }
			    
			   if (empty($_POST["website"])) {
			     $website = "";
			   } else {
			     $website = test_input($_POST["website"]);
			     // check if URL address syntax is valid (this regular expression also allows dashes in the URL)
			     if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
			       $websiteErr = "Invalid URL";
			     }
			   }

			   if (empty($_POST["comment"])) {
			     $comment = "";
			   } else {
			     $comment = test_input($_POST["comment"]);
			   }

			   if (empty($_POST["gender"])) {
			     $genderErr = "Gender is required";
			   } else {
			     $gender = test_input($_POST["gender"]);
			   }
			}

			function test_input($data) {
			   $data = trim($data);
			   $data = stripslashes($data);
			   $data = htmlspecialchars($data);
			   return $data;
			}
			

			// priting basic info about user on the screen
			#print_r($profile_response);
		  	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
		} else {
			$helper = $fb->getRedirectLoginHelper();
			$loginUrl = $helper->getLoginUrl('https://apps.facebook.com/getting_meaty/', $permissions);
			echo "<script>window.top.location.href='".$loginUrl."'</script>";
		}
		?>
		<p><span class="error">* required field.</span></p>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		   Name: <input type="text" name="name" value="<?php echo $name;?>">
		   <span class="error">* <?php echo $nameErr;?></span>
		   <br><br>
		   E-mail: <input type="text" name="email" value="<?php echo $email;?>">
		   <span class="error">* <?php echo $emailErr;?></span>
		   <br><br>
		   Website: <input type="text" name="website" value="<?php echo $website;?>">
		   <span class="error"><?php echo $websiteErr;?></span>
		   <br><br>
		   Comment: <textarea name="comment" rows="5" cols="40"><?php echo $comment;?></textarea>
		   <br><br>
		   Gender:
		   <input type="radio" name="gender" <?php if (isset($gender) && $gender=="female") echo "checked";?>  value="female">Female
		   <input type="radio" name="gender" <?php if (isset($gender) && $gender=="male") echo "checked";?>  value="male">Male
		   <span class="error">* <?php echo $genderErr;?></span>
		   <br><br>
		   <input type="submit" name="submit" value="Submit">
		</form>

		<?php
		echo "<h2>Your Input:</h2>";
		echo $name;
		echo "<br>";
		echo $email;
		echo "<br>";
		echo $website;
		echo "<br>";
		echo $comment;
		echo "<br>";
		echo $gender;
		?>
	</body>
</html>
