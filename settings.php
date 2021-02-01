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

/**
 * @var int $hassiteconfig
 * @var stdClass $CFG
 * @var moodle_page $PAGE
 * @var admin_root $ADMIN
 */

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_quickregister_settings',
        get_string('setting_visiblename', 'local_quickregister'),
        'moodle/site:config'
    );

    $ADMIN->add('localplugins', $settings);

    $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/quickregister/js/local_quickregister.js'));

    $buttons = [
        html_writer::tag('button', get_string('key_setting_generate_new', 'local_quickregister'), ['type' => 'button', 'class' => 'btn btn-secondary', 'onclick' => 'local_quickregister.generateRandomKey(32, "#id_s_local_quickregister_key")']),
        html_writer::tag('button', get_string('key_setting_copy_to_clipboard', 'local_quickregister'), ['type' => 'button', 'class' => 'btn btn-secondary', 'onclick' => 'local_quickregister.copyToClipboard("#id_s_local_quickregister_key")']),
    ];

    $settings->add(new admin_setting_configtext(
        'local_quickregister/key',
        get_string('key_setting_name', 'local_quickregister'),
        implode(' ', $buttons),
        null,
        PARAM_TEXT,
        40
    ));

    $ADMIN->add('localplugins', new admin_externalpage('local_quickregister_link_generator', get_string('link_generator_page_title', 'local_quickregister'), new moodle_url('/local/quickregister/link_generator.php')));
}