<?php

class ExpiryModel {
    // Save expiry date as user meta.
    public static function setExpiry($user_id, $expiry_date) {
        // Store the date in the user's meta data.
        update_user_meta($user_id, 'user_expiry_date', date('Y-m-d H:i:s', strtotime($expiry_date)));
    }

    // Retrieve expiry date from user meta.
    public static function getExpiry($user_id) {
        return get_user_meta($user_id, 'user_expiry_date', true);
    }

    // Delete expiry date for a user.
    public static function deleteExpiry($user_id) {
        delete_user_meta($user_id, 'user_expiry_date');
    }
}
