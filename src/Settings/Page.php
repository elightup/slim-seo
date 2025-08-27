<?php
namespace SlimSEO\Settings;

class Page {
	public static function setup(): void {
		add_action( 'admin_menu', [ __CLASS__, 'add_menu' ] );
	}

	public static function add_menu(): void {
		$page_hook = add_options_page(
			__( 'Slim SEO', 'slim-seo' ),
			__( 'Slim SEO', 'slim-seo' ),
			'manage_options',
			'slim-seo',
			[ __CLASS__, 'render' ]
		);
		add_action( "admin_print_styles-{$page_hook}", [ __CLASS__, 'enqueue' ] );
		add_action( "load-{$page_hook}", [ __CLASS__, 'save' ] );
	}

	public static function render(): void {
		?>
		<div class="wrap">
			<h1 class="ss-title">
				<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M472 0H40C17.9086 0 0 17.9086 0 40V472C0 494.091 17.9086 512 40 512H472C494.091 512 512 494.091 512 472V40C512 17.9086 494.091 0 472 0Z" fill="url(#paint0_linear)"/><path d="M259.353 398.8C238.82 398.8 220.42 395.467 204.153 388.8C187.886 382.133 174.82 372.267 164.953 359.2C155.353 346.133 150.286 330.4 149.753 312H222.553C223.62 322.4 227.22 330.4 233.353 336C239.486 341.333 247.486 344 257.353 344C267.486 344 275.486 341.733 281.353 337.2C287.22 332.4 290.153 325.867 290.153 317.6C290.153 310.667 287.753 304.933 282.953 300.4C278.42 295.867 272.686 292.133 265.753 289.2C259.086 286.267 249.486 282.933 236.953 279.2C218.82 273.6 204.02 268 192.553 262.4C181.086 256.8 171.22 248.533 162.953 237.6C154.686 226.667 150.553 212.4 150.553 194.8C150.553 168.667 160.02 148.267 178.953 133.6C197.886 118.667 222.553 111.2 252.953 111.2C283.886 111.2 308.82 118.667 327.753 133.6C346.686 148.267 356.82 168.8 358.153 195.2H284.153C283.62 186.133 280.286 179.067 274.153 174C268.02 168.667 260.153 166 250.553 166C242.286 166 235.62 168.267 230.553 172.8C225.486 177.067 222.953 183.333 222.953 191.6C222.953 200.667 227.22 207.733 235.753 212.8C244.286 217.867 257.62 223.333 275.753 229.2C293.886 235.333 308.553 241.2 319.753 246.8C331.22 252.4 341.086 260.533 349.353 271.2C357.62 281.867 361.753 295.6 361.753 312.4C361.753 328.4 357.62 342.933 349.353 356C341.353 369.067 329.62 379.467 314.153 387.2C298.686 394.933 280.42 398.8 259.353 398.8Z" fill="#fff"/><defs><linearGradient id="paint0_linear" x1="0" y1="0" x2="512" y2="512" gradientUnits="userSpaceOnUse"><stop stop-color="#C21500"/><stop offset="1" stop-color="#FFC500"/></linearGradient></defs></svg>
				<?php echo esc_html( get_admin_page_title() ); ?>

				<?php if ( ! defined( 'SLIM_SEO_PRO_VER' ) ) : ?>
					<a href="https://elu.to/ssp" target="_blank" class="ss-title__upgrade">
						<span class="dashicons dashicons-awards"></span>
						<strong><?php esc_html_e( 'Pro', 'slim-seo' ); ?></strong>
					</a>
				<?php endif ?>

				<a href="https://elu.to/ssd" target="_blank">
					<span class="dashicons dashicons-editor-help"></span>
					<?php esc_html_e( 'Documentation', 'slim-seo' ); ?>
				</a>
				<a href="https://elu.to/ssfb" target="_blank">
					<span class="dashicons dashicons-groups"></span>
					<?php esc_html_e( 'Facebook Group', 'slim-seo' ); ?>
				</a>
			</h1>

			<div class="ss-content" id="poststuff">

				<form action="" method="post" class="ss-tabs">
					<nav class="ss-tab-list">
						<?php
						$tabs = apply_filters( 'slim_seo_settings_tabs', [] );
						foreach ( $tabs as $key => $label ) {
							printf( '<a href="#%s" class="ss-tab">%s</a>', esc_attr( $key ), esc_html( $label ) );
						}
						?>
					</nav>
					<?php
					wp_nonce_field( 'save' );
					$panes = apply_filters( 'slim_seo_settings_panes', [] );
					echo implode( '', $panes ); // @codingStandardsIgnoreLine.
					?>
				</form>

				<aside class="ss-sidebar">
					<?php if ( ! defined( 'SLIM_SEO_PRO_VER' ) ) : ?>
						<div class="ss-upgrade postbox">
							<h3 class="hndle"><?php esc_html_e( 'Advanced SEO features', 'slim-seo' ); ?></h3>
							<div class="inside">
								<p>
									<?php
									// Translators: %1$s - plugin URL, %2$s - plugin name.
									echo wp_kses_post( sprintf( __( 'Wanna advanced SEO features without complexity? Check out <a href="%1$s"><strong>%2$s</strong></a>, our powerful & lightweight pro version that has:', 'slim-seo' ), 'https://elu.to/ssp', 'Slim SEO Pro' ) );
									?>
								</p>
								<ul>
									<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Visual schema builder', 'slim-seo' ) ?></li>
									<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( '30+ pre-built schema types', 'slim-seo' ) ?></li>
									<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Custom schema with JSON-LD', 'slim-seo' ) ?></li>
									<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Contextual link suggestions', 'slim-seo' ) ?></li>
									<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Real-time link health monitoring', 'slim-seo' ) ?></li>
									<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Broken link repair', 'slim-seo' ) ?></li>
									<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Link updater', 'slim-seo' ) ?></li>
									<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'And more in the future...', 'slim-seo' ) ?></li>
								</ul>
								<a class="button button-primary" href="https://elu.to/ssp" target="_blank">
									<?php // Translators: %s - plugin name ?>
									<?php echo esc_html( sprintf( __( 'Get %s', 'slim-seo' ), 'Slim SEO Pro' ) ); ?> &rarr;
								</a>
							</div>
						</div>
					<?php endif ?>

					<div class="postbox">
						<h3 class="hndle"><?php esc_html_e( 'Our WordPress products', 'slim-seo' ) ?></h3>
						<div class="inside">
							<p><?php esc_html_e( 'Like this plugin? Check out our other WordPress products:', 'slim-seo' ) ?></p>
							<p><a href="https://elu.to/ssm" target="_blank"><strong>Meta Box</strong></a>: <?php esc_html_e( 'A framework for dynamic WordPress websites', 'slim-seo' ) ?></p>
							<p><a href="https://wordpress.org/plugins/falcon/" target="_blank"><strong>Falcon</strong></a>: <?php esc_html_e( 'WordPress optimization & tweaks', 'slim-seo' ) ?></p>
						</div>
					</div>

					<div class="postbox">
						<h3 class="hndle">
							<span><?php esc_html_e( 'Write a review for Slim SEO', 'slim-seo' ) ?></span>
						</h3>
						<div class="inside">
							<p><?php esc_html_e( 'If you like Slim SEO, please write a review on WordPress.org to help us spread the word. We really appreciate that!', 'slim-seo' ) ?></p>
							<p><a href="https://elu.to/ssr" target="_blank"><?php esc_html_e( 'Write a review', 'slim-seo' ) ?> &rarr;</a></p>
						</div>
					</div>
				</aside>
			</div>

		</div>
		<?php
	}

	public static function enqueue(): void {
		wp_enqueue_style( 'slim-seo-components', 'https://cdn.jsdelivr.net/gh/elightup/slim-seo@master/css/components.css', [], '1.0.0' );
		wp_enqueue_style( 'slim-seo-settings', 'https://cdn.jsdelivr.net/gh/elightup/slim-seo@master/css/settings.css', [], '1.0.0' );
		wp_enqueue_script( 'slim-seo-settings', 'https://cdn.jsdelivr.net/gh/elightup/slim-seo@master/js/settings.js', [], '1.0.0', true );
	}

	public static function save(): void {
		if ( empty( $_POST['submit'] ) || ! check_ajax_referer( 'save', false, false ) ) {
			return;
		}

		do_action( 'slim_seo_save' );

		add_settings_error( null, 'slim-seo', __( 'Settings updated.', 'slim-seo' ), 'success' );
	}
}
