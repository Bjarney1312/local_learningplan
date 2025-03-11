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
/**
 * Class index_page
 *
 * @package    local_greetings
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_learningplan\output;

use renderable;
use templatable;
use renderer_base;
use dml_exception;
use moodle_url;

class index_page implements renderable, templatable {
    public function export_for_template(renderer_base $output) {
        global $DB, $USER;

        try {
            $sections = $DB->get_records('local_learningplan', ['user' => $USER->id]);

            $data = [];
            foreach ($sections as $section) {
                $course = $DB->get_record('course', ['id' => $section->course]);
                $course_section = $DB->get_record('course_sections', ['section' => $section->section, 'course' => $section->course]);

                $sectionname = !empty($course_section->name) ? $course_section->name : get_section_name($course_section->course, $course_section);

                // URL zum Kurs
                $course_url = new moodle_url('/course/view.php', ['id' => $section->course]);

                // URL zum Abschnitt innerhalb des Kurses
                $section_url = new moodle_url('/course/view.php', [
                    'id' => $section->course,
                    'section' => $section->section
                ]);


                $progress_offen = ($section->state == 'offen') ? true : false;
                $progress_in_bearbeitung = ($section->state == 'in_bearbeitung') ? true : false;
                $progress_abgeschlossen = ($section->state == 'abgeschlossen') ? true : false;

                $data[] = [
                    'userid' => $USER->id,
                    'coursename' => $course->fullname,
                    'courseid' => $section->course,
                    'courseurl' => $course_url->out(false),
                    'sectionid' => $section->section,
                    'sectionname' => $sectionname,
                    'sectionurl' => $section_url->out(false),
                    'addeddate' => date('d.m.Y', $section->timecreated),
                    'processing_deadline' => !empty($section->processing_deadline) ? date('Y-m-d', $section->processing_deadline) : '',
                    'progress' => $section->state,  // Aktueller Bearbeitungsstand
                    'progress_offen' => $progress_offen,
                    'progress_in_bearbeitung' => $progress_in_bearbeitung,
                    'progress_abgeschlossen' => $progress_abgeschlossen
                ];

            }

            return ['sections' => $data];
        } catch (dml_exception $e) {
            return ['sections' => []];
        }
    }
}

