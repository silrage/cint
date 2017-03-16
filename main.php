<?include "src/core.php";?>

<div class="jumbotron">
  <h1> <i class="glyphicon glyphicon-inbox"></i> <?=$app['name'];?> </h1>
  <p><?=$app['description'];?></p>
  <p>
    <a class="btn btn-primary btn-lg" href="panel" role="button">Panel</a>
  </p>
  <div class="ver"><?=$app['version'];?></div>
</div>
