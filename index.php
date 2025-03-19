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
 * @package     local_learningplan
 * @copyright   2024 Ivonne Kneißig <kneissig.ivonne@fh-swf.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->dirroot . '/local/learningplan/lib.php');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/learningplan/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_learningplan'));
$PAGE->set_heading(get_string('pluginname', 'local_learningplan'));

require_login();
if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$PAGE->requires->js_call_amd('local_learningplan/learningplan', 'init');
$PAGE->requires->css(new moodle_url('/local/learningplan/styles/learningplan.css'));


$output = $PAGE->get_renderer('local_learningplan');
echo $output->header();
$renderable = new local_learningplan\output\index_page();
echo $output->render($renderable);
echo $output->footer();