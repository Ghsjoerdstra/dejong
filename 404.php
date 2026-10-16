<?php
/**
 * 404 – pagina niet gevonden.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="hero hero-leeg"></section>
<section class="intro-vak">
	<h1>Pagina niet gevonden</h1>
	<div class="intro-tekst">
		<div class="smal">
			<p>De pagina die u zoekt bestaat niet (meer). Ga terug naar de <a href="<?php echo esc_url( home_url( '/' ) ); ?>">homepage</a> of bekijk onze activiteiten hieronder.</p>
		</div>
	</div>
</section>
<?php
bdj_render_activiteiten_tegels( array( 'titel' => 'Onze activiteiten', 'behalve' => false ) );
get_footer();
