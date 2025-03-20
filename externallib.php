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

namespace local_learningplan\external;

global $CFG;

use dml_exception;
use external_api;
use external_function_parameters;
use external_value;
use invalid_parameter_exception;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * Defines external API functions for the plugin and the parameters and return values of the web services.
 *
 * @package     local_learningplan
 * @copyright   2025 Ivonne Moritz <moritz.ivonne@fh-swf.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class learningplan_service extends external_api
{
    /**
     * Defines which parameters the web service function 'check_section_data' expects and specifies the data types of
     * the parameters. Also ensures automatic validation of the parameters before the actual web service function is
     * called.
     *
     * @return external_function_parameters
     */
    public static function check_section_data_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section ID'),
            'userid' => new external_value(PARAM_INT, 'User ID')
        ]);
    }

    /**
     * Checks if a record exists for the specified course, section, and user in the 'local_learningplan' table.*
     *
     * @param int $courseid The ID of the course.
     * @param int $sectionid The ID of the section.
     * @param int $userid The ID of the user.
     *
     * @return bool Returns true if the record exists, false otherwise.
     * @throws dml_exception|invalid_parameter_exception If there is a database error.
     */
    public static function check_section_data(int $courseid, int $sectionid, int $userid): bool
    {
        // Validierung der Eingabeparameter
        $params = self::validate_parameters(self::check_section_data_parameters(), [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'userid' => $userid
        ]);

        global $DB;

        try {
            // Überprüfen, ob der Datensatz in der Tabelle existiert
            return $DB->record_exists('local_learningplan', [
                'course' => $params['courseid'],
                'section' => $params['sectionid'],
                'user' => $params['userid']
            ]);
        } catch (dml_exception $e) {
            throw new dml_exception('Error checking for record existence in the database', $e);
        }
    }

    /**
     * Defines the return type for the check_section_data web service function.
     *
     * @return external_value A text string representing the existence status.
     */
    public static function check_section_data_returns(): external_value
    {
        return new external_value(PARAM_TEXT, 'Existence status');
    }

    /**
     * Defines which parameters the web service function save_section_data expects and specifies the data types of
     * the parameters. Also ensures automatic validation of the parameters before the actual web service function is
     * called.
     *
     * @return external_function_parameters
     */
    public static function save_section_data_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section ID'),
            'userid' => new external_value(PARAM_INT, 'User ID')
        ]);
    }

    /**
     * Inserts a new record into the learning plan table for a specific course section and user.
     *
     * This function creates a new entry in the 'local_learningplan' database table,
     * associating a user with a specific course section. The entry includes the course ID,
     * section ID, user ID, and the timestamp of when it was created.
     *
     * @param int $courseid The ID of the course.
     * @param int $sectionid The ID of the course section.
     * @param int $userid The ID of the user.
     *
     * @return string Returns 'success' if the record was successfully inserted.
     * @throws dml_exception|invalid_parameter_exception If an error occurs while inserting the record.
     */
    public static function save_section_data(int $courseid, int $sectionid, int $userid): string
    {
        global $DB;

        $params = self::validate_parameters(self::save_section_data_parameters(), [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'userid' => $userid
        ]);

        $record = new stdClass();
        $record->course = $params['courseid'];
        $record->section = $params['sectionid'];
        $record->user = $params['userid'];
        $record->timecreated = time();

        try {
            $DB->insert_record('local_learningplan', $record);
        } catch (dml_exception $e) {
            throw new dml_exception('Error inserting record into the database', $e);
        }

        return 'success';
    }

    /**
     * Defines the return type for the save_section_data web service function.
     *
     * @return external_value A text string representing the existence status.
     */
    public static function save_section_data_returns(): external_value
    {
        return new external_value(PARAM_TEXT, 'Status message');
    }

    /**
     * Defines which parameters the web service function delete_section_data expects and specifies the data types of
     * the parameters. Also ensures automatic validation of the parameters before the actual web service function is
     * called.
     *
     * @return external_function_parameters
     */
    public static function delete_section_data_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section ID'),
            'userid' => new external_value(PARAM_INT, 'User ID')
        ]);
    }

    /**
     * Deletes a record from the 'local_learningplan' database table for a specific course section and user.
     *
     * @param int $courseid The ID of the course.
     * @param int $sectionid The ID of the course section.
     * @param int $userid The ID of the user.
     *
     * @return string Returns 'success' if the record was successfully deleted.
     * @throws dml_exception | invalid_parameter_exception  If an error occurs while deleting the record.
     */
    public static function delete_section_data(int $courseid, int $sectionid, int $userid): string
    {
        global $DB;

        $params = self::validate_parameters(self::delete_section_data_parameters(), [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'userid' => $userid
        ]);

        $DB->delete_records('local_learningplan', [
            'course' => $params['courseid'],
            'section' => $params['sectionid'],
            'user' => $params['userid']
        ]);

        return 'deleted';
    }

    /**
     * Defines the return type for the delete_section_data web service function.
     *
     * @return external_value A text string representing the existence status.
     */
    public static function delete_section_data_returns(): external_value
    {
        return new external_value(PARAM_TEXT, 'Status message');
    }

    /**
     * Defines which parameters the web service function update_deadline expects and specifies the data types of
     * the parameters. Also ensures automatic validation of the parameters before the actual web service function is
     * called.
     *
     * @return external_function_parameters
     */
    public static function update_deadline_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course-ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section-ID'),
            'userid' => new external_value(PARAM_INT, 'User-ID'),
            'deadline' => new external_value(PARAM_RAW, 'New date in YYYY-MM-DD format')
        ]);
    }

    /**
     * Updates the processing deadline for a specific user's course section.
     *
     * This function checks if a record exists for the given course, section, and user in the
     * 'local_learningplan' table. If the record exists, it updates the processing deadline
     * for that user in the learning plan. The deadline is provided as a date in 'YYYY-MM-DD' format,
     * and is converted to a Unix timestamp before being stored.
     *
     * @param int $courseid The ID of the course.
     * @param int $sectionid The ID of the section.
     * @param int $userid The ID of the user.
     * @param string $deadline The deadline to set, in 'YYYY-MM-DD' format.
     *
     * @return string A status message: 'success' if the deadline was updated, or an error message if something went wrong.
     * @throws moodle_exception If the record is not found.
     * @throws dml_exception If there is a database error.
     * @throws invalid_parameter_exception If invalid parameters are passed.
     */
    public static function update_deadline(int $courseid, int $sectionid, int $userid, string $deadline): string
    {
        global $DB;

        $params = self::validate_parameters(self::update_deadline_parameters(), [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'userid' => $userid,
            'deadline' => $deadline
        ]);

        // Ensure the record exists
        $record = $DB->get_record('local_learningplan', [
            'course' => $params['courseid'],
            'section' => $params['sectionid'],
            'user' => $params['userid']
        ]);

        if (!$record) {
            throw new moodle_exception('Entry not found');
        }

        // Convert the deadline (YYYY-MM-DD) to a Unix timestamp
        $record->processing_deadline = strtotime($params['deadline']);

        $DB->update_record('local_learningplan', $record);

        return 'success';
    }

    /**
     * Defines the return type for the update_deadline web service function.
     *
     * @return external_value A text string representing the existence status.
     */
    public static function update_deadline_returns(): external_value
    {
        return new external_value(PARAM_BOOL, 'Status message');
    }

    /**
     * Defines which parameters the web service function update_progress expects and specifies the data types of
     * the parameters. Also ensures automatic validation of the parameters before the actual web service function is
     * called.
     *
     * @return external_function_parameters
     */
    public static function update_progress_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Die Kurs-ID'),
            'sectionid' => new external_value(PARAM_INT, 'Die Abschnitts-ID'),
            'userid' => new external_value(PARAM_INT, 'Die Benutzer-ID'),
            'progress' => new external_value(PARAM_ALPHANUMEXT, 'Der neue Fortschritt (offen, in_bearbeitung, abgeschlossen)')
        ]);
    }

    /**
     * Updates the progress state of a specific course section for a user in the learning plan.
     *
     * This function updates the progress status (`state`) of a record in the 'local_learningplan' table
     * for a specific user, course, and section. If no matching record is found, an exception is thrown.
     *
     * @param int $courseid The ID of the course.
     * @param int $sectionid The ID of the course section.
     * @param int $userid The ID of the user whose progress is being updated.
     * @param string $progress The new progress state.
     *
     * @return string A status message: 'success' if the deadline was updated, or an error message if something went wrong.
     * @throws moodle_exception If no matching record is found in the database.
     */
    public static function update_progress(int $courseid, int $sectionid, int $userid, string $progress): string
    {
        global $DB;

        $params = self::validate_parameters(self::update_progress_parameters(), [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'userid' => $userid,
            'progress' => $progress
        ]);

        $record = $DB->get_record('local_learningplan', [
            'course' => $params['courseid'],
            'section' => $params['sectionid'],
            'user' => $params['userid']
        ]);

        if (!$record) {
            throw new moodle_exception('Entry not found');
        }

        $record->state = $params['progress'];
        $DB->update_record('local_learningplan', $record);

        return 'success';
    }

    /**
     * Defines the return type for the update_progress web service function.
     *
     * @return external_value A text string representing the existence status.
     */
    public static function update_progress_returns(): external_value
    {
        return new external_value(PARAM_BOOL, 'Status message');
    }

    /**
     * Defines which parameters the web service function toggle_section_option expects and specifies the data types of
     * the parameters. Also ensures automatic validation of the parameters before the actual web service function is
     * called.
     *
     * @return external_function_parameters
     */
    public static function toggle_section_option_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'sectionid' => new external_value(PARAM_INT, 'ID des Abschnitts'),
            'courseid' => new external_value(PARAM_INT, 'ID des Kurses')
        ]);
    }

    /**
     * Toggles the option to allow or disallow the learning plan for a specific course section.
     *
     * This function checks if an entry exists for a given course and section in the
     * `local_learningplan_options` table. If an entry exists, it toggles the
     * `allow_learningplan` value between 1 (enabled) and 0 (disabled). If no entry exists,
     * it creates a new record with a default value of 0 (disabled).
     *
     * @param int $sectionid The ID of the course section.
     * @param int $courseid The ID of the course.
     *
     * @return int The new value of the `allow_learningplan` field (either 1 or 0).
     * @throws dml_exception If there is a database error.
     * @throws invalid_parameter_exception If invalid parameters are passed.
     */
    public static function toggle_section_option(int $sectionid, int $courseid): int
    {
        global $DB;

        self::validate_parameters(self::toggle_section_option_parameters(), [
            'sectionid' => $sectionid,
            'courseid' => $courseid
        ]);

        $record = $DB->get_record('local_learningplan_options', [
            'section' => $sectionid,
            'course' => $courseid]);

        if ($record) {
            $newvalue = $record->allow_learningplan == 1 ? 0 : 1;
            $DB->update_record('local_learningplan_options', [
                'id' => $record->id,
                'allow_learningplan' => $newvalue
            ]);
        } else {
            // Default value: 0
            $newvalue = 0;
            $DB->insert_record('local_learningplan_options', [
                'section' => $sectionid,
                'course' => $courseid,
                'allow_learningplan' => $newvalue
            ]);
        }
        return $newvalue;
    }

    /**
     * Defines the return type for the toggle_section_option web service function.
     *
     * @return external_value A text string representing the existence status.
     */
    public static function toggle_section_option_returns(): external_value
    {
        return new external_value(PARAM_INT, 'Neuer Wert (0 oder 1)');
    }

    /**
     * Defines which parameters the web service function get_section_option expects and specifies the data types of
     * the parameters. Also ensures automatic validation of the parameters before the actual web service function is
     * called.
     *
     * @return external_function_parameters
     */
    public static function get_section_option_parameters(): external_function_parameters
    {
        return self::toggle_section_option_parameters();
    }

    /**
     * Retrieves the option to allow or disallow the learning plan for a specific course section.
     *
     * This function checks if an entry exists for a given course and section in the
     * `local_learningplan_options` table. If an entry exists, it returns the value
     * of the `allow_learningplan` field. If no entry exists, it returns the default value of 1.
     *
     * @param int $sectionid The ID of the course section.
     * @param int $courseid The ID of the course.
     *
     * @return int The value of the `allow_learningplan` field (either 1 or 0). If no entry exists, returns 1 (default value).
     * @throws dml_exception If there is a database error.
     * @throws invalid_parameter_exception If invalid parameters are passed.
     */
    public static function get_section_option(int $sectionid, int $courseid): int
    {
        global $DB;

        self::validate_parameters(self::toggle_section_option_parameters(), [
            'sectionid' => $sectionid,
            'courseid' => $courseid
        ]);

        $record = $DB->get_record('local_learningplan_options', ['section' => $sectionid, 'course' => $courseid]);

        return $record ? $record->allow_learningplan : 1; // Default value 1, if not presend
    }

    /**
     * Defines the return type for the get_section_option web service function.
     *
     * @return external_value A text string representing the existence status.
     */
    public static function get_section_option_returns(): external_value
    {
        return new external_value(PARAM_INT, 'Value 0 or 1');
    }

    /**
     * Defines which parameters the web service function delete_section_data_for_all expects and specifies the data types of
     * the parameters. Also ensures automatic validation of the parameters before the actual web service function is
     * called.
     *
     * @return external_function_parameters
     */
    public static function delete_section_data_for_all_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section ID'),
        ]);
    }

    /**
     * Deletes all learning plan data for a specific course section across all users.
     *
     * This function deletes all records from the `local_learningplan` table for the specified course
     * and section. It removes any existing entries related to the given section in the given course
     * for all users.
     *
     * @param int $courseid The ID of the course.
     * @param int $sectionid The ID of the course section.
     *
     * @return string A status message indicating that the deletion has occurred ('deleted').
     * @throws dml_exception|invalid_parameter_exception If an error occurs during the deletion process.
     */
    public static function delete_section_data_for_all(int $courseid, int $sectionid): string
    {
        global $DB;

        self::validate_parameters(self::delete_section_data_for_all_parameters(), [
            'sectionid' => $sectionid,
            'courseid' => $courseid
        ]);

        $DB->delete_records('local_learningplan', [
            'course' => $courseid,
            'section' => $sectionid,
        ]);

        return 'deleted';
    }

    /**
     * Defines the return type for the delete_section_data_for_all web service function.
     *
     * @return external_value A text string representing the existence status.
     */
    public static function delete_section_data_for_all_returns(): external_value
    {
        return new external_value(PARAM_TEXT, 'Status message');
    }
}