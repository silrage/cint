<?php
	header('Content-type:application/json;charset=utf-8');

	function json_output($array) {
		return print_r( json_encode($array) );
	}

	if(!$_REQUEST['url']) return json_output(array('error'=>TRUE, 'message'=>'Bad request'));
	if(!$_REQUEST['save']) {
		//Main loader
		$resp = file_get_contents( $_REQUEST['url'] );
		$jsonArr = json_decode($resp, TRUE);
		return array(json_output( $jsonArr ), $_REQUEST['url']);
	}
		else
	{
		//File sets
		$folder = '../uploads/';
		$fileType = 'jpg';
		$zipType = 'zip';
		$files = [];
		$i = 0;
		$save = json_decode($_REQUEST['save']);

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

?>