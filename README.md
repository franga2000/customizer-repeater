# Customizer Repeater

## What is Customizer Repeater?

Customizer Repeater is a custom control for the WordPress Theme Customizer. It's designed to be super user-friendly not only for your customers but for you too.

## How to install?

### (Option 1) Use composer.

Step 1: Add customizer repeater to composer.json repositories section.

         "repositories" : [
                  {
                           "type": "vcs",
                           "url": "https://github.com/mrbobbybryant/customizer-repeater"
                  }
         ]

Step 2: Add Composer Autoload Call to your functions.php if you havn't already.
 
         if ( file_exists( get_template_directory() . '/vendor/autoload.php' ) ) {
            require( 'vendor/autoload.php' );
         }

Step 3: Run `composer update or composer install`

### (Option 2) Clone Github repo

Step 1: Clone repo into you theme folder.
 
         git clone https://github.com/mrbobbybryant/customizer-repeater.git

Step 2: Require root file for customizer-repeater in you `functions.php` file.

         if ( file_exists( get_template_directory() . '/customizer-repeater/customizer-repeater.php' ) ) {
            require_once get_template_directory() . '/customizer-repeater/customizer-repeater.php';
         }

## How to use?

Step 1: Create a customizer setting. This is done the normal way.

          $wp_customize->add_setting( 'customizer_repeater_example', array(
             'sanitize_callback' => 'customizer_repeater_sanitize'
          ));

Step 2: Create a control using the Customizer Repeater field.

         $manager->add_control( new Customizer_Repeater( $manager, 'text_slider', array(
             'label'   => esc_html__( 'Text Slider','drb' ),
             'section' => 'text_slider_section',
             'priority' => 1,
             'controls' => [
                 [
                     'type'  => 'text',
                     'id'    => 'title',
                     'label' =>  esc_html__( 'Text Slide Title', 'text-domain' )
                 ],
                 [
                     'type'  =>  'url',
                     'id'    =>  'link',
                     'label' =>  esc_html__( 'Text Slide Link', 'text-domain' )
                 ],
                 [
                     'type'  =>  'textarea',
                     'id'    =>  'text',
                     'label' =>  esc_html__( 'Text Slide Description', 'text-domain' )
                 ],
                 [
                     'type'  =>  'image',
                     'label' =>  esc_html__( 'Slide Image', 'text-domain' ),
                     'id'    =>  'image',
                     'description'   =>  esc_html__( 'Slider Background Image', 'text-domain' )
                 ],
             ]
             ) ) );

### Currently the Customizer Repeater supports the following field types:
* Text Field
* Link Field
* Textarea
* Image/Attachment Upload Field

### Lets break down one of the controls above.
`'type' => 'image'` - This is the type of field you want to use. (See list above)

`'label' =>  esc_html__( 'Slide Image', 'text-domain' ),` - This is the label that will appear by the field in the repeater.

`'id'    =>  'image'` - This is the key which we will use next to pull this value out when displaying it on the frontend.

`'description'   =>  esc_html__( 'Slider Background Image', 'text-domain' )` - This lets you pass a description similar to standard Customizer fields.

## How to display repeater on the frontend.
Step 1: Customizer Repeater comes with a helper function to fetch a repeater fields data.

         $text_slider = get_customizer_values( 'text_slider' );

Just pass it the name you gave the repeater field when you created it.

Step 2: Loop over this array of data and output the values:

          $text_slider = get_customizer_values( 'text_slider' );
          
          if ( ! empty( $text_slider ) ) :
            foreach ( $text_slider as $slide ) {
                if ( isset( $slide[ 'title' ] ) && ! empty( $slide[ 'title' ] ) ) ) {
                    echo $slide[ 'title' ]
                }
                
                if ( isset( $slide[ 'link' ] ) && ! empty( $slide[ 'link' ] ) ) ) {
                    echo $slide[ 'link' ]
                }
            }
          endif;

## Working with Images.
Customizer Repeater saves the selected image's ID. So when loop through the data you will need to fetch the image url. The benefit to this approach is that it gives you complete control over what size image to fetch.

          $text_slider = get_customizer_values( 'text_slider' );
          
          if ( ! empty( $text_slider ) ) :
            foreach ( $text_slider as $slide ) {
                if ( isset( $slide[ 'image' ] ) && ! empty( $slide[ 'image' ] ) ) ) {
                    $url = wp_get_attachment_image_url( $slide[ 'image' ], 'full' );
                    if ( ! empty( $url ) ) {
                           echo $url;
                    }
                }
            }
          endif;

## Roadmap
* Add more field types (Radio, Checkbox, Select, etc...)
* Allow you to change image upload button text from always being 'Image'.
* Allow the ability to pass custom sanitization callbacks for each control.
* Add hooks and filter to make repeater more customizable
* Create autosuggest field.
* Allow you the abilty to limit the number of repeaters a user can make.
