<?php

	header('Content-type:application/json;charset=utf-8');

	function _isCurl(){
	    return function_exists('curl_version');
	}

	function json_output($array) {
		return print_r( json_encode($array) );
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

	function saveFiles($req) {
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

		return json_decode($respsave, TRUE);
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
		if($_REQUEST['task'] === 'status') {

		}
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
			$offset = $obj['offset'];
			$token = $obj['token'];
			$countMax = $obj['countMax'];

			//File sets
			$folder = '../uploads/';
			$fileType = 'jpg';

			// return json_output( [$f, "run"] );

			if(_isCurl()) {
				//First get server url for upload photos
				$url = 'https://api.vk.com/method/photos.getUploadServer?group_id='.$destination_group.'&album_id='. $destination_album.'&access_token='.$token;

				$resp = file_get_contents( $url );
				$jsonArr = json_decode($resp, TRUE);

				if(isset($jsonArr['response']['upload_url'])) {
					//Load photos and create array for uploader
					$url = 'https://api.vk.com/method/photos.get?owner_id='.$group_id.'&album_id='.$album_id.'&offset='.$offset.'&count=5&access_token='.$token;
					$respLoad = file_get_contents( $url );
					$jsonArrLoad = json_decode($respLoad, TRUE);

					if(isset($jsonArrLoad['response'])) {
						// Parse images
						$files = [];
						$fileInit = array();
						$tmpName = 0;
						foreach($jsonArrLoad['response'] as $pic) {
							// $fileOrig = fopen($jsonArrLoad['response'][33]['src_big'], "r");
							//Get big original photo & caption text
							$file = [
								'link' => $pic['src_big'],
								'caption' => $pic['text'],
								'tempName' => $tmpName
							];
							array_push($files, $file);
							$tmpName++;
						}
						unset($pic);


						// Method for upload five photos per connect
						foreach($files as $file) {
							//Download file & save TEMP
							$furl = $file['link'];
							$fget = fopen($furl, 'r');
							$fname = $folder.$file['tempName'].".".$fileType;
							$f = file_put_contents($fname, $fget);
							fclose($fget);
							$fileInit[ 'file'.$file['tempName'] ] = '@'.realpath($fname);

							// Load temp
							// $fget = fopen($folder.$tmpName.".".$fileType, 'r');
							// $f = fstat($fget);

							// $cfile = curl_file_create(realpath($folder.$tmpName.".".$fileType), 'image/jpg', '0');
							// $cfile = new CURLFile('resource/test.png','image/png','testpic');
							// $imgdata = array('file' => $cfile);

							// fclose($fget);
							// return json_output([
							// 	$f,
							// 	$cfile
							// ]);

							// Put to next
							// file_put_contents($folder."1.".$fileType, $fget);
							

							// fclose($fget);
						}
						unset($file);

						$jsonArrLoad = sendFiles(stripslashes($jsonArr['response']['upload_url']), $fileInit);

						if(isset($jsonArrLoad['hash'])) {
							$fu = json_decode(stripslashes( $jsonArrLoad['photos_list']), TRUE);
							// return json_output($fu[0]);
							foreach($files as $file) {


								$req = array(
							    	'group_id' => intval($destination_group),
							    	'album_id' => intval($destination_album),
							    	'server' => $jsonArrLoad['server'],
							    	'photos_list' =>  $fu[$file['tempName']],
							    	'caption' => $file['caption'],
							    	'hash' => $jsonArrLoad['hash'],
							    	'access_token' => $token
						    	);

								if($file['tempName'] == 1) return json_output(
									$req
									);
								$jsonArrSave = saveFiles($req);
								// return json_output( $jsonArrSave );
								//Delete TEMP
								unlink($folder.$file['tempName'].".".$fileType);

								if(!isset($jsonArrSave['response'])) {
									return json_output( $jsonArrSave['response'] );
								}
							}
								// return json_output( [
								// 	$fu,
								// 	$files,
							 //    	'group_id' => $destination_group,
							 //    	'album_id' => $destination_album,
							 //    	'server' => $jsonArrLoad['server'],
							 //    	'photos_list' =>  $fu[$files[0]['tempName']],
							 //    	'caption' => $files[0]['caption'],
							 //    	'hash' => $jsonArrLoad['hash'],
							 //    	'access_token' => $token
								// 	] );

						}




/*
						// Method for upload one photos per connect
						$tmpName = 0;
						foreach($files as $file) {
							//Download file & save TEMP
							$furl = $file['link'];
							$fget = fopen($furl, 'r');
							$fname = $folder.$tmpName.".".$fileType;
							$f = file_put_contents($fname, $fget);
							fclose($fget);

							// Load temp
							// $fget = fopen($folder.$tmpName.".".$fileType, 'r');
							// $f = fstat($fget);

							// $cfile = curl_file_create(realpath($folder.$tmpName.".".$fileType), 'image/jpg', '0');
							// $cfile = new CURLFile('resource/test.png','image/png','testpic');
							// $imgdata = array('file' => $cfile);

							// fclose($fget);
							// return json_output([
							// 	$f,
							// 	$cfile
							// ]);

							// Put to next
							// file_put_contents($folder."1.".$fileType, $fget);
							


									$caption = $file['caption'];
									$fileInit = array(
								      'file' =>
								        '@' . realpath($fname)
								    );
									$jsonArrLoad = sendFiles(stripslashes($jsonArr['response']['upload_url']), $fileInit);

									if(isset($jsonArrLoad['hash'])) {
										//Delete TEMP
										unlink($fname);

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

										if(!isset($jsonArrSave['response'])) {
											return json_output( $jsonArrSave['response'] );
										}
									}



							// fclose($fget);
							$tmpName++;
						}
						unset($file);*/
						

						// return json_output([
						// 	// $fileOrig,
						// 	$files,
						// 	$jsonArrLoad['response'],
						// ]);
					}




					return json_output( array(
						$url,
						$jsonArr,
						$jsonArrLoad,
						$jsonArrSave,
						'finish' => 'false',
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