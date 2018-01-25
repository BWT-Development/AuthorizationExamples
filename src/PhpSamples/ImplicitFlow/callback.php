<?php

$token = 	$_POST['access_token'];	// access token to call the api
$state = 	$_POST['state'];		// state sent on authorization request, to be validated

if ($state != "asdf1234") {
	echo "invalid authorization!"; // authorizaion response from server not from our session
}
else
{
	// token should be stored in secure cookie,
	// in this sample just redirect to index providing the token via parameter
	echo '<script type="text/javascript">'
		. '	setTimeout(function() {'
		. '		location.href = "index.php?token=' . $token . '";'
		. '	}, 1000);'
		. '</script>';
}
?>
