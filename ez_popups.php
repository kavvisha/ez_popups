<?php 
/**
 * Plugin Name: EZ Popups
 * Plugin URI: https://websitepunks.com/
 * Description: Simplified Popups for Wordpress
 * Version: 0.0.1
 * Author: WebsitePunks
 * Author URI: https://websitepunks.com/
 * Text Domain: ez_pf
 * Domain Path: /languages/
 * Requires at least: 6.3
 * Requires PHP: 7.4
 *
 * @package WooCommerce
 */


// exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// #Add delay for popup
// #Add start and end time to show the popup BAED ON SERVER TIME
// #Button always on the bottom centered
// #When All pages are selected, hide "Add pages" pages selector
// add language translate support . like in swisspost 
// #dropdown to select if target _blank or not 
// #close popup when black bg clicked
// #in the same page selector, add the option to select/deselect all blog posts at once or select specific blog post(s)
// #startdate  -  today  -  enddate (popup shows)
// #startdate  - enddate  -  today (popup will not show)
// #if today is between start and enddate
// #What i also just saw, if i center the content, it's not centering in the popup
// #for daily weekly monthly . remove checking the time.
// #consider the time only for popup campaign start and ending time.
// #make the metabox not collapsible

if( ! class_exists('WebsitePunks_EZ_Popups')){
    class WebsitePunks_EZ_Popups{
        public $version = "";

        public function __construct(){
            define('EZ_Popups_Dir', $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/ez_popups/');
            define('EZ_Popups_Plugin_Dir', plugin_dir_url(__FILE__));
            define('EZ_Popups_Ver', idate("U"));

            add_action('init', array($this,'create_ez_popups_post_type')); 
            add_action('add_meta_boxes', array($this,'ez_popups_add_custom_box')); 
            add_action('save_post', array($this,'ez_popups_save_postdata'));
            add_action('admin_enqueue_scripts', array($this,'load_custom_admin_script'));

            add_action('wp_enqueue_scripts', array($this,'load_public_scripts_and_styles'));
            add_action('wp_footer',array($this,'load_popup_in_footer'));
        }

        function load_public_scripts_and_styles() {
            // Enqueue JavaScript
            wp_enqueue_script('custom_public_script', plugin_dir_url(__FILE__) . 'public/src/js/ez_popups_public.js', array('jquery'), time(), true);
        
            // Enqueue CSS
            wp_enqueue_style('custom_public_style', plugin_dir_url(__FILE__) . 'public/src/css/ez_popups_public.css', array(),time(), 'all');
        }
        
        
        function load_custom_admin_script() {
            // Use plugin_dir_url(__FILE__) to get the URL of your plugin directory, then append the path to your JS file.
            wp_enqueue_script('my_custom_admin_script', plugin_dir_url(__FILE__) . 'admin/src/js/ez_popups_admin.js', array('jquery'), time(), true);
        }
        
        function create_ez_popups_post_type() {
            $labels = array(
                'name'               => 'EZ Popups', // name for the post type.
                'singular_name'      => 'EZ Popup', // name for single post of that type.
                'add_new'            => 'Add EZ Popup', // to add a new post.
                'add_new_item'       => 'Adding EZ Popup', // title for a newly created post in the admin panel.
                'edit_item'          => 'Edit EZ Popup', // for editing post type.
                'new_item'           => 'New EZ Popup', // new post's text.
                'view_item'          => 'See EZ Popup', // for viewing this post type.
                'search_items'       => 'Search EZ Popup', // search for these post types.
                'not_found'          => 'Not Found', // if search has not found anything.
                'parent_item_colon'  => '', // for parents (for hierarchical post types).
                'menu_name'          => 'EZ Popups', // menu name.
            );
        
            $args = array(
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array('slug' => 'ez_popups'),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array('title'),
            );
        
            register_post_type('ez_popups', $args);
        }
               
        function ez_popups_add_custom_box() {
            add_meta_box(
                'ez_popups_options',       // Unique ID
                'EZ Popups Options',       // Box title
                array($this,'ez_popups_custom_box_html'),  // Content callback, must be of type callable
                'ez_popups'                // Post type
            );
        } 

        function ez_popups_custom_box_html($post) {
            $button_text = get_post_meta($post->ID, 'ez_popup_button_text', true);
            $button_url = get_post_meta($post->ID, 'ez_popup_button_url', true);
            $popup_delay = get_post_meta($post->ID, 'ez_popup_delay', true);
            $popup_button_url_target = get_post_meta($post->ID, 'ez_popup_button_url_target', true);
            $popup_frequency = get_post_meta($post->ID, 'ez_popup_frequency', true);
            $popup_start_date = get_post_meta($post->ID, 'ez_popup_start_date', true);
            $popup_end_date = get_post_meta($post->ID, 'ez_popup_end_date', true);
            $popup_content = get_post_meta($post->ID, 'ez_popup_content', true); // For WYSIWYG editor
            $pages_selector = get_post_meta($post->ID, 'ez_popup_pages_selector', true);
            $pages_include_exclude = get_post_meta($post->ID, 'ez_popup_pages_include_exclude', true);

            ?>

            <div class="ez_popup_settings_box">
                <p class="settings_box_title">Popup Content Settings</p>
                <p>
                    <label for="ez_popup_content">Popup Content:</label>
                    <?php 
                        wp_editor(htmlspecialchars_decode($popup_content), 'ez_popup_content', array(
                            'wpautop'       => true,
                            'media_buttons' => true,
                            'textarea_name' => 'ez_popup_content',
                            'textarea_rows' => 10,
                            'teeny'         => false
                        ));
                    ?>
                </p>
                <p>
                    <label for="ez_popup_button_text">Button Text:</label>
                    <input type="text" id="ez_popup_button_text" name="ez_popup_button_text" value="<?php echo esc_attr($button_text); ?>" class="widefat" />
                </p>
                <p>
                    <label for="ez_popup_button_url">Button URL:</label>
                    <input type="text" id="ez_popup_button_url" name="ez_popup_button_url" value="<?php echo esc_url($button_url); ?>" class="widefat" />
                </p>
                <p>
                <label for="ez_popup_button_url_target">Open Link from:</label>
                    <select id="ez_popup_button_url_target" name="ez_popup_button_url_target" class="widefat">
                        <option value="same_tab" <?php selected($popup_button_url_target, 'same_tab'); ?>>Same Tab</option>
                        <option value="new_tab" <?php selected($popup_button_url_target, 'new_tab'); ?>>New Tab</option>
                    </select>
                </p>
            </div>

            <div class="ez_popup_settings_box">     
                <p class="settings_box_title">Popup Displaying Settings</p>      
                <p>
                    <label for="ez_popup_delay">Popup Delay ( miliseconds | 1 sec = 1000 ms):</label>
                    <input type="number" id="ez_popup_delay" name="ez_popup_delay" value="<?php echo esc_attr($popup_delay); ?>" class="widefat" />
                </p>
                <p>
                    <label for="ez_popup_frequency">Frequency:</label>
                    <select id="ez_popup_frequency" name="ez_popup_frequency" class="widefat">
                        <option value="every_page_load" <?php selected($popup_frequency, 'every_page_load'); ?>>Every Page Load</option>
                        <option value="once_a_day" <?php selected($popup_frequency, 'once_a_day'); ?>>Once a Day</option>
                        <option value="once_a_week" <?php selected($popup_frequency, 'once_a_week'); ?>>Once a Week</option>
                        <option value="once_a_month" <?php selected($popup_frequency, 'once_a_month'); ?>>Once a Month</option>
                    </select>
                </p>
                <p>
                    <div class="column_fields_wrapper">
                        <div class="column_field">
                            <label for="ez_popup_start_date">Start Date:</label>
                            <input type="datetime-local" min="<?php echo date("yyyy-mm-dd HH:MM:SS.ss"); ?>" id="ez_popup_start_date" name="ez_popup_start_date" value="<?php echo esc_attr($popup_start_date); ?>" class="widefat" />
                        </div>
                        <div class="column_field">
                            <label for="ez_popup_end_date">End Date:</label>
                            <input type="datetime-local" min="<?php echo date("yyyy-mm-dd HH:MM:SS.ss"); ?>" id="ez_popup_end_date" name="ez_popup_end_date" value="<?php echo esc_attr($popup_end_date); ?>" class="widefat" />
                        </div>
                    </div>
                </p>
                <p>
                    <label for="ez_popup_pages_include_exclude">Select all / Include / Exclude on pages :</label>
                    <select id="ez_popup_pages_include_exclude" name="ez_popup_pages_include_exclude" class="widefat">
                        <option value="all_pages" <?php selected($pages_include_exclude, 'all_pages'); ?>>All pages</option>
                        <option value="exclude" <?php selected($pages_include_exclude, 'exclude'); ?>>Exclude</option>
                        <option value="include" <?php selected($pages_include_exclude, 'include'); ?>>Include</option>
                    </select>
                </p>
                <p>
                    <?php $hide_ez_popup_select_pages = ($pages_include_exclude == 'all_pages') ? 'style="display:none;"': '';?>
                    <div class="section_ez_popup_select_pages" <?php echo $hide_ez_popup_select_pages; ?>>
                        <label for="ez_popup_pages_selector">Add Pages :</label>
                        <input type="hidden" id="ez_popup_pages_selector" name="ez_popup_pages_selector" value="<?php echo esc_attr($pages_selector); ?>" class="widefat" />
                        <div class="ez_popup_select_pages_outer">
                            <div class="ez_popup_select_pages_wrapper">
                                <div class="ez_popup_select_pages">
                                    <p>Select the Pages</p>
                                    <?php 
                                        // Fetch all pages with default arguments
                                        $pages = get_pages(); 
                                        $selected_pages_array = explode('_',esc_attr($pages_selector));
                                        // Loop through the pages and list their titles and permalinks
                                        echo '<ul class="listpages">';
                                        foreach ( $pages as $page ) {
                                            $page_id = $page->ID;
                                            $link = get_page_link(  $page_id );
                                            $title = $page->post_title;
                                            $hide =  (in_array($page_id,$selected_pages_array)) ? 'style="display:none;"' : '';
                                            echo '<li '.$hide.' data_page = "'. $page_id .'"><span >'.$title.'</span><span class="ez_add_remove_pgs"><span></span></span></li>';
                                            
                                        }
                                        echo '</ul>';
                                    ?>
                                </div>
                                <div class="ez_popup_selected_pages">
                                    <!--  -->
                                    <p>Selected Pages</p>
                                    <ul class="listpages">
                                        <?php 
                                            foreach ( $pages as $page ) {
                                                $page_id = $page->ID;
                                                $link = get_page_link(  $page_id );
                                                $title = $page->post_title;
                                                if(in_array($page_id,$selected_pages_array)){
                                                    echo '<li data_page = "'. $page_id .'"><span >'.$title.'</span><span class="ez_add_remove_pgs"><span></span></span></li>';
                                                } 
                                            }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </p>
            </div> 
            <style>
                .ez_popup_settings_box {
                    display: inline-flex;
                    width: 100%;
                    flex-direction: column;
                    margin-bottom: 10px;
                    border-bottom: 1px solid;
                    padding-bottom: 30px;
                }
                p.settings_box_title {
                    font-size: inherit;
                    font-weight: bold;
                    margin: 0;
                    margin-top: 20px;
                    background: #dedede;
                    padding: 5px 10px;
                    border-radius: 5px;
                }
                .column_fields_wrapper {
                    display: inline-flex;
                    width: 100%;
                    flex-direction: row;
                    justify-content: start;
                    gap: 20px;
                }
                .column_field {
                    display: inline-block;
                    width: 100%;
                }
                .ez_popup_select_pages_wrapper {
                    display: inline-flex;
                    flex-direction: row;
                    width: 100%;
                    box-shadow: 0 0 0 transparent;
                    border-radius: 4px;
                    border: 1px solid #8c8f94;
                    background-color: #fff;
                    color: #2c3338;
                }
                .ez_popup_select_pages, .ez_popup_selected_pages {
                    display: inline-flex;
                    flex-direction: column;
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #68686840;
                }

                .ez_popup_select_pages_wrapper ul {
                    margin: 0 !important;
                    display: inline-flex;
                    width: 100%;
                    flex-direction: column;
                    align-items: stretch;
                }

                .ez_popup_select_pages_wrapper ul li {
                    border: 1px solid #b4b4b4;
                    padding: 5px 10px;
                    border-radius: 5px;
                    display: inline-flex;
                    width: auto;
                    justify-content: space-between;
                }
                .ez_add_remove_pgs {
                    display: inline-flex;
                    line-height: 1;
                    font-size: inherit;
                    gap: 10px;
                }
                span.ez_add_remove_pgs span {
                    display: inline-block;
                    line-height: 20px;
                    font-size: unset;
                    border: 1px solid #d1d1d1;
                    width: 20px;
                    height: 20px;
                    cursor: pointer;
                    text-align: center;
                    border-radius: 100%;
                }
                .ez_add_pg{

                }

                .ez_popup_select_pages .ez_add_remove_pgs span:hover {
                    background: #d3d3ff;
                }
                .ez_popup_selected_pages .ez_add_remove_pgs span{
                    background: #ff9292;
                    border-color: #ff9292 !important;
                    color: black;
                    font-weight: bold;
                }

                .ez_popup_select_pages .ez_add_remove_pgs span::after {
                    content: '+';
                }
                .ez_popup_selected_pages .ez_add_remove_pgs span::after {
                    content: '-';
                }
            </style>
            <?php
        }

        function ez_popups_save_postdata($post_id) {
            if (array_key_exists('ez_popup_button_text', $_POST)) {
                update_post_meta($post_id, 'ez_popup_button_text', sanitize_text_field($_POST['ez_popup_button_text']));
            }
            if (array_key_exists('ez_popup_button_url', $_POST)) {
                update_post_meta($post_id, 'ez_popup_button_url', esc_url_raw($_POST['ez_popup_button_url']));
            }
            if (array_key_exists('ez_popup_button_url_target', $_POST)) {
                update_post_meta($post_id, 'ez_popup_button_url_target', sanitize_text_field(wp_kses_post($_POST['ez_popup_button_url_target'])));
            }
            if (array_key_exists('ez_popup_delay', $_POST)) {
                update_post_meta($post_id, 'ez_popup_delay', sanitize_text_field($_POST['ez_popup_delay']));
            }
            if (array_key_exists('ez_popup_frequency', $_POST)) {
                update_post_meta($post_id, 'ez_popup_frequency', sanitize_text_field($_POST['ez_popup_frequency']));
            }
            if (array_key_exists('ez_popup_start_date', $_POST)) {
                update_post_meta($post_id, 'ez_popup_start_date', sanitize_text_field($_POST['ez_popup_start_date']));
            }
            if (array_key_exists('ez_popup_end_date', $_POST)) {
                update_post_meta($post_id, 'ez_popup_end_date', sanitize_text_field($_POST['ez_popup_end_date']));
            }
            if (array_key_exists('ez_popup_content', $_POST)) {
                update_post_meta($post_id, 'ez_popup_content', htmlspecialchars(wp_kses_post($_POST['ez_popup_content'])));
            }
            if (array_key_exists('ez_popup_pages_include_exclude', $_POST)) {
                update_post_meta($post_id, 'ez_popup_pages_include_exclude', sanitize_text_field(wp_kses_post($_POST['ez_popup_pages_include_exclude'])));
            }
            if (array_key_exists('ez_popup_pages_selector', $_POST)) {
                update_post_meta($post_id, 'ez_popup_pages_selector', sanitize_text_field($_POST['ez_popup_pages_selector']));
            }
        }

        function load_popup_in_footer(){

            $current_page_id = get_queried_object_id();
            $args = array(
                'post_type'      => 'ez_popups', // Set to your custom post type
                'posts_per_page' => -1,          // How many posts to show on one page
                'post_status'    => 'publish',   // Only get posts that are published
                // Add more parameters as needed
            );
            
            $query = new WP_Query($args);
            
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $event_id = get_the_ID();
                    $get_event_meta = get_post_meta($event_id);

                    // echo '<pre style="display:none;">';
                    // print_r(get_post_meta($event_id));
                    // echo '</pre>';

                    $ez_popup_button_text = isset($get_event_meta['ez_popup_button_text'][0]) ? $get_event_meta['ez_popup_button_text'][0] : null;
                    $ez_popup_button_url = isset($get_event_meta['ez_popup_button_url'][0]) ? $get_event_meta['ez_popup_button_url'][0] : null;
                    $ez_popup_button_url_target = isset($get_event_meta['ez_popup_button_url_target'][0]) ? $get_event_meta['ez_popup_button_url_target'][0] : null;
                    $ez_popup_delay = isset($get_event_meta['ez_popup_delay'][0]) ? $get_event_meta['ez_popup_delay'][0] : 0;
                    $ez_popup_frequency = isset($get_event_meta['ez_popup_frequency'][0]) ? $get_event_meta['ez_popup_frequency'][0] : null;
                    $ez_popup_start_date = isset($get_event_meta['ez_popup_start_date'][0]) ? $get_event_meta['ez_popup_start_date'][0] : null;
                    $ez_popup_end_date = isset($get_event_meta['ez_popup_end_date'][0]) ? $get_event_meta['ez_popup_end_date'][0] : null;
                    
                    $ez_popup_content = isset($get_event_meta['ez_popup_content'][0]) ? $get_event_meta['ez_popup_content'][0] : null;
                    $ez_popup_pages_include_exclude = isset($get_event_meta['ez_popup_pages_include_exclude'][0]) ? $get_event_meta['ez_popup_pages_include_exclude'][0] : null;
                    $ez_popup_pages_selector = isset($get_event_meta['ez_popup_pages_selector'][0]) ? $get_event_meta['ez_popup_pages_selector'][0] : null;
                    $ez_popup_content_safe = htmlspecialchars_decode($ez_popup_content);
                   
                    $ez_popup_pages_selector_array = explode('_', $ez_popup_pages_selector);
                    $show_popup_condition = false;
                    
                    // show popup html conditions
                        // all_pages
                        // exclude
                        // include
                    $show_popup_condition = ($ez_popup_pages_include_exclude == 'all_pages') ? true : ( (($ez_popup_pages_include_exclude == 'include') && in_array($current_page_id,$ez_popup_pages_selector_array )) ? true : ( ($ez_popup_pages_include_exclude == 'exclude') && (!in_array($current_page_id,$ez_popup_pages_selector_array)) ? true : false ));
                    
                    echo 'ez_popup_pages_selector_array : '.get_the_title($event_id).'<br/> current page : '.$current_page_id.'<br/>';
                    print_r($ez_popup_pages_selector_array);

                    if( $show_popup_condition ):
                    ?>  
                        <div class="ez_popup_wrapper" style="display: none;" data_popup_delay="<?php echo $ez_popup_delay; ?>" data_popup_frequencey="<?php echo $ez_popup_frequency; ?>" data_popup_start_date="<?php echo $ez_popup_start_date; ?>" data_popup_end_date="<?php echo $ez_popup_end_date; ?>" data_popup_id="<?php echo  $event_id ;?>" data_timezone="<?php echo date_default_timezone_get(); ?>">
                            <div id="ez_popup_background"></div>
                            <div class="ez_popup_inner">
                                <button class="close_ez_popup">X</button>
                                <div class="ez_popup_content_outer">
                                    <div class="ez_popup_content">
                                        <?php echo $ez_popup_content_safe; ?>
                                        
                                    </div>
                                    <div class="ez_popup_button">
                                        <a href="<?php echo  $ez_popup_button_url; ?>" target="<?php echo ($ez_popup_button_url_target == "new_tab") ? '_blank' : ''; ?>" class="ez_popup_button"><?php echo  $ez_popup_button_text; ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    endif;
                }
                wp_reset_postdata(); // Reset the global post object so that the rest of the page works correctly
            }
            
        }
    }
    $WebsitePunks_EZ_Popups = new WebsitePunks_EZ_Popups();
}