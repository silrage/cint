

  <div class="insta col-md-4">
    <!-- Instagram API -->
    <h3 class="lead">Instagram</h3>
    <a ng-click="instagram.Authorize()" ng-if="!auth.instagram.token">Authorize</a>
    <div  ng-if="auth.instagram.token">
      <a class="" ng-click="instagram.Authorize()">
        <i class="glyphicon glyphicon-refresh"></i> refresh
      </a>
      <a class="" ng-click="instagram.Exit()">
        <i class="glyphicon glyphicon-log-out"></i> exit
      </a>
    </div>
  </div>

  <div class="vk col-md-4">
    <!-- VK API -->
    <h3 class="lead">VK</h3>
    <a ng-click="vk.Authorize()" ng-if="!auth.vk.token">Authorize</a>
    <div ng-if="auth.vk.token">
      <a class="" ng-click="vk.Authorize()">
        <i class="glyphicon glyphicon-refresh"></i> refresh
      </a>
      <a class="" ng-click="vk.Exit()">
        <i class="glyphicon glyphicon-log-out"></i> exit
      </a>
    </div>
  </div>

</div>

<insta-panel></insta-panel>
<vk-panel></vk-panel>