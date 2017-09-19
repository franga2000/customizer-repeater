/* global jQuery */
/* global wp */
function media_upload(button_class) {
    'use strict';
    jQuery('body').on('click', button_class, function () {
        var button_id = '#' + jQuery(this).attr('id');
        var parent = jQuery(this).parent();
        var display_field = jQuery(this).parent().find('.custom-media-url');
        var image = parent[0].querySelector( 'img' );
        var uploadBtn = parent[0].querySelector('.customizer-repeater-custom-media-button');
        var removeButton = parent[0].querySelector('.customizer-repeater-custom-media-remove');

        wp.media.editor.send.attachment = function (props, attachment) {
            image.setAttribute( 'src', attachment.url );
            image.style.display = 'block';

            removeButton.style.display = 'inline-block';
            uploadBtn.value = 'Replace Image';

            display_field.val(attachment.id);
            display_field.trigger('change');
        };
        wp.media.editor.open(button_class);
        window.send_to_editor = function (html) {

        };
        return false;
    });
}

/********************************************
 *** Generate unique id ***
 *********************************************/
function customizer_repeater_uniqid(prefix, more_entropy) {
    'use strict';
    if (typeof prefix === 'undefined') {
        prefix = '';
    }

    var retId;
    var php_js;
    var formatSeed = function (seed, reqWidth) {
        seed = parseInt(seed, 10)
            .toString(16); // to hex str
        if (reqWidth < seed.length) { // so long we split
            return seed.slice(seed.length - reqWidth);
        }
        if (reqWidth > seed.length) { // so short we pad
            return new Array(1 + (reqWidth - seed.length))
                    .join('0') + seed;
        }
        return seed;
    };

    // BEGIN REDUNDANT
    if (!php_js) {
        php_js = {};
    }
    // END REDUNDANT
    if (!php_js.uniqidSeed) { // init seed with big random int
        php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
    }
    php_js.uniqidSeed++;

    retId = prefix; // start with prefix, add current milliseconds hex string
    retId += formatSeed(parseInt(new Date()
            .getTime() / 1000, 10), 8);
    retId += formatSeed(php_js.uniqidSeed, 5); // add seed hex string
    if (more_entropy) {
        // for more entropy we add a float lower to 10
        retId += (Math.random() * 10)
            .toFixed(8)
            .toString();
    }

    return retId;
}


/********************************************
 *** General Repeater ***
 *********************************************/
function customizer_repeater_refresh_social_icons(th) {
    'use strict';
    var icons_repeater_values = [];
    th.find('.customizer-repeater-social-repeater-container').each(function () {
        var icon = jQuery(this).find('.icp').val();
        var link = jQuery(this).find('.customizer-repeater-social-repeater-link').val();
        var id = jQuery(this).find('.customizer-repeater-social-repeater-id').val();

        if (!id) {
            id = 'customizer-repeater-social-repeater-' + customizer_repeater_uniqid();
            jQuery(this).find('.customizer-repeater-social-repeater-id').val(id);
        }

        if (icon !== '' && link !== '') {
            icons_repeater_values.push({
                'icon': icon,
                'link': link,
                'id': id
            });
        }
    });

    th.find('.social-repeater-socials-repeater-colector').val(JSON.stringify(icons_repeater_values));
    customizer_repeater_refresh_general_control_values();
}

function setRepeaterValues( repeaters, hiddenEl ) {
  var values = [].map.call( repeaters, function( repeater ) {
    var hiddenEl = repeater.nextElementSibling;
    var inputs = repeater.querySelectorAll('.repeater-value');

    if ( 0 === inputs.length ) {
      return false;
    }

    return [].reduce.call( inputs, function(acc, el) {
      if ( 'radio' === el.getAttribute( 'type' ) ) {
        if ( el.checked ) {
          acc[el.dataset.id] = el.value;
        }
      } else {
        acc[el.dataset.id] = el.value;
      }

      return acc;
    }, {} );
  });

  hiddenEl.value = JSON.stringify( values );
  jQuery( hiddenEl ).trigger( 'change' );
}


function customizer_repeater_refresh_general_control_values() {
    var repeaters = document.querySelectorAll( '.customizer-repeater-general-control-repeater' );

    if ( ! repeaters ) {
      return false;
    }

    [].forEach.call( repeaters, function( repeater ) {
      var repeaterItems = repeater.querySelectorAll( '.customizer-repeater-general-control-repeater-container' );
      var hiddenEl = repeater.querySelector( '.customizer-repeater-colector' );

      if ( 0 === repeaterItems.length ) {
        return false;
      }

      setRepeaterValues(repeaterItems, hiddenEl);
    });
}


jQuery(document).ready(function () {
    'use strict';
    var theme_conrols = jQuery('#customize-theme-controls');
    theme_conrols.on('click', '.customizer-repeater-customize-control-title', function () {
        // jQuery(this).next().slideToggle('medium', function () {
        //     if (jQuery(this).is(':visible')){
        //         jQuery(this).css('display', 'block');
        //     }
        // });
        //
        var repeaterEl = jQuery(this);
        var parent = repeaterEl[0].parentElement;
        var sibling = parent.querySelector( '.customizer-repeater-box' );
        sibling.classList.toggle( 'customizer-repeater-box-content-hidden' );
        parent.classList.toggle( 'customizer-section-open' );
    });

    theme_conrols.on('change', '.icp',function(){
        customizer_repeater_refresh_general_control_values();
        return false;
    });

    theme_conrols.on('change', '.customizer-repeater-image-choice', function () {
        if (jQuery(this).val() === 'customizer_repeater_image') {
            jQuery(this).parent().parent().find('.social-repeater-general-control-icon').hide();
            jQuery(this).parent().parent().find('.customizer-repeater-image-control').show();
        }
        if (jQuery(this).val() === 'customizer_repeater_icon') {
            jQuery(this).parent().parent().find('.social-repeater-general-control-icon').show();
            jQuery(this).parent().parent().find('.customizer-repeater-image-control').hide();
        }
        if (jQuery(this).val() === 'customizer_repeater_none') {
            jQuery(this).parent().parent().find('.social-repeater-general-control-icon').hide();
            jQuery(this).parent().parent().find('.customizer-repeater-image-control').hide();
        }

        customizer_repeater_refresh_general_control_values();
        return false;
    });
    media_upload('.customizer-repeater-custom-media-button');
    jQuery('.custom-media-url').live('change', function () {
        customizer_repeater_refresh_general_control_values();
        return false;
    });

    theme_conrols.on('click', '.customizer-repeater-custom-media-remove', function () {
        var parent = jQuery(this).parent();
        var image = parent.find( 'img' );
        var hidden = parent.find( '.custom-media-url' );
        var uploadBtn = parent.find('.customizer-repeater-custom-media-button');
        var removeBtn = uploadBtn.next();

        image[0].setAttribute( 'src' , '');
        image[0].style.display = 'none';

        removeBtn[0].style.display = 'none';

        uploadBtn[0].value = 'Upload Image';

        hidden[0].value = '';
        hidden.trigger('change');
    });



    /**
     * This adds a new box to repeater
     *
     */
    var addNewButton = document.querySelectorAll( '.customizer-repeater-new-field' );
    [].forEach.call( addNewButton, function( element ) {
      element.addEventListener( 'click', function(e) {
        var parent = e.target.previousElementSibling;

        var id = 'customizer-repeater-' + customizer_repeater_uniqid();

        if ( ! parent ) {
          return false;
        }

        var firstRepeater = parent.querySelector('.customizer-repeater-general-control-repeater-container');

        if ( ! firstRepeater ) {
          return false;
        }

        var newRepeater = firstRepeater.cloneNode(true);

        var newRepeaterDelete = newRepeater.querySelector('.social-repeater-general-control-remove-field');
        newRepeaterDelete.style.display = 'line-block';

        var inputFields = newRepeater.querySelectorAll('.repeater-value');

        [].forEach.call( inputFields, function( field ) {
          field.value = '';

          if ( field.classList.contains( 'custom-media-url' ) ) {
            var imageParent = field.parentNode;
            var image = imageParent.querySelector('img');
            var uploadBtn = imageParent.querySelector('.customizer-repeater-custom-media-button');
            var cancelBtn = imageParent.querySelector('.customizer-repeater-custom-media-remove');

            image.setAttribute('src', '');
            image.style.display = 'none';
            uploadBtn.value = 'Upload Image';
            cancelBtn.style.display = 'none';
          }
        } );

        /*Append new box*/
        parent.appendChild(newRepeater);

        /*Refresh values*/
        customizer_repeater_refresh_general_control_values();
      } );
    } );

    theme_conrols.on('click', '.social-repeater-general-control-remove-field', function () {
        if (typeof    jQuery(this).parent() !== 'undefined') {
            jQuery(this).parent().parent().remove();
            customizer_repeater_refresh_general_control_values();
        }
        return false;
    });


    theme_conrols.on('keyup', '.customizer-repeater-title-control', function () {
        customizer_repeater_refresh_general_control_values();
    });

    theme_conrols.on('keyup', '.customizer-repeater-subtitle-control', function () {
        customizer_repeater_refresh_general_control_values();
    });

    theme_conrols.on('keyup', '.customizer-repeater-shortcode-control', function () {
        customizer_repeater_refresh_general_control_values();
    });

    theme_conrols.on('keyup', '.customizer-repeater-text-control', function () {
        customizer_repeater_refresh_general_control_values();
    });

    theme_conrols.on('click', '.customizer-repeater-radio-control', function () {
        customizer_repeater_refresh_general_control_values();
    });

    theme_conrols.on('change', '.customizer-repeater-select-control', function (e) {
        customizer_repeater_refresh_general_control_values();
    });

    theme_conrols.on('keyup', '.customizer-repeater-link-control', function () {
        customizer_repeater_refresh_general_control_values();
    });

    /*Drag and drop to change icons order*/

    jQuery('.customizer-repeater-general-control-droppable').sortable({
        update: function () {
            customizer_repeater_refresh_general_control_values();
        }
    });


    /*----------------- Socials Repeater ---------------------*/
    theme_conrols.on('click', '.social-repeater-add-social-item', function (event) {
        event.preventDefault();
        var th = jQuery(this).parent();
        var id = 'customizer-repeater-social-repeater-' + customizer_repeater_uniqid();
        if (typeof th !== 'undefined') {
            var field = th.find('.customizer-repeater-social-repeater-container:first').clone();
            if (typeof field !== 'undefined') {
                field.find( '.icp' ).iconpicker();
                field.find( '.icp' ).val('');
                field.find( '.input-group-addon' ).find('.fa').attr('class','fa');
                field.find('.social-repeater-remove-social-item').show();
                field.find('.customizer-repeater-social-repeater-link').val('');
                field.find('.customizer-repeater-social-repeater-id').val(id);
                th.find('.customizer-repeater-social-repeater-container:first').parent().append(field);
            }
        }
        return false;
    });

    theme_conrols.on('click', '.social-repeater-remove-social-item', function (event) {
        event.preventDefault();
        var th = jQuery(this).parent();
        var repeater = jQuery(this).parent().parent();
        th.remove();
        customizer_repeater_refresh_social_icons(repeater);
        return false;
    });

    theme_conrols.on('keyup', '.customizer-repeater-social-repeater-link', function (event) {
        event.preventDefault();
        var repeater = jQuery(this).parent().parent();
        customizer_repeater_refresh_social_icons(repeater);
        return false;
    });

    theme_conrols.on( 'iconpickerUpdated','.icp', function(event) {
        event.preventDefault();
        var th = jQuery(this).parent().parent().parent();
        customizer_repeater_refresh_social_icons(th);
        return false;
    } );

});

var entityMap = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    '\'': '&#39;',
    '/': '&#x2F;'
};

function escapeHtml(string) {
    'use strict';
    //noinspection JSUnresolvedFunction
    string = String(string).replace(new RegExp('\r?\n', 'g'), '<br />');
    string = String(string).replace(/\\/g, '&#92;');
    return String(string).replace(/[&<>"'\/]/g, function (s) {
        return entityMap[s];
    });

}
