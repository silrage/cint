<div ng-if="auth.instagram">
  <div class="insta">
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
      <select ng-model="action" ng-options="option.name for option in instagram.ListActions()">
        <option value="">----- Select action -----</option>
      </select>
      <button ng-click="instagram.Action(action)" class="btn btn-info">Action</button>
    </div>

    <div class="follows widget" ng-show="profile.insta.follows">
      <h3 class="title">Мои подписки</h3>
      <ul>
        <li ng-repeat="follows in profile.insta.follows">
          <div><img ng-src="{{follows.profile.profile_picture}}"/></div>
          <div> {{follows.username}}</div>
        </li>
      </ul>
    </div>

    <div class="subscribers widget" ng-show="profile.insta.followed_by">
      <h3 class="title">Мои подписчики</h3>
      <ul>
        <li ng-repeat="followed_by in profile.insta.followed_by | limitTo: followed_by.countFollowers">
          <div><img ng-src="{{followed_by.profile.profile_picture}}"/></div>
          <div> {{followed_by.username}}</div>
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
