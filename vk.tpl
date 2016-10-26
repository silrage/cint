<div ng-if="auth.vk.token">
  <div class="vk">
    <div class="profile row bg-info">
      <div class="col-md-6">
        <img ng-src="{{profile.vk.photo_200}}" />
      </div>
      <div class="col-md-6">
        <p>
          <b>{{profile.vk.first_name}} {{profile.vk.last_name}}</b>
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
