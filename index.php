<?php get_header(); ?>

<main>

    <h1>OnlineCookbook Blog</h1>

    <?php
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            ?>
            
            <div class="post">
                <h2>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h2>

                <p>
                    Posted on <?php the_time('F j, Y'); ?> 
                    by <?php the_author(); ?>
                </p>

                <div>
                    <?php the_excerpt(); ?>
                </div>

            </div>

            <?php
        }
    } else {
        echo "<p>No posts found.</p>";
    }
    ?>

</main>

<?php get_footer(); ?>