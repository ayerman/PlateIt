<?php

/* @var $this yii\web\View */

$this->title = 'PlateIt - Menu Item';

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>


<script type="text/javascript" src="jquery.js">
function post()
{
  var comment = document.getElementById("comment").value;
  var id = document.getElementById("userid").value;
  if(comment && id) {
    $.ajax ({
      type: 'POST',
      url: 'post_comment.php',
      data: {
         user_comm:comment,
	     user_id:id
      },
      success: function (response) {
		  
       }
    });
	
  }
  
  return false;
}

</script>

<div class="site-index"  style="font-family: 'Palatino Linotype', 'Book Antiqua', Palatino, serif;">

    <div class="jumbotron">
        <div class="row">
            <div class="col-md-6">
                <?php header('Content-Type: ' . $item->imagetype);?>
				<?php echo '<img class="img-responsive" src="data:image/jpeg;base64,'. base64_encode( $item->image ).'"/>'; ?>
            </div>
            <!-- /.col-md-8 -->
            <div class="col-md-6">
                <h1><?php echo $item->name;?></h1>
                <p><?php echo $item->description;?></p>
            </div>
            <!-- /.col-md-4 -->
        </div>
    </div>

    <div class="body-content">
 <!-- Page Content -->
    
    <!-- /.container -->


<!--------------------------------------------------------------------->
<div class="well" style="text-align: center;">
  <form method='post' action="" onsubmit="return post();">
  <textarea id="comment" class="form-control" rows="3" placeholder="How was it?.... "></textarea>
  <br>
 <input type="hidden" id="userid" value="<?php echo $user['userid']; ?>">
  <br>
  <input type="submit" class="btn btn-secondary" style="" value="Tell Everyone!">
  </form>
</div>


  <div id="all_comments">
	
	
  <?php foreach($reviews as $review) {?>
	
	<div class="comment_div"> 
	  <p class="name"><?php echo $review->username;?> says:</p>
	  <p class="time"><?php echo $review->description;?></p>
	</div>
  
  <?php } ?>
  </div>
<!--------------------------------------------------------------------->


    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>


    </div>
</div>