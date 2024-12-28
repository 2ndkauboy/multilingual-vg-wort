<?php
/**
 * Plugin Name: Multilingual VG WORT
 * Description: Adds a VG WORT pixel from the German site to connected sites in other languages.
 * Requires Plugins: multilingualpress, wp-worthy
 * GitHub Plugin URI: 2ndkauboy/multilingual-vg-wort
 */

function multilingual_vg_wort_footer() {
	/** @var \Inpsyde\MultilingualPress\Framework\Api\Translation $base_post */
	$base_post = multilingual_vg_wort_find_base_post();

	require_once plugin_dir_path( __FILE__ ) . '/../wp-worthy/class-wp-worthy-pixel.php';

	switch_to_blog( $base_post->remoteSiteId() );
	$vgw_pixel = wp_worthy_pixel::getPixelForPost( $base_post->remoteContentId() );

	echo multilingual_vg_wort_wp_worthy_pixel( $vgw_pixel );

	restore_current_blog();
}

add_action( 'wp_footer', 'multilingual_vg_wort_footer' );

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

function multilingual_vg_wort_wp_worthy_pixel( $vgw_pixel ) {
	$imageClasses = [ 'wp-worthy-pixel-img', 'skip-lazy' ];

	$imageElement =
		'<img ' .
		'class="' . esc_attr( implode( ' ', $imageClasses ) ) . '" ' .
		'src="' . esc_attr( $vgw_pixel->url ) . '" ' .
		'loading="eager" ' .
		'data-no-lazy="1" data-skip-lazy="1" ' .
		'height="1" ' .
		'width="1" ' .
		'alt="" ' .
		'/>';

	return '<div id="wp-worthy-pixel">' . $imageElement . '</div>';
}
