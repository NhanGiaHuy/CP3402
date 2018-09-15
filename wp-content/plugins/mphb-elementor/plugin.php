<?php

if (!defined('ABSPATH')) {
    exit('Press Enter to proceed...');
}

class MPHBElementor
{
    const SLUG = 'mphb-elementor';

    private static $instance = null;

    private function __construct() {
        $this->addActions();
    }

    private function addActions()
    {
        add_action('plugins_loaded', array($this, 'loadTextdomain'));

        // Check if the MotoPress Hotel Booking is active
        if (!class_exists('HotelBookingPlugin')) {
            return;
        }

        // Check if the Elementor is active
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Check required version
        if (!version_compare(ELEMENTOR_VERSION, '1.8.0', '>=')) {
            return;
        }

        add_action('elementor/init', array($this, 'registerCategories'));
        add_action('elementor/widgets/widgets_registered', array($this, 'registerWidgets'));
        add_action('elementor/preview/enqueue_styles', array($this, 'enqueuePreviewStyles'));
    }

    public function loadTextdomain()
    {
        global $wp_version;

        $isWp47 = version_compare($wp_version, '4.7', '>=');

        $locale = $isWp47 ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, self::SLUG);

        // wp-content/languages/mphb-elementor/mphb-elementor-{lang}_{locale}.mo
        $moFile = sprintf('%1$s/%2$s/%2$s-%3$s.mo', WP_LANG_DIR, self::SLUG, $locale);

        load_textdomain(self::SLUG, $moFile);
        load_plugin_textdomain(self::SLUG, false, self::SLUG . '/languages');
    }

    /**
     * Note that the categories are displayed in the widgets panel, only if they
     * have widgets assigned to them.
     */
    public function registerCategories()
    {
        \Elementor\Plugin::instance()->elements_manager->add_category(
            'motopress-hotel-booking',
            array(
                'title' => __('MotoPress Hotel Booking', 'mphb-elementor'),
                'icon'  => 'fa fa-plug'
            )
        );
    }

    public function registerWidgets($widgetsManager)
    {
        require __DIR__ . '/widgets/abstract-widget.php';
        require __DIR__ . '/widgets/abstract-gallery-widget.php';
        require __DIR__ . '/widgets/search-form-widget.php';
        require __DIR__ . '/widgets/search-results-widget.php';
        require __DIR__ . '/widgets/rooms-widget.php';
        require __DIR__ . '/widgets/room-widget.php';
        require __DIR__ . '/widgets/services-widget.php';
        require __DIR__ . '/widgets/rates-widget.php';
        require __DIR__ . '/widgets/availability-widget.php';
        require __DIR__ . '/widgets/booking-confirmation-widget.php';
        require __DIR__ . '/widgets/checkout-widget.php';

        $widgetsManager->register_widget_type(new \mphbe\widgets\SearchFormWidget());
        $widgetsManager->register_widget_type(new \mphbe\widgets\SearchResultsWidget());
        $widgetsManager->register_widget_type(new \mphbe\widgets\RoomsWidget());
        $widgetsManager->register_widget_type(new \mphbe\widgets\RoomWidget());
        $widgetsManager->register_widget_type(new \mphbe\widgets\ServicesWidget());
        $widgetsManager->register_widget_type(new \mphbe\widgets\RatesWidget());
        $widgetsManager->register_widget_type(new \mphbe\widgets\AvailabilityWidget());
        $widgetsManager->register_widget_type(new \mphbe\widgets\BookingConfirmationWidget());
        $widgetsManager->register_widget_type(new \mphbe\widgets\CheckoutWidget());
    }

    public function enqueuePreviewStyles()
    {
        wp_enqueue_style('mphb-flexslider-css');
    }

    public static function create()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
    }
}
