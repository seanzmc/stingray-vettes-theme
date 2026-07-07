<?php
/**
 * /process/ — Corvette process guide surface (SiteWireframe item 7).
 *
 * Binds by slug: page-process.php ↔ the "process" page. This is static
 * customer-facing guide content ported from the live Process Guide onto the
 * shared Stingray Corvette design-system surface components.
 *
 * @package Stingray_Corvette
 */

$process_status_cards = array(
	array(
		'name'    => __( 'Stingray', 'stingray-corvette' ),
		'status'  => __( 'OPEN', 'stingray-corvette' ),
		'details' => array(
			__( 'Deposit: $500', 'stingray-corvette' ),
			__( 'Fully refundable until order placed', 'stingray-corvette' ),
			__( 'Sold at MSRP unless specifically noted', 'stingray-corvette' ),
		),
	),
	array(
		'name'    => __( 'Z06', 'stingray-corvette' ),
		'status'  => __( 'OPEN', 'stingray-corvette' ),
		'details' => array(
			__( 'Deposit: $500 + $2,500 at order', 'stingray-corvette' ),
			__( 'Full deposit becomes non-refundable at order', 'stingray-corvette' ),
			__( 'Sold at MSRP unless specifically noted', 'stingray-corvette' ),
		),
	),
	array(
		'name'    => __( 'Grand Sport', 'stingray-corvette' ),
		'status'  => __( 'OPEN', 'stingray-corvette' ),
		'details' => array(
			__( 'Deposit: $500', 'stingray-corvette' ),
			__( 'Fully refundable until order placed', 'stingray-corvette' ),
			__( 'Sold at MSRP unless specifically noted', 'stingray-corvette' ),
		),
	),
	array(
		'name'    => __( 'Grand Sport X', 'stingray-corvette' ),
		'status'  => __( 'OPEN', 'stingray-corvette' ),
		'details' => array(
			__( 'Deposit: $500 + $2,500 at order', 'stingray-corvette' ),
			__( 'Full deposit becomes non-refundable at order', 'stingray-corvette' ),
			__( 'Sold at MSRP unless specifically noted', 'stingray-corvette' ),
		),
	),
	array(
		'id'    => 'zr1',
		'name'    => __( 'ZR1', 'stingray-corvette' ),
		'status'  => __( 'CLOSED', 'stingray-corvette' ),
		'details' => array(
			__( 'Not accepting new deposits at this time', 'stingray-corvette' ),
			__( 'See the ZR1 Process Page for details', 'stingray-corvette' ),
		),
		'link'    => 'https://stingraychevroletcorvette.com/zr1-process/',
	),
	array(
		'name'    => __( 'ZR1X', 'stingray-corvette' ),
		'status'  => __( 'CLOSED', 'stingray-corvette' ),
		'details' => array(
			__( 'Not accepting new deposits at this time', 'stingray-corvette' ),
			__( 'Contact us if you are already on this list', 'stingray-corvette' ),
		),
	),
	array(
		'name'    => __( 'E-Ray', 'stingray-corvette' ),
		'status'  => __( 'DISCONTINUED', 'stingray-corvette' ),
		'details' => array(
			__( 'GM has discontinued the E-Ray', 'stingray-corvette' ),
			__( 'No new deposits accepted', 'stingray-corvette' ),
			__( 'Existing E-Ray depositors have been automatically transferred to the Grand Sport X list; contact us if you would like to adjust that or request a refund.', 'stingray-corvette' ),
		),
	),
);

$process_model_details = array(
	array(
		'id'    => 'stingray',
		'name'  => __( 'Stingray', 'stingray-corvette' ),
		'items' => array(
			__( '$500 deposit to secure your place on the list.', 'stingray-corvette' ),
			__( 'Deposit is fully refundable until your order is submitted to the factory.', 'stingray-corvette' ),
			__( 'An additional deposit may be due at time of order if your build includes BV4 Personalized Plaque, R8C Museum Delivery, or D30 Color Combination Override.', 'stingray-corvette' ),
		),
	),
	array(
		'name'  => __( 'Grand Sport', 'stingray-corvette' ),
		'items' => array(
			__( 'List is open and accepting new deposits now.', 'stingray-corvette' ),
			__( '$500 deposit to secure your place on the Grand Sport list.', 'stingray-corvette' ),
			__( 'Deposit is fully refundable until your order is submitted to the factory.', 'stingray-corvette' ),
			__( 'Grand Sport and Grand Sport X are maintained as separate lists due to separate GM allocations. Placing a deposit on one does not reserve a spot on the other.', 'stingray-corvette' ),
			__( 'Sold at MSRP unless specifically noted.', 'stingray-corvette' ),
		),
	),
	array(
		'name'  => __( 'Grand Sport X', 'stingray-corvette' ),
		'items' => array(
			__( 'List is open and accepting new deposits now. Former E-Ray depositors have been automatically transferred here.', 'stingray-corvette' ),
			__( '$500 deposit to secure your place on the Grand Sport X list.', 'stingray-corvette' ),
			__( 'An additional $2,500 is due when your order is placed for production.', 'stingray-corvette' ),
			__( 'The entire deposit becomes non-refundable once your order is submitted to the factory.', 'stingray-corvette' ),
			__( 'If you were previously on the E-Ray deposit list, your deposit has been automatically transferred to the Grand Sport X list. Contact us if you would like to move it elsewhere or request a refund instead.', 'stingray-corvette' ),
			__( 'Sold at MSRP unless specifically noted.', 'stingray-corvette' ),
		),
	),
	array(
		'name'  => __( 'Z06', 'stingray-corvette' ),
		'items' => array(
			__( 'List is open and accepting new deposits now.', 'stingray-corvette' ),
			__( '$500 deposit to secure your place on the list.', 'stingray-corvette' ),
			__( 'An additional $2,500 is due when your order is placed for production.', 'stingray-corvette' ),
			__( 'The entire deposit becomes non-refundable at the time the order is submitted to the factory.', 'stingray-corvette' ),
			__( 'Sold at MSRP unless specifically noted.', 'stingray-corvette' ),
		),
	),
	array(
		'name'  => __( 'ZR1 & ZR1X', 'stingray-corvette' ),
		'items' => array(
			__( 'These lists are currently closed.', 'stingray-corvette' ),
			__( 'Stingray has collected more deposits than anticipated allocations and is not accepting new names at this time.', 'stingray-corvette' ),
			__( 'Visit the ZR1 Process Page or contact Stingray directly for more information.', 'stingray-corvette' ),
		),
		'link'  => 'https://stingraychevroletcorvette.com/zr1-process/',
	),
);

$process_policy_cards = array(
	array(
		'title' => __( 'Refund Policy', 'stingray-corvette' ),
		'items' => array(
			__( 'Your deposit is fully refundable until your order is placed for production.', 'stingray-corvette' ),
			__( 'Once your order is submitted to the factory, the entire deposit becomes non-refundable.', 'stingray-corvette' ),
			__( 'If you decide to cancel, we will verify your mailing address and issue a refund check.', 'stingray-corvette' ),
			__( 'Refunds are issued only to the individual named on the original deposit receipt.', 'stingray-corvette' ),
		),
	),
	array(
		'title' => __( 'Placing a Deposit', 'stingray-corvette' ),
		'items' => array(
			__( 'Use our online Deposit Form, which accepts credit card payments.', 'stingray-corvette' ),
			__( 'Your position in line is determined by the date your deposit is received.', 'stingray-corvette' ),
			__( 'We allow one deposit per household on each model list.', 'stingray-corvette' ),
			__( 'Additional deposits may be placed only after your first order is in production.', 'stingray-corvette' ),
		),
	),
);

$process_order_cards = array(
	array(
		'title' => __( 'Ordering Cycles', 'stingray-corvette' ),
		'items' => array(
			__( 'We place Corvette orders twice per month.', 'stingray-corvette' ),
			__( 'Allocation quantities for each model vary with each ordering cycle.', 'stingray-corvette' ),
			__( 'GM provides allocation information approximately two weeks before each ordering period.', 'stingray-corvette' ),
		),
	),
	array(
		'title' => __( 'Configuration & Constraints', 'stingray-corvette' ),
		'items' => array(
			__( 'Specific option constraints, if any, are released 5 days before orders must be finalized.', 'stingray-corvette' ),
			__( 'Constraints can change monthly, affecting available colors, options, and packages.', 'stingray-corvette' ),
			__( 'We recommend scheduling an appointment with our Corvette Specialist to configure your car and discuss options before the ordering window opens.', 'stingray-corvette' ),
			__( 'We will contact you when it is your turn to finalize your order.', 'stingray-corvette' ),
		),
	),
);

$process_payment_cards = array(
	array(
		'title' => __( 'Cash & Check', 'stingray-corvette' ),
		'items' => array(
			__( 'We accept cash, cashier’s checks, and personal checks.', 'stingray-corvette' ),
			__( 'Make checks payable to Stingray Chevrolet.', 'stingray-corvette' ),
			__( 'Additional verification may be required for certain payment methods.', 'stingray-corvette' ),
		),
	),
	array(
		'title' => __( 'Financing', 'stingray-corvette' ),
		'items' => array(
			__( 'We work with various retail banks and credit unions to secure competitive financing.', 'stingray-corvette' ),
			__( 'Loans should not be secured more than 30 days before vehicle delivery.', 'stingray-corvette' ),
		),
	),
	array(
		'title' => __( 'Leasing', 'stingray-corvette' ),
		'items' => array(
			__( 'We offer leasing through multiple lenders.', 'stingray-corvette' ),
			__( 'Lease programs and terms are set by lenders and change monthly.', 'stingray-corvette' ),
		),
	),
	array(
		'title' => __( 'Your Deposit & Down Payment', 'stingray-corvette' ),
		'items' => array(
			__( 'Your deposit will be applied to the final purchase as a down payment or partial payment.', 'stingray-corvette' ),
			__( 'All funds must be verified in-house before the vehicle will be released.', 'stingray-corvette' ),
		),
	),
);

$process_delivery_cards = array(
	array(
		'title' => __( 'Shipping & Preparation', 'stingray-corvette' ),
		'items' => array(
			__( 'It typically takes approximately 2 days for your Corvette to arrive after shipping from the factory.', 'stingray-corvette' ),
			__( 'Upon arrival, we perform a comprehensive Pre-Delivery Inspection (PDI).', 'stingray-corvette' ),
			__( 'We install all dealer-installed (LPO) accessories you have ordered.', 'stingray-corvette' ),
		),
	),
	array(
		'title' => __( 'Delivery Requirements', 'stingray-corvette' ),
		'items' => array(
			__( 'Buyers must present a valid driver’s license and proof of insurance.', 'stingray-corvette' ),
			__( 'The person whose name is on the order must be present at delivery. Exceptions for extraordinary circumstances must be approved in advance.', 'stingray-corvette' ),
			__( 'We handle all tax and registration paperwork on your behalf.', 'stingray-corvette' ),
			__( 'Trade-ins are evaluated at current market value at time of contract. We cannot accept early trade-ins.', 'stingray-corvette' ),
		),
	),
);

get_header();
?>

<main class="sc-page">
	<div class="sc-page-inner">
		<header class="sc-section">
			<p class="sc-page-eyebrow"><?php esc_html_e( 'Order Process', 'stingray-corvette' ); ?></p>
			<h1 class="sc-page-title"><?php esc_html_e( 'Corvette Order Process Guide', 'stingray-corvette' ); ?></h1>
			<p class="sc-page-lede"><?php esc_html_e( 'A step-by-step guide to ordering your new Corvette through Stingray Chevrolet, from initial deposit through delivery.', 'stingray-corvette' ); ?></p>
			<div class="sc-note sc-note--info">
				<strong><?php esc_html_e( 'Policy note:', 'stingray-corvette' ); ?></strong>
				<span><?php esc_html_e( 'Stingray Chevrolet reserves the right to change its policy regarding deposits, ordering, and purchasing at any time.', 'stingray-corvette' ); ?></span>
			</div>
		</header>

		<section class="sc-section" aria-labelledby="process-actions-heading">
			<h2 id="process-actions-heading" class="sc-section-title"><?php esc_html_e( 'Start with the right step', 'stingray-corvette' ); ?></h2>
			<div class="sc-grid">
				<a class="sc-link-card" href="<?php echo esc_url( home_url( '/deposit/' ) ); ?>">
					<span><span class="sc-link-eyebrow"><?php esc_html_e( 'Reserve', 'stingray-corvette' ); ?></span><span class="sc-link-title"><?php esc_html_e( 'Place a Deposit', 'stingray-corvette' ); ?></span></span>
					<span class="sc-link-arrow" aria-hidden="true">&#8594;</span>
				</a>
				<a class="sc-link-card" href="<?php echo esc_url( home_url( '/order/' ) ); ?>">
					<span><span class="sc-link-eyebrow"><?php esc_html_e( 'Configure', 'stingray-corvette' ); ?></span><span class="sc-link-title"><?php esc_html_e( 'Start an Order Form', 'stingray-corvette' ); ?></span></span>
					<span class="sc-link-arrow" aria-hidden="true">&#8594;</span>
				</a>
				<a class="sc-link-card" href="<?php echo esc_url( home_url( '/factory/' ) ); ?>">
					<span><span class="sc-link-eyebrow"><?php esc_html_e( 'Production', 'stingray-corvette' ); ?></span><span class="sc-link-title"><?php esc_html_e( 'Track Factory Orders', 'stingray-corvette' ); ?></span></span>
					<span class="sc-link-arrow" aria-hidden="true">&#8594;</span>
				</a>
				<a class="sc-link-card" href="<?php echo esc_url( home_url( '/build-and-price/' ) ); ?>">
					<span><span class="sc-link-eyebrow"><?php esc_html_e( 'Build Code', 'stingray-corvette' ); ?></span><span class="sc-link-title"><?php esc_html_e( 'Share a Build Code', 'stingray-corvette' ); ?></span></span>
					<span class="sc-link-arrow" aria-hidden="true">&#8594;</span>
				</a>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="deposit-list-status-heading">
			<h2 id="deposit-list-status-heading" class="sc-section-title"><?php esc_html_e( 'Deposit Lists — Current Status', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'All deposit lists are currently open and accepting new customers except for the ZR1 and ZR1X, where we have collected more deposits than we anticipate receiving allocations for.', 'stingray-corvette' ); ?></p>

			<div class="sc-grid">
				<?php foreach ( $process_status_cards as $process_status_card ) : ?>
					<div class="sc-card-panel">
						<h3 class="sc-card-panel-title">
							<span><?php echo esc_html( $process_status_card['name'] ); ?></span>
							<span class="sc-pill"><?php echo esc_html( $process_status_card['status'] ); ?></span>
						</h3>
						<ul>
							<?php foreach ( $process_status_card['details'] as $process_status_detail_index => $process_status_detail ) : ?>
								<li>
									<?php if ( isset( $process_status_card['link'] ) && isset( $process_status_card['id'] ) && 'zr1' === $process_status_card['id'] && count( $process_status_card['details'] ) - 1 === $process_status_detail_index ) : ?>
										<a href="<?php echo esc_url( $process_status_card['link'] ); ?>"><?php echo esc_html( $process_status_detail ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $process_status_detail ); ?>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="deposit-details-heading">
			<h2 id="deposit-details-heading" class="sc-section-title"><?php esc_html_e( 'Deposit Details by Model', 'stingray-corvette' ); ?></h2>
			<p class="sc-section-lede"><?php esc_html_e( 'Each list has its own deposit requirements and allocation path. Review the details before placing or moving a deposit.', 'stingray-corvette' ); ?></p>

			<div class="sc-accordion">
				<?php foreach ( $process_model_details as $process_model_detail ) : ?>
					<details>
						<summary><?php echo esc_html( $process_model_detail['name'] ); ?></summary>
						<div class="sc-accordion-body">
							<ul>
								<?php foreach ( $process_model_detail['items'] as $process_model_item ) : ?>
									<li><?php echo esc_html( $process_model_item ); ?></li>
								<?php endforeach; ?>
							</ul>
							<?php if ( isset( $process_model_detail['id'] ) && 'stingray' === $process_model_detail['id'] ) : ?>
								<div class="sc-fact-row"><strong><span class="sc-code">BV4</span></strong><span><?php esc_html_e( 'Personalized Plaque', 'stingray-corvette' ); ?></span></div>
								<div class="sc-fact-row"><strong><span class="sc-code">R8C</span></strong><span><?php esc_html_e( 'Museum Delivery', 'stingray-corvette' ); ?></span></div>
								<div class="sc-fact-row"><strong><span class="sc-code">D30</span></strong><span><?php esc_html_e( 'Color Combination Override', 'stingray-corvette' ); ?></span></div>
							<?php endif; ?>
							<?php if ( isset( $process_model_detail['link'] ) ) : ?>
								<p><a href="<?php echo esc_url( $process_model_detail['link'] ); ?>"><?php esc_html_e( 'Visit the ZR1 Process Page', 'stingray-corvette' ); ?></a></p>
							<?php endif; ?>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="deposit-policies-heading">
			<h2 id="deposit-policies-heading" class="sc-section-title"><?php esc_html_e( 'Deposit Policies', 'stingray-corvette' ); ?></h2>
			<div class="sc-grid">
				<?php foreach ( $process_policy_cards as $process_policy_card ) : ?>
					<div class="sc-card-panel">
						<h3 class="sc-card-panel-title"><?php echo esc_html( $process_policy_card['title'] ); ?></h3>
						<ul>
							<?php foreach ( $process_policy_card['items'] as $process_policy_item ) : ?>
								<li><?php echo esc_html( $process_policy_item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
				<a class="sc-link-card" href="<?php echo esc_url( home_url( '/deposit/' ) ); ?>">
					<span><span class="sc-link-eyebrow"><?php esc_html_e( 'Online', 'stingray-corvette' ); ?></span><span class="sc-link-title"><?php esc_html_e( 'Deposit Form', 'stingray-corvette' ); ?></span></span>
					<span class="sc-link-arrow" aria-hidden="true">&#8594;</span>
				</a>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="order-process-heading">
			<h2 id="order-process-heading" class="sc-section-title"><?php esc_html_e( 'The Order Process', 'stingray-corvette' ); ?></h2>
			<div class="sc-grid">
				<?php foreach ( $process_order_cards as $process_order_card ) : ?>
					<div class="sc-card-panel">
						<h3 class="sc-card-panel-title"><?php echo esc_html( $process_order_card['title'] ); ?></h3>
						<ul>
							<?php foreach ( $process_order_card['items'] as $process_order_item ) : ?>
								<li><?php echo esc_html( $process_order_item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="pricing-heading">
			<h2 id="pricing-heading" class="sc-section-title"><?php esc_html_e( 'Pricing Policy', 'stingray-corvette' ); ?></h2>
			<div class="sc-grid">
				<div class="sc-card-panel">
					<h3 class="sc-card-panel-title"><?php esc_html_e( 'Vehicle Pricing', 'stingray-corvette' ); ?></h3>
					<ul>
						<li><?php esc_html_e( 'The majority of new Corvettes at Stingray Chevrolet are sold at or below MSRP.', 'stingray-corvette' ); ?></li>
						<li><?php esc_html_e( 'Certain limited-edition or high-demand variants may be priced differently at the dealer’s discretion. Any such pricing will be clearly communicated before you are asked to commit to an order.', 'stingray-corvette' ); ?></li>
						<li><?php esc_html_e( 'Pricing is subject to change by GM without notice. We do not offer price protection against GM-initiated price adjustments.', 'stingray-corvette' ); ?></li>
						<li><?php esc_html_e( 'All prices are plus tax, title, and dealer fees.', 'stingray-corvette' ); ?></li>
					</ul>
				</div>
				<div class="sc-card-panel">
					<h3 class="sc-card-panel-title"><?php esc_html_e( 'Dealer Fees', 'stingray-corvette' ); ?></h3>
					<p><?php esc_html_e( 'The following fees apply to every customer on every deal, without exception.', 'stingray-corvette' ); ?></p>
					<div class="sc-fact-row"><strong><?php esc_html_e( 'Dealer Fee', 'stingray-corvette' ); ?></strong><span><?php esc_html_e( '$999', 'stingray-corvette' ); ?></span></div>
					<div class="sc-fact-row"><strong><?php esc_html_e( 'Tag Agency Fee', 'stingray-corvette' ); ?></strong><span><?php esc_html_e( '$362', 'stingray-corvette' ); ?></span></div>
					<p><?php esc_html_e( 'These fees are in addition to the vehicle’s MSRP, titling fees, and any applicable taxes.', 'stingray-corvette' ); ?></p>
				</div>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="payment-options-heading">
			<h2 id="payment-options-heading" class="sc-section-title"><?php esc_html_e( 'Payment Options', 'stingray-corvette' ); ?></h2>
			<div class="sc-grid">
				<?php foreach ( $process_payment_cards as $process_payment_card ) : ?>
					<div class="sc-card-panel">
						<h3 class="sc-card-panel-title"><?php echo esc_html( $process_payment_card['title'] ); ?></h3>
						<ul>
							<?php foreach ( $process_payment_card['items'] as $process_payment_item ) : ?>
								<li><?php echo esc_html( $process_payment_item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="vehicle-delivery-heading">
			<h2 id="vehicle-delivery-heading" class="sc-section-title"><?php esc_html_e( 'Vehicle Delivery', 'stingray-corvette' ); ?></h2>
			<div class="sc-grid">
				<?php foreach ( $process_delivery_cards as $process_delivery_card ) : ?>
					<div class="sc-card-panel">
						<h3 class="sc-card-panel-title"><?php echo esc_html( $process_delivery_card['title'] ); ?></h3>
						<ul>
							<?php foreach ( $process_delivery_card['items'] as $process_delivery_item ) : ?>
								<li><?php echo esc_html( $process_delivery_item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="restrictions-heading">
			<h2 id="restrictions-heading" class="sc-section-title"><?php esc_html_e( 'Restrictions', 'stingray-corvette' ); ?></h2>
			<div class="sc-note">
				<strong><?php esc_html_e( 'We do not sell or lease vehicles:', 'stingray-corvette' ); ?></strong>
				<ul>
					<li><?php esc_html_e( 'To be titled and registered in New Hampshire, Oregon, Montana, Alaska, or Delaware.', 'stingray-corvette' ); ?></li>
					<li><?php esc_html_e( 'To individuals who may not be permanent U.S. residents, may live or work outside the U.S., or may conduct business outside the U.S.', 'stingray-corvette' ); ?></li>
					<li><?php esc_html_e( 'To brokers, wholesalers, or exporters.', 'stingray-corvette' ); ?></li>
				</ul>
			</div>
		</section>

		<section class="sc-section" aria-labelledby="staying-informed-heading">
			<h2 id="staying-informed-heading" class="sc-section-title"><?php esc_html_e( 'Staying Informed', 'stingray-corvette' ); ?></h2>
			<div class="sc-card-panel">
				<ul>
					<li><?php esc_html_e( 'We will reach out when it is your turn to finalize your order.', 'stingray-corvette' ); ?></li>
					<li>
						<?php esc_html_e( 'Add', 'stingray-corvette' ); ?>
						<a href="mailto:smccann@stingraychevrolet.com"><?php esc_html_e( 'smccann@stingraychevrolet.com', 'stingray-corvette' ); ?></a>
						<?php esc_html_e( 'to your safe senders list so communications do not end up in spam.', 'stingray-corvette' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'For the most up-to-date information on allocations and process changes, contact our Corvette Specialist directly at', 'stingray-corvette' ); ?>
						<a href="tel:18133595000"><?php esc_html_e( '813-359-5000', 'stingray-corvette' ); ?></a>.
					</li>
				</ul>
			</div>
			<div class="sc-note sc-note--info">
				<strong><?php esc_html_e( 'Current policy:', 'stingray-corvette' ); ?></strong>
				<span><?php esc_html_e( 'Stingray Chevrolet reserves the right to modify deposit requirements, pricing policies, and order procedures at any time. All information on this page reflects current policy and supersedes any previously published versions.', 'stingray-corvette' ); ?></span>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
