<?php include "admin/includes/admin_header.php";?>
<?php include "includes/db.php";?>

    <!-- Navigation -->
    <?php include "includes/navigation.php";?>

    <!-- Page Content -->
    <div class="container">

        <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-md-8">

                <?php
                if(isset($_GET['category'])) {
                    $post_category_id = $_GET['category'];
                    // Prepared statements. First parameter is connection and second is SQL statement with placeholders
                    if(is_admin($_SESSION['username'])) {
                        // assign prepared statement to stmt1 and stmt2
                        $stmt1 = mysqli_prepare($connection, "SELECT post_id, post_title, post_author, post_date, post_image, post_content FROM posts WHERE post_category_id = ?");
                    } else {
                        $stmt2 = mysqli_prepare($connection, "SELECT post_id, post_title, post_author, post_date, post_image, post_content FROM posts WHERE post_category_id = ? AND post_status = ? ");
                        // Prepared statements don't take strings so assigned post status to published variable.
                        $published = 'published';
                    }

                    if(isset($stmt1)){
                        // Stmt1 is cast and bound to an integer
                        mysqli_stmt_bind_param($stmt1, "i", $post_category_id);
                        // execute statement above
                        mysqli_stmt_execute($stmt1);
                        // binding stmt1 to the variables
                        mysqli_stmt_bind_result($stmt1, $post_id, $post_title, $post_author, $post_date, $post_image, $post_content);
                        $stmt = $stmt1;
                    } else {
                        // integer is identified with an 'i' while strings are 's' as below
                        mysqli_stmt_bind_param($stmt2, "is", $post_category_id, $published);
                        mysqli_stmt_execute($stmt2);
                        mysqli_stmt_bind_result($stmt2, $post_id, $post_title, $post_author, $post_date, $post_image, $post_content);
                        $stmt = $stmt2;
                    }
                
                    //$query = " SELECT * FROM posts WHERE post_category_id = $post_category_id AND post_status = 'published' ";

                    if(mysqli_stmt_num_rows($stmt) === 0) {
                        echo "<h1 class='text-center'>NO CATEGORIES AVAILABLE!</h1>";
                    } 
                        while(mysqli_stmt_fetch($stmt)):
                            
                            ?>

<h1 class="page-header">
                    Page Heading
                    <small>Secondary Text</small>
                </h1>

                <!-- First Blog Post -->
                <h2>
                    <a href="post.php?p_id=<?php echo $post_id ?>"><?php echo $post_title ?></a>
                </h2>
                <p class="lead">
                    by <a href="./"><?php echo $post_author ?></a>
                </p>
                <p><span class="glyphicon glyphicon-time"></span> <?php echo $post_date ?></p>
                <hr>
                <img class="img-responsive" src="images/<?php echo $post_image;?>" alt="">
                <hr>
                <p><?php echo $post_content ?></p>
                <a class="btn btn-primary" href="#">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>

                <hr>

                <?php
                         endwhile; } else {
                            header("Location: ./");
                        }
                ?>


            </div>

            <!-- Blog Sidebar Widgets Column -->
            <?php include "includes/sidebar.php"; ?>

        </div>
        <!-- /.row -->

        <hr>

<?php include "includes/footer.php"; ?>
