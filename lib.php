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
 * Central file that contains basic and reusable functions. Bundles functionalities such as database operations,
 * interaction with other Moodle components, caching and general help functions.
 *
 * @package     local_greetings
 * @copyright   2025 Ivonne Moritz <moritz.ivonne@fh-swf.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Adds a link to the Learning Plan plugin in the frontpage second navigation menu, visible only to logged-in users who
 * are not guests.
 *
 * This function is used to add a custom navigation link to the learningplan plugin on the Moodle site's frontpage.
 * The link will only be displayed if the user is logged in and is not a guest user.
 *
 * @param navigation_node $frontpage The node representing the frontpage in the navigation tree.
 * @throws coding_exception If there is an error adding the link to the navigation.
 */
function local_learningplan_extend_navigation_frontpage(navigation_node $frontpage): void
{
    if (isloggedin() && !isguestuser()) {
        $frontpage->add(
            get_string('pluginname', 'local_learningplan'),
            new moodle_url('/local/learningplan/index.php'),
            navigation_node::TYPE_CUSTOM,
        );
    }
}

/**
 * Extends the global navigation with additional JavaScript functionality for the Learning Plan plugin.
 *
 * This function is used to include JavaScript functionality for the learningplan plugin in the global navigation.
 * It checks if the user is logged in and not a guest, and then initializes JavaScript modules for navigation, buttons,
 * and section menus, passing relevant data (such as the link URL and user ID) to these scripts.
 *
 * @param global_navigation $navigation The global navigation object.
 * @throws coding_exception
 */
function local_learningplan_extend_navigation(global_navigation $navigation): void
{
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








