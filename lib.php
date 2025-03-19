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
 * @package     local_greetings
 * @copyright   2024 Ivonne Kneißig <kneissig.ivonne@fh-swf.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Insert a link to index.php on the site front page navigation menu.
 *
 * @param navigation_node $frontpage Node representing the front page in the navigation tree.
 */
function local_learningplan_extend_navigation_frontpage(navigation_node $frontpage) {
    if (isloggedin() && !isguestuser()) {
        $frontpage->add(
            get_string('pluginname', 'local_learningplan'),
            new moodle_url('/local/learningplan/index.php'),
            navigation_node::TYPE_CUSTOM,
        );
    }
}

function local_learningplan_extend_navigation(global_navigation $navigation) {
    global $PAGE;
    global $USER;

    if (isloggedin() && !isguestuser()) {
        $PAGE->requires->js_call_amd('local_learningplan/navigation', 'init', [
            'linkUrl'   => (new moodle_url('/moodle/local/learningplan/index.php'))->out_omit_querystring(),
            'linkLabel' => get_string('pluginname', 'local_learningplan')
        ]);
    }

    $PAGE->requires->js_call_amd('local_learningplan/button', 'init', ['userid' => $USER->id]);
    $PAGE->requires->js_call_amd('local_learningplan/sectionmenu', 'init');
}








