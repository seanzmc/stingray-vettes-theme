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
		'label' => __( 'Stingray Configurator', 'stingray-corvette' ),
		'model' => __( 'Corvette Stingray', 'stingray-corvette' ),
		'url'   => 'https://www.chevrolet.com/shopping/configurator/performance/2026/corvette/corvette/config',
	),
	array(
		'label' => __( 'E-Ray Configurator', 'stingray-corvette' ),
		'model' => __( 'Corvette E-Ray', 'stingray-corvette' ),
		'url'   => 'https://www.chevrolet.com/shopping/configurator/performance/2026/corvette/corvette-eray/config',
	),
	array(
		'label' => __( 'Z06 Configurator', 'stingray-corvette' ),
		'model' => __( 'Corvette Z06', 'stingray-corvette' ),
		'url'   => 'https://www.chevrolet.com/shopping/configurator/performance/2026/corvette/corvette-z06/config',
	),
	array(
		'label' => __( 'ZR1 Configurator', 'stingray-corvette' ),
		'model' => __( 'Corvette ZR1', 'stingray-corvette' ),
		'url'   => 'https://www.chevrolet.com/shopping/configurator/performance/2026/corvette/corvette-zr1/config',
	),
);

$summary_screenshot = 'https://stingraychevroletcorvette.com/wp-content/uploads/pictures/google-chrome-2026-corvette-stingray-summary-chevrolet-002093-09-06-1133am-598x1024.png';
$share_screenshot   = 'https://stingraychevroletcorvette.com/wp-content/uploads/pictures/google-chrome-2026-corvette-stingray-summary-chevrolet-002095-09-06-0127pm-1024x580.png';

get_header();
?>

<main class="sc-page">
	<div class="sc-page-inner">
		<header class="sc-section">
			<p class="sc-page-eyebrow"><?php esc_html_e( 'Share Your Build', 'stingray-corvette' ); ?></p>
			<h1 class="sc-page-title"><?php esc_html_e( 'Build & Price Share', 'stingray-corvette' ); ?></h1>
			<p class="sc-page-lede"><?php esc_html_e( 'Use Chevrolet’s online configurator, copy your six-digit build code from the summary page, and send it to our Corvette team so we can review the exact configuration with you.', 'stingray-corvette' ); ?></p>
		</header>

		<section class="sc-section" aria-labelledby="configurator-heading">
			<h2 id="configurator-heading" class="sc-section-title"><?php esc_html_e( 'Start with Chevrolet Build & Price', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'Open the Chevrolet configurator for the Corvette model you want, complete your build, then return here with the build code from the summary page.', 'stingray-corvette' ); ?></p>

			<div class="sc-grid">
				<?php foreach ( $configurator_links as $configurator_link ) : ?>
					<a class="sc-link-card" href="<?php echo esc_url( $configurator_link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<span>
							<span class="sc-link-eyebrow"><?php echo esc_html( $configurator_link['model'] ); ?></span>
							<span class="sc-link-title"><?php echo esc_html( $configurator_link['label'] ); ?></span>
						</span>
						<span class="sc-link-arrow" aria-hidden="true">&#8599;</span>
					</a>
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
				<figure class="sc-figure">
					<img src="<?php echo esc_url( $summary_screenshot ); ?>" alt="<?php esc_attr_e( 'Example Chevrolet Build & Price summary page showing where the build code appears.', 'stingray-corvette' ); ?>">
					<figcaption><?php esc_html_e( 'The build code appears near the top of the Chevrolet Build & Price summary page. Your screen layout may differ.', 'stingray-corvette' ); ?></figcaption>
				</figure>

				<figure class="sc-figure">
					<img src="<?php echo esc_url( $share_screenshot ); ?>" alt="<?php esc_attr_e( 'Example Chevrolet Build & Price share screen showing the copy control for the build code.', 'stingray-corvette' ); ?>">
					<figcaption><?php esc_html_e( 'The Share button can display a panel with a copy control for your build code.', 'stingray-corvette' ); ?></figcaption>
				</figure>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="build-share-form-heading">
			<h2 id="build-share-form-heading" class="sc-section-title"><?php esc_html_e( 'Send us your build code', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'Paste your build code and contact information below. We’ll use it to view your exact configuration and help with the next steps.', 'stingray-corvette' ); ?></p>
			<div class="sc-embed">
				<?php echo do_shortcode( '[formidable id=30]' ); ?>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
