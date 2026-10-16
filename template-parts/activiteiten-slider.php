<?php
/**
 * Activiteitenslider met iconenbalk (werkt zonder JavaScript, via radioknoppen).
 *
 * $args: start (slug van de activiteit die eerst getoond wordt),
 *        items (per slug eigen titel/tekst/knop/foto/icoon uit het blok; leeg = gegevens van de activiteitpagina)
 */

defined( 'ABSPATH' ) || exit;

$activiteiten = bdj_activiteiten();
if ( ! $activiteiten ) {
	return;
}
$start = isset( $args['start'] ) ? $args['start'] : $activiteiten[0]->post_name;
$slugs = wp_list_pluck( $activiteiten, 'post_name' );
$items = isset( $args['items'] ) ? (array) $args['items'] : array();
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
			$item     = isset( $items[ $act->post_name ] ) ? (array) $items[ $act->post_name ] : array();
			$fallback = get_the_post_thumbnail_url( $act, 'bdj-hero' );
			$foto     = bdj_veld_afbeelding( 'slider_afbeelding', $fallback, 'bdj-hero', $act->ID );
			$knop     = bdj_veld( 'knoptekst', 'Bekijk projecten in ' . strtolower( get_the_title( $act ) ), $act->ID );
			$titel    = get_the_title( $act );
			$tekst    = get_the_excerpt( $act );
			$icoon    = bdj_activiteit_icoon_img( $act );
			if ( ! empty( $item['titel'] ) ) {
				$titel = $item['titel'];
			}
			if ( ! empty( $item['tekst'] ) ) {
				$tekst = $item['tekst'];
			}
			if ( ! empty( $item['knop'] ) ) {
				$knop = $item['knop'];
			}
			if ( ! empty( $item['foto'] ) && wp_get_attachment_image_url( (int) $item['foto'], 'bdj-hero' ) ) {
				$foto = wp_get_attachment_image_url( (int) $item['foto'], 'bdj-hero' );
			}
			if ( ! empty( $item['icoon'] ) && wp_attachment_is_image( (int) $item['icoon'] ) ) {
				$icoon = wp_get_attachment_image( (int) $item['icoon'], 'medium', false, array( 'alt' => '' ) );
			}
			?>
			<div class="act-slide" style="background-image:url('<?php echo esc_url( $foto ); ?>')">
				<div class="act-kaart">
					<div class="rij">
						<div>
							<h2><?php echo esc_html( $titel ); ?></h2>
							<p><?php echo esc_html( $tekst ); ?></p>
						</div>
						<div class="icoon"><?php echo $icoon; // phpcs:ignore ?></div>
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
