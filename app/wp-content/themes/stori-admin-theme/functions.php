<?php
    defined('ABSPATH') or exit;

    /**
     * Change theme options to add new features
     *
     * @return void
     */
    function stori_theme_setup() {        
        register_nav_menus(array(
            'top'    => __('Top Menu', 'stori-admin-theme'),
            'footer' => __('Footer Menu', 'stori-admin-theme'),
        ));

        add_theme_support('post-thumbnails');
    }

    add_action('after_setup_theme', 'stori_theme_setup');


    /**
     * Redirect to Login if not logged to create headless effect
     *
     * @return void
     */
    function stori_redirect_to_admin() {        
        if (!is_admin()) {  
            if (is_user_logged_in()) {
                wp_safe_redirect(admin_url('post.php?post=' . get_the_id() . '&action=edit'));
            } else {
                header('HTTP/1.1 403 Forbidden');

                if (defined('HEADLESS_MODE_CLIENT_URL')) {
                    header('Location: ' . HEADLESS_MODE_CLIENT_URL, true, 302);
                } else {
                    header('Location: ' . wp_login_url());
                }
    
                die();
            }
        }        
    }

    add_action('wp', 'stori_redirect_to_admin', 0);

    /**
     * Remove unecessary widgets from dashboard
     *
     * @return void
     */
    function stori_remove_dashboard_widgets() {
        global $wp_meta_boxes;
     
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
     
    }
     
    add_action('wp_dashboard_setup', 'stori_remove_dashboard_widgets');

    /**
     * Remove all unecessary menus from admin
     *
     * @return void
     */
    function stori_admin_menu_remove() {
        define('DISALLOW_FILE_EDIT', true);

        remove_menu_page('index.php');                  // Dashboard
        remove_menu_page('jetpack');                    // Jetpack*
        remove_menu_page('edit.php');                   // Posts
        remove_menu_page('edit.php?post_type=page');    // Pages
        remove_menu_page('edit-comments.php');          // Comments
        remove_menu_page('tools.php');                  // Tools

        remove_submenu_page('options-general.php', 'options-discussion.php');
        remove_submenu_page('options-general.php', 'options-writing.php');
        remove_submenu_page('options-general.php', 'options-reading.php');
        remove_submenu_page('options-general.php', 'privacy.php');
        remove_submenu_page('edit.php?post_type=acf-field-group', 'acf-tools');
        remove_submenu_page('themes.php', 'customize.php?return=' . urlencode($_SERVER['SCRIPT_NAME']));
    }

    add_action('admin_menu', 'stori_admin_menu_remove', 999);
    
    /**
     * Remove admin bar menu items
     *
     * @return void
     */
    function stori_admin_bar_menu_remove()  {
        global $wp_admin_bar;   
        $wp_admin_bar->remove_node('new-post');
        $wp_admin_bar->remove_node('new-link');
        $wp_admin_bar->remove_node('new-page');
        $wp_admin_bar->remove_menu('comments');
    }

    add_action('admin_bar_menu', 'stori_admin_bar_menu_remove', 999);

    /**
     * Change default Login page to new branding
     *
     * @return void
     */
    function stori_login_enqueue_scripts() {
        wp_enqueue_style('stori-login', get_stylesheet_directory_uri() . '/css/login.css' );
    }

    add_action('login_enqueue_scripts', 'stori_login_enqueue_scripts');
