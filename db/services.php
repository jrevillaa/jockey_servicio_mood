<?php

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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwsjockey
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// We defined the web service functions to install.
$functions = array(
    'local_wsjockey_get_users_by_id' => array(
        'classname' => 'local_wsjockey_external',
        'methodname' => 'get_users_by_id',
        'classpath' => 'local/wsjockey/externallib.php',        
        'description' => 'Get users by id.',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),
    'local_wsjockey_create_users' => array(
        'classname'   => 'local_wsjockey_external',
        'methodname'  => 'create_users',
        'classpath'   => 'local/wsjockey/externallib.php',
        'description' => 'Creates new users.',
        'type'        => 'write',
        'capabilities' => 'moodle/user:create',
    ), 
    'local_wsjockey_update_users' => array(
        'classname'   => 'local_wsjockey_external',
        'methodname'  => 'update_users',
        'classpath'   => 'local/wsjockey/externallib.php',
        'description' => 'Update Users users.',
        'type'        => 'write',
        'capabilities' => 'moodle/user:update',
    ), 
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'Jockey Web Services' => array(
        'functions' => array(
            'local_wsjockey_get_users_by_id'),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);
