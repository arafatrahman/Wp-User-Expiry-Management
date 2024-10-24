<?php

class ExpiryEmailer {
    public static function sendExpiryNotification($user_id) {
        $user = get_userdata($user_id);
        $to = $user->user_email;
        $subject = __('Your Account Expiry Notification');
        $message = __('Your account will expire soon. Please take the necessary actions.');
        wp_mail($to, $subject, $message);
    }
}
