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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Determines which functions can be called via the Moodle web service API and which user roles (e.g. administrators,
 * teachers, students) have access to certain web service functions of the learning plan plugin.
 *
 * @package     local_greetings
 * @copyright   2025 Ivonne Moritz <moritz.ivonne@fh-swf.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = [
    'local_learningplan_save_section_data' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'save_section_data',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Save section data',
        'type' => 'write',
        'ajax' => true
    ],
    'local_learningplan_delete_section_data' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'delete_section_data',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Delete section data',
        'type' => 'write',
        'ajax' => true
    ],
    'local_learningplan_check_section_data' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'check_section_data',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Checks if section data already exists in the database',
        'type' => 'read',
        'ajax' => true
    ],
    'local_learningplan_update_deadline' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'update_deadline',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Updates the processing deadline for a learning plan entry',
        'type' => 'write',
        'ajax' => true
    ],
    'local_learningplan_update_progress' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'update_progress',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Updates the completion status for a learning plan entry',
        'type' => 'write',
        'ajax' => true
    ],
    'local_learningplan_toggle_section_option' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'toggle_section_option',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Toggles the learning plan option for a section',
        'type' => 'write',
        'ajax' => true
    ],
    'local_learningplan_get_section_option' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'get_section_option',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Gets the learning plan option for a section',
        'type' => 'read',
        'ajax' => true
    ],
    'local_learningplan_delete_section_data_for_all' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'delete_section_data_for_all',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Delete section data for all users',
        'type' => 'write',
        'ajax' => true
    ],
];

