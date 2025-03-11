<?php
namespace local_learningplan\external;

global $CFG;

use external_api;
use external_function_parameters;
use external_value;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/externallib.php');

class learningplan_service extends external_api {

    public static function check_section_data_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section ID'),
            'userid' => new external_value(PARAM_INT, 'User ID')
        ]);
    }

    public static function check_section_data($courseid, $sectionid, $userid) {
        global $DB;

        $exists = $DB->record_exists('local_learningplan', array('course' => $courseid, 'section' => $sectionid, 'user' => $userid));

        return $exists;
    }

    public static function check_section_data_returns()
    {
        return new external_value(PARAM_TEXT, 'Existenzstatus');
    }

    public static function save_section_data_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section ID'),
            'userid' => new external_value(PARAM_INT, 'User ID')
        ]);
    }

    public static function save_section_data($courseid, $sectionid, $userid) {
        global $DB;

        // Daten in die Tabelle einfügen
        $record = new stdClass();
        $record->course = $courseid;
        $record->section = $sectionid;
        $record->user = $userid;
        $record->timecreated = time();

        $DB->insert_record('local_learningplan', $record);

        //return ['status' => 'success'];
        return 'success';
    }

    public static function save_section_data_returns() {
        return new external_value(PARAM_TEXT, 'Status message');
    }

    // Parameter für das Löschen von Daten
    public static function delete_section_data_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section ID'),
            'userid' => new external_value(PARAM_INT, 'User ID')
        ]);
    }

    // Funktion zum Löschen der Daten
    public static function delete_section_data($courseid, $sectionid, $userid) {
        global $DB;

        // Datensatz in der Tabelle löschen
        $DB->delete_records('local_learningplan', [
            'course' => $courseid,
            'section' => $sectionid,
            'user' => $userid
        ]);

        return 'deleted';
    }

    // Rückgabetyp für das Löschen
    public static function delete_section_data_returns() {
        return new external_value(PARAM_TEXT, 'Status message');
    }

    // Hier Datepicker
    public static function update_deadline($courseid, $sectionid, $userid, $deadline) {
        global $DB;

        // Überprüfe die Parameter
        $params = self::validate_parameters(self::update_deadline_parameters(), [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'userid' => $userid,
            'deadline' => $deadline
        ]);

        // Stelle sicher, dass der Datensatz existiert
        $record = $DB->get_record('local_learningplan', [
            'course' => $params['courseid'],
            'section' => $params['sectionid'],
            'user' => $params['userid']
        ]);

        if (!$record) {
            throw new moodle_exception('Eintrag nicht gefunden');
        }

        // Konvertiere das Datum (YYYY-MM-DD) in Unix-Timestamp
        $record->processing_deadline = strtotime($params['deadline']);

        // Aktualisiere den Datensatz
        $DB->update_record('local_learningplan', $record);

        return true;
    }

    public static function update_deadline_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Die Kurs-ID'),
            'sectionid' => new external_value(PARAM_INT, 'Die Abschnitts-ID'),
            'userid' => new external_value(PARAM_INT, 'Die Benutzer-ID'),
            'deadline' => new external_value(PARAM_RAW, 'Das neue Datum im Format YYYY-MM-DD')
        ]);
    }

    public static function update_deadline_returns() {
        return new external_value(PARAM_BOOL, 'Gibt zurück, ob das Speichern erfolgreich war');
    }
}
