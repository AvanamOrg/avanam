<?php
/**
 * The Background customize control extends the WP_Customize_Control class.
 *
 * @package customizer-controls
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}


/**
 * Class Base_Control_Background
 *
 * @access public
 */
class Base_Control_Background extends WP_Customize_Media_Control {
	/**
	 * Control type
	 *
	 * @var string
	 */
	public $type = 'base_background_control';

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $default = array();

	/**
	 * Additional arguments passed to JS.
	 *
	 * @var array
	 */
	public $input_attrs = array(
		'attachments' => array(
			'desktop' => array(),
			'tablet'  => array(),
			'mobile'  => array(),
		),
	);
	/**
	 * Additional arguments passed to JS.
	 *
	 * @var string
	 */
	public $mime_type = 'image';

	/**
	 * Send to JS.
	 */
	public function to_json() {
		parent::to_json();
		$value = $this->value();
		if ( $value && is_array( $value ) ) {
			foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
				if ( isset( $value[ $device ] ) && isset( $value[ $device ]['image'] ) && isset( $value[ $device ]['image']['url'] ) && ! empty( $value[ $device ]['image']['url'] ) ) {
					$attachment_id                               = attachment_url_to_postid( $value[ $device ]['image']['url'] );
					$this->input_attrs['attachments'][ $device ] = wp_prepare_attachment_for_js( $attachment_id );
				}
			}
		}
		$this->json['input_attrs'] = $this->input_attrs;
		$this->json['default'] = $this->default;
	}
	/**
	 * Empty Render Function to prevent errors.
	 */
	public function render_content() {
	}
}
$wp_customize->register_control_type( 'Base_Control_Background' );
