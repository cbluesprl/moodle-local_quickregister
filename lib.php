<?php

function encode_subscription_data($data) {
    return base64_encode(json_encode($data));
}

function decode_subscription_data($data, $associative = true) {
    return json_decode(base64_decode($data), $associative);
}

function local_quickregister_before_http_headers()
{
    global $SESSION;

    if (isset($_GET['subscription_data'], $_GET['subscription_signature'], $_GET['subscription_ts'])) {
        $subscription_data = $_GET['subscription_data'];
        $subscription_signature = $_GET['subscription_signature'];
        $subscription_ts = $_GET['subscription_ts'];

        $key = get_config('local_quickregister', 'key');
        $signature = hash_hmac('sha256', $subscription_data . $subscription_ts, $key);
        $valid = ($subscription_ts < time() + 600) && $subscription_signature === $signature;

        if ($valid) {
            $subscription_data = decode_subscription_data($subscription_data);
            $SESSION->local_quickregister = compact('subscription_data');

            if (!empty($subscription_data['campaign'])) {
                $local_installed_plugins = core_plugin_manager::instance()->get_installed_plugins('local');

                if (array_key_exists('campaign', $local_installed_plugins)) {
                    $SESSION->local_campaign = $subscription_data['campaign'];
                }
            }
        } else {
            unset($SESSION->local_quickregister);
        }
    }
}

function local_quickregister_extend_signup_form(MoodleQuickForm $mform) {
    global $SESSION;

    /**
     * On signup page local_quickregister_extend_signup_form is called before local_quickregister_before_http_headers
     * But we want $SESSION->local_quickregister defined first
     */
    local_quickregister_before_http_headers();

    if (!empty($SESSION->local_quickregister['subscription_data'])) {
        $subscription_data = $SESSION->local_quickregister['subscription_data'];
        $mform->setDefaults($subscription_data);

        if (!empty($subscription_data['email'])) {
            $mform->setDefault('email2', $subscription_data['email']);
        }
    }
}
