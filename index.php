<?php
 //Core v0.1 alpha
 include_once 'settings.php';
 $DEBUG = FALSE;
?>

<!DOCTYPE html>
<html ng-app="cint">
	<head>
		<title>Cint - demo page</title>
		<meta charset="utf-8">
    <base href="/">

		<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
      .debug {
        background: #ccc;
        color: #777;
        padding: 20px;
      }
    </style>
	</head>
	<body>

   <!-- Libs -->
 	 <script type="text/javascript" src="node_modules/angular/angular.min.js"></script>
   <script type="text/javascript" src="node_modules/angular-route/angular-route.min.js"></script>
   <script type="text/javascript" src="app.js"></script>
   <!-- <script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
 	 <script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script> -->

	 <div class="container">
		<a href="http://cint.dev"><h1> <i class="glyphicon glyphicon-inbox"></i> Cint - content integrator</h1></a>

    <div class="panel" ng-controller="authorize">

      <?if($plugins['insta']['enabled']):?>
        <div class="insta" ng-if="!auth.insta">
          <?$currentPlugin = $plugins['insta'];?>
          <!-- Instagram API -->
          <?include_once $currentPlugin['config'];?>
          <h3>Instagram</h3>
          <a href="https://api.instagram.com/oauth/authorize/?client_id=cb2e702fde06407da2bfeb9ffdb6618f&redirect_uri=http://cint.dev&response_type=token&scope=basic">Authorize</a>
        </div>
        <!-- Library -->
        <script type="text/javascript" src="<?=$currentPlugin['libs'];?>"></script>
      <?endif;?>

    </div>

    <div class="load" ng-if="auth.insta" ng-controller="objects">
      <a class="" ng-click="view()">
        load
      </a>
    </div>


    <?if($DEBUG):?>
      <div class="debug">
        <p>Plugins:</p>
        <p>
          <?var_dump( $plugins );?>
        </p>
      </div>
    <?endif;?>
	 </div>

	</body>
</html>
