<?php
 //Core v0.1 alpha
 include_once 'settings.php';
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Cint - demo page</title>
		<meta charset="utf-8">

		<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
	</head>
	<body>

	 <div class="container">
		<h1>Cint - content integrator</h1>

		<p>Plugins:</p>
		<p>
			<?=$insta;?>
		</p>
	 </div>

	 <!-- Libs -->
	 <script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
	 <script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

	</body>
</html>