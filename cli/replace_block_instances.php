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
 * Generates a null provider implementation for a given plugin.
 *
 * @package    local_mrooms
 * @author     David Castro
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_snapfeeds\api\block_replacer;

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// CLI options.
[$options, $unrecognized] = cli_get_params(
    [
        'help'     => false,
        'rollback' => false,
        'confirm'  => false,
    ],
    [
        'h' => 'help',
        'r' => 'rollback',
        'c' => 'confirm',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}


if (!empty($options['help'])) {
    echo "Replaces instances of 'block_calendar_upcoming' with 'block_snapfeeds' or vice versa.

Options:
-h, --help       Print out this help.
-r, --rollback   Rollback to 'block_calendar_upcoming'.
-c, --confirm    Confirm update, will only list instance amounts otherwise.

Example:
$ /usr/bin/php blocks/snapfeeds/cli/replace_block_instances.php" . PHP_EOL;

    exit(0);
}

$blocknames = (object) (empty($options['rollback']) ?
    ['source' => 'calendar_upcoming', 'target' => 'snapfeeds'] :
    ['target' => 'calendar_upcoming', 'source' => 'snapfeeds'] );

// Review what will be done.
$query = <<<SQL
    SELECT COUNT(1)
      FROM {block_instances}
     WHERE blockname = :blockname
SQL;
$params = ['blockname' => $blocknames->source];
if ($blocknames->source == 'snapfeeds') {
    $config = (object) [
        'feedtype' => 'deadlines'
    ];
    $params['configdata'] = base64_encode(serialize($config));
    $query .= '  AND configdata = :configdata';
}

$toreplacecount = $DB->count_records_sql($query, $params);

if (empty($toreplacecount)) {
    cli_writeln("[WARN] No instances found for 'block_{$blocknames->source}'.");
} else {
    cli_writeln("[INFO] Will replace $toreplacecount instances of 'block_{$blocknames->source}' with 'block_{$blocknames->target}'");
}

$murl = (new moodle_url('admin/blocks.php'))->out();
cli_writeln("[INFO] You can check the instance uses in the page $murl");
if (empty($options['confirm'])) {
    cli_writeln("[INFO] To execute the change, run again with '--confirm'");
    exit(0);
}

if (empty($toreplacecount)) {
    cli_error("[ERROR] Nothing to replace.");
}

if (empty($options['rollback'])) {
    block_replacer::get_instance()->replace_upcoming_events_with_snap_feeds_deadlines();
} else {
    block_replacer::get_instance()->replace_snap_feeds_deadlines_with_upcoming_events();
}
cli_writeln("[INFO] Replacement done.");

exit(0);