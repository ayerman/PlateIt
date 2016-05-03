<?php

/* @var $this yii\web\View */

$this->title = 'PlateIt - DashBoard';
?>
<div class="site-index">

    <div class="body-content">

           <!-- Page Content -->
    <div class="container">

        <!-- Jumbotron Header -->
        <header class="jumbotron hero-spacer">
            <h1>PlateIt</h1>
            <p>Browse and rate restaurants' food and drink items. We encourage you to try new restaurants or dishes, maybe you will find a new favorite!
            </p>
        </header>

        <hr>


<div class="panel-group">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" href="#collapse1"><h3>Account Information</h3></a>
      </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse">
      <div class="panel-body">
        <p><h4>Name : <?php echo $loginUser->username; ?></h4></p>
        <p><h4>Email : <?php echo $loginUser->email; ?></h4></p>
        <p><h4>Address : <?php echo $loginUser->address; ?></h4></p>
        <p><h4>Phone Number : <?php echo $loginUser->phonenumber; ?></h4></p>
      </div>
      <div class="panel-footer"></div>
    </div>
  </div>
</div>

        <!-- Title -->
        <div class="row">
            <div class="col-lg-12">
                <h3>Suggested Spots</h3>
            </div>
        </div>
        <!-- /.row -->

        <!-- Page Features -->
        <div class="row text-center">
            <?php foreach($model as $item){?>
            <div class="col-md-3 col-sm-6 hero-feature">
                <div class="thumbnail">
					<?php header('Content-Type: ' . $item->imagetype);?>
					<?php echo '<img class="img-responsive" src="data:image/jpeg;base64,'. base64_encode( $item->image ).'"/>'; ?>
                    <div class="caption">
                        <h3><?php echo $item->name; ?></h3>
                        <p><?php echo $item->description; ?></p>
                        <p>
                            <a href="<?php echo  Yii::$app->request->baseUrl;?>/site/restaurant?id=<?php echo $item->userid; ?>" class="btn btn-default">Learn More</a>
                        </p>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <hr>

    </div>

    </div>
</div>