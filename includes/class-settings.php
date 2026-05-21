<?php
/**
 * Plugin settings page.
 *
 * @package InstagramProfileEmbed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin settings for Instagram Profile Embed.
 */
class IPE_Settings {

	const OPTION_KEY = 'ipe_settings';

	/**
	 * Register hooks.
	 */
	public static function register() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_defaults() {
		return array(
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
			__( 'Instagram Profile Embed', 'instagram-profile-embed' ),
			__( 'Instagram Profile Embed', 'instagram-profile-embed' ),
			'manage_options',
			'ipe-settings',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Register settings fields.
	 */
	public static function register_settings() {
		register_setting(
			'ipe_settings_group',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
				'default'           => self::get_defaults(),
			)
		);

		add_settings_section(
			'ipe_main_section',
			__( 'Upotusasetukset', 'instagram-profile-embed' ),
			'__return_false',
			'ipe-settings'
		);

		add_settings_field(
			'ipe_default_height',
			__( 'Oletuskorkeus (px)', 'instagram-profile-embed' ),
			array( __CLASS__, 'render_default_height_field' ),
			'ipe-settings',
			'ipe_main_section'
		);

		add_settings_field(
			'ipe_lazy_load',
			__( 'Lazy load', 'instagram-profile-embed' ),
			array( __CLASS__, 'render_lazy_load_field' ),
			'ipe-settings',
			'ipe_main_section'
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
			'default_height' => max( 200, min( 1200, (int) ( $input['default_height'] ?? $defaults['default_height'] ) ) ),
			'lazy_load'      => empty( $input['lazy_load'] ) ? 0 : 1,
		);
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
			<?php esc_html_e( 'Lataa Instagram-upotus vasta kun se tulee näkyviin', 'instagram-profile-embed' ); ?>
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
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Instagram Profile Embed', 'instagram-profile-embed' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'ipe_settings_group' );
				do_settings_sections( 'ipe-settings' );
				submit_button();
				?>
			</form>
			<hr />
			<h2><?php esc_html_e( 'Käyttö', 'instagram-profile-embed' ); ?></h2>
			<p><?php esc_html_e( 'Shortcode:', 'instagram-profile-embed' ); ?></p>
			<code>[instagram_profile username="kayttajanimi"]</code>
			<p><?php esc_html_e( 'Elementor: lisää Shortcode-widget ja käytä shortcodea.', 'instagram-profile-embed' ); ?></p>
		</div>
		<?php
	}
}
