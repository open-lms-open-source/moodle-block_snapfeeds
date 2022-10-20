# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
# Tests for Snap feeds block.
#
# @package   block_snapfeeds
# @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@block @block_snapfeeds @theme_snap
Feature: Add snapfeeds blocks.

  @javascript
  Scenario: Add a Snap feeds block to the main page.
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Snap feeds" block
    Then I should see "This Snap feed has not been configured"
    And I configure the "Snap feeds" block
    When I set the following fields to these values:
      | id_config_feedtype | deadlines |
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "Deadlines" in the "Snap feeds" "block"
