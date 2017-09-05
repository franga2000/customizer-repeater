<?php
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

class Customizer_Repeater extends WP_Customize_Control {

	public $id;
	private $repeater_title = array();
	private $controls;

	/*Class constructor*/
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		/*Get options from customizer.php*/

		if ( ! isset( $this->label ) || empty( $this->label ) ) {
		    throw new Exception( 'Missing Label Field for Customizer Repeater' );
        }

        if ( ! isset( $args['controls'] ) || empty( $args['controls'] ) ) {
	        throw new Exception( 'You did not pass any controls to Customizer Repeater' );
        }

		$this->repeater_title = $this->label;
		//TODO need to set default sanitization functions.
		$this->controls = $args[ 'controls' ];

		if ( ! empty( $args['id'] ) ) {
			$this->id = $args['id'];
		}
	}

	/*Enqueue resources for the control*/
	public function enqueue() {
		wp_enqueue_style( 'customizer-repeater-font-awesome', CUSTOMIZER_REPEATER_URL .'/customizer-repeater/css/font-awesome.min.css','1.0.0' );

		wp_enqueue_style( 'customizer-repeater-admin-stylesheet', CUSTOMIZER_REPEATER_URL .'/customizer-repeater/css/admin-style.css','1.0.0' );

		wp_enqueue_script( 'customizer-repeater-script', CUSTOMIZER_REPEATER_URL . '/customizer-repeater/js/customizer_repeater.js', array('jquery', 'jquery-ui-draggable' ), '1.0.1', true  );

		wp_enqueue_script( 'customizer-repeater-fontawesome-iconpicker', CUSTOMIZER_REPEATER_URL . '/customizer-repeater/js/fontawesome-iconpicker.min.js', array( 'jquery' ), '1.0.0', true );

		wp_enqueue_script( 'customizer-repeater-iconpicker-control', CUSTOMIZER_REPEATER_URL . '/customizer-repeater/js/iconpicker-control.js', array( 'jquery' ), '1.0.0', true );

		wp_enqueue_style( 'customizer-repeater-fontawesome-iconpicker-script', CUSTOMIZER_REPEATER_URL . '/customizer-repeater/css/fontawesome-iconpicker.min.css' );
	}

	public function render_content() {

		/*Get values (json format)*/
		$values = $this->value();

		/*Decode values*/
		$values = json_decode( $values );
        ?>

		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<div class="customizer-repeater-general-control-repeater customizer-repeater-general-control-droppable">
			<?php $this->output_repeater_section( $values ); ?>
            <input type="hidden"
                   id="customizer-repeater-<?php echo $this->id; ?>-colector" <?php $this->link(); ?>
                   class="customizer-repeater-colector"/>
			</div>
		<button type="button" class="button add_field customizer-repeater-new-field">
			<?php esc_html_e( 'Add new field', 'your-textdomain' ); ?>
		</button>
		<?php
	}

	private function output_repeater_section( $values ) {
		$num_repeaters = ( 0 === count( $values ) ) ? 1 :  ( count( $values ) );
        $counter = 0;
		while( $counter < $num_repeaters ) : ?>
            <div class="customizer-repeater-general-control-repeater-container customizer-repeater-draggable">
                <div class="customizer-repeater-customize-control-title">
					<?php echo esc_html( $this->repeater_title ); ?>
                </div>
                <div class="customizer-repeater-box-content-hidden">
					<?php $this->output_repeater_setting( (array) $values[ $counter ] ); ?>
                    <input type="hidden" class="social-repeater-box-id" value="<?php if ( ! empty( $this->id ) ) {
						echo esc_attr( $this->id );
					} ?>">
                    <button type="button" class="social-repeater-general-control-remove-field button" >
						<?php esc_html_e( 'Delete field', 'your-textdomain' ); ?>
                    </button>

                </div>
            </div>
			<?php $counter++;
        endwhile;
	}

	private function output_repeater_setting( $values ) {
	    $text_controls = [ 'text', 'textarea', 'url' ];
        foreach( $this->controls as $control ) {
            if ( in_array( $control['type'], $text_controls ) ) {
	            echo $this->input_control( $control, $values );
            } else if ( 'image' === $control['type'] ) {
                echo $this->image_control( $control, $values );
            }
        }
    }

	private function input_control( $options, $values ) {
	    $class = $this->get_control_class_name( $options[ 'type' ] );
	    $value = ( ! empty( $values[$options['id'] ] ) ) ? $values[$options['id'] ] : false;
	    ?>
		<span class="customize-control-item"><?php echo esc_html( $options['label'] ); ?></span>
		<?php
		if( $options['type'] === 'textarea' ){ ?>
			<textarea
                class="<?php echo esc_attr( $class ); ?>"
                placeholder="<?php echo esc_attr( $options['label'] ); ?>"
                data-id="<?php echo esc_attr( $options['id'] ) ?>"><?php echo ( ! empty($options['sanitize_callback'] ) ?  call_user_func_array( $options['sanitize_callback'], array( $value ) ) : esc_attr($value) ); ?></textarea>
			<?php
		} else { ?>
            <input
                type="text"
                value="<?php echo esc_attr( $value ); ?>"
                class="<?php echo esc_attr( $class ); ?>"
                placeholder="<?php echo esc_attr( $options['label'] ); ?>"
                data-id="<?php echo esc_attr( $options['id'] ) ?>"
            />
			<?php
		}
	}

	private function get_control_class_name( $type ) {
		switch ( $type ) {
			case 'text':
				return 'customizer-repeater-title-control repeater-value';
			case 'textarea':
				return 'customizer-repeater-text-control repeater-value';
			case 'url':
				return 'customizer-repeater-link-control repeater-value';
		}
    }

	private function icon_picker_control($value = '', $show = ''){ ?>
		<div class="social-repeater-general-control-icon" <?php if( $show === 'customizer_repeater_image' || $show === 'customizer_repeater_none' ) { echo 'style="display:none;"'; } ?>>
            <span class="customize-control-title">
                <?php esc_html_e('Icon','your-textdomain'); ?>
            </span>
			<span class="description customize-control-description">
                <?php
                echo sprintf(
	                __( 'Note: Some icons may not be displayed here. You can see the full list of icons at %1$s', 'your-textdomain' ),
	                sprintf( '<a href="http://fontawesome.io/icons/" rel="nofollow">%s</a>', esc_html__( 'http://fontawesome.io/icons/', 'your-textdomain' ) )
                ); ?>
            </span>
			<div class="input-group icp-container">
				<input data-placement="bottomRight" class="icp icp-auto" value="<?php if(!empty($value)) { echo esc_attr( $value );} ?>" type="text">
				<span class="input-group-addon"></span>
			</div>
		</div>
		<?php
	}

	private function description_exists( $settings ) {
	    return ( isset( $settings[ 'description' ] ) && ! empty( $settings[ 'description' ] ) );
    }

	private function image_control( $options, $values ) {
        $value = ( ! empty( $values[ $options['id'] ] ) ) ? $values[ $options['id'] ] : false;
		$img_url = ( ! empty( $value ) ) ? wp_get_attachment_image_url( absint( $value ), 'medium' ) : false;
        $hidden = ( ! empty( $img_url ) ) ? '' : 'display:none;';
        $upload_value = ( ! empty( $img_url ) ) ? esc_html__('Replace Image' ) : esc_html__( 'Upload Image' );
    ?>
		<div class="customizer-repeater-image-control">
            <span class="customize-control-title">
                <?php echo esc_html( $options['label'] ); ?>
            </span>
            <?php if ( $this->description_exists( $options ) ) : ?>
                <span class="customize-control-description">
                <?php echo esc_html( $options['description'] ); ?>
            </span>
            <?php endif; ?>
            <div class="customizer-repeater-image-wrapper">
                <img src="<?php echo esc_url( $img_url ); ?>" alt="" style="<?php echo $hidden;  ?>">
            </div>
			<input type="hidden" class="widefat custom-media-url repeater-value" value="<?php echo esc_attr( $value ); ?>" data-id="<?php echo esc_attr( $options['id'] ) ?>">
			<input type="button" class="button button-primary customizer-repeater-custom-media-button" value="<?php echo esc_attr( $upload_value ); ?>" />
		    <input type="button" class="button customizer-repeater-custom-media-remove" value="<?php esc_html_e( 'Remove Image' ); ?>" style="<?php echo ( empty( $img_url ) ) ? 'display:none;' : ''; ?>" />
		</div>
		<?php
	}

	private function icon_type_choice($value='customizer_repeater_icon'){ ?>
		<span class="customize-control-title">
            <?php esc_html_e('Image type','your-textdomain');?>
        </span>
		<select class="customizer-repeater-image-choice">
			<option value="customizer_repeater_icon" <?php selected($value,'customizer_repeater_icon');?>><?php esc_html_e('Icon','your-textdomain'); ?></option>
			<option value="customizer_repeater_image" <?php selected($value,'customizer_repeater_image');?>><?php esc_html_e('Image','your-textdomain'); ?></option>
			<option value="customizer_repeater_none" <?php selected($value,'customizer_repeater_none');?>><?php esc_html_e('None','your-textdomain'); ?></option>
		</select>
		<?php
	}

	private function repeater_control($value = ''){
		$social_repeater = array();
		$show_del        = 0; ?>
		<span class="customize-control-title"><?php esc_html_e( 'Social icons', 'your-textdomain' ); ?></span>
		<?php
		if(!empty($value)) {
			$social_repeater = json_decode( html_entity_decode( $value ), true );
		}
		if ( ( count( $social_repeater ) == 1 && '' === $social_repeater[0] ) || empty( $social_repeater ) ) { ?>
			<div class="customizer-repeater-social-repeater">
				<div class="customizer-repeater-social-repeater-container">
					<div class="customizer-repeater-rc input-group icp-container">
						<input data-placement="bottomRight" class="icp icp-auto" value="<?php if(!empty($value)) { echo esc_attr( $value ); } ?>" type="text">
						<span class="input-group-addon"></span>
					</div>

					<input type="text" class="customizer-repeater-social-repeater-link"
					       placeholder="<?php esc_html_e( 'Link', 'your-textdomain' ); ?>">
					<input type="hidden" class="customizer-repeater-social-repeater-id" value="">
					<button class="social-repeater-remove-social-item" style="display:none">
						<?php esc_html_e( 'X', 'your-textdomain' ); ?>
					</button>
				</div>
				<input type="hidden" id="social-repeater-socials-repeater-colector" class="social-repeater-socials-repeater-colector" value=""/>
			</div>
			<button class="social-repeater-add-social-item"><?php esc_html_e( 'Add icon', 'your-textdomain' ); ?></button>
			<?php
		} else { ?>
			<div class="customizer-repeater-social-repeater">
				<?php
				foreach ( $social_repeater as $social_icon ) {
					$show_del ++; ?>
					<div class="customizer-repeater-social-repeater-container">
						<div class="customizer-repeater-rc input-group icp-container">
							<input data-placement="bottomRight" class="icp icp-auto" value="<?php if( !empty($social_icon['icon']) ) { echo esc_attr( $social_icon['icon'] ); } ?>" type="text">
							<span class="input-group-addon"></span>
						</div>
						<input type="text" class="customizer-repeater-social-repeater-link"
						       placeholder="<?php esc_html_e( 'Link', 'your-textdomain' ); ?>"
						       value="<?php if ( ! empty( $social_icon['link'] ) ) {
							       echo esc_url( $social_icon['link'] );
						       } ?>">
						<input type="hidden" class="customizer-repeater-social-repeater-id"
						       value="<?php if ( ! empty( $social_icon['id'] ) ) {
							       echo esc_attr( $social_icon['id'] );
						       } ?>">
						<button class="social-repeater-remove-social-item"
						        style="<?php if ( $show_del == 1 ) {
							        echo "display:none";
						        } ?>"><?php esc_html_e( 'X', 'your-textdomain' ); ?></button>
					</div>
					<?php
				} ?>
				<input type="hidden" id="social-repeater-socials-repeater-colector"
				       class="social-repeater-socials-repeater-colector"
				       value="<?php echo esc_textarea( html_entity_decode( $value ) ); ?>" />
			</div>
			<button class="social-repeater-add-social-item"><?php esc_html_e( 'Add icon', 'your-textdomain' ); ?></button>
			<?php
		}
	}
}
