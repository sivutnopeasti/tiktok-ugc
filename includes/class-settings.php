<?php
/**
 * Plugin settings page.
 *
 * @package TikTokProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin settings for TikTok Profile Embed.
 */
class TPE_Settings {

	const OPTION_KEY = 'tpe_settings';

	/**
	 * Register hooks.
	 */
	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_post_tpe_flush_cache', array( __CLASS__, 'handle_flush_cache' ) );
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_defaults() {
		return array(
			'cache_hours'    => 24,
			'default_height' => 600,
			'lazy_load'      => 0,
		);
	}

	/**
	 * Get merged settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_settings() {
		$stored = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		return wp_parse_args( $stored, self::get_defaults() );
	}

	/**
	 * Add settings page under Settings menu.
	 */
	public static function add_menu() {
		add_options_page(
			__( 'TikTok Profile Embed', 'tiktok-profile-embed' ),
			__( 'TikTok Profile Embed', 'tiktok-profile-embed' ),
			'manage_options',
			'tpe-settings',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Register settings fields.
	 */
	public static function register_settings() {
		register_setting(
			'tpe_settings_group',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
				'default'           => self::get_defaults(),
			)
		);

		add_settings_section(
			'tpe_main_section',
			__( 'Upotusasetukset', 'tiktok-profile-embed' ),
			'__return_false',
			'tpe-settings'
		);

		add_settings_field(
			'tpe_cache_hours',
			__( 'Cache-aika (tuntia)', 'tiktok-profile-embed' ),
			array( __CLASS__, 'render_cache_hours_field' ),
			'tpe-settings',
			'tpe_main_section'
		);

		add_settings_field(
			'tpe_default_height',
			__( 'Oletuskorkeus (px)', 'tiktok-profile-embed' ),
			array( __CLASS__, 'render_default_height_field' ),
			'tpe-settings',
			'tpe_main_section'
		);

		add_settings_field(
			'tpe_lazy_load',
			__( 'Lazy load', 'tiktok-profile-embed' ),
			array( __CLASS__, 'render_lazy_load_field' ),
			'tpe-settings',
			'tpe_main_section'
		);
	}

	/**
	 * Sanitize settings input.
	 *
	 * @param array<string, mixed> $input Raw input.
	 * @return array<string, mixed>
	 */
	public static function sanitize_settings( $input ) {
		$defaults = self::get_defaults();
		$input    = is_array( $input ) ? $input : array();

		return array(
			'cache_hours'    => max( 1, min( 168, (int) ( $input['cache_hours'] ?? $defaults['cache_hours'] ) ) ),
			'default_height' => max( 200, min( 1200, (int) ( $input['default_height'] ?? $defaults['default_height'] ) ) ),
			'lazy_load'      => empty( $input['lazy_load'] ) ? 0 : 1,
		);
	}

	/**
	 * Render cache hours field.
	 */
	public static function render_cache_hours_field() {
		$settings = self::get_settings();
		?>
		<input
			type="number"
			min="1"
			max="168"
			name="<?php echo esc_attr( self::OPTION_KEY ); ?>[cache_hours]"
			value="<?php echo esc_attr( (string) $settings['cache_hours'] ); ?>"
			class="small-text"
		/>
		<p class="description">
			<?php esc_html_e( 'Kuinka kauan TikTok-upotus tallennetaan välimuistiin.', 'tiktok-profile-embed' ); ?>
		</p>
		<?php
	}

	/**
	 * Render default height field.
	 */
	public static function render_default_height_field() {
		$settings = self::get_settings();
		?>
		<input
			type="number"
			min="200"
			max="1200"
			name="<?php echo esc_attr( self::OPTION_KEY ); ?>[default_height]"
			value="<?php echo esc_attr( (string) $settings['default_height'] ); ?>"
			class="small-text"
		/>
		<p class="description">
			<?php esc_html_e( 'Oletuskorkeus shortcodelle ja lohkolle, jos korkeutta ei ole määritelty.', 'tiktok-profile-embed' ); ?>
		</p>
		<?php
	}

	/**
	 * Render lazy load field.
	 */
	public static function render_lazy_load_field() {
		$settings = self::get_settings();
		?>
		<label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( self::OPTION_KEY ); ?>[lazy_load]"
				value="1"
				<?php checked( ! empty( $settings['lazy_load'] ) ); ?>
			/>
			<?php esc_html_e( 'Lataa TikTok-upotus vasta kun se tulee näkyviin', 'tiktok-profile-embed' ); ?>
		</label>
		<?php
	}

	/**
	 * Render settings page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$flushed = isset( $_GET['tpe_flushed'] ) && $_GET['tpe_flushed'] === '1';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'TikTok Profile Embed', 'tiktok-profile-embed' ); ?></h1>

			<?php if ( $flushed ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Välimuisti tyhjennetty.', 'tiktok-profile-embed' ); ?></p>
				</div>
			<?php endif; ?>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'tpe_settings_group' );
				do_settings_sections( 'tpe-settings' );
				submit_button();
				?>
			</form>

			<hr />

			<h2><?php esc_html_e( 'Välimuisti', 'tiktok-profile-embed' ); ?></h2>
			<p><?php esc_html_e( 'Tyhjennä kaikki tallennetut TikTok-upotukset.', 'tiktok-profile-embed' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'tpe_flush_cache' ); ?>
				<input type="hidden" name="action" value="tpe_flush_cache" />
				<?php submit_button( __( 'Tyhjennä cache', 'tiktok-profile-embed' ), 'secondary' ); ?>
			</form>

			<hr />

			<h2><?php esc_html_e( 'Käyttö', 'tiktok-profile-embed' ); ?></h2>
			<p><?php esc_html_e( 'Shortcode:', 'tiktok-profile-embed' ); ?></p>
			<code>[tiktok_profile username="kayttajanimi"]</code>
			<p><?php esc_html_e( 'Gutenberg-lohko: lisää lohko "TikTok Profile Embed" editorissa.', 'tiktok-profile-embed' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Handle cache flush action.
	 */
	public static function handle_flush_cache() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Ei oikeuksia.', 'tiktok-profile-embed' ) );
		}

		check_admin_referer( 'tpe_flush_cache' );

		TPE_Cache::flush_all();

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'        => 'tpe-settings',
					'tpe_flushed' => '1',
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}
}
