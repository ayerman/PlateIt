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
  var name = document.getElementById("username").value;
  if(comment && name) {
    $.ajax ({
      type: 'post',
      url: 'post_comment.php',
      data: {
         user_comm:comment,
	     user_name:name
      },
      success: function (response) {
	    document.getElementById("all_comments").innerHTML=response+document.getElementById("all_comments").innerHTML;
	    document.getElementById("comment").value="";
        document.getElementById("username").value="";
       }
    });
  }
  
  return false;
}

</script>

<div class="site-index">

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


<!--------------------------------------------------------------------->
<div class="well">
  <form method='post' action="" onsubmit="return post();">
  <textarea id="comment" class="form-control" rows="3" placeholder="How was it?.... "></textarea>
  <br>
 <input type="hidden" id="username" value="<?php  ?>">
  <br>
  <input type="submit" class="btn btn-primary" value="Post Comment">
  </form>
</div>


  <div id="all_comments">
	
	
  <?php
    $host="localhost";
    $username="root";
    $password="";
    $databasename="sample";

    $connect=mysql_connect($host,$username,$password);
    $db=mysql_select_db($databasename);
  
    $comm = mysql_query("select name,comment,post_time from comments order by post_time desc");
    while($row=mysql_fetch_array($comm)){
	  $name=$row['name'];
	  $comment=$row['comment'];
      $time=$row['post_time'];
    ?>
	
	<div class="comment_div"> 
	  <p class="name"><?php echo $name;?> says:</p>
      <p class="comment"><?php echo $comment;?></p>	
	  <p class="time"><?php echo $time;?></p>
	</div>
  
    <?php
    }
    ?>
  </div>
<!--------------------------------------------------------------------->


    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>


    </div>
</div>