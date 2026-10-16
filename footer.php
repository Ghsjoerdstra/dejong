<?php
/**
 * Footer: logo + contactgegevens, activiteiten, menu, SBB en onderregel.
 */

defined( 'ABSPATH' ) || exit;

$telefoon = bdj_optie( 'telefoon' );
$email    = bdj_optie( 'email' );
?>
</main>

<footer class="site-footer">
	<div class="footer-grid">
		<div>
			<a class="footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> – home">
				<img class="logo-beeld" src="<?php echo esc_url( bdj_asset( 'logo-footer.svg' ) ); ?>" width="188" height="53" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			</a>
			<address><?php echo esc_html( bdj_optie( 'adres' ) ); ?><br><?php echo esc_html( bdj_optie( 'postcode' ) ); ?></address>
			<p>
				<?php if ( $telefoon ) : ?><a href="<?php echo esc_attr( bdj_tel_link( $telefoon ) ); ?>"><?php echo esc_html( $telefoon ); ?></a><br><?php endif; ?>
				<?php if ( $email ) : ?><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a><?php endif; ?>
			</p>
		</div>

		<div class="up">
			<strong>Activiteiten</strong>
			<ul>
				<?php foreach ( bdj_activiteiten() as $act ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $act ) ); ?>"><?php echo esc_html( get_the_title( $act ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="up">
			<ul>
				<?php foreach ( bdj_menu_items() as $item ) : ?>
					<?php if ( ! $item['is_activiteiten'] ) : ?>
						<li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['titel'] ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<?php if ( bdj_optie( 'sbb_tonen' ) ) : ?>
				<div class="sbb">
					<div class="rij"><img src="<?php echo esc_url( bdj_asset( 'sbb-logo.png' ) ); ?>" alt="SBB"><span>Leerbedrijf SBB</span></div>
					(Samenwerkingsorganisatie Beroepsonderwijs Bedrijfsleven)
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="footer-onder">
		<?php if ( bdj_optie( 'kvk' ) ) : ?><span>KVK <?php echo esc_html( bdj_optie( 'kvk' ) ); ?></span><?php endif; ?>
		<?php if ( bdj_optie( 'btw' ) ) : ?><span>BTW <?php echo esc_html( bdj_optie( 'btw' ) ); ?></span><?php endif; ?>
		<span>Copyright © 2023 - <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
