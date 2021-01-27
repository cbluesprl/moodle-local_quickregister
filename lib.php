<?php

function local_quickregister_before_http_headers()
{
    global $SESSION, $PAGE;

    if (isset($_GET['subscription_data'], $_GET['subscription_signature'], $_GET['subscription_ts'])) {
        $key = get_config('local_quickregister', 'key');
        $signature = hash_hmac('sha256', $_GET['subscription_data'] . $_GET['subscription_ts'], $key);
        $valid = ($_GET['subscription_ts'] < time() + 600) && $_GET['subscription_signature'] === $signature;

        if ($valid) {
            $subscription_data = $_GET['subscription_data'];
            $local_plugins = core_plugin_manager::instance()->get_plugins_of_type('local');
            if (array_key_exists('campaign', $local_plugins)) {
                $subscription_data['campaign'] = $_COOKIE['local_campaign'] ?? null; // TODO: use session
            }

            $SESSION->local_quickregister = [
                'subscription_data' => base64_encode(json_encode($subscription_data))
            ];
        }
    }
}

function local_quickregister_extend_signup_form(MoodleQuickForm $mform) {
    //$subscription_data = json_decode(base64_decode($SESSION->local_quickregister['subscription_data']));
    $subcription_data = [
        'username' => 'foo',
        'password' => '8T8r.#Xz',
        'email' => 'foo@bar.com',
        'firstname' => 'Foo',
        'lastname' => 'Bar',
        'city' => 'My city',
        'country' => 'BE',
    ];

    $mform->setDefaults($subcription_data);

    if (!empty($subcription_data['email'])) {
        $mform->setDefault('email2', $subcription_data['email']);
    }
}
