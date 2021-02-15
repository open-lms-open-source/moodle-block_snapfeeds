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
 * Block replacer.
 *
 * @package   block_snapfeeds
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_snapfeeds\api;

defined('MOODLE_INTERNAL') || die();

/**
 * Block replacer.
 *
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_replacer {

    /**
     * @var block_replacer
     */
    private static $instance;

    /**
     * block_replacer constructor.
     */
    private function __construct() {
    }

    /**
     * Singleton instance getter.
     * @return block_replacer
     */
    public static function get_instance() : block_replacer {
        if (self::$instance == null) {
            self::$instance = new block_replacer();
        }
        return self::$instance;
    }

    /**
     * @param $targetname
     * @param $sourcename
     * @param null $targetcdata
     * @param null $sourcecdata
     * @param int|null $parentcontextid
     * @throws \dml_exception
     */
    private function replace_block($targetname,  $sourcename, $targetcdata = null, $sourcecdata = null, $parentcontextid = null) {
        global $DB;
        $params = [
            'target_name' => $targetname,
            'source_name' => $sourcename,
        ];
        $targetcdatasql = ', configdata = NULL'; // Config data should be cleared if not used.
        if (is_object($targetcdata)) {
            $targetcdatasql = ', configdata = :target_cdata';
            $params['target_cdata'] = base64_encode(serialize($targetcdata));
        }
        $sourcecdatasql = '';
        if (is_object($sourcecdata)) {
            $sourcecdatasql = 'AND configdata = :source_cdata';
            $params['source_cdata'] = base64_encode(serialize($sourcecdata));
        }
        $parentctxtsql = '';
        if (!is_null($parentcontextid)) {
            $parentctxtsql = 'AND parentcontextid = :parentcontextid';
            $params['parentcontextid'] = $parentcontextid;
        }

        $query = <<<SQL
UPDATE {block_instances}
   SET blockname = :target_name
       $targetcdatasql
 WHERE blockname = :source_name
       $sourcecdatasql
       $parentctxtsql
SQL;
        $DB->execute($query, $params);
    }

    /**
     * Replaces all instances of upcoming events blocks with snap feeds block with the option to use deadlines.
     * @param int|null $parentcontextid
     */
    public function replace_upcoming_events_with_snap_feeds_deadlines($parentcontextid = null) {
        $config = (object) [
            'feedtype' => 'deadlines'
        ];
        $this->replace_block('snapfeeds', 'calendar_upcoming', $config, null, $parentcontextid);
    }

    /**
     * Replaces all instances of snap feeds block with the option to use deadlines with upcoming events blocks.
     * @param int|null $parentcontextid
     */
    public function replace_snap_feeds_deadlines_with_upcoming_events($parentcontextid = null) {
        $config = (object) [
            'feedtype' => 'deadlines'
        ];
        $this->replace_block('calendar_upcoming', 'snapfeeds', null, $config, $parentcontextid);
    }
}