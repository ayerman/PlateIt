<?php

/* @var $this yii\web\View */

$this->title = 'PlateIt - Restaurant';
?>

<div class="site-index">

    <div class="jumbotron">
        <div class="row">
            <div class="col-md-6">
                <?php header('Content-Type: ' . $retail->imagetype);?>
				<?php echo '<img class="img-responsive" src="data:image/jpeg;base64,'. base64_encode( $retail->image ).'"/>'; ?>
            </div>
            <!-- /.col-md-8 -->
            <div class="col-md-6">
				<h1><?php echo $retail->name;?></h1>
                <p><?php echo $retail->description;?></p>
            </div>
            <!-- /.col-md-4 -->
        </div>
    </div>

    <div class="body-content">


<div>
    <p>
        <h3>Address : <?php echo $retail->address;?></h3>
        <h3>Phone Number: <?php echo $retail->phonenumber;?></h3>
        <h3>Email: <?php echo $retail->email;?></h3>
    </p>
</div>



 <!-- Page Content -->
    <div class="container">

        <!-- Page Header -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Menu
                    <small>Let the world know!</small>
                </h1>
            </div>
        </div>

        <div class="row">
            <?php foreach($items as $item){?>
            <div class="col-md-4 portfolio-item">
                <a href="#">
                <?php header('Content-Type: ' . $item->imagetype);?>
				<?php echo '<img class="img-responsive" src="data:image/jpeg;base64,'. base64_encode( $item->image ).'"/>'; ?>
                    <!--<img class="img-responsive" src="http://placehold.it/700x400" alt="">-->
                </a>
                <h3>
                    <a href="<?php echo  Yii::$app->request->baseUrl;?>/site/menuitem?id=<?php echo $item->id . '"'; ?>><?php  echo $item->name; ?></a>
                </h3>
                <p><?php  echo $item->description; ?></p>
            </div>
            <?php } ?>
        </div>

    </div>


    </div>
</div>