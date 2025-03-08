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

class index_page implements renderable, templatable {
    public function export_for_template(renderer_base $output) {
        global $DB, $USER;

        try {
            $sections = $DB->get_records('local_learningplan', ['user' => $USER->id]);

            $data = [];
            foreach ($sections as $section) {
                $course = $DB->get_record('course', ['id' => $section->course]);
                $sectionname = "Abschnitt {$section->section}"; // Oder spezifischer Name aus DB holen

                $data[] = [
                    'coursename' => $course->fullname,
                    'courseid' => $section->course,
                    'sectionid' => $section->section,
                    'sectionname' => $sectionname,
                    'addeddate' => date('d.m.Y', $section->timecreated),
                    'processing_deadline' => date('d.m.Y', $section->processing_deadline), // Falls als Timestamp gespeichert
                    'progress' => $section->state,
                ];
            }

            return ['sections' => $data];
        } catch (dml_exception $e) {
            return ['sections' => []];
        }
    }
}

