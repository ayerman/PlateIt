<?php

if(isset($_POST['user_comm']) && isset($_POST['user_id'])){
  ?>
      <div class="comment_div"> 
	    <p class="name">Posted By:<?php echo $_POST['user_id'];?></p>
        <p class="comment"><?php echo $_POST['user_comm'];?></p>	
	    
	  </div>
  <?php
  }
exit;
}

?>