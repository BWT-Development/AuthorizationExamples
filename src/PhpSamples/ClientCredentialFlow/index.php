<?php
	// oauth constants
	$tokenEndpoint 			= "/token";
	$authorizeEndpoint		= "/authorize";
	
	// oauth client credential flow
	$grantType 				= "client_credentials";
	$clientSecret			= "<client_secret>";

	// enviroment variables
	$authBaseUrl			= "<openid_provider_url>";
	$authPath				= "<openid_connect_endpoint>";
	$apiBaseUrl				= "<api_url>";
	$clientId 				= "<client_id>";
	$scope					= "<scopes>";

	// api endpoints
	$apiOperation 			= "<api_operation_endpoint>";
	$apiPostData			= "<post_data>";
	
	try
	{
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $authBaseUrl . $authPath . $tokenEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, "grant_type=$grantType&client_id=$clientId&client_secret=$clientSecret&scope=$scope");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$headers = array();
		$headers[] = "Content-Type: application/x-www-form-urlencoded";
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$authorizationResponse = json_decode(curl_exec($curl));

		if (curl_errno($curl))
		{
			echo "Error: " . curl_error($curl);
		}
		else
		{
			curl_close($curl);
			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $apiBaseUrl . $apiOperation);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $apiPostData);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

			$headers = array();
			$headers[] = "Accept: application/json";
			$headers[] = "Authorization: Bearer ". $authorizationResponse->access_token;
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


			$apiResponse = curl_exec($curl);
			
			if (curl_errno($curl))
			{
				echo "Error: " . curl_error($curl);
			}
			else
			{
				echo "<p>Api successfully called</p>";
				echo "Json response: " . json_encode($apiResponse, JSON_PRETTY_PRINT);
			}
		}

		curl_close($curl);
	}
	catch(Exception $ex)
	{
		echo "Exception: " . $ex->getMessage();
	}
?>