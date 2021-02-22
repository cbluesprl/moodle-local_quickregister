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

require_once __DIR__ . '/../../config.php';

require_login();

require_capability('moodle/site:config', context_system::instance());

$url = new moodle_url('/local/quickregister/link_generator.php');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(implode(': ', [$SITE->fullname, get_string('link_generator_page_title', 'local_quickregister')]));
$PAGE->set_url($url);

$PAGE->requires->js(new moodle_url('/local/quickregister/js/local_quickregister.js'));

$link_generator_form = new \local_quickregister\link_generator_form();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('link_generator_page_title', 'local_quickregister'));
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide link_generator');

$link_generator_form->display();

if ($link_generator_form->is_cancelled()) {
    redirect($PAGE->url);
} elseif ($link_generator_form_data = $link_generator_form->get_data()) {
    $link_generator_result_form = new \local_quickregister\link_generator_result_form();

    $subscription_data_fields = ['username', 'password', 'email', 'firstname', 'lastname', 'city', 'country', ];
    if (array_key_exists('campaign', core_plugin_manager::instance()->get_installed_plugins('local'))) {
        $subscription_data_fields[] = 'campaign';
    }
    $subscription_data = [];
    foreach ($subscription_data_fields as $field) {
        $subscription_data[$field] = $link_generator_form_data->{$field};
    }
    $subscription_data = local_quickregister_encode_subscription_data($subscription_data);
    $subscription_ts = time();
    $subscription_signature = hash_hmac('sha256', $subscription_data . $subscription_ts, get_config('local_quickregister', 'key'));

    $result_url = new moodle_url($link_generator_form_data->link_url ?? '/', compact('subscription_data', 'subscription_signature', 'subscription_ts'));

    $link_generator_result_form->set_data(
        [
            'result_url' => $result_url->out(false),
            'result_link' => html_writer::link($result_url, $link_generator_form_data->link_anchor),
        ]
    );

    echo '<hr>';
    echo '<h3>' . get_string('link_generator_generated_heading', 'local_quickregister') . '</h3>';
    $link_generator_result_form->display();
}

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
