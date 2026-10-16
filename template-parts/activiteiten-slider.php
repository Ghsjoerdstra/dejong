<?php
/**
 * Activiteitenslider met iconenbalk (werkt zonder JavaScript, via radioknoppen).
 *
 * $args: start (slug van de activiteit die eerst getoond wordt)
 */

defined( 'ABSPATH' ) || exit;

$activiteiten = bdj_activiteiten();
if ( ! $activiteiten ) {
	return;
}
$start = isset( $args['start'] ) ? $args['start'] : $activiteiten[0]->post_name;
$slugs = wp_list_pluck( $activiteiten, 'post_name' );
if ( ! in_array( $start, $slugs, true ) ) {
	$start = $slugs[0];
}
?>
<section class="act-slider<?php echo ! empty( $args['mobiel_boven'] ) ? ' mobiel-boven' : ''; ?>" id="activiteiten">
	<?php foreach ( $activiteiten as $act ) : ?>
		<input type="radio" name="activiteit" id="act-<?php echo esc_attr( $act->post_name ); ?>"<?php checked( $act->post_name, $start ); ?>>
	<?php endforeach; ?>

	<div class="act-slides">
		<?php foreach ( $activiteiten as $act ) : ?>
			<?php
			$fallback = get_the_post_thumbnail_url( $act, 'bdj-hero' );
			$foto     = bdj_veld_afbeelding( 'slider_afbeelding', $fallback, 'bdj-hero', $act->ID );
			$knop     = bdj_veld( 'knoptekst', 'Bekijk projecten in ' . strtolower( get_the_title( $act ) ), $act->ID );
			?>
			<div class="act-slide" style="background-image:url('<?php echo esc_url( $foto ); ?>')">
				<div class="act-kaart">
					<div class="rij">
						<div>
							<h2><?php echo esc_html( get_the_title( $act ) ); ?></h2>
							<p><?php echo esc_html( get_the_excerpt( $act ) ); ?></p>
						</div>
						<div class="icoon"><?php echo bdj_activiteit_icoon_img( $act ); // phpcs:ignore ?></div>
					</div>
					<a class="btn" href="<?php echo esc_url( get_permalink( $act ) ); ?>"><?php echo esc_html( $knop ); ?></a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<nav class="act-tabs" aria-label="Kies een activiteit">
		<ul>
			<?php foreach ( $activiteiten as $act ) : ?>
				<li><label for="act-<?php echo esc_attr( $act->post_name ); ?>" title="<?php echo esc_attr( get_the_title( $act ) ); ?>"><?php echo bdj_activiteit_tab_icoon( $act ); // phpcs:ignore ?></label></li>
			<?php endforeach; ?>
		</ul>
	</nav>
</section>
