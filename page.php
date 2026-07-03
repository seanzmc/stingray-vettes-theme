<?php
/**
 * Generic interior page — a base DS-styled wrapper for the content pages
 * (Order, Deposit, B&P, Calculator, @Factory, Process) that get built in
 * later passes.
 *
 * @package Stingray_Corvette
 */

get_header();
?>

<main class="sc-page">
	<div class="sc-page-inner">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h1 class="sc-page-title"><?php the_title(); ?></h1>
				<div class="sc-page-content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	</div>
</main>

<?php
get_footer();
