<?php

class ExpiryScheduler {
    public static function scheduleExpiryChecks() {
        if (!wp_next_scheduled('expire_users_expired')) {
            wp_schedule_event(time(), 'hourly', 'expire_users_expired');
        }
    }

    public static function runExpiryChecks() {
        // Logic to check for expired users and handle them.
        $users = get_users();
        foreach ($users as $user) {
            ExpiryController::handleUserExpiry($user->ID);
        }
    }
}

// Schedule the expiry checks on plugin activation.
register_activation_hook(__FILE__, ['ExpiryScheduler', 'scheduleExpiryChecks']);
add_action('expire_users_expired', ['ExpiryScheduler', 'runExpiryChecks']);
