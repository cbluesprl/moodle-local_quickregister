<?php

// This file is part of Moodle - http://moodle.org/
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
 * @package    local_quickregister
 * @copyright  2022 CBlue
 * @author     amayard@cblue.be
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quickregister;

use core\event\user_created;
use stdClass;

defined('MOODLE_INTERNAL') || die();

class observer {

    public static function user_created(user_created $event) {
        global $SESSION, $DB, $CFG;
        $config_campaigns = get_config('local_campaign', 'campaigns');

        // Don't do anything if local_campaign has no config/is not installed
        if (!empty($config_campaigns)) {
            require_once $CFG->dirroot . '/local/campaign/lib.php';

            $config_campaigns = local_campaign_split_lines($config_campaigns);

            // Don't do anything if there's nothing in Session or if it doesn't match anything in local_campaign config
            if (isset($SESSION->local_campaign) && !empty($config_campaigns) && array_key_exists($SESSION->local_campaign, $config_campaigns)) {

                // Last security check : user exists & has been signed up using email-based method
                $user = $DB->get_record('user', ['id' => $event->relateduserid]);
                if (empty($user) || $user->auth != 'email') {
                    return;
                }

                $field = $DB->get_record('user_info_field', ['shortname' => 'campaigns']);

                $conditions = ['userid' => $event->relateduserid, 'fieldid' => $field->id];
                $user_info_data = $DB->get_record('user_info_data', $conditions);

                $dataobject = new stdClass();
                $dataobject->userid = $event->relateduserid;
                $dataobject->fieldid = $field->id;
                $dataobject->data = $config_campaigns[$SESSION->local_campaign];

                if (!empty($user_info_data)) {
                    $dataobject->id = $user_info_data->id;
                    $DB->update_record('user_info_data', $dataobject, $conditions);
                } else {
                    $DB->insert_record('user_info_data', $dataobject);
                }
            }
        }
    }

}
