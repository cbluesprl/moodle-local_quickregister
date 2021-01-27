<?php

namespace local_quickregister;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once "$CFG->libdir/formslib.php";
require_once "$CFG->libdir/moodlelib.php";
require_once "$CFG->libdir/classes/user.php";

class link_generator_result_form extends \moodleform
{
    public function definition()
    {
        $mform = $this->_form;

        $mform->addElement('text', 'result_url', get_string('link_generator_result_form_url_label', 'local_quickregister'), ['size' => 50]);
        $mform->setType('result_url', PARAM_TEXT);

        $mform->addElement('static', 'result_url_copy', '', \html_writer::tag('button', get_string('link_generator_result_form_copy_to_clipboard', 'local_quickregister'), ['type' => 'button', 'class' => 'btn btn-secondary', 'onclick' => 'local_quickregister.copyToClipboard("#id_result_url")']));

        $mform->addElement('textarea', 'result_link', get_string('link_generator_result_form_link_label', 'local_quickregister'), ['cols' => 50, 'rows' => 4]);
        $mform->setType('result_link', PARAM_TEXT);

        $mform->addElement('static', 'result_link_copy', '', \html_writer::tag('button', get_string('link_generator_result_form_copy_to_clipboard', 'local_quickregister'), ['type' => 'button', 'class' => 'btn btn-secondary', 'onclick' => 'local_quickregister.copyToClipboard("#id_result_link")']));
    }
}