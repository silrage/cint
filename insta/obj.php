<?php

	function getInstaID($username, $token)
	{
	    $username = strtolower($username);
	    $url = "https://api.instagram.com/v1/users/search?q=".$username."&access_token=".$token;
	    $get = file_get_contents($url);
	    $json = json_decode($get);
	    foreach($json->data as $user)
	    {
	        if($user->username == $username)
	        {
	            return $user->id;
	        }
	    }
	    return $json;
	}
	function json_output($array) {
		return print_r( json_encode($array) );
	}

	header('Content-type:application/json;charset=utf-8');
	if(!$_REQUEST['url']) return json_output(array('error'=>TRUE, 'message'=>'Bad request'));
	if(!$_REQUEST['name']) {
		$resp = file_get_contents( $_REQUEST['url'] );
		$jsonArr = json_decode($resp);
		return array(json_output( $jsonArr ), $_REQUEST['url']);
	}
		else
	{
		$str = getInstaID( $_REQUEST['name'], $_REQUEST['access_token'] );
		return json_output( 
				array('id'=>$str, 'name'=>$_REQUEST['name'], 'access_token'=>$_REQUEST['access_token']) 
			);
	}

  ?>
