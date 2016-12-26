<?include "core.php";?>

<div class="panel">
  <div class="row">
    <div class="col-md-12">
      <a href="http://cint.dev" class="btn btn-success" style="margin: 30px auto">Main page</a>
      <center>
        <h3><?=$app['name'];?></h3>
        <div class="ver"><?=$app['version'];?></div>
      </center>
    </div>
  </div>
  <div class="row">
    <div class="insta col-md-6">
      <!-- Instagram API -->
      <h3 class="lead">Instagram</h3>
      <a ng-click="instagram.Authorize()" ng-if="!auth.insta.token">Authorize</a>
      <div  ng-if="auth.insta.token">
        <a class="" ng-click="instagram.Authorize()">
          <i class="glyphicon glyphicon-refresh"></i> refresh
        </a>
        <a class="" ng-click="instagram.Exit()">
          <i class="glyphicon glyphicon-log-out"></i> exit
        </a>
      </div>

      <insta-panel></insta-panel>

    </div>

    <div class="vk col-md-6">
      <!-- VK API -->
      <h3 class="lead">VK</h3>
      <a ng-click="VK.Authorize()" ng-if="!auth.vk.token">Authorize</a>
      <div ng-if="auth.vk.token">
        <a class="" ng-click="VK.Authorize()">
          <i class="glyphicon glyphicon-refresh"></i> refresh
        </a>
        <a class="" ng-click="VK.Exit()">
          <i class="glyphicon glyphicon-log-out"></i> exit
        </a>
      </div>

      <vk-panel></vk-panel>

    </div>
  </div>


</div>