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
      a {
        cursor: pointer;
      }
      .gallery {}
      .gallery .image-item {
        float: left;
        position: relative;
      }
      .gallery .image-item .mouse-in {
        position: absolute;
        background: rgba(0,0,0, .3);
        width: 100%;
        height: 100%;
        opacity: 0;
      }
      .gallery .image-item .mouse-in i {
        position: absolute;
        margin: auto;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        width: 20px;
        height: 20px;
        color: #fff;
        font-size: 20px;
      }
      .gallery .image-item:hover .mouse-in {
        opacity: 1;
      }
      .gallery .image-item .counts {
        background: rgba(0,0,0, .6);
        position: absolute;
        top: 0;
      }
      .gallery .image-item .counts > div {
        float: left;
        color: #fff;
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
        <div class="insta" ng-if="!profile.insta">
          <?$currentPlugin = $plugins['insta'];?>
          <!-- Instagram API -->
          <?include_once $currentPlugin['config'];?>
          <h3>Instagram</h3>
          <a href="https://api.instagram.com/oauth/authorize/?client_id=cb2e702fde06407da2bfeb9ffdb6618f&redirect_uri=http://cint.dev&response_type=token&scope=basic+public_content+follower_list+relationships+likes ">Authorize</a>
        </div>
        <!-- Library -->
        <script type="text/javascript" src="<?=$currentPlugin['libs'];?>"></script>
      <?endif;?>

    </div>

    <div class="load" ng-if="auth.insta" ng-controller="objects">
      <div class="insta">
        <a class="" ng-click="view()">
          <i class="glyphicon glyphicon-refresh"></i> refresh
        </a>
        <a class="" ng-click="exit()">
          <i class="glyphicon glyphicon-log-out"></i> exit
        </a>
        <div class="profile row">
          <div class="col-md-6">
            <img ng-src="{{profile.insta.profile_picture}}" />
          </div>
          <div class="col-md-6">
            <h3>{{profile.insta.username}}</h3>
            <p>
              <b>{{profile.insta.full_name}}</b>
            </p>
            <span>Публикаций {{profile.insta.counts.media}}</span>
            <span>Подписчиков {{profile.insta.counts.followed_by}}</span>
            <span>Подписки: {{profile.insta.counts.follows}}</span>
          </div>
        </div>

        <div class="subscribers">
          <ul>
            <li ng-repeat="followed_by in profile.insta.followed_by | limitTo: followed_by.countFollowers">
              {{followed_by.name}}
            </li>
          </ul>
        </div>

        <div class="gallery">
          <h3>Популярное (более 7 лайков)</h3>
          <div class="image-item" ng-repeat="image in gallery | limitTo: gallery.countImages">
            <a href="{{image.link}}" target="_blank">
              <div class="mouse-in">
                <i class="glyphicon glyphicon-new-window"></i>
              </div>
              <img ng-src="{{image.images[gallery.size].url}}" />
            </a>
            <div class="counts">
              <div class="likes">
                <i class="glyphicon glyphicon-heart"></i>{{image.likes.count}}
              </div>
              <div class="comments">
                <i class="glyphicon glyphicon-comment">{{image.comments.count}}</i>
              </div>
            </div>
          </div>
        </div>
      </div>
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
