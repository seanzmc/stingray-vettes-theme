<?php
/**
 * /deposit/ — Corvette deposit form surface (SiteWireframe item 3).
 *
 * Binds by slug: page-deposit.php ↔ the "deposit" page. The Formidable form
 * is rendered inside .sc-embed so assets/css/embeds.css can skin it with the
 * theme design system whether Formidable's own styling is enabled or disabled.
 *
 * @package Stingray_Corvette
 */

get_header();
?>

<main class="sc-page">
	<div class="sc-page-inner">
		<header class="sc-section">
			<p class="sc-page-eyebrow"><?php esc_html_e( 'Reserve Your Allocation', 'stingray-corvette' ); ?></p>
			<h1 class="sc-page-title"><?php esc_html_e( 'Deposit Form', 'stingray-corvette' ); ?></h1>
			<p class="sc-page-lede"><?php esc_html_e( 'Place your refundable Corvette deposit, choose the list you want to join, and receive the payment portal link for the $500 deposit.', 'stingray-corvette' ); ?></p>
		</header>

		<section class="sc-section" aria-labelledby="deposit-process-heading">
			<h2 id="deposit-process-heading" class="sc-section-title"><?php esc_html_e( 'How the deposit works', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'Start with the list form below, then use the payment portal link that appears after submission. The same payment link is also emailed to you in case you navigate away.', 'stingray-corvette' ); ?></p>

			<ol class="sc-step-list">
				<li><?php esc_html_e( 'Select the Corvette list you want to place a deposit on.', 'stingray-corvette' ); ?></li>
				<li><?php esc_html_e( 'Enter your contact information carefully so we can communicate with you throughout the order process.', 'stingray-corvette' ); ?></li>
				<li><?php esc_html_e( 'Read and accept the terms and conditions in the form.', 'stingray-corvette' ); ?></li>
				<li><?php esc_html_e( 'Click Submit. The confirmation page will include the link to enter your payment information, and a copy will be emailed to you.', 'stingray-corvette' ); ?></li>
				<li><?php esc_html_e( 'Open the payment portal and complete the required top fields exactly as shown below.', 'stingray-corvette' ); ?></li>
				<li><?php esc_html_e( 'Enter your payment information and submit the $500 deposit. A 3.0% credit card surcharge is collected to cover card processing costs.', 'stingray-corvette' ); ?></li>
			</ol>
		</section>

		<section class="sc-section" aria-labelledby="payment-fields-heading">
			<h2 id="payment-fields-heading" class="sc-section-title"><?php esc_html_e( 'Payment portal fields', 'stingray-corvette' ); ?></h2>
			<div class="sc-note">
				<strong><?php esc_html_e( 'Important:', 'stingray-corvette' ); ?></strong>
				<span><?php esc_html_e( 'These three payment portal fields must be filled out correctly or you will not be added to a deposit list and your deposit may be returned.', 'stingray-corvette' ); ?></span>
			</div>

			<div class="sc-grid" aria-label="<?php esc_attr_e( 'Required payment portal field values', 'stingray-corvette' ); ?>">
				<div class="sc-card-panel">
					<h3 class="sc-card-panel-title"><?php esc_html_e( 'RO/Stock#', 'stingray-corvette' ); ?></h3>
					<p class="sc-fineprint"><span class="sc-code"><?php esc_html_e( 'C8 Deposit', 'stingray-corvette' ); ?></span></p>
				</div>
				<div class="sc-card-panel">
					<h3 class="sc-card-panel-title"><?php esc_html_e( 'Department', 'stingray-corvette' ); ?></h3>
					<p class="sc-fineprint"><span class="sc-code"><?php esc_html_e( 'SALES', 'stingray-corvette' ); ?></span></p>
				</div>
				<div class="sc-card-panel">
					<h3 class="sc-card-panel-title"><?php esc_html_e( 'Total Amount Due', 'stingray-corvette' ); ?></h3>
					<p class="sc-fineprint"><span class="sc-code"><?php esc_html_e( '$500.00', 'stingray-corvette' ); ?></span></p>
				</div>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="before-deposit-heading">
			<h2 id="before-deposit-heading" class="sc-section-title"><?php esc_html_e( 'Before you submit', 'stingray-corvette' ); ?></h2>
			<div class="sc-grid">
				<div class="sc-card-panel">
					<h3 class="sc-card-panel-title"><?php esc_html_e( 'Review the process', 'stingray-corvette' ); ?></h3>
					<ul>
						<li><?php esc_html_e( 'Deposits reserve your place on the selected waitlist as of the submission date.', 'stingray-corvette' ); ?></li>
						<li><?php esc_html_e( 'One deposit per model per household is allowed until that order is placed for production.', 'stingray-corvette' ); ?></li>
						<li><?php esc_html_e( 'Your deposit remains refundable until your order is officially placed for production.', 'stingray-corvette' ); ?></li>
					</ul>
				</div>

				<a class="sc-link-card" href="<?php echo esc_url( home_url( '/process/' ) ); ?>">
					<span>
						<span class="sc-link-eyebrow"><?php esc_html_e( 'Next read', 'stingray-corvette' ); ?></span>
						<span class="sc-link-title"><?php esc_html_e( 'Deposit Policy Guidelines', 'stingray-corvette' ); ?></span>
					</span>
					<span class="sc-link-arrow" aria-hidden="true">&#8594;</span>
				</a>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="deposit-form-heading">
			<h2 id="deposit-form-heading" class="sc-section-title"><?php esc_html_e( 'Submit your deposit request', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'Complete the form below first. After submitting, follow the payment portal link to complete the deposit.', 'stingray-corvette' ); ?></p>
			<div class="sc-embed">
				<?php echo do_shortcode( '[formidable id=8]' ); ?>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
