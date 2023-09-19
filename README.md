# Snap feeds Block
Snap feeds is a block, which uses Snap theme’s advanced feeds API 
to present users with their upcoming deadlines and latest forum posts.

This plugin was contributed by the Open LMS Product Development team. Open LMS is an education technology company
dedicated to bringing excellent online teaching to institutions across the globe.  We serve colleges and universities,
schools and organizations by supporting the software that educators use to manage and deliver instructional content to
learners in virtual classrooms.

## Installation
Extract the contents of the plugin into _/wwwroot/blocks_ then visit `admin/upgrade.php` or use the CLI script to upgrade your site.

## Flags

### The `block_snapfeeds_restore_from_upcoming_events`flag.

## Important notes
* This block depends on the [Snap](https://github.com/open-lms-open-source/moodle-theme_snap) theme, therefore for it to work correctly. 
The platform should have Snap enabled (even if it is not in use).
* The block works in any theme.
* The block currently supports 2 feed types, deadlines, and forum posts.
* This block can be enabled on the site front page, in a course and in My page.
* Within a course, the feed type available will be Deadlines.

## License
Copyright (c) 2021 Open LMS (https://www.openlms.net)

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
