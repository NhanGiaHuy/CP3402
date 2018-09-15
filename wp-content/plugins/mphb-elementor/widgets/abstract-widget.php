<?php

namespace mphbe\widgets;

use \Elementor\Widget_Base;

abstract class AbstractWidget extends Widget_Base
{
    public function get_icon()
    {
        // Elementor icon class ( https://pojome.github.io/elementor-icons/ ) or
        // Font Awesome icon class ( https://fontawesome.com/ ), like:
        return 'eicon-wordpress';
    }

    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * Note that currently Elementor supports only one category.
     * When multiple categories passed, Elementor uses the first one.
     *
     * @return string[] Widget categories.
     */
    public function get_categories() {
        return array('motopress-hotel-booking');
    }

    /**
     * Render the widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live
     * preview.
     */
    protected function _content_template() {}
}
