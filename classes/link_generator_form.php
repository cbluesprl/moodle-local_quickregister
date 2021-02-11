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

namespace local_quickregister;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once "$CFG->libdir/formslib.php";
require_once "$CFG->libdir/moodlelib.php";
require_once "$CFG->libdir/classes/user.php";

class link_generator_form extends \moodleform {

    /**
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'user_infos', get_string('link_generator_form_user_infos_header', 'local_quickregister'), '');

        $mform->addElement('text', 'username', get_string('username'), ['maxlength' => 100, 'size' => 12, 'autocapitalize' => 'none']);
        $mform->setType('username', PARAM_RAW);

        if (!empty($CFG->passwordpolicy)) {
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('password', 'password', get_string('password'), ['maxlength' => 32, 'size' => 12]);
        $mform->setType('password', \core_user::get_property_type('password'));

        $mform->addElement('text', 'email', get_string('email'), ['maxlength' => 100, 'size' => 25]);
        $mform->setType('email', \core_user::get_property_type('email'));

        $mform->addElement('text', 'firstname', get_string('firstname'), ['maxlength' => 100, 'size' => 30]);
        $mform->setType('firstname', \core_user::get_property_type('firstname'));

        $mform->addElement('text', 'lastname', get_string('lastname'), ['maxlength' => 100, 'size' => 30]);
        $mform->setType('lastname', \core_user::get_property_type('firstname'));

        $mform->addElement('text', 'city', get_string('city'), ['maxlength' => 120, 'size' => 20]);
        $mform->setType('city', \core_user::get_property_type('city'));

        $countries = get_string_manager()->get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $countries = array_merge($default_country, $countries);
        $mform->addElement('select', 'country', get_string('country'), $countries);
        $mform->setDefault('country', $CFG->country ?? '');

        $local_installed_plugins = \core_plugin_manager::instance()->get_installed_plugins('local');
        if (array_key_exists('campaign', $local_installed_plugins)) {
            $mform->addElement('header', 'campaign_infos', get_string('pluginname', 'local_campaign'), '');
            $mform->setExpanded('campaign_infos');

            require_once "$CFG->dirroot/local/campaign/lib.php";

            $campaigns = local_campaign_split_lines(get_config('local_campaign', 'campaigns'));
            $campaigns_options = ['' => get_string('link_generator_select_campaign', 'local_quickregister')] + $campaigns;
            $mform->addElement('select', 'campaign', get_string('pluginname', 'local_campaign'), $campaigns_options);
            $mform->setType('campaign', PARAM_TEXT);
        }

        $mform->addElement('header', 'link_infos', get_string('link_generator_form_link_infos_header', 'local_quickregister'), '');

        $mform->addElement('url', 'link_url', get_string('link_generator_form_link_url_label', 'local_quickregister'), ['size' => 50], ['usefilepicker' => false]);
        $mform->setType('link_url', PARAM_URL);
        $mform->addRule('link_url', get_string('required'), 'required', null, 'client');
        $mform->setDefault('link_url', new \moodle_url('/login/signup.php'));

        $mform->addElement('text', 'link_anchor', get_string('link_generator_form_link_anchor_label', 'local_quickregister'));
        $mform->setType('link_anchor', PARAM_TEXT);
        $mform->addRule('link_anchor', get_string('required'), 'required', null, 'client');
        $mform->setDefault('link_anchor', get_string('link_generator_form_link_anchor_default', 'local_quickregister'));

        $this->add_action_buttons(true, get_string('link_generator_form_button_label', 'local_quickregister'));
    }
}
