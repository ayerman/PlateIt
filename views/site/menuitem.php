<?php

/* @var $this yii\web\View */

$this->title = 'PlateIt - Menu Item';

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>



<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

<script>
function post()
{
  var comment = document.getElementById("comment").value;
  var id = document.getElementById("userid").value;
  var itid = document.getElementById("itemid").value;
  if(comment && id && itid) {
    $.ajax ({
      type: 'post',
      url: '<?php echo Yii::$app->request->baseUrl. '/site/addreview' ?>',
      data: {
         user_comm:comment,
	     user_id:id,
		 item_id:itid,
          _csrf : '<?=Yii::$app->request->getCsrfToken()?>'
      },
      success: function (response) {
          alert(response.comment);
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
<div class="well" style="text-align: center;">
  <textarea id="comment" class="form-control" rows="3" placeholder="How was it?.... "></textarea>
  <br>
 <input type="hidden" id="userid" value="<?php echo $user['userid']; ?>">
  <br>
    <br>
 <input type="hidden" id="itemid" value="<?php echo $item['id']; ?>">
  <br>
  <input type="submit" onclick="post()" class="btn btn-secondary" style="" value="Tell Everyone!">
</div>


  <div id="all_comments">
	
	
  <?php foreach($reviews as $review) {?>
	
	<div class="comment_div"> 
	  <p class="name"><?php echo $review->reviewer;?> says:</p>
	  <p class="time"><?php echo $review->review;?></p>
	</div>
  
  <?php } ?>
  </div>


    </div>
</div>