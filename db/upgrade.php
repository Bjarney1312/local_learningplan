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
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_learningplan
 * @copyright   2024 Ivonne Moritz <moritz.ivonne@fh-swf.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define upgrade steps to be performed to upgrade the plugin from the old version to the current one.
 *
 * @param int $oldversion Version number the plugin is being upgraded from.
 */
function xmldb_local_learningplan_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025020413) {

        // Define field state to be added to local_learningplan.
        $table = new xmldb_table('local_learningplan');
        $field1 = new xmldb_field('state', XMLDB_TYPE_CHAR, '25', null, XMLDB_NOTNULL, null, 'Offen', 'section');
        $field2 = new xmldb_field('processing_deadline', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'state');
        $key = new xmldb_key('un_learningplan', XMLDB_KEY_UNIQUE, ['user', 'course', 'section']);

        // Conditionally launch add field state.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        // Conditionally launch add field state.
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Launch add key un_learningplan.
        $dbman->add_key($table, $key);

        // Learningplan savepoint reached.
        upgrade_plugin_savepoint(true, 2025020413, 'local', 'learningplan');
    }


    return true;
}

