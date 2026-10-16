<?php
/**
 * Terugvaltemplate (berichten/archieven).
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="hero hero-leeg"></section>
<section class="intro-vak">
	<h1><?php echo esc_html( is_home() ? get_the_title( get_option( 'page_for_posts' ) ) : get_the_archive_title() ); ?></h1>
	<div class="intro-tekst">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article class="smal bericht">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php is_singular() ? the_content() : the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p>Er is niets gevonden.</p>
		<?php endif; ?>
	</div>
</section>
<?php
bdj_render_band( array( 'band' => 'leeg' ) );
get_footer();
