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
 * Event handlers.
 * @package   block_snapfeeds
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_snapfeeds;

use block_snapfeeds\api\block_replacer;
use core\event\base;

/**
 * Event handlers class.
 *
 * @package   block_snapfeeds
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class event_handlers {

    /**
     * @param base $event
     */
    public static function review_course(base $event) {
        global $CFG;
        if (empty($CFG->block_snapfeeds_restore_from_upcoming_events)) {
            return;
        }
        block_replacer::get_instance()->replace_upcoming_events_with_snap_feeds_deadlines($event->contextid);
    }
}