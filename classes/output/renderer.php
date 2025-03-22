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

namespace local_learningplan\output;

use moodle_exception;
use plugin_renderer_base;

/**
 * Renderer for learningplan plugin.
 *
 * @package     local_learningplan
 * @copyright   2025 Ivonne Moritz <moritz.ivonne@fh-swf.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Retrieves the data for the display and uses the Mustache template to generate an HTML page from it.
     * Returns the rendered HTML string.
     *
     * @throws moodle_exception
     */
    public function render_index_page($page): string {

        $data = $page->export_for_template($this);
        return parent::render_from_template('local_learningplan/index', $data);
    }
}
