<?php

namespace mphbe\widgets;

abstract class AbstractGalleryWidget extends AbstractWidget
{
    /**
     * Retrieve the list of scripts the counter widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     */
    public function get_script_depends()
    {
        return array('mphb-flexslider');
    }

    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     */
    protected function render()
    {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'elementor_render_widget') {
            // Add initialization script
            echo '<script>'
                    // Search only not inited sliders
                    . 'jQuery(".mphb-flexslider-gallery-wrapper:not(.mphb-flexslider)").each(function (index, wrapper) {'
                        . 'var gallery = new MPHB.FlexsliderGallery(wrapper);'
                        . 'gallery.initSliders();'
                    . '});'
                . '</script>';
        }
    }
}
