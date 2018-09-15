// executes this when the DOM is ready
jQuery(document).ready(function(){

    var data = {
        action: 'yasr_create_shortcode'
    }

    jQuery.post(ajaxurl, data, function(button_content) {

        var response=button_content;

        jQuery(response).appendTo('body').hide();

    });
});

//});
