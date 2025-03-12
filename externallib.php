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



    public static function update_progress($courseid, $sectionid, $userid, $progress) {
        global $DB;

        // Parameter validieren
        $params = self::validate_parameters(self::update_progress_parameters(), [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'userid' => $userid,
            'progress' => $progress
        ]);

        // Prüfen, ob der Eintrag existiert
        $record = $DB->get_record('local_learningplan', [
            'course' => $params['courseid'],
            'section' => $params['sectionid'],
            'user' => $params['userid']
        ]);

        if (!$record) {
            throw new moodle_exception('Eintrag nicht gefunden');
        }

        // Fortschritt aktualisieren
        $record->state = $params['progress'];
        $DB->update_record('local_learningplan', $record);

        return true;
    }

    public static function update_progress_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Die Kurs-ID'),
            'sectionid' => new external_value(PARAM_INT, 'Die Abschnitts-ID'),
            'userid' => new external_value(PARAM_INT, 'Die Benutzer-ID'),
            'progress' => new external_value(PARAM_ALPHANUMEXT, 'Der neue Fortschritt (offen, in_bearbeitung, abgeschlossen)')
        ]);
    }

    public static function update_progress_returns() {
        return new external_value(PARAM_BOOL, 'Gibt zurück, ob das Speichern erfolgreich war');
    }






    // Menüpunkt für sections


    public static function toggle_section_option($sectionid, $courseid) {
        global $DB;

        self::validate_parameters(self::toggle_section_option_parameters(), [
            'sectionid' => $sectionid,
            'courseid' => $courseid
        ]);

        $record = $DB->get_record('local_learningplan_options', ['section' => $sectionid, 'course' => $courseid]);

        if ($record) {
            // Toggle zwischen 0 und 1
            $newvalue = $record->allow_learningplan == 1 ? 0 : 1;
            $DB->update_record('local_learningplan_options', [
                'id' => $record->id,
                'allow_learningplan' => $newvalue
            ]);
        } else {
            // Standardwert: 1 (hinzufügbar)
            $newvalue = 1;
            $DB->insert_record('local_learningplan_options', [
                'section' => $sectionid,
                'course' => $courseid,
                'allow_learningplan' => $newvalue
            ]);
        }

        return $newvalue;
    }

    public static function toggle_section_option_parameters() {
        return new external_function_parameters([
            'sectionid' => new external_value(PARAM_INT, 'ID des Abschnitts'),
            'courseid' => new external_value(PARAM_INT, 'ID des Kurses')
        ]);
    }

    public static function toggle_section_option_returns() {
        return new external_value(PARAM_INT, 'Neuer Wert (0 oder 1)');
    }

    public static function get_section_option($sectionid, $courseid) {
        global $DB;

        self::validate_parameters(self::toggle_section_option_parameters(), [
            'sectionid' => $sectionid,
            'courseid' => $courseid
        ]);

        $record = $DB->get_record('local_learningplan_options', ['section' => $sectionid, 'course' => $courseid]);
        return $record ? $record->allow_learningplan : 1; // Standardwert 1, falls nicht vorhanden
    }

    public static function get_section_option_parameters() {
        return self::toggle_section_option_parameters();
    }

    public static function get_section_option_returns() {
        return new external_value(PARAM_INT, 'Wert (0 oder 1)');
    }
}
