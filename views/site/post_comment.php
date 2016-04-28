<?php
$host="localhost";
$username="root";
$password="";
$databasename="sample";

$connect=mysql_connect($host,$username,$password);
$db=mysql_select_db($databasename);



if(isset($_POST['user_comm']) && isset($_POST['user_name'])){
  $comment=$_POST['user_comm'];
  $name=$_POST['user_name'];
  $insert=mysql_query("insert into comments values('','$name','$comment',CURRENT_TIMESTAMP)");
  
  $id=mysql_insert_id($insert);

  $select=mysql_query("select name,comment,post_time from comments where name='$name' and comment='$comment' and id='$id'");
  
  if($row=mysql_fetch_array($select)){
	  $name=$row['name'];
	  $comment=$row['comment'];
      $time=$row['post_time'];
  ?>
      <div class="comment_div"> 
	    <p class="name">Posted By:<?php echo $name;?></p>
        <p class="comment"><?php echo $comment;?></p>	
	    <p class="time"><?php echo $time;?></p>
	  </div>
  <?php
  }
exit;
}

?>