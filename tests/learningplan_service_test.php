<?php

declare(strict_types=1);

use local_learningplan\external\learningplan_service;

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->libdir . '/externallib.php');

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit test case for local_learningplan service functions.
 *
 * @runInSeparateProcess
 */
class learningplan_service_test extends advanced_testcase
{

    protected function setUp(): void
    {
        $this->resetAfterTest(true);
    }

    /**
     * Tests checking  section data.
     * 
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public function test_check_section_data()
    {
        $courseid = 1;
        $sectionid = 2;
        $userid = 3;

        global $DB;
        $DB->insert_record('local_learningplan', [
            'course' => $courseid,
            'section' => $sectionid,
            'user' => $userid,
        ]);

        $result = learningplan_service::check_section_data($courseid, $sectionid, $userid);
        $this->assertTrue($result);
    }

    /**
     * Tests saving section data into the database.
     *
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public function test_save_section_data(): void
    {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $sectionid = 1;

        $result = learningplan_service::save_section_data($course->id, $sectionid, $user->id);

        $record = $DB->get_record('local_learningplan', [
            'course' => $course->id,
            'section' => $sectionid,
            'user' => $user->id,
        ]);

        $this->assertEquals('success', $result);
        $this->assertNotFalse($record);
        $this->assertEquals('open', $record->state);
    }

    /**
     * Tests deleting section data from the database.
     *
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public function test_delete_section_data(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $sectionid = 1;

        learningplan_service::save_section_data($course->id, $sectionid, $user->id);
        $result = learningplan_service::delete_section_data($course->id, $sectionid, $user->id);

        $record = $DB->get_record('local_learningplan', [
            'course' => $course->id,
            'section' => $sectionid,
            'user' => $user->id,
        ]);

        $this->assertEquals('deleted', $result);
        $this->assertFalse($record);
    }

    /**
     * Tests updating the deadline for a section.
     *
     * @throws dml_exception
     * @throws moodle_exception
     * @throws invalid_parameter_exception
     */
    public function test_update_deadline(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $sectionid = 1;

        learningplan_service::save_section_data($course->id, $sectionid, $user->id);
        $deadline = '2025-12-31';
        $result = learningplan_service::update_deadline($course->id, $sectionid, $user->id, $deadline);

        $record = $DB->get_record('local_learningplan', [
            'course' => $course->id,
            'section' => $sectionid,
            'user' => $user->id,
        ]);

        $this->assertEquals('success', $result);
        $this->assertEquals(strtotime($deadline), $record->processing_deadline);
    }

    /**
     * Tests updating the progress state of a section.
     *
     * @throws moodle_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public function test_update_progress(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $sectionid = 1;

        learningplan_service::save_section_data($course->id, $sectionid, $user->id);
        $progress = 'completed';
        $result = learningplan_service::update_progress($course->id, $sectionid, $user->id, $progress);

        $record = $DB->get_record('local_learningplan', [
            'course' => $course->id,
            'section' => $sectionid,
            'user' => $user->id,
        ]);

        $this->assertEquals('success', $result);
        $this->assertEquals($progress, $record->state);
    }

    /**
     * Tests toggling the section option value.
     *
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public function test_toggle_section_option(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $sectionid = 1;

        $initial = learningplan_service::toggle_section_option($sectionid, $course->id);
        $newvalue = learningplan_service::toggle_section_option($sectionid, $course->id);

        $this->assertNotEquals($initial, $newvalue);
    }

    /**
     * Tests retrieving the section option value.
     *
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public function test_get_section_option(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $sectionid = 1;

        $default = learningplan_service::get_section_option($sectionid, $course->id);
        $this->assertEquals(1, $default);
    }

    /**
     * Tests deleting section data for all users.
     *
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public function test_delete_section_data_for_all(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $sectionid = 1;

        learningplan_service::save_section_data($course->id, $sectionid, $user1->id);
        learningplan_service::save_section_data($course->id, $sectionid, $user2->id);

        $result = learningplan_service::delete_section_data_for_all($course->id, $sectionid);
        $records = $DB->get_records('local_learningplan', [
            'course' => $course->id,
            'section' => $sectionid,
        ]);

        $this->assertEquals('deleted', $result);
        $this->assertEmpty($records);
    }
}
