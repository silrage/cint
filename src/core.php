<?php
	/**
	 * Core app
	 * DON'T EDIT THIS CODE BUT THIS AUTOUPDATE VALUES
	 */

	header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    session_start();

	$app = [
		'name' => 'Sociaman - API fast media',
		'description' => 'Get you content faster and automatically!',
		'version' => 'v0.1 alpha',
	];
	$clean_path = substr( $_SERVER['REQUEST_URI'], 1, strpos(substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI'])), '/') );
	// var_dump();
	$base_dir = $_SERVER['DOCUMENT_ROOT'].'/'.$clean_path;


	/**
	 * Main processing functions and helpers
	 */
	function _isCurl(){
	    return function_exists('curl_version');
	}
	function output($msg) {
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<title>Error</title>
		</head>
		<body>
			<div class="header">
				<a href="/">HOME</a>
			</div>
			<div class="error"><?=$msg;?></div>
		</body>
		</html>
		<?php
		return;
	}
	function json_output($array) {
		header('Content-type:application/json;charset=utf-8');
		return print_r( json_encode($array) );
	}
	function http($url, $method="GET", $data=NULL) {
		$request = curl_init($url);
		curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($request, CURLOPT_POSTFIELDS, $data);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
		$resp = curl_exec($request);
		curl_close($request);
		return $resp;//[$url,$method,$data];//$resp;
	}
	function sendFiles($url, $file) {
		//Upload file to serv
		$request = curl_init( $url );
		curl_setopt($request, CURLOPT_POST, TRUE);
		curl_setopt(
		    $request,
		    CURLOPT_POSTFIELDS,
		    // $imgdata
		    // array(
		    //   'file' =>
		    //     '@' . $_FILES['file']['tmp_name']
   //    			. ';filename=' . $_FILES['file']['name']
   //    			. ';type='     . $_FILES['file']['type']
		    // )
		    $file
		);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
		$respload = curl_exec($request);
		curl_close($request);

		return json_decode($respload, TRUE);
	}



	/**
	 * Load custom configuration
	 */
	if(!is_file($base_dir.'/config.php')) {
		echo 'Приложение не инициализировано..';
	    die();
	}
	include $base_dir.'/config.php';

	define(front_page, $config['front_page']);
	define(DEBUG, $config['debug']);


	/**
	 * ========================================
	 * Main functions for get code and token
	 * ========================================
	 * 
	 * -First step for get params from query
	 * -Next step convert query for get great token
	 *
	 */
	$code = (isset($_REQUEST['code'])) ? $_REQUEST['code'] : FALSE;
	$vk = (isset($_SESSION['vk']['token'])) ? $_SESSION['vk'] : [];

	// VK auth
	if($code) {
		// Verify them
		if(strlen($code) > 8) {
			// var_dump(FALSE);
			$resp = json_decode(get_token(['name'=>'vk', 'code'=>$code]), TRUE);
			if(isset($resp['access_token'])) {
				$_SESSION['vk'] = [
					'token'=>$resp['access_token'],
					'expires'=>$resp['expires_in'],
					'uid'=>$resp['user_id']
				];
				// Clean url
				header('Location: http://dir.dev/cint/panel');
				return;
			}
		}
	}

	function get_token($plugin) {
		include 'config.php';
		if(is_array($plugin)) {
			if(!isset( $config['plugins'][$plugin['name']])) return ['status'=>FALSE, 'error'=>'No found plugin '.$plugin['name']];
			switch ($plugin['name']) {
				case 'vk':
					$cur_plugin = $config['plugins']['vk'];
					$redirect_uri = 'http://dir.dev/cint/panel';
					if(isset($plugin['code'])) {
						$auth = http('https://oauth.vk.com/access_token?client_id='.$cur_plugin['client_id'].'&client_secret='.$cur_plugin['client_secret'].'&redirect_uri='.$redirect_uri.'&code='.$plugin['code']);
						return $auth;
					}
				break;
			}
		}else{
			return ['status'=>FALSE, 'error'=>'Bad request'];
		}
	}

?>