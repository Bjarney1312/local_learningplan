<?php
namespace local_learningplan\external;

global $CFG;

use external_api;
use external_function_parameters;
use external_value;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/externallib.php');

class save_section_data extends external_api {

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
}
