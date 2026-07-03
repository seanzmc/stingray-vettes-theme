<?php
/**
 * Shared document head + topbar for interior pages.
 * front-page.php does NOT use this — the homepage is full-bleed and renders
 * its own document with the same topbar/footer partials.
 *
 * @package Stingray_Corvette
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'carbon-checker' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'inc/topbar' ); ?>
