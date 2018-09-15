<?php

namespace mphbe\widgets;

use \Elementor\Controls_Manager;

class SearchResultsWidget extends AbstractWidget
{
    public function get_name()
    {
        return 'mphbe-search-results';
    }

    public function get_title()
    {
        return __('Search Results', 'mphb-elementor');
    }

    public function get_icon()
    {
        // Elementor icon class ( https://pojome.github.io/elementor-icons/ ) or
        // Font Awesome icon class ( https://fontawesome.com/ ), like:
        return 'eicon-search';
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

        $this->add_control('title', array(
            'type'        => Controls_Manager::SELECT,
            'label'       => __('Title', 'mphb-elementor'),
            'description' => __('Whether to display title of the accommodation type.', 'mphb-elementor'),
            'default'     => 'true',
            'options'     => array(
                'true'          => __('Yes', 'mphb-elementor'),
                'false'         => __('No', 'mphb-elementor')
            )
        ));

        $this->add_control('featured_image', array(
            'type'        => Controls_Manager::SELECT,
            'label'       => __('Featured image', 'mphb-elementor'),
            'description' => __('Whether to display featured image of the accommodation type.', 'mphb-elementor'),
            'default'     => 'true',
            'options'     => array(
                'true'          => __('Yes', 'mphb-elementor'),
                'false'         => __('No', 'mphb-elementor')
            )
        ));

        $this->add_control('gallery', array(
            'type'        => Controls_Manager::SELECT,
            'label'       => __('Gallery', 'mphb-elementor'),
            'description' => __('Whether to display gallery of the accommodation type.', 'mphb-elementor'),
            'default'     => 'true',
            'options'     => array(
                'true'          => __('Yes', 'mphb-elementor'),
                'false'         => __('No', 'mphb-elementor')
            )
        ));

        $this->add_control('excerpt', array(
            'type'        => Controls_Manager::SELECT,
            'label'       => __('Excerpt', 'mphb-elementor'),
            'description' => __('Whether to display excerpt (short description) of the accommodation type.', 'mphb-elementor'),
            'default'     => 'true',
            'options'     => array(
                'true'          => __('Yes', 'mphb-elementor'),
                'false'         => __('No', 'mphb-elementor')
            )
        ));

        $this->add_control('details', array(
            'type'        => Controls_Manager::SELECT,
            'label'       => __('Details', 'mphb-elementor'),
            'description' => __('Whether to display details of the accommodation type.', 'mphb-elementor'),
            'default'     => 'true',
            'options'     => array(
                'true'          => __('Yes', 'mphb-elementor'),
                'false'         => __('No', 'mphb-elementor')
            )
        ));

        $this->add_control('price', array(
            'type'        => Controls_Manager::SELECT,
            'label'       => __('Price', 'mphb-elementor'),
            'description' => __('Whether to display price of the accommodation type.', 'mphb-elementor'),
            'default'     => 'true',
            'options'     => array(
                'true'          => __('Yes', 'mphb-elementor'),
                'false'         => __('No', 'mphb-elementor')
            )
        ));

        $this->add_control('view_button', array(
            'type'        => Controls_Manager::SELECT,
            'label'       => __('View button', 'mphb-elementor'),
            'description' => __('Whether to display "View Details" button with the link to accommodation type.', 'mphb-elementor'),
            'default'     => 'true',
            'options'     => array(
                'true'          => __('Yes', 'mphb-elementor'),
                'false'         => __('No', 'mphb-elementor')
            )
        ));

        $this->add_control('default_sorting', array(
            'type'        => Controls_Manager::SELECT,
            'label'       => __('Sort by', 'mphb-elementor'),
            'default'     => 'order',
            'options'     => array(
                'order'         => __('Order', 'mphb-elementor'),
                'price'         => __('Price', 'mphb-elementor')
            )
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
        $shortcode = new \MPHB\Shortcodes\SearchResultsShortcode();
        echo $shortcode->render($atts, null, MPHB()->getShortcodes()->getSearchResults()->getName());
    }
}
