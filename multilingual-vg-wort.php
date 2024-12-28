<?php
/**
 * Multilingual VG WORT
 *
 * @package 2ndkauboy/campaign-archive-block-for-mailchimp
 * @author  Bernhard Kau
 * @license GPLv3
 *
 * Plugin Name: Multilingual VG WORT
 * Plugin URI: https://github.com/2ndkauboy/multilingual-vg-wort
 * Description: Adds a VG WORT pixel from the German site to connected sites in other languages.
 * Version: 0.2.0
 * Author: Bernhard Kau
 * Author URI: https://kau-boys.com
 * Requires Plugins: multilingualpress
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * GitHub Plugin URI: 2ndkauboy/multilingual-vg-wort
 * Primary Branch: develop
 */

/**
 * Print out VG WORT pixel in the footer
 *
 * @return void
 */
function multilingual_vg_wort_footer() {
	$base_post = multilingual_vg_wort_find_base_post();

	require_once plugin_dir_path( __FILE__ ) . '/../wp-worthy/class-wp-worthy-pixel.php';

	switch_to_blog( $base_post->remoteSiteId() );
	$wp_worthy_pixel = wp_worthy_pixel::getPixelForPost( $base_post->remoteContentId() );

	if ( $wp_worthy_pixel ) {
		echo multilingual_vg_wort_wp_worthy_pixel_markup( $wp_worthy_pixel ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	restore_current_blog();
}

add_action( 'wp_footer', 'multilingual_vg_wort_footer' );

/**
 * Get German base post that potentially stores the VG WORT pixel
 *
 * @return false|\Inpsyde\MultilingualPress\Framework\Api\Translation
 */
function multilingual_vg_wort_find_base_post() {
	$args = \Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs::forContext(
		new \Inpsyde\MultilingualPress\Framework\WordpressContext()
	)->forSiteId( get_current_blog_id() )->includeBase();

	$translations = \Inpsyde\MultilingualPress\resolve(
		\Inpsyde\MultilingualPress\Framework\Api\Translations::class
	)->searchTranslations( $args );

	foreach ( $translations as $translation ) {
		if ( $translation->language()->isoCode() === 'de' ) {
			return $translation;
		}
	}

	return false;
}

/**
 * Get the Worthy pixel HTML string
 *
 * @param wp_worthy_pixel $wp_worthy_pixel The Worthy pixel object.
 *
 * @return string
 */
function multilingual_vg_wort_wp_worthy_pixel_markup( $wp_worthy_pixel ) {
	$image_element =
		'<img ' .
		'class="wp-worthy-pixel-img skip-lazy" ' .
		'src="' . esc_attr( $wp_worthy_pixel->url ) . '" ' .
		'loading="eager" ' .
		'data-no-lazy="1" data-skip-lazy="1" ' .
		'height="1" ' .
		'width="1" ' .
		'alt="" ' .
		'/>';

	return '<div id="wp-worthy-pixel">' . $image_element . '</div>';
}
