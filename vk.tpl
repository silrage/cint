<div ng-if="auth.vk.token">
  <div class="vk">
    <div class="profile row bg-info">
      <div class="col-md-6">
        <img ng-src="{{profile.vk.profile_picture}}" />
      </div>
      <div class="col-md-6">
        <h3>{{profile.vk.username}}</h3>
        <p>
          <b>{{profile.vk.full_name}}</b>
        </p>
      </div>
    </div>

    <!-- <div class="jumbotron">
      <select ng-model="action" ng-options="option for option in vkListActions">
        <option value="">----- Select action -----</option>
      </select>
      <button ng-click="vk.Action(action)" class="btn btn-info">Action</button>
    </div> -->

    </div>

  </div>
</div>
