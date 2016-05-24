
<div class="panel" ng-controller="authorize">

  <?if($plugins['insta']['enabled']):?>
    <div class="insta col-md-4" ng-if="!profile.insta">
      <?$currentPlugin = $plugins['insta'];?>
      <!-- Instagram API -->
      <?include_once $currentPlugin['config'];?>
      <h3 class="lead">Instagram</h3>
      <a href="https://api.instagram.com/oauth/authorize/?client_id=cb2e702fde06407da2bfeb9ffdb6618f&redirect_uri=http://cint.dev&response_type=token&scope=basic+comments+public_content+follower_list+relationships+likes ">Authorize</a>
    </div>
    <!-- Library -->
    <script type="text/javascript" src="<?=$currentPlugin['libs'];?>"></script>
  <?endif;?>


  <div class="vk col-md-4" ng-if="settings.vk">
    <!-- Instagram API -->
    <h3 class="lead">VK</h3>
    <a ng-click="vk.Authorize()">Authorize</a>
  </div>


</div>

<div class="load" ng-if="auth.insta" ng-controller="objects">
  <div class="insta">
    <a class="" ng-click="view(auth.token)">
      <i class="glyphicon glyphicon-refresh"></i> refresh
    </a>
    <a class="" ng-click="exit()">
      <i class="glyphicon glyphicon-log-out"></i> exit
    </a>
    <div class="profile row bg-info">
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

    <div class="jumbotron">
      <button ng-click="action(auth.token)" class="btn btn-info">Action</button>
      <div class="subscribers" ng-show="profile.insta.followed_by">
        <div class="title">Мои подписчики</div>
        <ul>
          <li ng-repeat="followed_by in profile.insta.followed_by | limitTo: followed_by.countFollowers">
            <div><img ng-src="{{followed_by.profile_picture}}"/></div>
            <div> {{followed_by.username}}</div>
          </li>
        </ul>
      </div>
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