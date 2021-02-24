<?php
//This file is part of Moodle - http://moodle.org/
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
 * Tests for block_snapfeeds replacement tools.
 *
 * @package   block_snapfeeds
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_snapfeeds\api\block_replacer;

defined('MOODLE_INTERNAL') || die();

class block_snapfeeds_replace_test extends advanced_testcase  {

    protected function setUp(): void {
        $this->resetAfterTest();
    }

    public function test_replace_all_with_snapfeeds() {
        global $DB;
        $snapdeadlinesconfigdata = (object) [
            'feedtype' => 'deadlines'
        ];

        $course = $this->getDataGenerator()->create_course();
        $time = new DateTime("now", core_date::get_user_timezone_object());

        $blockinsert = (object) [
            'blockname' => 'calendar_upcoming',
            'parentcontextid' => context_course::instance($course->id)->id,
            'pagetypepattern' => 'course-view-*',
            'defaultregion' => 'side-pre',
            'defaultweight' => 1,
            'configdata' => null,
            'showinsubcontexts' => 1,
            'timecreated' => $time->getTimestamp(),
            'timemodified' => $time->getTimestamp()
        ];
        $blockid = $DB->insert_record('block_instances', $blockinsert);

        block_replacer::get_instance()->replace_upcoming_events_with_snap_feeds_deadlines();

        $block = $DB->get_record('block_instances', ['id' => $blockid]);

        $this->assertEquals($block->blockname, 'snapfeeds');
        $this->assertEquals($block->configdata, base64_encode(serialize($snapdeadlinesconfigdata)));
    }

    public function test_replace_all_with_upcoming_events() {
        global $DB;
        $snapdeadlinesconfigdata = (object) [
            'feedtype' => 'deadlines'
        ];

        $course = $this->getDataGenerator()->create_course();
        $time = new DateTime("now", core_date::get_user_timezone_object());

        $blockinsert = (object) [
            'blockname' => 'snapfeeds',
            'parentcontextid' => context_course::instance($course->id)->id,
            'pagetypepattern' => 'course-view-*',
            'defaultregion' => 'side-pre',
            'defaultweight' => 1,
            'configdata' => base64_encode(serialize($snapdeadlinesconfigdata)),
            'showinsubcontexts' => 1,
            'timecreated' => $time->getTimestamp(),
            'timemodified' => $time->getTimestamp()
        ];
        $blockid = $DB->insert_record('block_instances', $blockinsert);

        block_replacer::get_instance()->replace_snap_feeds_deadlines_with_upcoming_events();

        $block = $DB->get_record('block_instances', ['id' => $blockid]);

        $this->assertEquals($block->blockname, 'calendar_upcoming');
        $this->assertNull($block->configdata);
    }

    public function test_replace_in_context_with_snapfeeds() {
        global $DB, $CFG;

        $snapdeadlinesconfigdata = (object) [
            'feedtype' => 'deadlines'
        ];

        $course = $this->getDataGenerator()->create_course();
        $time = new DateTime("now", core_date::get_user_timezone_object());

        $blockinsert = (object) [
            'blockname' => 'calendar_upcoming',
            'parentcontextid' => context_course::instance($course->id)->id,
            'pagetypepattern' => 'course-view-*',
            'defaultregion' => 'side-pre',
            'defaultweight' => 1,
            'configdata' => null,
            'showinsubcontexts' => 1,
            'timecreated' => $time->getTimestamp(),
            'timemodified' => $time->getTimestamp()
        ];
        $blockid = $DB->insert_record('block_instances', $blockinsert);

        // Flag disabled, nothing happens.
        $CFG->block_snapfeeds_restore_from_upcoming_events = false;

        // Course creation triggers course_updated event.
        $createevent = \core\event\course_created::create([
            'objectid' => $course->id,
            'context' => context_course::instance($course->id),
            'other' => [
                'shortname' => $course->shortname,
                'fullname' => $course->fullname,
                'idnumber' => $course->idnumber
            ]
        ]);
        $createevent->add_record_snapshot('course', $course);
        $createevent->trigger();

        $block = $DB->get_record('block_instances', ['id' => $blockid]);

        $this->assertEquals($block->blockname, 'calendar_upcoming');
        $this->assertNull($block->configdata);

        // Flag enabled, instances should be replaced.
        $CFG->block_snapfeeds_restore_from_upcoming_events = true;

        // Course creation triggers course_updated event.
        $createevent = \core\event\course_created::create([
            'objectid' => $course->id,
            'context' => context_course::instance($course->id),
            'other' => [
                'shortname' => $course->shortname,
                'fullname' => $course->fullname,
                'idnumber' => $course->idnumber
            ]
        ]);
        $createevent->add_record_snapshot('course', $course);
        $createevent->trigger();

        $block = $DB->get_record('block_instances', ['id' => $blockid]);

        $this->assertEquals($block->blockname, 'snapfeeds');
        $this->assertEquals($block->configdata, base64_encode(serialize($snapdeadlinesconfigdata)));
    }
}
