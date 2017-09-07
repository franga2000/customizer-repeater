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
            } else if ( 'radio' === $control['type'] ) {
	            echo $this->radio_control( $control, $values );
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

	private function radio_control( $options, $values ) {
		$current_value = ( ! empty( $values[$options['id'] ] ) ) ? $values[$options['id'] ] : false; ?>
        <span class="customize-control-item"><?php echo esc_html( $options['label'] ); ?></span>
		<?php if ( $this->description_exists( $options ) ) : ?>
            <span class="customize-control-description">
                <?php echo esc_html( $options['description'] ); ?>
            </span>
		<?php endif; ?>
		<?php foreach ( $options[ 'choices' ] as $key => $value ) : ?>
            <label for="<?php echo esc_attr( $options[ 'id' ] ); ?>">
                <?php echo esc_html( $value ); ?>
            </label>
            <input
                class="customizer-repeater-radio-control repeater-value"
                type="radio" value="<?php echo esc_html( $key ); ?>"
                name="<?php echo esc_attr( $options[ 'id' ] ); ?>"
                data-id="<?php echo esc_attr( $options['id'] ) ?>"
                <?php checked( $current_value, $key ); ?>
            />
        <?php endforeach;
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

	private function description_exists( $settings ) {
		return ( isset( $settings[ 'description' ] ) && ! empty( $settings[ 'description' ] ) );
	}

}
