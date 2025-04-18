# Lernplan #

A Moodle plugin for managing personalized learning plans for students. Course sections can be added per user, assigned deadlines, and marked with progress statuses. Includes filtering, searching, and sorting features for better overview.

🔧 Features:

    Add course sections to a personal learning plan

    Set progress status (open, in progress, completed)

    Assign individual deadlines

    Filter, search, and sort sections

📎 Compatible with Moodle 4.5
📁 Plugin type: local
📌 Designed for individual learning organization

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/learningplan

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2025 Ivonne Moritz <moritz.ivonne@fh-swf.de>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
