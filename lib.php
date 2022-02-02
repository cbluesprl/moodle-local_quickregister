<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @author    sreynders@cblue.be
 * @copyright CBlue SPRL, support@cblue.be
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   local_quickregister
 */

defined('MOODLE_INTERNAL') || die();

define('LOCAL_QUICKREGISTER_TS_EXPIRY_AFTER', 600); // 10 minutes

/**
 * @param $data
 * @return string
 */
function local_quickregister_encode_subscription_data($data) {
    return base64_encode(json_encode($data));
}

/**
 * @param $data
 * @param bool $associative
 * @return mixed
 */
function local_quickregister_decode_subscription_data($data, $associative = true) {
    return json_decode(base64_decode($data), $associative);
}

/**
 * @throws coding_exception
 * @throws dml_exception
 */
function local_quickregister_before_http_headers() {
    global $SESSION, $SCRIPT;

    if ($SCRIPT === '/login/signup.php') {
        $subscription_data = optional_param('subscription_data', false, PARAM_RAW);
        $subscription_signature = optional_param('subscription_signature', false, PARAM_ALPHANUM);
        $subscription_ts = optional_param('subscription_ts', false, PARAM_INT);

        if ($subscription_data && $subscription_signature && $subscription_ts) {
            $key = get_config('local_quickregister', 'key');
            $link_validity_period = (int)get_config('local_quickregister', 'link_validity_period');
            $signature = hash_hmac('sha256', $subscription_data . $subscription_ts, $key);
            $valid = ($subscription_ts > time() - $link_validity_period) && $subscription_signature === $signature;

            if ($valid) {
                $subscription_data = local_quickregister_decode_subscription_data($subscription_data);
                $SESSION->local_quickregister_subscription_data = $subscription_data;

                if (array_key_exists('campaign', core_plugin_manager::instance()->get_installed_plugins('local'))) {
                    if (!empty($subscription_data['campaign'])) {
                        $SESSION->local_campaign = $subscription_data['campaign'];
                    }
                }
            } else {
                unset($SESSION->local_quickregister_subscription_data);
            }
        }
    }
}

/**
 * @param MoodleQuickForm $mform
 * @throws coding_exception
 * @throws dml_exception
 */
function local_quickregister_extend_signup_form(MoodleQuickForm $mform) {
    global $SESSION;

    // On signup page local_quickregister_extend_signup_form is called before local_quickregister_before_http_headers
    // But we want $SESSION->local_quickregister_subscription_data defined first
    local_quickregister_before_http_headers();

    if (!empty($SESSION->local_quickregister_subscription_data)) {
        $subscription_data = $SESSION->local_quickregister_subscription_data;
        $mform->setDefaults($subscription_data);

        if (!empty($subscription_data['email'])) {
            $mform->setDefault('email2', $subscription_data['email']);
        }
        if (isset($subscription_data['campaign'])) {
            $SESSION->local_campaign = $subscription_data['campaign'];
        }
        unset($SESSION->local_quickregister_subscription_data);
    }
}
