<?php include "includes/header.php";?>
<?php include "includes/db.php";?>


<?php 
// echo loggedInUserId();

if(userLikedPost(1)){
    echo "post was liked";
} else{
    echo "post is not liked";
}



?>