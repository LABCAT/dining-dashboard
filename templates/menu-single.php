<?php
get_header();

	while ( have_posts() ) :
		the_post();

		?>				
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/Menu">
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<?php include DD_PLUGIN_PATH . '/templates/entry-footer.php'; ?>

			</article>
		<?php 
	endwhile; 

get_footer();
