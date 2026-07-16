<?php
/**
 * /build-and-price/ — Chevrolet Build & Price link-share surface (SiteWireframe item 4).
 *
 * Binds by slug: page-build-and-price.php ↔ the "build-and-price" page. The
 * Formidable form is rendered inside .sc-embed so assets/css/embeds.css can
 * skin it with the theme design system whether Formidable's own styling is
 * enabled or disabled.
 *
 * @package Stingray_Corvette
 */

$configurator_links = array(
	array(
		'label' => __( 'Stingray', 'stingray-corvette' ),
		'model' => __( '2027 Corvette', 'stingray-corvette' ),
		'url'   => 'https://www.chevrolet.com/shopping/configurator/performance/2027/corvette/corvette',
	),
	array(
		'label' => __( 'Grand Sport', 'stingray-corvette' ),
		'model' => __( '2027 Corvette', 'stingray-corvette' ),
		'url'   => 'https://www.chevrolet.com/shopping/configurator/performance/2027/corvette/corvette-gs',
	),
	array(
		'label'  => __( 'Grand Sport X', 'stingray-corvette' ),
		'model'  => __( '2027 Corvette', 'stingray-corvette' ),
		'status' => __( 'Coming soon', 'stingray-corvette' ),
	),
	array(
		'label' => __( 'Z06', 'stingray-corvette' ),
		'model' => __( '2027 Corvette', 'stingray-corvette' ),
		'url'   => 'https://www.chevrolet.com/shopping/configurator/performance/2027/corvette/corvette-z06',
	),
	array(
		'label' => __( 'ZR1', 'stingray-corvette' ),
		'model' => __( '2027 Corvette', 'stingray-corvette' ),
		'url'   => 'https://www.chevrolet.com/shopping/configurator/performance/2027/corvette/corvette-zr1',
	),
	array(
		'label' => __( 'ZR1X', 'stingray-corvette' ),
		'model' => __( '2027 Corvette', 'stingray-corvette' ),
		'url'   => 'https://www.chevrolet.com/shopping/configurator/performance/2027/corvette/corvette-zr1x',
	),
);

get_header();
?>

<main class="sc-page">
	<div class="sc-page-inner">
		<header class="sc-section">
			<p class="sc-page-eyebrow"><?php esc_html_e( 'Share Your Build', 'stingray-corvette' ); ?></p>
			<h1 class="sc-page-title"><?php esc_html_e( 'Build & Price Share', 'stingray-corvette' ); ?></h1>
			<p class="sc-page-lede"><?php esc_html_e( 'Use Chevrolet’s online configurator, copy your six-digit build code from the summary page, and send it to our Corvette team so we can review the exact configuration with you.', 'stingray-corvette' ); ?></p>
		</header>

		<?php echo stingray_corvette_render_page_prose(); ?>

		<section class="sc-section" aria-labelledby="configurator-heading">
			<h2 id="configurator-heading" class="sc-section-title"><?php esc_html_e( 'Start with Chevrolet Build & Price', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'Open the Chevrolet configurator for the Corvette model you want, complete your build, then return here with the build code from the summary page.', 'stingray-corvette' ); ?></p>

			<div class="sc-grid">
				<?php foreach ( $configurator_links as $configurator_link ) : ?>
					<?php if ( ! empty( $configurator_link['url'] ) ) : ?>
					<a class="sc-link-card" href="<?php echo esc_url( $configurator_link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<span>
							<span class="sc-link-eyebrow"><?php echo esc_html( $configurator_link['model'] ); ?></span>
							<span class="sc-link-title"><?php echo esc_html( $configurator_link['label'] ); ?></span>
						</span>
						<span class="sc-link-arrow" aria-hidden="true">&#8599;</span>
					</a>
					<?php else : ?>
					<div class="sc-link-card sc-link-card--disabled" aria-disabled="true">
						<span>
							<span class="sc-link-eyebrow"><?php echo esc_html( $configurator_link['model'] ); ?></span>
							<span class="sc-link-title"><?php echo esc_html( $configurator_link['label'] ); ?></span>
						</span>
						<span class="sc-link-status"><?php echo esc_html( $configurator_link['status'] ); ?></span>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="build-code-heading">
			<h2 id="build-code-heading" class="sc-section-title"><?php esc_html_e( 'Find your build code', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'On the Chevrolet summary page, look near the top under the “Your 2027 Corvette” heading. Copy the six-digit build code and paste it into the form below.', 'stingray-corvette' ); ?></p>

			<div class="sc-note sc-note--info">
				<strong><?php esc_html_e( 'What to copy:', 'stingray-corvette' ); ?></strong>
				<span><?php esc_html_e( 'Copy only the six-digit build code. The Share button can also display a copy action for the same code.', 'stingray-corvette' ); ?></span>
			</div>

			<div class="sc-grid">
				<div class="sc-card-panel">
					<h3 class="sc-card-panel-title"><?php esc_html_e( 'Summary page location', 'stingray-corvette' ); ?></h3>
					<ul>
						<li><?php esc_html_e( 'Look near the top of the Chevrolet Build & Price summary page.', 'stingray-corvette' ); ?></li>
						<li><?php esc_html_e( 'Find the six-digit code under the “Your 2027 Corvette” heading.', 'stingray-corvette' ); ?></li>
						<li><?php esc_html_e( 'Your screen layout may differ depending on device size and Chevrolet’s current configurator layout.', 'stingray-corvette' ); ?></li>
					</ul>
				</div>

				<div class="sc-card-panel">
					<h3 class="sc-card-panel-title"><?php esc_html_e( 'Share panel copy action', 'stingray-corvette' ); ?></h3>
					<ul>
						<li><?php esc_html_e( 'The Share button may open a panel with a copy control for the same build code.', 'stingray-corvette' ); ?></li>
						<li><?php esc_html_e( 'Copy the code, return to this page, and paste it into the form below.', 'stingray-corvette' ); ?></li>
						<li><?php esc_html_e( 'If you cannot find the code, submit the closest available Chevrolet link and add a note for our Corvette team.', 'stingray-corvette' ); ?></li>
					</ul>
				</div>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="build-share-form-heading">
			<h2 id="build-share-form-heading" class="sc-section-title"><?php esc_html_e( 'Send us your build code', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'Paste your build code and contact information below. We’ll use it to view your exact configuration and help with the next steps.', 'stingray-corvette' ); ?></p>
			<div class="sc-embed">
				<?php echo stingray_corvette_render_page_embed_shortcode(); ?>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
