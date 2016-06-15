<html>
	<head>
		<style>

		.body {
			background:url(img/m2.jpg) no-repeat center center fixed;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
		}
				
		</style>
	</head>
	<body class="body">
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

				$request_friends = $fb->get('/me/taggable_friends?fields=user&limit=5000');
				$friends = $request_friends->getGraphEdge();

				#$post_message = ['link' => 'https://carpres1.herokuapp.com/'];
				#$post_request = $fb->post('/me/feed', $post_message);

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


			// priting basic info about user on the screen
			#print_r($profile_response);
		  	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
		} else {
			$helper = $fb->getRedirectLoginHelper();
			$loginUrl = $helper->getLoginUrl('https://apps.facebook.com/getting_meaty/', $permissions);
			echo "<script>window.top.location.href='".$loginUrl."'</script>";
		}
		?>
		<FORM method ="post" action ="Survey.php">
			
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
			<script language="javascript">
					var question=0;
					var variable=["favoritefood", "restriction", "alergy"];
				$(document).ready(function(){
					$("input[type=Submit]").hide();
					$("#back").click(function(){
						if(question>0){
							var q1=document.getElementById("question"+question)
							var q0=document.getElementById("question"+(question-1))
							question=question-1;
  							q1.style.display="none";
							q0.style.display="block";
						}else{
							window.alert("No es posible retroceder más.");
						}
   					});

					$("#next").click(function(){
						if($('input[name='+variable[question]+']').is(':checked')){
							var q1=document.getElementById("question"+question)
							var q2=document.getElementById("question"+(question+1))
							question=question+1;
		       					q1.style.display="none";
							q2.style.display="block";
							if(question==(variable.length-1)){
								var buttonback=document.getElementById("back");
								var buttonnext=document.getElementById("next");
								//buttonback.style.display="none";
								//buttonnext.style.display="none";
								$("input[type=Submit]").show();
							}
						}else{
								window.alert("Para avanzar es necesario dar una resputesta.")
						}
   					});
					
				});
			</script>
			<div id='question0' align="center">
			<p><strong>¿Cuál de los siguientes tipos de comida es tu favorita?</strong></p>
			<Input type = 'Radio' Name ='favoritefood' value= '1'>Italiana
			<br></br>
			<Input type = 'Radio' Name ='favoritefood' value= '2'>Española
			<br></br>
			<Input type = 'Radio' Name ='favoritefood' value= '3'>Asiática
			<br></br>
			<Input type = 'Radio' Name ='favoritefood' value= '4'>Latina
			<br></br>
			<Input type = 'Radio' Name ='favoritefood' value= '5'>Árabe
			<br></br>
			<Input type = 'Radio' Name ='favoritefood' value= '6'>Comida Rápida
			</div>

			<div id='question1' style="display: none" align="center">
			<p><strong>¿En cuál de estos grupos te definirías?</strong></p>
			<Input type = 'Radio' Name ='restriction' value= '1'>omnívoro
			<br></br>
			<Input type = 'Radio' Name ='restriction' value= '2'>Vegetariano
			<br></br>
			<Input type = 'Radio' Name ='restriction' value= '3'>Vegano
			</div>

			<div id='question2' style="display: none" align="center">
			<p><strong>¿Eres alergico/sufres intolerancia a alguno de estos alimentos?</strong></p>
			<Input type = 'checkbox' Name ='alergy' value= '1'>Italiana
			<br></br>
			<Input type = 'checkbox' Name ='alergy' value= '2'>Española
			<br></br>
			<Input type = 'checkbox' Name ='alergy' value= '3'>Asiática
			<br></br>
			<Input type = 'checkbox' Name ='alergy' value= '4'>Comida Rápida
			<br></br>
			</div>

			<div align="center"><Input type = "Submit" Name = "Submit1" VALUE = "Finalizar"></div>
				

		</FORM>
		<button id="back" align="center">Atrás</button>
		<button id="next" align="center">Siguiente</button>

	</body>
</html>
