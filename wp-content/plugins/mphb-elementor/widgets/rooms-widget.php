<?php

namespace mphbe\widgets;

use \Elementor\Controls_Manager;

class RoomsWidget extends AbstractGalleryWidget
{
    public function get_name()
    {
        return 'mphbe-rooms';
    }

    public function get_title()
    {
        return __('Accommodation Types', 'mphb-elementor');
    }

    public function get_icon()
    {
        // Elementor icon class ( https://pojome.github.io/elementor-icons/ ) or
        // Font Awesome icon class ( https://fontawesome.com/ ), like:
        return 'eicon-photo-library';
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
            'label'       => __('View Button', 'mphb-elementor'),
            'description' => __('Whether to display "View Details" button with the link to accommodation type.', 'mphb-elementor'),
            'default'     => 'true',
            'options'     => array(
                'true'          => __('Yes', 'mphb-elementor'),
                'false'         => __('No', 'mphb-elementor')
            )
        ));

        $this->add_control('book_button', array(
            'type'        => Controls_Manager::SELECT,
            'label'       => __('Book Button', 'mphb-elementor'),
            'description' => __('Whether to display "Book" button.', 'mphb-elementor'),
            'default'     => 'true',
            'options'     => array(
                'true'          => __('Yes', 'mphb-elementor'),
                'false'         => __('No', 'mphb-elementor')
            )
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

        $this->add_control('category', array(
            'type'        => Controls_Manager::TEXT,
            'label'       => __('Categories', 'mphb-elementor'),
            'description' => __('Comma-separated IDs of categories that will be shown.', 'mphb-elementor'),
            'default'     => ''
        ));

        $this->add_control('tags', array(
            'type'        => Controls_Manager::TEXT,
            'label'       => __('Tags', 'mphb-elementor'),
            'description' => __('Comma-separated IDs of tags that will be shown.', 'mphb-elementor'),
            'default'     => ''
        ));

        $this->add_control('ids', array(
            'type'        => Controls_Manager::TEXT,
            'label'       => __('IDs', 'mphb-elementor'),
            'description' => __('Comma-separated IDs of accommodations that will be shown.', 'mphb-elementor'),
            'default'     => ''
        ));

        $this->add_control('relation', array(
            'type'        => Controls_Manager::SELECT,
            'label'       => __('Relation', 'mphb-elementor'),
            'description' => __('Logical relationship between each taxonomy when there is more than one.', 'mphb-elementor'),
            'default'     => 'OR',
            'options'     => array(
                'AND'           => __('AND', 'mphb-elementor'),
                'OR'            => __('OR', 'mphb-elementor')
            )
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
        $shortcode = new \MPHB\Shortcodes\RoomsShortcode();
        echo $shortcode->render($atts, null, MPHB()->getShortcodes()->getRooms()->getName());
        parent::render();
    }
}
