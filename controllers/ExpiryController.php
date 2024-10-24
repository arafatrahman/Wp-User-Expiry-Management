<?php

class ExpiryController {
    public static function init() {
      
        add_action('user_register', ['ExpiryController', 'assignDefaultExpiry']);
        add_action('expire_users_expired', ['ExpiryController', 'handleUserExpiry'], 10, 1);
        add_filter('manage_users_columns', ['ExpiryController', 'addExpiryColumn']);
        add_action('manage_users_custom_column', ['ExpiryController', 'showExpiryColumn'], 10, 3);
        add_action('admin_enqueue_scripts', ['ExpiryController', 'enqueueScripts']);
        
     
        
        add_action('user_register', ['ExpiryController', 'saveExpiryDateField']);
        add_filter('wp_authenticate_user', ['ExpiryController', 'checkUserExpiryOnLogin'], 10, 2);
        add_action('admin_footer', ['ExpiryController', 'loadModalView']);
        add_action('wp_ajax_update_user_expiry_date', ['ExpiryController', 'updateExpiryDate']);

        
       
      //  add_action('init', ['ExpiryController', 'auto_logout_expired_user']);
    }

    /*
    public static function auto_logout_expired_user() {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $expiry_date = get_user_meta($user_id, 'user_expiry_date', true);

            if($expiry_date){
    
                // Get the current server time in the 'Y-m-d H:i:s' format
                $current_time = current_time('mysql'); 
            
                // If there's an expiry date set and it's in the past, prevent login
                if ($expiry_date && strtotime($expiry_date) <= strtotime($current_time)) {
                   
                    wp_logout();
                    // Redirect to the login page with an expired parameter
                    wp_redirect(wp_login_url() . '?expired=1');
                    return new WP_Error('user_expired', __('Your account has expired and you cannot log in. Please contact the site administrator.'));
                    exit;
                }
            }

            
        }
    }
        */

    public static function loadModalView() {
        // Include the modal HTML if the view file exists
        $view_file = plugin_dir_path(__FILE__) . '../views/modal-view.php';
        if (file_exists($view_file)) {
            include $view_file;
        }
    }
    /**
     * Prevents expired users from logging in.
     *
     * @param WP_User|WP_Error $user WP_User object if successful, or a WP_Error object if not.
     * @return WP_User|WP_Error
     */
    public static function checkUserExpiryOnLogin($user) {
        
        if (is_wp_error($user)) {
            return $user; // If there's already an error, return it.
        }
    
        // Get the user's expiry date from the model
        $expiry_date = ExpiryModel::getExpiry(user_id: $user->ID);

        if($expiry_date){
    
            // Get the current server time in the 'Y-m-d H:i:s' format
            $current_time = current_time('mysql'); 
        
            // If there's an expiry date set and it's in the past, prevent login
            if ($expiry_date && strtotime($expiry_date) <= strtotime($current_time)) {
                return new WP_Error('user_expired', __('Your account has expired and you cannot log in. Please contact the site administrator.'));
            }
        }
    
        return $user;
    }



    public static function addExpiryColumn($columns) {
        $columns['user_expiry_date'] = __('Expiry Date');
        return $columns;
    }

    public static function showExpiryColumn($value, $column_name, $user_id) {
        if ($column_name == 'user_expiry_date') {
            $expiry_date = ExpiryModel::getExpiry($user_id);
            if ($expiry_date) {
                $formatted_date = date('Y-m-d H:i:s', strtotime($expiry_date));
                $value = "<span id='expiry-date-$user_id'>$formatted_date</span>";
                $value .= " <a href='#' class='edit-expiry' data-user-id='$user_id'>Edit</a>";
            } else {
                $value = "<span id='expiry-date-$user_id'>Never</span>";
                if (!is_super_admin($user_id)) {
                $value .= " <a href='#' class='edit-expiry' data-user-id='$user_id'>Set Expiry</a>";
                }
            }
        }
        return $value;
    }



    public static function saveExpiryDateField($user_id) {
        if (isset($_POST['user_expiry_date']) && !empty($_POST['user_expiry_date'])) {
            $expiry_date = sanitize_text_field($_POST['user_expiry_date']);
            ExpiryModel::setExpiry($user_id, $expiry_date);
        }
    }

    public static function assignDefaultExpiry($user_id) {
        // Set the expiry date to 30 days from the current date by default.
        $default_expiry_date = date('Y-m-d H:i:s', strtotime('+30 days'));
        ExpiryModel::setExpiry($user_id, $default_expiry_date);
    }

    public static function handleUserExpiry($user_id) {
        // Retrieve the expiry date for the user
        $expiry_date = ExpiryModel::getExpiry($user_id);
        
        // If there's no expiry date, exit early.
        if (!$expiry_date) {
            error_log('No expiry date found for User ID: ' . $user_id);
            return;
        }
    
        // Log retrieved expiry date and current date for debugging.
        error_log('User ID ' . $user_id . ' - Stored Expiry Date: ' . $expiry_date);
        error_log('Current Date: ' . date('Y-m-d H:i:s'));
    
        // Compare the stored expiry date with the current date.
        if (strtotime($expiry_date) <= time()) {
            error_log('User ID ' . $user_id . ' has expired.');
    
            // Change the user role to "subscriber" or any other action you want to perform.
            $user = new WP_User($user_id);
            $user->set_role('subscriber'); // Change role to a lower privilege
    
            // Send notification email to user.
            ExpiryEmailer::sendExpiryNotification($user_id);
    
            // Optionally, delete the user's expiry data.
            ExpiryModel::deleteExpiry($user_id);
        } else {
            error_log('User ID ' . $user_id . ' has NOT expired.');
        }
    }
    

    public static function enqueueScripts() {
        wp_enqueue_script('user-expiry-js', plugin_dir_url(__FILE__) . '../assets/js/user-expiry.js', ['jquery'], '1.0', true);
        wp_enqueue_style('user-expiry-css', plugin_dir_url(__FILE__) . '../assets/css/user-expiry.css', [], '1.0');
    
        // Localize script for AJAX
        wp_localize_script('user-expiry-js', 'userExpiryAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('user_expiry_nonce'),
        ]);
    }

    public static function updateExpiryDate() {
        // Ensure the nonce is verified
    if (!isset($_POST['user_id'], $_POST['expiry_date'])) {
        wp_send_json_error('Invalid data.');
        return;
    }

    $user_id = intval($_POST['user_id']);
    $expiry_date = sanitize_text_field($_POST['expiry_date']); // e.g., "2024-10-30T10:38"

    // If the expiry date is in ISO format (YYYY-MM-DDTHH:MM), convert it
    if (strpos($expiry_date, 'T') !== false) {
        // Replace 'T' with a space and add seconds
        $expiry_date = str_replace('T', ' ', $expiry_date) . ':00'; // e.g., "2024-10-30 10:38:00"
    }

    // Save the expiry date to user meta or wherever you need it
    if (update_user_meta($user_id, 'user_expiry_date', meta_value: $expiry_date)) {
        $url = admin_url('users.php');
       
        wp_send_json_success('Expiry date updated.');
    } else {
        wp_send_json_error('Failed to update expiry date.');
    }
    }
}
