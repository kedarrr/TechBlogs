<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

$get_id = $_GET['post_id'];

if(isset($_POST['delete'])){

   $p_id = $_POST['post_id'];
   $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
   $delete_image->execute([$p_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if($fetch_delete_image['image'] != ''){
      unlink('../uploaded_img/'.$fetch_delete_image['image']);
   }
   $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
   $delete_post->execute([$p_id]);
   header('location:view_posts.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>posts</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="read-post">

   <?php
      $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ? AND id = ?");
      $select_posts->execute([$admin_id, $get_id]);
      if($select_posts->rowCount() > 0){
         while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
            
            $post_id = $fetch_posts['id'];

   ?>
   <form method="post">
      <input type="hidden" name="post_id" value="<?= $post_id; ?>">
      <div class="status" style="background-color:<?php if($fetch_posts['status'] == 'active'){echo 'limegreen'; }else{echo 'coral';}; ?>;"><?= $fetch_posts['status']; ?></div>
      <?php if($fetch_posts['image'] != ''){ ?>
         <img src="../uploaded_img/<?= $fetch_posts['image']; ?>" class="image" alt="">
      <?php } ?>
      <div class="title"><?= $fetch_posts['title']; ?></div>
      <div class="content"><?= $fetch_posts['content']; ?></div>
      
      <div class="flex-btn">
         <button type="submit" name="delete" class="inline-delete-btn" onclick="return confirm('delete this post?');">delete</button>
         <a href="view_posts.php" class="inline-option-btn">go back</a>
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">no posts added yet! <a href="add_posts.php" class="btn" style="margin-top:1.5rem;">add post</a></p>';
      }
   ?>

</section>



<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>