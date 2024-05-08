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
 * Snap feeds.
 *
 * @package   block_snapfeeds
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_snap\output\ce_render_helper;

/**
 * Snap feeds block class.
 *
 * @package   block_snapfeeds
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_snapfeeds extends block_base {
    public function init() {
        $this->title = get_string('snapfeeds', 'block_snapfeeds');
    }

    /**
     * By all means, be our guest.
     * @return bool
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * No header.
     * @return bool
     */
    public function hide_header() {
        return true;
    }

    /**
     * Custom element bliss.
     * @return stdClass|null
     * @throws coding_exception
     */
    public function get_content() {
        global $CFG, $PAGE, $COURSE;
        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->config)) {
            $this->config = new stdClass();
            $this->config->feedtype = '';
        }

        if ($this->context->get_course_context(false)) {
            if (is_guest($this->context)) {
                return $this->content; // Course guests don't see this block.
            }
        } else if (isguestuser()) {
            return $this->content;  // Site guests don't see this block.
        }

        // Custom elements JS library load.
        $paths['theme_snap/snapce'] = [
            $CFG->wwwroot . '/pluginfile.php/' . $PAGE->context->id . '/theme_snap/vendorjs/snap-custom-elements/snap-ce',
        ];
        $PAGE->requires->js_call_amd('theme_snap/wcloader', 'init', [
            'componentPaths' => json_encode($paths),
        ]);

        // Should we get data for a specific course.
        $courseid = 0;
        if ($COURSE->id != SITEID) {
            $courseid = $COURSE->id;
        }
        $this->content = new stdClass;
        switch ($this->config->feedtype) {
            case 'forumposts':
            case 'deadlines':
                $feedtype = $this->config->feedtype;
                $heading = get_string($feedtype, 'theme_snap');
                $virtualpaging = true; // Web service retrieves all elements, need to do virtual paging.
                $nocontentstr = get_string('no' . $feedtype, 'theme_snap');
                $showreload = true;
                $waitforpm = false; // We are out of Snap, we don't need to wait for the PM to open.
                $this->content->text = ce_render_helper::get_instance()
                    ->render_feed_web_component($feedtype, $heading,
                        $nocontentstr, $virtualpaging, $showreload, $waitforpm, $courseid);
                break;
            default:
                $this->content->text = get_string('nofeedconfigured', 'block_snapfeeds');
                break;
        }

        return $this->content;
    }
}