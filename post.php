<?php include "includes/header.php";?>
<?php include "includes/db.php";?>

    <!-- Navigation -->
    <?php include "includes/navigation.php";?>

    <?php 
        if(isset($_POST['liked'])){
            $post_id = $_POST['post_id'];
            $user_id = $_POST['user_id'];

            // fetch post
            $query = "SELECT * FROM posts WHERE post_id=$post_id";
            $postResult = mysqli_query($connection, $query);
            $post = mysqli_fetch_array($postResult);
            $likes = $post['likes'];

            // update post with likes
            mysqli_query($connection, "UPDATE posts SET likes=$likes+1 WHERE post_id=$post_id");

            // like for post
            mysqli_query($connection, "INSERT INTO likes(user_id, post_id) VALUES($user_id, $post_id)");
            exit();
        }
    ?>

    <?php
        if(isset($_POST['unliked'])){
            $post_id = $_POST['post_id'];
            $user_id = $_POST['user_id'];

            // fetch post
            $query = "SELECT * FROM posts WHERE post_id=$post_id";
            $postResult = mysqli_query($connection, $query);
            $post = mysqli_fetch_array($postResult);
            $likes = $post['likes'];

            // delete likes from post
            mysqli_query($connection, "DELETE FROM likes WHERE post_id=$post_id AND user_id=$user_id");

            // delete likes from post
            mysqli_query($connection, "UPDATE posts SET likes=$likes-1 WHERE post_id=$post_id");
            exit();
        }
    ?>

    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-md-8">

                <?php
                    if(isset($_GET['p_id'])){
                        $the_post_id = $_GET['p_id'];

                        $view_query = "UPDATE posts SET post_views_count = post_views_count + 1 WHERE post_id = $the_post_id";
                        $send_query = mysqli_query($connection, $view_query);

                        if(!$send_query) {
                            die("Query Failed");
                        }

                        if(isset($_SESSION['user_role']) && ($_SESSION['user_role'] == 'admin')) {
                            $query = " SELECT * FROM posts WHERE post_id = $the_post_id ";
                        } else {
                            $query = " SELECT * FROM posts WHERE post_id = $the_post_id AND post_status = 'published' ";
                        }

                        $select_all_posts = mysqli_query($connection, $query);

                        if(mysqli_num_rows($select_all_posts) < 1) {
                            echo "<h1 class='text-center'>NO POSTS AVAILABLE!</h1>";
                        } else {
                    
                        //$select_all_posts = mysqli_query($connection, $query);
                        while($row = mysqli_fetch_assoc($select_all_posts)){
                            $post_id = $row['post_id'];
                            $post_title = $row['post_title'];
                            $post_author = $row['post_author'];
                            $post_date = $row['post_date'];
                            $post_image = $row['post_image'];
                            $post_content = $row['post_content'];
                            
                ?>

                            <!-- First Blog Post -->
                            <h1 class="page-header">
                            Posts
                            </h1>
                            <h2>
                                <a href="/cms"><?php echo $post_title ?></a>
                            </h2>
                            <p class="lead">
                                by <small><a href="/cms/authorpost"><?php echo $post_author ?></a></small>
                            </p>
                            <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date ?></p>
                            <hr>
                            <img class="img-responsive" src="/cms/images/<?php echo imgPlaceholder($post_image); ?>" alt="">
                            <hr>
                            <p><?php echo $post_content ?></p>

                            <?php 
                                if(isLoggedIn()){ ?>
                                <div class="row">
                                <p class="pull-right">
                                    <a href="" class="<?php echo userLikedPost($the_post_id) ? 'unlike' : 'like'?>">
                                        <span class="glyphicon glyphicon-thumbs-up"
                                        data-toggle="tooltip" 
                                        data-placement="top" 
                                        title="<?php echo userLikedPost($the_post_id) ? ' You liked this already' : ' Care to like it?'; ?>"></span>
                                        <?php echo userLikedPost($the_post_id) ? ' Unlike' : ' Like'; ?>
                                    </a>
                                </p>
                                </div>
                              <?php  } else { ?>
                                <div class="row">
                                <p class="pull-right"><a href="/cms/login">Login Here</a> to like.
                                </p>
                                </div>

                              <?php }
                            ?>

                            <hr>
                            
                            <div class="row">
                                <p class="pull-right">Likes:<?php getPostLikes($the_post_id);?> </p>
                            </div>

                            <div class="clearfix"></div>

<?php
     } 
?>

<!-- Blog Comments -->

<?php 
    if(isset($_POST['create_comment'])){
        $the_post_id = $_GET['p_id'];
        $comment_author = $_POST['comment_author'];
        $comment_email = $_POST['comment_email'];
        $comment_content = $_POST['comment_content'];

        if(!empty($comment_author) && !empty($comment_email) && !empty($comment_content)){

            $query = "INSERT INTO comments (comment_post_id, comment_author, comment_email, comment_content, comment_status, comment_date)";
    
            $query .= "VALUES ($the_post_id, '{$comment_author}', '{$comment_email}', '{$comment_content}', 'unapproved', now())";
    
    
            $create_comment_query = mysqli_query($connection, $query);
    
            if(!$create_comment_query) {
                die('Query Failed' .mysqli_error($connection));
            }
    
            // $query = "UPDATE posts SET post_comment_count = post_comment_count + 1 ";
            // $query .= "WHERE post_id = $the_post_id ";
            // $update_comment_count = mysqli_query($connection, $query);

            
        } else {
            echo "<script>alert('Fields cannot be empty')</script>";
        }


    }
?>

<!-- Comments Form -->
<div class="well">
    <h4>Leave a Comment:</h4>
    <form action="" method="post" role="form">
        <div class="form-group">
        <label for="author">Author</label>
           <input class="form-control" name="comment_author" type="text" name="comment_author">
        </div>
        <div class="form-group">
        <label for="author">Email</label>
           <input class="form-control" name="comment_email" type="email" name="comment_email">
        </div>
        <div class="form-group">
        <label for="comment">Your Comment</label>
            <textarea name="comment_content" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" name="create_comment" class="btn btn-primary">Submit</button>
    </form>
</div>

<hr>

<!-- Posted Comments -->
<?php
    $query = "SELECT * FROM comments WHERE comment_post_id = {$the_post_id} ";
    $query .= "AND comment_status = 'approved' ";
    $query .= "ORDER BY comment_id DESC ";
    $select_comment_query = mysqli_query($connection, $query);
    if(!$select_comment_query){
        die('Query Failed' . mysqli_error($connection));
    }
    while ($row = mysqli_fetch_assoc($select_comment_query)){
        $comment_date = $row['comment_date'];
        $comment_content = $row['comment_content'];
        $comment_author = $row['comment_author'];
        ?>
        <!-- Comment -->
    <div class="media">
        <a class="pull-left" href="#">
            <img class="media-object" src="http://placehold.it/64x64" alt="">
        </a>
    <div class="media-body">
        <h4 class="media-heading"><?php echo $comment_author; ?>
            <small><?php echo $comment_date; ?></small>
        </h4>
        <?php echo $comment_content;?>
    </div>
</div>



        <?php
    } } } else {
        redirect('/');
    }
?>

</div>

    <!-- Blog Sidebar Widgets Column -->
    <?php include "includes/sidebar.php"; ?>

</div>
<!-- /.row -->

<hr>

<?php include "includes/footer.php"; ?>
<script>
    $(document).ready(function(){
        $("[data-toggle='tooltip']").tooltip();
        var post_id = <?php echo $the_post_id; ?>;
        var user_id = <?php echo loggedInUserId(); ?>;
        // Like function
        //tooltip for thumbsup and down
        $('.like').click(function(){
            $.ajax({
                url: "/cms/post.php?p_id=<?php echo $the_post_id; ?>",
                type: 'post',
                data: {
                    'liked': 1,
                    'post_id': post_id,
                    'user_id': user_id,
                }
            });
        });
        // unlike function
        $('.unlike').click(function(){
            $.ajax({
                url: "/cms/post.php?p_id=<?php echo $the_post_id; ?>",
                type: 'post',
                data: {
                    'unliked': 1,
                    'post_id': post_id,
                    'user_id': user_id,
                }
            });
        });
    });

</script>