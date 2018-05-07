<?php
if ( ! defined('ABSPATH') ) {
	exit;
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

class Customizer_Repeater extends WP_Customize_Control {

	public $id;
	private $repeater_title = array();
    private $max = false;
	private $controls;

	/*Class constructor*/
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		/*Get options from customizer.php*/

        if ( isset( $args['max'] ) || ! empty( $args['max'] ) ) {
            if ( ! is_numeric( $args['max'] ) ) {
                throw new Exception( 'Customizer  Repeater expects Max argument to be a number.' );
            }

            $this->max = $args['max'];
        }


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
		wp_enqueue_style(
            'customizer-repeater-font-awesome',
            plugins_url( '/customizer-repeater/css/font-awesome.min.css', __DIR__ ),
            '1.0.0'
        );

		wp_enqueue_style(
            'customizer-repeater-admin-stylesheet',
            plugins_url( '/customizer-repeater/css/admin-style.css', __DIR__ ),
            '1.0.0'
        );

		wp_enqueue_script(
            'customizer-repeater-script',
            plugins_url( '/customizer-repeater/js/customizer_repeater.js', __DIR__ ),
            array('jquery', 'jquery-ui-draggable' ),
            '1.0.1',
            true
        );

		wp_enqueue_script(
            'customizer-repeater-fontawesome-iconpicker',
            plugins_url( '/customizer-repeater/js/fontawesome-iconpicker.min.js' ),
            array( 'jquery' ),
            '1.0.0',
            true
        );

		wp_enqueue_script(
            'customizer-repeater-iconpicker-control',
            plugins_url( '/customizer-repeater/js/iconpicker-control.js' ),
            array( 'jquery' ),
            '1.0.0',
            true
        );

		wp_enqueue_style(
            'customizer-repeater-fontawesome-iconpicker-script',
            plugins_url( '/customizer-repeater/css/fontawesome-iconpicker.min.css' )
        );
	}

	public function render_content() {

		/*Get values (json format)*/
		$values = $this->value();

		/*Decode values*/
		$values = json_decode( $values );

        $disabled = ( count( $values ) === $this->max ) ? 'disabled' : '';
        $max = ( $this->max ) ? sprintf('data-max="%s"', $this->max ) : '';
        ?>

		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<div class="customizer-repeater-general-control-repeater customizer-repeater-general-control-droppable">
			<?php $this->output_repeater_section( $values ); ?>
            <input type="hidden"
                   id="customizer-repeater-<?php echo $this->id; ?>-colector" <?php $this->link(); ?>
                   class="customizer-repeater-colector"/>
			</div>
		<button type="button" class="button add_field customizer-repeater-new-field" <?php echo $max;?> <?php echo $disabled;?>>
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
                    <span><?php echo esc_html( $this->repeater_title ); ?></span>
                    <svg x="0px" y="0px" viewBox="0 0 386.257 386.257" style="enable-background:new 0 0 386.257 386.257;" xml:space="preserve">
                        <polygon points="0,96.879 193.129,289.379 386.257,96.879 "/>
                    </svg>
                </div>
                <div class="customizer-repeater-box customizer-repeater-box-content-hidden">
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
            if ( in_array( $control[ 'type' ], $text_controls ) ) {
	            echo $this->input_control( $control, $values );
            } else if ( 'image' === $control[ 'type' ] ) {
                echo $this->image_control( $control, $values );
            } else if ( 'radio' === $control[ 'type' ] ) {
	            echo $this->radio_control( $control, $values );
            } else if ( 'select' === $control[ 'type' ] ) {
	            echo $this->select_control( $control, $values );
            }
        }
    }

	private function input_control( $options, $values ) {
	    $class = $this->get_control_class_name( $options[ 'type' ] );
	    $value = ( ! empty( $values[$options['id'] ] ) ) ? $values[$options['id'] ] : false;
	    ?>
        <div class="customizer-control">
            <span class="customize-control-title"><?php echo esc_html( $options['label'] ); ?></span>
	        <?php if ( $this->description_exists( $options ) ) : ?>
                <span class="customize-control-description">
                <?php echo esc_html( $options['description'] ); ?>
            </span>
	        <?php endif; ?>
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
            } ?>
		</div>
        <?php
	}

	private function image_control( $options, $values ) {
        $value = ( ! empty( $values[ $options['id'] ] ) ) ? $values[ $options['id'] ] : false;
		$img_url = ( ! empty( $value ) ) ? wp_get_attachment_image_url( absint( $value ), 'medium' ) : false;
        $hidden = ( ! empty( $img_url ) ) ? '' : 'display:none;';
        $upload_value = ( ! empty( $img_url ) ) ? esc_html__('Replace Image' ) : esc_html__( 'Upload Image' );

        $file_name = ( ! empty( $value ) ) ? basename( get_attached_file( $value ) ) : false;
        $file_ext = wp_check_filetype( $file_name );
        $hidden_name = ( $file_ext['ext'] === 'mp4' ) ? '' : 'display:none;';
    ?>
		<div class="customizer-repeater-image-control customizer-control">
            <span class="customize-control-title">
                <?php echo esc_html( $options['label'] ); ?>
            </span>
            <?php if ( $this->description_exists( $options ) ) : ?>
                <span class="customize-control-description">
                <?php echo esc_html( $options['description'] ); ?>
            </span>
            <?php endif; ?>
            <div class="customizer-repeater-image-wrapper">
                
                <p class="txt" style="<?php echo $hidden_name;  ?>"><?php echo $file_name; ?></p>
                <img src="<?php echo esc_url( $img_url ); ?>" alt="" style="<?php echo $hidden;  ?>">
            </div>

            <input type="hidden" class="widefat custom-media-url repeater-value" value="<?php echo esc_attr( $value ); ?>" data-id="<?php echo esc_attr( $options['id'] ) ?>">

            <input type="button" class="button button-primary customizer-repeater-custom-media-button" style="<?php echo ( empty( $value ) ) ? '' : 'display:none'; ?>" value="<?php echo esc_attr( $options['upload_button'] ); ?>" />

            <input type="button" class="button button-primary customizer-repeater-custom-media-button replace_button" style="<?php echo ( empty( $value ) ) ? 'display:none;' : ''; ?>" value="<?php echo esc_attr( $options['replace_button'] ); ?>" />

            <input type="button" class="button customizer-repeater-custom-media-remove" value="<?php esc_html_e( $options['remove_button']); ?>" style="<?php echo ( empty( $value ) ) ? 'display:none;' : ''; ?>" />
		</div>
		<?php
	}

	private function radio_control( $options, $values ) {
		$current_value = ( ! empty( $values[$options['id'] ] ) ) ? $values[$options['id'] ] : false; ?>
        <div class="customizer-control">
            <span class="customize-control-title"><?php echo esc_html( $options['label'] ); ?></span>
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
            <?php endforeach; ?>
        </div>
        <?php
    }

	private function select_control( $options, $values ) {
		$current_value = ( ! empty( $values[$options['id'] ] ) ) ? $values[$options['id'] ] : false; ?>
        <div class="customizer-control">
            <span class="customize-control-title"><?php echo esc_html( $options['label'] ); ?></span>
            <?php if ( $this->description_exists( $options ) ) : ?>
                <span class="customize-control-description">
                    <?php echo esc_html( $options['description'] ); ?>
                </span>
            <?php endif; ?>
            <select data-id="<?php echo esc_attr( $options['id'] ) ?>" class="customizer-repeater-select-control repeater-value">
            <?php foreach ( $options[ 'choices' ] as $key => $value ) : ?>
                <option value="<?php echo esc_attr( $key ) ?>" <?php selected( $current_value, $key ); ?>>
                    <?php echo esc_html( $value ) ?>
                </option>
            <?php endforeach; ?>
            </select>
        </div>
        <?php
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
