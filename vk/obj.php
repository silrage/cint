<?php
	include "../src/core.php";

	/**
	 * VK.plugin
	 */
	define( client_secret, $config['plugins']['vk']['client_secret']);
	define( client_id, $config['plugins']['vk']['client_id']);

	function getMaxImageSRC($obj) {
		$src = ""; //Link return
		if(isset($obj['photo_75'])) $src = $obj['photo_75'];
		if(isset($obj['photo_130'])) $src = $obj['photo_130'];
		if(isset($obj['photo_604'])) $src = $obj['photo_604'];
		if(isset($obj['photo_807'])) $src = $obj['photo_807'];
		if(isset($obj['photo_1280'])) $src = $obj['photo_1280'];
		if(isset($obj['photo_2560'])) $src = $obj['photo_2560'];
		return $src;
	}

	
	function cleanText($str) {
		if(stripos($str, '<br>')) 
		return $str;
	}


	// if(!$_REQUEST['url']) return json_output(array('error'=>TRUE, 'message'=>'Bad request'));
	// if(!isset($_REQUEST['task'])) {
	// 	//Main loader
	// 	$resp = file_get_contents( $_REQUEST['url'] );
	// 	$jsonArr = json_decode($resp, TRUE);
	// 	return array(json_output( $jsonArr ), $_REQUEST['url']);
	// }
		// else
	// {

	if(!isset($_REQUEST['task'])) return output('Bad request');
	$task = $_REQUEST['task'];
	run($task);

	function run($task) {
		switch ($task) {
			case 'status':
				$app = ['status'=>TRUE];
				return json_output($app);
			break;

			case 'auth':
				$url = 'https://oauth.vk.com/authorize?client_id='.client_id.'&redirect_uri='.base_path.'/panel&scope=photos';
				return json_output(['status'=>TRUE,'url'=>$url]);
			break;

			case 'logout':
				session_destroy();
				return json_output(['status'=>TRUE,'out'=>$_SESSION]);
			break;

			case 'custom':
				$url = $_REQUEST['url'];
				return json_output(['status'=>TRUE,'out'=>json_decode(http($url),TRUE),'url'=>$url]);
			break;

			case 'upload':
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
			break;

			case 'copy':
				$obj = json_decode($_REQUEST['obj'], TRUE);
				$group_id = $obj['group_id'];
				$album_id = $obj['album_id'];
				$destination_group = $obj['destination_group'];
				$destination_album = $obj['destination_album'];
				$offset = $obj['offset'];
				$token = $obj['token'];

				//File sets
				$folder = '../uploads/';
				$fileType = 'jpg';
				$arrFilesLoaded = [];

				// return json_output( [
				// 	$obj,
				// 	'count' => 5,
				// 	"run"
				// ]);
				if(_isCurl()) {
					//First get server url for upload photos
					$url = 'https://api.vk.com/method/photos.getUploadServer?group_id='.$destination_group.'&album_id='. $destination_album.'&access_token='.$token;

					
					$resp = file_get_contents( $url );

					if(!isset($resp)) return json_output(['NO MEIDA']);
					$jsonArr = json_decode($resp, TRUE);

					if(isset($jsonArr['response']['upload_url'])) {
						//Load photos and create array for uploader
						$url = 'https://api.vk.com/method/photos.get?owner_id='.$group_id.'&album_id='.$album_id.'&offset='.$offset.'&count=5&v=5.6&access_token='.$token;
						$respLoad = file_get_contents( $url );
						$jsonArrLoad = json_decode($respLoad, TRUE);
						// return json_output( $jsonArrLoad['response']['items'] );
						if(isset($jsonArrLoad['response'])) {
							// Parse images
							$files = [];
							$caption = "";
							foreach($jsonArrLoad['response']['items'] as $pic) {
								// $fileOrig = fopen($jsonArrLoad['response'][33]['src_big'], "r");
								//Get big original photo & caption text
								$file = [
									// 'link' => $pic['src_big'],
									'link' => getMaxImageSRC($pic),
									// 'caption' => $pic['text'],
								];
								$caption = $file['caption'];
								array_push($files, $file);
							}

							// Method for upload five photos per connect
							$tmpName = 0;
							foreach($files as $file) {
								//Download file & save TEMP
								$furl = $file['link'];
								$fget = fopen($furl, 'r');
								$fname = $folder.$tmpName.".".$fileType;
								$f = file_put_contents($fname, $fget);
								array_push($arrFilesLoaded, $fname);
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
								

								// fclose($fget);
								$tmpName++;
							}
							unset($file);

							$fileInit = array(
						    	'file1' => curl_file_create(realpath($folder."0.".$fileType), 'image/jpeg','0.jpg'),
						    	'file2' => curl_file_create(realpath($folder."1.".$fileType), 'image/jpeg','1.jpg'),
						    	'file3' => curl_file_create(realpath($folder."2.".$fileType), 'image/jpeg','2.jpg'),
						    	'file4' => curl_file_create(realpath($folder."3.".$fileType), 'image/jpeg','3.jpg'),
						    	'file5' => curl_file_create(realpath($folder."4.".$fileType), 'image/jpeg','4.jpg'),
						    );
							$jsonArrLoad = sendFiles(stripslashes($jsonArr['response']['upload_url']), $fileInit);

							// return json_output( [$fileInit, is_file(realpath($folder."0.".$fileType)), $jsonArrLoad ] );

							if(isset($jsonArrLoad['hash'])) {
								//Delete TEMP
								foreach($arrFilesLoaded as $name) {
									unlink($name);
								}

								$req = array(
							    	'group_id' => $destination_group,
							    	'album_id' => $destination_album,
							    	'server' => $jsonArrLoad['server'],
							    	'photos_list' =>  $jsonArrLoad['photos_list'],
							    	// 'caption' => $caption,
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
							'saved' => TRUE,
							'count' => count($arrFilesLoaded),
							// 'pushed' => , // For save locally temp files
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
			break;

			case 'save':
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
			break;

			default:
				return json_output('Bad request');
			break;
			
		}
	}


?>