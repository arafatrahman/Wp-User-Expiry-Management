<?php
/*
Plugin Name: WP Temp User Expiry
Description: Manage temporary user accounts with expiry dates. Allows setting expiry dates when adding users, and handles role changes, notifications, and more when a user expires.
Version: 1.0
Author: Arafat Rahman
Author URI: https://rrrplus.co.uk/
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include required files.
include_once plugin_dir_path(__FILE__) . 'models/ExpiryModel.php';
include_once plugin_dir_path(__FILE__) . 'controllers/ExpiryController.php';
include_once plugin_dir_path(__FILE__) . 'includes/ExpiryEmailer.php';
include_once plugin_dir_path(__FILE__) . 'includes/ExpiryScheduler.php';

// Initialize the controller.
ExpiryController::init();
