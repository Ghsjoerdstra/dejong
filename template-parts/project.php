<?php
/**
 * Eén projectkaart met fotoslider (radioknoppen, geen JavaScript).
 *
 * $args: project (WP_Post)
 */

defined( 'ABSPATH' ) || exit;

$project = $args['project'];
$fotos   = array_slice( bdj_project_fotos( $project->ID ), 0, 8 ); // CSS ondersteunt max. 8 foto's per project.
$plaats  = get_post_meta( $project->ID, '_bdj_plaats', true );
$pid     = 'p' . $project->ID;
$n       = count( $fotos );
?>
<div class="project">
	<?php for ( $i = 1; $i <= $n; $i++ ) : ?>
		<input type="radio" name="<?php echo esc_attr( $pid ); ?>" id="<?php echo esc_attr( $pid . '-' . $i ); ?>"<?php checked( $i, 1 ); ?>>
	<?php endfor; ?>
	<div class="project-venster">
		<div class="project-label">
			<strong><?php echo esc_html( get_the_title( $project ) ); ?></strong>
			<?php if ( $plaats ) : ?>
				<span><?php echo bdj_svg( 'pin' ); // phpcs:ignore ?> <?php echo esc_html( $plaats ); ?></span>
			<?php endif; ?>
		</div>
		<div class="project-track">
			<?php foreach ( $fotos as $i => $foto_id ) : ?>
				<div class="project-foto"><?php echo wp_get_attachment_image( $foto_id, 'bdj-project', false, array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title( $project ) . ' – foto ' . ( $i + 1 ) ) ) ); ?></div>
			<?php endforeach; ?>
		</div>
		<?php if ( $n > 1 ) : ?>
			<?php for ( $i = 1; $i <= $n; $i++ ) : ?>
				<?php
				$vorige   = ( ( $i - 2 + $n ) % $n ) + 1;
				$volgende = ( $i % $n ) + 1;
				?>
				<div class="pijlen-<?php echo (int) $i; ?>">
					<label class="pijl vorige" for="<?php echo esc_attr( $pid . '-' . $vorige ); ?>" aria-label="Vorige foto"><?php echo bdj_svg( 'pijl-vorige' ); // phpcs:ignore ?></label>
					<label class="pijl volgende" for="<?php echo esc_attr( $pid . '-' . $volgende ); ?>" aria-label="Volgende foto"><?php echo bdj_svg( 'pijl-volgende' ); // phpcs:ignore ?></label>
				</div>
			<?php endfor; ?>
			<div class="bolletjes">
				<?php for ( $i = 1; $i <= $n; $i++ ) : ?>
					<label for="<?php echo esc_attr( $pid . '-' . $i ); ?>" aria-label="Foto <?php echo (int) $i; ?>"></label>
				<?php endfor; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
