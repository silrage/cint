<?php
  /**
    ************** Checklist: **************************
    * -View all followed_by & follows
    * X-Search followed_by users for changed profile
    * -Search users from HASHTAG
    * -Search users from GEO
    * -Search users from LIKES & COMMENTS in MEDIA
    * UPDATE POPULARITY
    * -Set like
    * -Set comment
    * -Follow
    ****************************************************
    */
  
  include "core.php";
?>

<!DOCTYPE html>
<html ng-app="cint">
	<head>
		<title>Sociaman - web app</title>
		<meta charset="utf-8">
    <base href="/">

		<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/styles.min.css">
	</head>
	<body>

   <!-- Libs -->
 	 <script type="text/javascript" src="node_modules/angular/angular.min.js"></script>
   <script type="text/javascript" src="node_modules/angular-route/angular-route.min.js"></script>
   <script type="text/javascript" src="src/app.js"></script>
   <!-- <script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
 	 <script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script> -->

	 <div class="container" ng-controller="getOBJ">

    <div ng-view id="content">
      Init page..
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
