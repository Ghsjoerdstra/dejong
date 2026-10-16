<?php
/**
 * Header: vaste blauwe balk met logo, hoofdmenu, submenu activiteiten en mobiel menu.
 */

defined( 'ABSPATH' ) || exit;

$menu        = bdj_menu_items();
$activiteiten = bdj_activiteiten();
$compact     = bdj_is_activiteit();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header<?php echo $compact ? ' compact' : ''; ?>">
	<div class="header-bar">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> – home">
			<img class="logo-beeld" src="<?php echo esc_url( bdj_asset( 'logo.svg' ) ); ?>" width="252" height="83" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		</a>
		<nav class="menu" aria-label="Hoofdmenu">
			<?php foreach ( $menu as $item ) : ?>
				<?php
				$classes = $item['classes'];
				if ( $item['actief'] ) {
					$classes[] = 'actief';
				}
				?>
				<a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $classes ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' : ''; ?>><?php echo esc_html( $item['titel'] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<label for="menu-toggle" class="menu-knop" aria-label="Menu"><img src="<?php echo esc_url( bdj_asset( 'icons/hamburger.svg' ) ); ?>" alt=""></label>
	</div>

	<input type="checkbox" id="menu-toggle" class="menu-toggle">
	<nav class="mobiel-menu" aria-label="Mobiel menu">
		<?php if ( $activiteiten ) : ?>
			<div class="kop">Activiteiten</div>
			<ul>
				<?php foreach ( $activiteiten as $act ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $act ) ); ?>"><?php echo esc_html( get_the_title( $act ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<ul>
			<?php foreach ( $menu as $item ) : ?>
				<?php if ( ! $item['is_activiteiten'] ) : ?>
					<li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['titel'] ); ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</nav>

	<?php if ( $activiteiten ) : ?>
		<nav class="submenu" aria-label="Activiteiten">
			<ul>
				<?php foreach ( $activiteiten as $act ) : ?>
					<li>
						<a href="<?php echo esc_url( get_permalink( $act ) ); ?>"<?php echo get_queried_object_id() === $act->ID ? ' class="actief"' : ''; ?>>
							<?php echo bdj_svg( 'menu-' . bdj_activiteit_icoon( $act ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- eigen SVG-bestand ?>
							<span><?php echo esc_html( get_the_title( $act ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>
</header>

<main id="inhoud">
