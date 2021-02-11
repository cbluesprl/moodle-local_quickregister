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

class link_generator_result_form extends \moodleform {

    /**
     * @throws \coding_exception
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'result_url', get_string('link_generator_result_form_url_label', 'local_quickregister'), ['size' => 50]);
        $mform->setType('result_url', PARAM_TEXT);

        $mform->addElement('static', 'result_url_copy', '', \html_writer::tag('button', get_string('link_generator_result_form_copy_to_clipboard', 'local_quickregister'), ['type' => 'button', 'class' => 'btn btn-secondary', 'onclick' => 'local_quickregister.copyToClipboard("#id_result_url")']));

        $mform->addElement('textarea', 'result_link', get_string('link_generator_result_form_link_label', 'local_quickregister'), ['cols' => 50, 'rows' => 4]);
        $mform->setType('result_link', PARAM_TEXT);

        $mform->addElement('static', 'result_link_copy', '', \html_writer::tag('button', get_string('link_generator_result_form_copy_to_clipboard', 'local_quickregister'), ['type' => 'button', 'class' => 'btn btn-secondary', 'onclick' => 'local_quickregister.copyToClipboard("#id_result_link")']));
    }
}
