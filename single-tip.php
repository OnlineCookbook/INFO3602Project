<?php
/**
 * single-tip.php
 * Single Tips & Tricks article — with optional YouTube embed.
 */



get_header();
the_post();

$tip_number  = get_post_meta(get_the_ID(), 'tip_number', true);
$tip_tag     = get_post_meta(get_the_ID(), 'tip_tag', true);
$youtube_url = get_post_meta(get_the_ID(), 'youtube_url', true);
$cats        = get_the_terms(get_the_ID(), 'tip_category');

function cookbook_get_youtube_embed_url($url) {
    if (empty($url)) return '';
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (strpos($url, '/embed/') !== false) {
        return $url;
    }
    return '';
}

$embed_url = cookbook_get_youtube_embed_url($youtube_url);
?>

<div class="page-banner">
    <div class="page-banner__bg-image"
         style="background-image: url(<?php echo get_theme_file_uri('images/tips-banner.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php the_title(); ?></h1>
        <div class="page-banner__intro">
            <p>Tips &amp; Tricks! Practical kitchen knowledge.</p>
        </div>
    </div>
</div>

<div class="container container--narrow page-section">

    <div class="metabox metabox--position-up metabox--with-home-link">
        <p>
            <a class="metabox__blog-home-link"
               href="<?php echo get_post_type_archive_link('tip'); ?>"> All Tips &amp; Tricks
            </a>
            <span class="metabox__main">
                <?php if (!empty($cats) && !is_wp_error($cats)) :
                    $cat_links = array();
                    foreach ($cats as $cat) {
                        $cat_links[] = '<a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a>';
                    }
                    echo 'In ' . implode(', ', $cat_links);
                endif; ?>
            </span>
        </p>
    </div>

    <?php if ($tip_number || $tip_tag) : ?>
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:18px;">
        <?php if ($tip_number) : ?>
            <span class="tip-badge tip-badge--number"><?php echo esc_html($tip_number); ?> Tips</span>
        <?php endif; ?>
        <?php if ($tip_tag) : ?>
            <span class="tip-badge tip-badge--tag"><?php echo esc_html($tip_tag); ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="metabox" style="margin-bottom:22px;">
        <p>By <strong><?php the_author(); ?></strong> &mdash; <?php echo get_the_date(); ?></p>
    </div>

    <?php if (has_post_thumbnail() && empty($embed_url)) : ?>
    <div style="text-align:center; margin-bottom:28px;">
        <?php the_post_thumbnail('large', array('style' => 'border-radius:18px; max-width:100%;')); ?>
    </div>
    <?php endif; ?>

    <?php if ($embed_url) : ?>
    <div class="tip-video-wrap">
        <iframe
            src="<?php echo esc_url($embed_url); ?>"
            title="<?php the_title_attribute(); ?>"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
    </div>
    <?php endif; ?>

    <div class="generic-content tip-body">
        <?php the_content(); ?>
    </div>

    <?php if (comments_open() || get_comments_number()) : ?>
        <div style="margin-top:40px;">
            <h3 style="margin-bottom:16px;">Share a comment or another tip</h3>
            <?php comments_template(); ?>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
