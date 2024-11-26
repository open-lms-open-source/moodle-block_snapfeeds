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
 * Form for editing settings.
 *
 * @package   block_snapfeeds
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for Snap feeds instances.
 *
 * @package   block_snapfeeds
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_snapfeeds_edit_form extends block_edit_form {
    /**
     * @param MoodleQuickForm $mform
     * @throws coding_exception
     */
    protected function specific_definition($mform) {
        global $COURSE;
        $feedtypes = [
            'deadlines' => get_string('deadlines', 'theme_snap'),
        ];
        if ($COURSE->id == SITEID) {
            $feedtypes['forumposts'] = get_string('forumposts', 'theme_snap');
        }
        $mform->addElement('select', 'config_feedtype', get_string('feedtype', 'block_snapfeeds'), $feedtypes);
        $mform->setDefault('config_feedtype', 'deadlines');
        $mform->setType('config_feedtype', PARAM_ALPHAEXT);
    }
}
