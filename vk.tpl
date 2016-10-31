<div ng-if="auth.vk.token">
  <div class="vk">
    <div class="profile row bg-info">
      <div class="col-md-5">
        <img ng-src="{{profile.vk.photo_200}}" />
      </div>
      <div class="col-md-7">
        <p>
          <b>{{profile.vk.first_name}} {{profile.vk.last_name}}</b>
        </p>
        <p>
          <b>id: {{profile.vk.uid}}</b>
        </p>

        <div class="info">
          <p ng-if="!tasks.vk.active">Нет активных заданий</p>
          <p ng-if="tasks.vk.active">Выполняется {{tasks.vk.active}}</p>
        </div>
      </div>
    </div>

    <div class="jumbotron">
      <select ng-model="action" ng-options="option for option in vkListActions">
        <option value="">----- Select action -----</option>
      </select>
      <div class="field-group">
        <div
          class="field"
          ng-if="
          action == 'get_albums' || 
          action == 'get_photos' || 
          action == 'get_comments' ||
          action == 'save_photos' "
          >
          <input type="text" ng-model="VK.Fields.group" placeholder="Group ID" />
        </div>
        <div
          class="field"
          ng-if="action == 'get_photos' || action == 'save_photos' "
          >
          <input type="text" ng-model="VK.Fields.album" placeholder="Album ID" />
        </div>
        <div
          class="field"
          ng-if="action == 'get_comments' "
          >
          <input type="text" ng-model="VK.Fields.photo" placeholder="Photo ID" />
        </div>
      </div>
      <button ng-click="VK.Action(action)" class="btn btn-info">Action</button>

      <div class="separator"></div>

      <div class="result">
        {{VK.Result}}
      </div>
    </div>

  </div>
</div>
