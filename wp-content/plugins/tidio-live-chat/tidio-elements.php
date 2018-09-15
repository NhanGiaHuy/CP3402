<?php

/**
 * Plugin Name: Tidio Chat
 * Plugin URI: http://www.tidiochat.com
 * Description: Tidio Live Chat - Live chat for your website. No logging in, no signing up - integrates with your website in less than 20 seconds.
 * Version: 3.3.3
 * Author: Tidio Ltd.
 * Author URI: http://www.tidiochat.com
 * License: GPL2
 */
define('TIDIOCHAT_VERSION', '3.3.3');

class TidioLiveChat {

    private $scriptUrl = '//code.tidio.co/';
    private static $apiUrl = 'https://api-v2.tidio.co';
    private static $chatUrl = 'https://www.tidiochat.com';

    public function __construct() {

        if (!empty($_GET['tidio_chat_version'])) {
            echo TIDIOCHAT_VERSION;
            exit;
        }

        /* Before add link to menu - check is user trying to unninstal */
        if (is_admin() && !empty($_GET['tidio_one_clear_cache'])) {
            delete_option('tidio-one-public-key');
            delete_option('tidio-one-private-key');
        }

        add_action('admin_menu', array($this, 'addAdminMenuLink'));

        if(get_option('tidio-one-public-key')){
            add_action('admin_footer', array($this, 'adminJS'));
        }

        if (!is_admin()) {
            add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'), 1000);
        }else{
            add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
        }

        add_action('wp_ajax_tidio_chat_save_keys', array($this, 'ajaxTidioChatSaveKeys'));

        /* Ajax functions to set up existing tidio account  */
        add_action('wp_ajax_get_project_keys', array($this, 'ajaxGetProjectKeys'));
        add_action('wp_ajax_get_private_key', array($this, 'ajaxGetPrivateKey'));

        add_filter('plugin_action_links', array($this, 'pluginActionLinks'), 10, 2);
        add_action('admin_post_tidio-chat-reset', array($this, 'uninstall'));
    }

    public function pluginActionLinks($links, $file) {
        if (strpos($file, 'tidio-elements.php') !== false && get_option('tidio-one-private-key')) {
            $links[] = '<a href="'.admin_url('admin-post.php').'?action=tidio-chat-reset">'.esc_html__( 'Clear Account Data' , 'tidio-live-chat').'</a>';
        }

        return $links;
    }

    public function ajaxGetProjectKeys(){
        update_option('tidio-one-public-key', $_POST['public_key']);
        update_option('tidio-one-private-key', $_POST['private_key']);
        echo self::getRedirectUrl($_POST['private_key']);
        exit();
    }

    // Ajax - Create an new project

    public function ajaxTidioChatSaveKeys() {

        if (!is_admin()) {
            exit;
        }

        if (empty($_POST['private_key']) || empty($_POST['public_key'])) {
            exit;
        }

        update_option('tidio-one-public-key', $_POST['public_key']);
        update_option('tidio-one-private-key', $_POST['private_key']);

        echo '1';
        exit;
    }

    // Front End Scripts
    public function enqueueScripts() {
        wp_enqueue_script('tidio-chat', $this->scriptUrl . self::getPublicKey() . '.js', array(), TIDIOCHAT_VERSION, true);
    }

    // Admin scripts and style enquee
    public function enqueueAdminScripts(){
        wp_enqueue_script('tidio-chat-admin', plugins_url('media/js/options.js', __FILE__), array(), TIDIOCHAT_VERSION, true);
        wp_enqueue_style('tidio-chat-admin-style', plugins_url('media/css/options.css', __FILE__), array(), TIDIOCHAT_VERSION);
    }

    // Admin JavaScript
    public function adminJS() {
        $privateKey = self::getPrivateKey();
        $redirectUrl = '';

        if ($privateKey && $privateKey != 'false') {
            $redirectUrl = self::getRedirectUrl($privateKey);
        } else {
            $redirectUrl = admin_url('admin-ajax.php?action=tidio_chat_redirect');
        }

        echo "<script>jQuery('a[href=\"admin.php?page=tidio-chat\"]').attr('href', '" . $redirectUrl . "').attr('target', '_blank') </script>";
    }

    // Menu Pages

    public function addAdminMenuLink() {
        $optionPage = add_menu_page(
            'Tidio Chat', 'Tidio Chat', 'manage_options', 'tidio-chat', array($this, 'addAdminPage'), content_url() . '/plugins/tidio-live-chat/media/img/icon.png'
        );
    }

    public function addAdminPage() {
        // Set class property
        $dir = plugin_dir_path(__FILE__);
        include $dir . 'options.php';
    }

    // Uninstall

    public function uninstall() {
        delete_option('tidio-one-public-key');
        delete_option('tidio-one-private-key');
        wp_redirect( admin_url('plugins.php') );
        die();
    }

    // Get Private Key

    public static function getPrivateKey() {
        self::syncPrivateKey();

        $privateKey = get_option('tidio-one-private-key');

        if ($privateKey) {
            return $privateKey;
        }

        try {
            $data = self::getContent(self::getAccessUrl());
        } catch(Exception $e){
            $data = null;
        }
        //
        if (!$data) {
            update_option('tidio-one-private-key', 'false');
            return false;
        }

        @$data = json_decode($data, true);
        if (!$data || !$data['status']) {
            update_option('tidio-one-private-key', 'false');
            return false;
        }

        update_option('tidio-one-private-key', $data['value']['private_key']);
        update_option('tidio-one-public-key', $data['value']['public_key']);

        return $data['value']['private_key'];
    }

    public static function getContent($url){

        if(function_exists('curl_version')){ // load trought curl
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);

            $data = curl_exec($ch);
            curl_close($ch);

            return $data;
        } else { // load trought file get contents
            return file_get_contents($url);
        }

    }

    // Sync private key with old version

    public static function syncPrivateKey() {
        if (get_option('tidio-one-public-key')) {
            return false;
        }

        $publicKey = get_option('tidio-chat-external-public-key');
        $privateKey = get_option('tidio-chat-external-private-key');

        if (!$publicKey || !$privateKey) {
            return false;
        }

        // sync old variables with new one

        update_option('tidio-one-public-key', $publicKey);
        update_option('tidio-one-private-key', $privateKey);

        return true;
    }

    // Get Access Url

    public static function getAccessUrl() {
        return self::$apiUrl . '/access/external/create?url=' . urlencode(site_url()) . '&platform=wordpress&email=' . urlencode(get_option('admin_email')) . '&_ip=' . $_SERVER['REMOTE_ADDR'];
    }

    public static function getRedirectUrl($privateKey) {
        return self::$chatUrl . '/access?' . http_build_query(
                array(
                    'privateKey' => $privateKey,
                    'utm_source' => 'platform',
                    'utm_medium' => 'wordpress',
                    'tour_default_email' => get_option('admin_email'),
                )
            );
    }

    public static function ajaxGetPrivateKey(){
        $privateKey = self::getPrivateKey();
        if(!$privateKey || $privateKey=='false'){
            echo 'error';
            exit();
        }
        echo self::getRedirectUrl($privateKey);
        exit();
    }

    // Get Public Key

    public static function getPublicKey() {
        $publicKey = get_option('tidio-one-public-key');

        if ($publicKey) {
            return $publicKey;
        }

        self::getPrivateKey();

        return get_option('tidio-one-public-key');
    }

}

$tidioLiveChat = new TidioLiveChat();

