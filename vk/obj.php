<?php

	header('Content-type:application/json;charset=utf-8');

	function _isCurl(){
	    return function_exists('curl_version');
	}

	function json_output($array) {
		return print_r( json_encode($array) );
	}

	if(!$_REQUEST['url']) return json_output(array('error'=>TRUE, 'message'=>'Bad request'));
	if(!isset($_REQUEST['task'])) {
		//Main loader
		$resp = file_get_contents( $_REQUEST['url'] );
		$jsonArr = json_decode($resp, TRUE);
		return array(json_output( $jsonArr ), $_REQUEST['url']);
	}
		else
	{
		if($_REQUEST['task'] === 'upload') {
			$group_id = $_REQUEST['group_id'];
			$album_id = $_REQUEST['album_id'];
			$token = $_REQUEST['token'];

			if(_isCurl()) {
				//First get server url
				$url = 'https://api.vk.com/method/photos.getUploadServer?group_id='.$group_id.'&album_id='. $album_id.'&access_token='.$token;

				$resp = file_get_contents( $url );
				$jsonArr = json_decode($resp, TRUE);

				if(isset($jsonArr['response']['upload_url'])) {
					//Upload file to serv
					$request = curl_init( stripslashes($jsonArr['response']['upload_url']) );
					curl_setopt($request, CURLOPT_POST, TRUE);
					curl_setopt(
					    $request,
					    CURLOPT_POSTFIELDS,
					    array(
					      'file' =>
					        '@' . $_FILES['file']['tmp_name']
		          			. ';filename=' . $_FILES['file']['name']
		          			. ';type='     . $_FILES['file']['type']
					    )
					);
					curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
					$respload = curl_exec($request);
					curl_close($request);

					$jsonArrLoad = json_decode($respload, TRUE);
					if(isset($jsonArrLoad['hash'])) {
						$req = array(
					    	'group_id' => $group_id,
					    	'album_id' => $album_id,
					    	'server' => $jsonArrLoad['server'],
					    	'photos_list' =>  $jsonArrLoad['photos_list'],
					    	'caption' => 'Test',
					    	'hash' => $jsonArrLoad['hash'],
					    	'access_token' => $token
				    	);
						//Save files
						$request = curl_init( 
							'https://api.vk.com/method/photos.save'
						);
						curl_setopt($request, CURLOPT_POST, TRUE
						);
						curl_setopt($request, CURLOPT_POSTFIELDS,
						    $req
						);
						curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
						$respsave = curl_exec($request);
						curl_close($request);
						$jsonArrSave = json_decode($respsave, TRUE);

						if(isset($jsonArrSave['response'])) {
							return json_output( $jsonArrSave['response'] );
						}
					}

					return json_output( array(
						$url,
						$jsonArr,
						$jsonArrLoad,
						$jsonArrSave,
					) );
				}else{
					return json_output( array(
						$url,
						$jsonArr
					) );
				}
				
			}else{
				return json_output(array('error'=>TRUE, 'message'=>'CURL is Disabled'));
			}
		}

		if($_REQUEST['task'] === 'copy') {
			$obj = json_decode($_REQUEST['obj'], TRUE);
			$group_id = $obj['group_id'];
			$album_id = $obj['album_id'];
			$destination_group = $obj['destination_group'];
			$destination_album = $obj['destination_album'];
			$token = $obj['token'];

			if(_isCurl()) {
				//First get server url for upload photos
				$url = 'https://api.vk.com/method/photos.getUploadServer?group_id='.$destination_group.'&album_id='. $destination_album.'&access_token='.$token;

				$resp = file_get_contents( $url );
				$jsonArr = json_decode($resp, TRUE);

				if(isset($jsonArr['response']['upload_url'])) {
					//Load photos and create array for uploader
					$url = 'https://api.vk.com/method/photos.get?owner_id='.$group_id.'&album_id='.$album_id.'&offset=0&count=100&access_token='.$token;
					$respLoad = file_get_contents( $url );
					$jsonArrLoad = json_decode($respLoad, TRUE);

					if(isset($jsonArrLoad['response'])) {
						
						return json_output($jsonArrLoad['response']);
						// fopen($fi, 'r');
					}




					$caption = "Test copy";
					//Upload file to serv
					$request = curl_init( stripslashes($jsonArr['response']['upload_url']) );
					curl_setopt($request, CURLOPT_POST, TRUE);
					curl_setopt(
					    $request,
					    CURLOPT_POSTFIELDS,
					    array(
					      'file' =>
					        '@' . $_FILES['file']['tmp_name']
		          			. ';filename=' . $_FILES['file']['name']
		          			. ';type='     . $_FILES['file']['type']
					    )
					);
					curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
					$respload = curl_exec($request);
					curl_close($request);

					$jsonArrLoad = json_decode($respload, TRUE);
					if(isset($jsonArrLoad['hash'])) {
						$req = array(
					    	'group_id' => $destination_group,
					    	'album_id' => $destination_album,
					    	'server' => $jsonArrLoad['server'],
					    	'photos_list' =>  $jsonArrLoad['photos_list'],
					    	'caption' => $caption,
					    	'hash' => $jsonArrLoad['hash'],
					    	'access_token' => $token
				    	);
						//Save files
						$request = curl_init( 
							'https://api.vk.com/method/photos.save'
						);
						curl_setopt($request, CURLOPT_POST, TRUE
						);
						curl_setopt($request, CURLOPT_POSTFIELDS,
						    $req
						);
						curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
						$respsave = curl_exec($request);
						curl_close($request);
						$jsonArrSave = json_decode($respsave, TRUE);

						if(isset($jsonArrSave['response'])) {
							return json_output( $jsonArrSave['response'] );
						}
					}

					return json_output( array(
						$url,
						$jsonArr,
						$jsonArrLoad,
						$jsonArrSave,
					) );
				}else{
					return json_output( array(
						$url,
						$jsonArr
					) );
				}
				
			}else{
				return json_output(array('error'=>TRUE, 'message'=>'CURL is Disabled'));
			}
		}

		if($_REQUEST['task'] === 'save') {
			//File sets
			$folder = '../uploads/';
			$fileType = 'jpg';
			$zipType = 'zip';
			$files = [];
			$i = 0;
			$save = json_decode($_REQUEST['obj']);

			if(is_array($save)) {
				foreach($save as $fi) {
					$i++;
					file_put_contents($folder.$i.".".$fileType, fopen($fi, 'r'));
					//Save files to archive
					array_push( $files, array('src'=>$folder.$i.".".$fileType, 'fname'=>$i.'.'.$fileType ) );
				}
			}else{
				file_put_contents($folder."1.".$fileType, fopen($save, 'r'));
				array_push( $files, array( 'src'=>$folder."00.".$fileType, 'fname'=>"00.".$fileType ) );
			}

			$zipname = $folder.'files.'.$zipType;
			$zip = new ZipArchive;
			$zip->open($zipname, ZipArchive::CREATE);
			foreach($files as $file) {
				$zip->addFile(
					$file['src'],
					$file['fname']
				);
			}
			$zip->close();

			//Delete original files
			$zi = 0;
			foreach ($files as $file) {
				$zi++;
				unlink($file['src']);
			}

			// header('Content-Description: File Transfer');
			// header('Content-Type: application/zip');
			// header('Content-disposition: attachment; filename=file.zip');
			// header('Content-Length: ' . filesize($zipname));
			// header('Expires: 0');
			// readfile($zipname);
			// exit;

			return array(json_output( 
				array(
					'first'=>$save[0],
					'archive_link'=>'/uploads/files.'.$zipType,
				) 
			) );
		}
	}

?>