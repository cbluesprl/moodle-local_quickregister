<?php

namespace local_quickregister;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once "$CFG->libdir/formslib.php";
require_once "$CFG->libdir/moodlelib.php";
require_once "$CFG->libdir/classes/user.php";

class link_generator_form extends \moodleform
{
    public function definition()
    {
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

        $mform->addElement('header', 'link_infos', get_string('link_generator_form_link_infos_header', 'local_quickregister'), '');

        $mform->addElement('url', 'link_url', get_string('link_generator_form_link_url_label', 'local_quickregister'), ['size' => 50, 'required' => 'required'], ['usefilepicker' => false]);
        $mform->setType('link_url', PARAM_URL);
        $mform->setDefault('link_url', new \moodle_url('/'));

        $mform->addElement('text', 'link_anchor', get_string('link_generator_form_link_anchor_label', 'local_quickregister'), ['required' => 'required']);
        $mform->setType('link_anchor', PARAM_TEXT);
        $mform->setDefault('link_anchor', get_string('link_generator_form_link_anchor_default', 'local_quickregister'));

        $this->add_action_buttons(true, get_string('link_generator_form_button_label', 'local_quickregister'));
    }
}