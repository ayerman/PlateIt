<?php

/* @var $this yii\web\View */

$this->title = 'PlateIt - Menu Item';
?>

<div class="site-index">

    <div class="jumbotron">
        <div class="row">
            <div class="col-md-8">
                <img class="img-responsive img-rounded" src="http://placehold.it/900x350" alt="">
            </div>
            <!-- /.col-md-8 -->
            <div class="col-md-4">
                <h1><?php echo $item->name;?></h1>
                <p><?php echo $item->description;?></p>
            </div>
            <!-- /.col-md-4 -->
        </div>
    </div>

    <div class="body-content">
 <!-- Page Content -->
    <div class="container">
        <div class="row">
        <?php foreach($comments as $comment){?>
            <div class="col-md-12">
                <p><?php  echo $comment->user; ?></p>
                <p><?php  echo $comment->description; ?></p>
            </div>
        <?php } ?>
		</div>
    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>


    </div>
</div>