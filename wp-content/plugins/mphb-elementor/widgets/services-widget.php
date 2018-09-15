<?php

namespace mphbe\widgets;

use \Elementor\Controls_Manager;

class ServicesWidget extends AbstractWidget
{
    public function get_name()
    {
        return 'mphbe-services';
    }

    public function get_title()
    {
        return __('Services', 'mphb-elementor');
    }

    public function get_icon()
    {
        // Elementor icon class ( https://pojome.github.io/elementor-icons/ ) or
        // Font Awesome icon class ( https://fontawesome.com/ ), like:
        return 'eicon-menu-card';
    }

    /**
     * Adds different input fields to allow the user to change and customize the
     * widget settings.
     */
    protected function _register_controls()
    {
        $this->start_controls_section('section_parameters', array(
            'label'       => __('Parameters', 'mphb-elementor')
        ));

        $this->add_control('ids', array(
            'type'        => Controls_Manager::TEXT,
            'label'       => __('IDs', 'mphb-elementor'),
            'description' => __('Comma-separated IDs of services that will be shown. All services by default.', 'mphb-elementor'),
            'default'     => ''
        ));

        $this->add_control('posts_per_page', array(
            'type'        => Controls_Manager::NUMBER,
            'label'       => __('Count per page', 'mphb-elementor'),
            'description' => __('-1 to display all.', 'mphb-elementor'),
            'default'     => '',
            'min'         => -1
        ));

        $this->add_control('class', array(
            'type'        => Controls_Manager::TEXT,
            'label'       => __('Class', 'mphb-elementor'),
            'description' => __('Custom CSS class for shortcode wrapper.', 'mphb-elementor'),
            'default'     => ''
        ));

        $this->end_controls_section();
    }

    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     */
    protected function render()
    {
        $atts = $this->get_settings();
        $shortcode = new \MPHB\Shortcodes\ServicesShortcode();
        echo $shortcode->render($atts, null, MPHB()->getShortcodes()->getServices()->getName());
    }
}
