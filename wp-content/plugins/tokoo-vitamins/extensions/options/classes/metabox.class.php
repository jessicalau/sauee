<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Metabox Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
class CSFramework_Metabox {

	public $options = array();

	// run metabox construct
	public function __construct() {
		$options = array();

		$this->options = apply_filters( 'tokoo_metabox_options', $options );	

		if ( ! empty( $this->options ) ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_post' ) );
		}

	}

	// add metabox
	public function add_meta_box() {

		foreach ( $this->options as $value ) {
			add_meta_box( $value['id'], $value['title'], array( &$this, 'render_meta_box_content' ), $value['post_type'], $value['context'], $value['priority'], $value );
		}

	}

	// metabox render content
	public function render_meta_box_content( $post, $callback ) {

		global $post;

		wp_nonce_field( 'cs-framework-metabox', 'cs-framework-metabox-nonce' );

		$unique       = $callback['args']['id'];
		$sections     = $callback['args']['sections'];
		$meta_value   = get_post_meta( $post->ID, $unique, true );
		$current_id   = get_transient( 'cs_section_id'. $unique );
		$has_nav      = ( count( $sections ) >= 2 && $callback['args']['context'] != 'side' ) ? true : false;
		$show_all     = ( ! $has_nav ) ? ' cs-show-all' : '';
		$section_name = ( ! empty( $sections[0]['fields'] ) ) ? $sections[0]['name'] : $sections[1]['name'];
		$section_id   = ( ! empty( $current_id ) ) ? $current_id : $section_name;
		$section_id   = ( ! empty( $_GET['cs-section'] ) ) ? esc_attr( $_GET['cs-section'] ) : $section_id;

		echo '<div class="cs-framework cs-metabox-framework">';

			echo '<input type="hidden" name="cs_section_id['. $unique .']" class="cs-reset-section" value="'. $section_id .'">';

			echo '<div class="cs-body'. $show_all .'">';

				if( $has_nav ) {

					echo '<div class="cs-nav">';

						echo '<ul>';
						foreach( $sections as $value ) {

							$tab_icon = ( ! empty( $value['icon'] ) ) ? '<i class="cs-icon '. $value['icon'] .'"></i>' : '';

							if( isset( $value['fields'] ) ) {
								$active_section = ( $section_id == $value['name'] ) ? ' class="cs-section-active"' : '';
								echo '<li><a href="#"'. $active_section .' data-section="'. $value['name'] .'">'. $tab_icon . $value['title'] .'</a></li>';
							} else {
								echo '<li><div class="cs-seperator">'. $tab_icon . $value['title'] .'</div></li>';
							}

						}
						echo '</ul>';

					echo '</div>';

				}

				echo '<div class="cs-content">';

					echo '<div class="cs-sections">';
					foreach( $sections as $val ) {

						if( isset( $val['fields'] ) ) {

							$active_content = ( $section_id == $val['name'] ) ? ' style="display: block;"' : '';

							echo '<div id="cs-tab-'. $val['name'] .'" class="cs-section"'. $active_content .'>';
							echo ( isset( $val['title'] ) ) ? '<div class="cs-section-title"><h3>'. $val['title'] .'</h3></div>' : '';

							foreach ( $val['fields'] as $field_key => $field ) {

								$default    = ( isset( $field['default'] ) ) ? $field['default'] : '';
								$elem_value = ( is_array( $meta_value ) && isset( $meta_value[$field['id']] ) ) ? $meta_value[$field['id']] : $default;
								echo cs_add_element( $field, $elem_value, $unique );

							}
							echo '</div>';

						}
					}
					echo '</div>';

					echo '<div class="clear"></div>';

				echo '</div>';

				echo ( $has_nav ) ? '<div class="cs-nav-background"></div>' : '';

				echo '<div class="clear"></div>';

			echo '</div>';

		echo '</div>';

	}

	// save metabox options
	public function save_post( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return $post_id; }

		$nonce = ( isset( $_POST['cs-framework-metabox-nonce'] ) ) ? $_POST['cs-framework-metabox-nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'cs-framework-metabox' ) ) { return $post_id; }

		$post_type   = ( isset( $_POST['post_type'] ) ) ? $_POST['post_type'] : '';
		$meta_errors = array();

		if ( 'page' == $post_type ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) { return $post_id; }
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) { return $post_id; }
		}

		foreach ( $this->options as $request_value ) {

			if( $post_type == $request_value['post_type'] ) {

				$request_key = $request_value['id'];
				$meta_value  = get_post_meta( $post_id, $request_key, true );
				$request     = ( isset( $_POST[$request_key] ) ) ? $_POST[$request_key] : array();

				// ignore _nonce
				if( isset( $request['_nonce'] ) ) {
					unset( $request['_nonce'] );
				}

				foreach( $request_value['sections'] as $key => $section ) {

					if( isset( $section['fields'] ) ) {

						foreach( $section['fields'] as $field ) {

							if( isset( $field['type'] ) && isset( $field['id'] ) ) {

								$field_value = ( isset( $_POST[$request_key][$field['id']] ) ) ? $_POST[$request_key][$field['id']] : '';

								// sanitize options
								if( isset( $field['sanitize'] ) && $field['sanitize'] !== false ) {
									$sanitize_type = $field['sanitize'];
								} else if ( ! isset( $field['sanitize'] ) ) {
									$sanitize_type = $field['type'];
								}

								if( has_filter( 'cs_sanitize_'. $sanitize_type ) ) {
									$request[$field['id']] = apply_filters( 'cs_sanitize_' . $sanitize_type, $field_value, $field, $section['fields'] );
								}

								// validate options
								if ( isset( $field['validate'] ) && has_filter( 'cs_validate_'. $field['validate'] ) ) {

									$validate = apply_filters( 'cs_validate_' . $field['validate'], $field_value, $field, $section['fields'] );

									if( ! empty( $validate ) ) {

										$meta_errors[$field['id']] = array(
											'setting' => 'cs-framework-errors',
											'code'    => $field['id'],
											'message' => $validate,
											'type'    => 'error'
										);

										$default_value = isset( $field['default'] ) ? $field['default'] : '';
										$request[$field['id']] = ( isset( $meta_value[$field['id']] ) ) ? $meta_value[$field['id']] : $default_value;

									}

								}

							}

						}

					}

				}

				set_transient( 'cs_section_id'. $request_key, $_POST['cs_section_id'][$request_key], 5 );

				if( count( $meta_errors ) ) {
					set_transient( 'settings_errors', $meta_errors, 5 );
				}

				$request = apply_filters( 'cs_save_post', $request, $request_key, $meta_value, $this );

				if( empty( $request ) ) {

					delete_post_meta( $post_id, $request_key );

				} else {

					if( get_post_meta( $post_id, $request_key ) ) {

						update_post_meta( $post_id, $request_key, $request );

					} else {

						add_post_meta( $post_id, $request_key, $request );

					}

				}

			}

		}


	}

}

new CSFramework_Metabox();