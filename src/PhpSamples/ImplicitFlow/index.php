<?php
	// oauth constants
	$authorizeEndpoint		= "/authorize";
	
	// oauth implicit flow
	$responseType			= "id_token token";
	$responseMode			= "form_post";
	$scope					= "openid";

	// enviroment variables
	$authBaseUrl			= "<openid_provider_url>";
	$authPath				= "<openid_connect_endpoint>";
	$apiBaseUrl				= "<api_url>";
	$clientId 				= "bwt.client.phpsample";
	$redirectUrl 			= "http://localhost:8080/callback.php";

	// api endpoints
	$apiGetOperation 		= "<api_operation>";
	
	// no token available -> request user authentication
	if (!isSet($_REQUEST["token"])) 
	{		
		// these should be generated
		$nonce = "A298AE";
		$state = "asdf1234";
		
		$authenticationRequest = $authBaseUrl . $authPath
			. $authorizeEndpoint
			. "?response_type=$responseType"
			. "&client_id=$clientId"
			. "&redirect_uri=$redirectUrl"
			. "&scope=$scope"
			. "&state=$state"
			. "&nonce=$nonce"
			. "&response_mode=$responseMode";

		// Redirect to authentication endpoint
		echo '<script type="text/javascript">'
			. '	setTimeout(function() {'
			. '		location.href = "' . $authenticationRequest . '";'
			. '	}, 1000);'
			. '</script>';
	}
	else
	{
		//CURL Variables for Api Call
		$headers = array();
		$headers[] = "Accept: application/json";
		$headers[] = "Authorization: Bearer " . $_REQUEST["token"];

		//CURL API Call
		$curlApi = curl_init();
		
		curl_setopt($curlApi, CURLOPT_URL, $apiBaseUrl . $apiGetOperation);
		curl_setopt($curlApi, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlApi, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($curlApi, CURLOPT_HTTPHEADER, $headers);
	
		$apiResponse = curl_exec($curlApi);
	
		if (curl_errno($curlApi)) 
		{
			echo "Error: " . curl_error($curlApi);
		}
		else
		{
			echo "<p>Api successfully called</p>";
			echo "Json response: " . json_encode($apiResponse, JSON_PRETTY_PRINT);
		}

		curl_close ($curlApi);
	}
?>
