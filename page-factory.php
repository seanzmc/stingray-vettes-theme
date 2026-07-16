<?php
/**
 * /factory/ — Orders @ Factory tracking surface (SiteWireframe item 6).
 *
 * Binds by slug: page-factory.php ↔ the "factory" page. The wpDataTables table
 * is rendered inside .sc-embed so assets/css/embeds.css can skin it with the
 * theme design system without changing table configuration or data sources.
 *
 * @package Stingray_Corvette
 */

$factory_status_codes = array(
	array(
		'code' => '1100',
		'label' => __( 'Preliminary Order', 'stingray-corvette' ),
		'body' => __( 'When you place a preliminary order with Stingray Chevrolet, you receive a unique 6-character order number to track your Corvette as it is built. For each order cycle, orders are organized based on deposit date and special requirements.', 'stingray-corvette' ),
	),
	array(
		'code' => '2000',
		'label' => __( 'Order Placed', 'stingray-corvette' ),
		'body' => __( 'When Stingray Chevrolet has an allocation slot available, the order can be placed for production scheduling. The order must pass all constraints for the current order cycle.', 'stingray-corvette' ),
	),
	array(
		'code' => '3000',
		'label' => __( 'Accepted by Production Control', 'stingray-corvette' ),
		'body' => __( 'Order accepted by Production Control; the Corvette order is officially in the production queue.', 'stingray-corvette' ),
	),
	array(
		'code' => '3100',
		'label' => __( 'Available to Sequence', 'stingray-corvette' ),
		'body' => __( 'Generally used as a placeholder to indicate the order is past initial order acceptance and a target production week will soon be posted. Not all orders will see this event status.', 'stingray-corvette' ),
	),
	array(
		'code' => '3300',
		'label' => __( 'Scheduled for Production', 'stingray-corvette' ),
		'body' => __( 'The order is scheduled for production and a target production week (TPW) is posted.', 'stingray-corvette' ),
	),
	array(
		'code' => '3400',
		'label' => __( 'Broadcast for Production', 'stingray-corvette' ),
		'body' => __( 'The factory is building the car at this stage.', 'stingray-corvette' ),
	),
	array(
		'code' => '3800',
		'label' => __( 'Produced', 'stingray-corvette' ),
		'body' => __( 'The car has been built and is in final production stages. Quality control checks are completed and accounting documents are produced. A VIN is available; contact Stingray to get it for insurance, accessories, and related next steps. Museum Delivery (R8C) transactions may be completed at this stage so documentation is ready for pickup.', 'stingray-corvette' ),
	),
	array(
		'code' => '4200',
		'label' => __( 'Shipped', 'stingray-corvette' ),
		'body' => __( 'The car is in transit to the dealership. Once Stingray gets the shipping notice, arrival usually takes 2–4 days. Jack Cooper Transport trucks Corvettes directly to Stingray; cars are not transported to Stingray by rail.', 'stingray-corvette' ),
	),
	array(
		'code' => '5000',
		'label' => __( 'Delivered to Dealer', 'stingray-corvette' ),
		'body' => __( 'The car has been delivered to Stingray or the delivery location indicated at ordering. Corvettes go through pre-delivery inspection (PDI); components and dealer-installed options (LPO) are installed. Your representative will set a final delivery appointment.', 'stingray-corvette' ),
	),
	array(
		'code' => '6000',
		'label' => __( 'Delivered to Customer', 'stingray-corvette' ),
		'body' => __( 'You have taken delivery. Owner manual resources and next-step reference materials are available in the Learning Center.', 'stingray-corvette' ),
	),
);

$factory_other_codes = array(
	'TPW'    => __( 'Target Production Week', 'stingray-corvette' ),
	'DFC'    => __( 'Destination Freight Charge', 'stingray-corvette' ),
	'1YC'    => __( 'Stingray', 'stingray-corvette' ),
	'1YH'    => __( 'Z06', 'stingray-corvette' ),
	'1YG'    => __( 'E-Ray', 'stingray-corvette' ),
	'07'     => __( 'Coupe', 'stingray-corvette' ),
	'67'     => __( 'Convertible', 'stingray-corvette' ),
	'1YC07'  => __( 'Stingray Coupe example', 'stingray-corvette' ),
	'255691' => __( 'Stingray Chevrolet BAC code', 'stingray-corvette' ),
	'184590' => __( 'National Corvette Museum BAC code in Bowling Green, Kentucky', 'stingray-corvette' ),
);

get_header();
?>

<main class="sc-page">
	<div class="sc-page-inner">
		<header class="sc-section">
			<p class="sc-page-eyebrow"><?php esc_html_e( 'Orders @ Factory', 'stingray-corvette' ); ?></p>
			<h1 class="sc-page-title"><?php esc_html_e( 'Orders in Production', 'stingray-corvette' ); ?></h1>
			<p class="sc-page-lede"><?php esc_html_e( 'Track Corvette orders placed by Stingray Chevrolet in Plant City, FL once they are scheduled, sequenced, built, shipped, or delivered.', 'stingray-corvette' ); ?></p>
		</header>

		<?php echo stingray_corvette_render_page_prose(); ?>

		<section class="sc-section" aria-labelledby="factory-track-heading">
			<h2 id="factory-track-heading" class="sc-section-title"><?php esc_html_e( 'Track your factory order', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'This page only shows orders placed by Stingray Chevrolet that are already in the factory production process.', 'stingray-corvette' ); ?></p>

			<div class="sc-note">
				<strong><?php esc_html_e( 'Factory-stage orders cannot be modified.', 'stingray-corvette' ); ?></strong>
				<span><?php esc_html_e( 'All orders displayed below are already at the factory. Email or call your sales representative with questions about your order.', 'stingray-corvette' ); ?></span>
			</div>

			<div class="sc-grid">
				<div class="sc-card-panel">
					<h3 class="sc-card-panel-title"><?php esc_html_e( 'When you reach 3800', 'stingray-corvette' ); ?></h3>
					<ul>
						<li><?php esc_html_e( 'Your Corvette has been produced.', 'stingray-corvette' ); ?></li>
						<li><?php esc_html_e( 'Contact us to get your VIN number for insurance, accessories, and related next steps.', 'stingray-corvette' ); ?></li>
					</ul>
				</div>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="factory-table-help-heading">
			<h2 id="factory-table-help-heading" class="sc-section-title"><?php esc_html_e( 'How to read the table', 'stingray-corvette' ); ?></h2>
			<ol class="sc-step-list">
				<li><?php esc_html_e( 'Type your Order # into the search box below.', 'stingray-corvette' ); ?></li>
				<li><?php esc_html_e( 'Click anywhere on a table row to see all available details in a popup.', 'stingray-corvette' ); ?></li>
				<li><?php esc_html_e( 'The data shown is a snapshot extracted directly from GM’s Order Workbench.', 'stingray-corvette' ); ?></li>
				<li><?php esc_html_e( 'Exact production and delivery timing is unknown and controlled by the Corvette Factory.', 'stingray-corvette' ); ?></li>
			</ol>
			<p class="sc-section-lede">
				<?php esc_html_e( 'If you do not know your order number or have other questions, contact Stingray Chevrolet at', 'stingray-corvette' ); ?>
				<a href="tel:18133595000"><?php esc_html_e( '813-359-5000', 'stingray-corvette' ); ?></a>
				<?php esc_html_e( 'or', 'stingray-corvette' ); ?>
				<a href="mailto:assistance@stingraychevrolet.com"><?php esc_html_e( 'assistance@stingraychevrolet.com', 'stingray-corvette' ); ?></a>.
			</p>
		</section>

		<section class="sc-section" aria-labelledby="factory-status-heading">
			<h2 id="factory-status-heading" class="sc-section-title"><?php esc_html_e( 'Order status codes explained', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'Use these GM event codes to understand where your Corvette is in the production process.', 'stingray-corvette' ); ?></p>

			<div class="sc-accordion">
				<?php foreach ( $factory_status_codes as $factory_status_code ) : ?>
					<details>
						<summary><span class="sc-code"><?php echo esc_html( $factory_status_code['code'] ); ?></span> <?php echo esc_html( $factory_status_code['label'] ); ?></summary>
						<div class="sc-accordion-body">
							<p><?php echo esc_html( $factory_status_code['body'] ); ?></p>
						</div>
					</details>
				<?php endforeach; ?>

				<details>
					<summary><span class="sc-code"><?php esc_html_e( 'Other', 'stingray-corvette' ); ?></span> <?php esc_html_e( 'Common abbreviations and codes', 'stingray-corvette' ); ?></summary>
					<div class="sc-accordion-body">
						<?php foreach ( $factory_other_codes as $factory_other_code => $factory_other_label ) : ?>
							<div class="sc-fact-row"><strong><?php echo esc_html( $factory_other_code ); ?></strong><span><?php echo esc_html( $factory_other_label ); ?></span></div>
						<?php endforeach; ?>
					</div>
				</details>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="factory-table-heading">
			<h2 id="factory-table-heading" class="sc-section-title"><?php esc_html_e( 'Factory order table', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'Search the current factory-order snapshot by order number, then select a row for the available details.', 'stingray-corvette' ); ?></p>
			<div class="sc-embed">
				<?php echo stingray_corvette_render_page_embed_shortcode(); ?>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
