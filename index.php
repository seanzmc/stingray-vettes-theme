<?php
/**
 * Fallback template. Every real surface has a more specific template
 * (front-page.php, page.php); this renders whatever else WordPress routes here.
 *
 * @package Stingray_Corvette
 */

get_header();
?>

<main class="sc-page">
	<div class="sc-page-inner">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<h1 class="sc-page-title"><?php the_title(); ?></h1>
					<div class="sc-page-content"><?php the_content(); ?></div>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<h1 class="sc-page-title"><?php esc_html_e( 'Nothing here', 'stingray-corvette' ); ?></h1>
			<p class="sc-page-content"><?php esc_html_e( 'The page you are looking for does not exist.', 'stingray-corvette' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
