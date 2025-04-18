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

namespace local_learningplan\output;

use moodle_exception;
use renderable;
use templatable;
use renderer_base;
use dml_exception;
use moodle_url;

/**
 * This class serves as a data source for the index page of the learningplan plugin.
 *
 * @package     local_learningplan
 * @copyright   2025 Ivonne Moritz <moritz.ivonne@fh-swf.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index_page implements renderable, templatable {
    /**
     * Prepares the data for display in a Mustache template.
     *
     * @param renderer_base $output
     * @return array[]
     */
    public function export_for_template(renderer_base $output): array {
        global $DB, $USER;

        try {
            $sections = $DB->get_records('local_learningplan', ['user' => $USER->id]);

            $data = [];

            $progressopen = get_string('progress_open', 'local_learningplan');
            $progressinprogress = get_string('progress_in_progress', 'local_learningplan');
            $progressfinished = get_string('progress_finished', 'local_learningplan');

            foreach ($sections as $section) {
                $course = $DB->get_record('course', ['id' => $section->course]);
                $coursesection = $DB->get_record('course_sections',
                    ['section' => $section->section, 'course' => $section->course]);

                $sectionname = !empty($coursesection->name) ? $coursesection->name : get_section_name($coursesection->course,
                    $coursesection);

                $courseurl = new moodle_url('/course/view.php', ['id' => $section->course]);

                $sectionurl = new moodle_url('/course/view.php', [
                    'id' => $section->course,
                    'section' => $section->section,
                ]);

                $progressopenselected = ($section->state == 'open') ? 'selected' : '';
                $progressinprogressselected = ($section->state == 'in_progress') ? 'selected' : '';
                $progressfinishedselected = ($section->state == 'finished') ? 'selected' : '';

                $data[] = [
                    'userid' => $USER->id,
                    'coursename' => $course->fullname,
                    'courseid' => $section->course,
                    'courseurl' => $courseurl->out(false),
                    'sectionid' => $section->section,
                    'sectionname' => $sectionname,
                    'sectionurl' => $sectionurl->out(false),
                    'addeddate' => date('d.m.Y', $section->timecreated),
                    'processing_deadline' => !empty($section->processing_deadline) ?
                        date('Y-m-d', $section->processing_deadline) : '',
                    'progress' => $section->state,
                    'progress_open_selected' => $progressopenselected,
                    'progress_in_progress_selected' => $progressinprogressselected,
                    'progress_finished_selected' => $progressfinishedselected,
                ];
            }
            return [
                'sections' => $data,
                'progress_open' => $progressopen,
                'progress_in_progress' => $progressinprogress,
                'progress_finished' => $progressfinished,
                'searchfield' => get_string('searchfield', 'local_learningplan'),
                'show_all' => get_string('show_all', 'local_learningplan'),
                'table_course' => get_string('table_course', 'local_learningplan'),
                'table_section' => get_string('table_section', 'local_learningplan'),
                'table_created_at' => get_string('table_created_at', 'local_learningplan'),
                'table_progress' => get_string('table_progress', 'local_learningplan'),
                'table_deadline' => get_string('table_deadline', 'local_learningplan'),
                'table_delete' => get_string('table_delete', 'local_learningplan'),
                'table_no_entry_part_one' => get_string('table_no_entry_part_one', 'local_learningplan'),
                'table_no_entry_part_two' => get_string('table_no_entry_part_two', 'local_learningplan'),
                'table_no_entry_part_three' => get_string('table_no_entry_part_three', 'local_learningplan'),

            ];

        } catch (dml_exception | moodle_exception $e) {
            debugging($e);
            return ['sections' => []];
        }
    }
}

