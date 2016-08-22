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
 * External Web Service Template
 *
 * @package    localwsjockey
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class local_wsjockey_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function get_users_by_id_parameters() {
        return new external_function_parameters(
                array(
            'user' => new external_single_structure(
                        array(
                            'username' => new external_value(PARAM_TEXT, 'array de los ids categorias'),
                              )
                        )
                    )
                );
    }

    /**
     * Get user information
     * - This function is matching the permissions of /user/profil.php
     * - It is also matching some permissions from /user/editadvanced.php for the following fields:
     *   auth, confirmed, idnumber, lang, theme, timezone, mailformat
     *
     * @param array $userids  array of user ids
     * @return array An array of arrays describing users
     * @since Moodle 2.2
     */
    public static function get_users_by_id($userids) {
        global $CFG, $USER, $DB, $OUTPUT;
        require_once($CFG->dirroot . "/user/lib.php");

      
    

        //iteramos los parametros y reemplazamos por los ID
  
        $params = self::validate_parameters(self::get_users_by_id_parameters(), array('user' => $userids));

        $usertmp = $DB->get_record('user',  array('username' => $userids['username']));
        if(!is_object($usertmp)){
            throw new moodle_exception('Error, no existe el username', 'wsjockey');
        }
        $courses = enrol_get_users_courses($usertmp->id);

    $cateogires = array();
    foreach($courses as $course){
        $category = $DB->get_record('course_categories',array('id'=>$course->category));
        $path = explode('/',$category->path);
        $root_category_id = $path[1];
        $root_category = $DB->get_record('course_categories',array('id'=>$root_category_id));
        unset($course->sortorder);
        unset($course->startdate);
        unset($course->defaultgroupingid);
        unset($course->groupmode);
        unset($course->groupmodeforce);
        unset($course->ctxid);
        unset($course->ctxpath);
        unset($course->ctxdepth);
        unset($course->ctxlevel);
        unset($course->ctxinstance);
        $course->path = $CFG->wwwroot . '/course/view.php?id=' . $course->id;

        $sql = " SELECT ".
            " f.contextid,".
            " f.component,f.filearea, f.filepath,".
            " f.filename, f.mimetype, c.instanceid".
            " FROM {files} f ".
            " INNER JOIN {context} c on f.contextid = c.id ".
            " where f.mimetype LIKE 'image%' ".
            " and f.component='course'".
            " AND f.filearea = 'overviewfiles'".
            " AND c.instanceid = " . $course->id;

        $image = $DB->get_record_sql($sql);

        if(is_object($image)){
            $uri = "$CFG->wwwroot/pluginfile.php" .
                                    '/'. $image->contextid . '/' . $image->component . 
                                    '/'. $image->filearea . $image->filepath . $image->filename;
       }else{
            $uri = '';
        }
        $course->imagen = $uri;

        $cateogires[$root_category->name][] = $course;

    $output = array();
    }
    foreach ($cateogires as $key => $value) {
        $tmpp = new stdClass();
        $tmpp->categoria = $key;
        $tmpp->cursos = $value;
        $output[] = $tmpp;
    }

    /*echo "<pre>";
    print_r($output);
    echo "</pre>";*/

        return $output;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function get_users_by_id_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'categoria'       => new external_value(PARAM_TEXT, 'cateogria'),
                    'cursos' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'id'  => new external_value(PARAM_INT, 'The name of the preference'),
                                        'category' => new external_value(PARAM_INT, 'The value of the preference'),
                                        'shortname' => new external_value(PARAM_TEXT, 'The value of the preference'),
                                        'fullname' => new external_value(PARAM_TEXT, 'The value of the preference'),
                                        'idnumber' => new external_value(PARAM_TEXT, 'The value of the preference'),
                                        'visible' => new external_value(PARAM_INT, 'The value of the preference'),
                                        'path' => new external_value(PARAM_TEXT, 'The value of the preference'),
                                        'imagen' => new external_value(PARAM_TEXT, 'The value of the preference'),
                                    )
                                ), 'lista de cursos', VALUE_OPTIONAL),
                )
            )
        );
    }


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function create_users_parameters() {
        global $CFG;

        return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'username' =>
                                new external_value(PARAM_USERNAME, 'Username policy is defined in Moodle security config.'),
                            'password' =>
                                new external_value(PARAM_RAW, 'Plain text password consisting of any characters'),
                            'firstname' =>
                                new external_value(PARAM_NOTAGS, 'The first name(s) of the user'),
                            'lastname' =>
                                new external_value(PARAM_NOTAGS, 'The family name of the user'),
                            'email' =>
                                new external_value(PARAM_EMAIL, 'A valid and unique email address'),
                            'auth' =>
                                new external_value(PARAM_PLUGIN, 'Auth plugins include manual, ldap, imap, etc', VALUE_DEFAULT,
                                    'manual', NULL_NOT_ALLOWED),
                            'idnumber' =>
                                new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution',
                                    VALUE_DEFAULT, ''),
                            'lang' =>
                                new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_DEFAULT,
                                    $CFG->lang, NULL_NOT_ALLOWED),
                            'calendartype' =>
                                new external_value(PARAM_PLUGIN, 'Calendar type such as "gregorian", must exist on server',
                                    VALUE_DEFAULT, $CFG->calendartype, VALUE_OPTIONAL),
                            'theme' =>
                                new external_value(PARAM_PLUGIN, 'Theme name such as "standard", must exist on server',
                                    VALUE_OPTIONAL),
                            'timezone' =>
                                new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default',
                                    VALUE_OPTIONAL),
                            'mailformat' =>
                                new external_value(PARAM_INT, 'Mail format code is 0 for plain text, 1 for HTML etc',
                                    VALUE_OPTIONAL),
                            'description' =>
                                new external_value(PARAM_TEXT, 'User profile description, no HTML', VALUE_OPTIONAL),
                            'city' =>
                                new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                            'country' =>
                                new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                            'firstnamephonetic' =>
                                new external_value(PARAM_NOTAGS, 'The first name(s) phonetically of the user', VALUE_OPTIONAL),
                            'lastnamephonetic' =>
                                new external_value(PARAM_NOTAGS, 'The family name phonetically of the user', VALUE_OPTIONAL),
                            'middlename' =>
                                new external_value(PARAM_NOTAGS, 'The middle name of the user', VALUE_OPTIONAL),
                            'alternatename' =>
                                new external_value(PARAM_NOTAGS, 'The alternate name of the user', VALUE_OPTIONAL),
                            'preferences' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preference'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the preference')
                                    )
                                ), 'User preferences', VALUE_OPTIONAL),
                            'customfields' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                                    )
                                ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL)
                        )
                    )
                )
            )
        );
    }

    /**
     * Create one or more users.
     *
     * @throws invalid_parameter_exception
     * @param array $users An array of users to create.
     * @return array An array of arrays
     * @since Moodle 2.2
     */
    public static function create_users($users) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/lib/weblib.php");
        require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); // Required for customfields related function.

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/user:create', $context);

        // Do basic automatic PARAM checks on incoming data, using params description.
        // If any problems are found then exceptions are thrown with helpful error messages.
        $params = self::validate_parameters(self::create_users_parameters(), array('users' => $users));

        $availableauths  = core_component::get_plugin_list('auth');
        unset($availableauths['mnet']);       // These would need mnethostid too.
        unset($availableauths['webservice']); // We do not want new webservice users for now.

        $availablethemes = core_component::get_plugin_list('theme');
        $availablelangs  = get_string_manager()->get_list_of_translations();

        $transaction = $DB->start_delegated_transaction();

        $userids = array();
        foreach ($params['users'] as $user) {
            // Make sure that the username doesn't already exist.
                // Make sure auth is valid.
                if (empty($availableauths[$user['auth']])) {
                    throw new invalid_parameter_exception('Invalid authentication type: '.$user['auth']);
                }

                // Make sure lang is valid.
                if (empty($availablelangs[$user['lang']])) {
                    throw new invalid_parameter_exception('Invalid language code: '.$user['lang']);
                }

                // Make sure lang is valid.
                if (!empty($user['theme']) && empty($availablethemes[$user['theme']])) { // Theme is VALUE_OPTIONAL,
                                                                                         // so no default value
                                                                                         // We need to test if the client sent it
                                                                                         // => !empty($user['theme']).
                    throw new invalid_parameter_exception('Invalid theme: '.$user['theme']);
                }

                $user['confirmed'] = true;
                $user['mnethostid'] = $CFG->mnet_localhost_id;

                // Start of user info validation.
                // Make sure we validate current user info as handled by current GUI. See user/editadvanced_form.php func validation().
                if (!validate_email($user['email'])) {
                    throw new invalid_parameter_exception('Email address is invalid: '.$user['email']);
                } else if ($DB->record_exists('user', array('email' => $user['email'], 'mnethostid' => $user['mnethostid']))) {
                    throw new invalid_parameter_exception('Email address already exists: '.$user['email']);
                }
                // End of user info validation.

                // Create the user data now!
                $user['id'] = user_create_user($user, true, false);

                // Custom fields.
                if (!empty($user['customfields'])) {
                    foreach ($user['customfields'] as $customfield) {
                        // Profile_save_data() saves profile file it's expecting a user with the correct id,
                        // and custom field to be named profile_field_"shortname".
                        $user["profile_field_".$customfield['type']] = $customfield['value'];
                    }
                    profile_save_data((object) $user);
                }

                // Trigger event.
                \core\event\user_created::create_from_userid($user['id'])->trigger();

                // Preferences.
                if (!empty($user['preferences'])) {
                    foreach ($user['preferences'] as $preference) {
                        set_user_preference($preference['type'], $preference['value'], $user['id']);
                    }
                }
                //var_dump(array('id' => $user['id'], 'username' => $user['username']));
                $userids[] = array('id' => $user['id'], 'username' => $user['username']);
            

        }

        $transaction->allow_commit();

        return $userids;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function create_users_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'       => new external_value(PARAM_INT, 'user id'),
                    'username' => new external_value(PARAM_USERNAME, 'user name'),
                )
            )
        );
    }



    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function update_users_parameters() {
        return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' =>
                                new external_value(core_user::get_property_type('id'), 'ID of the user',VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'username' =>
                                new external_value(core_user::get_property_type('username'), 'Username policy is defined in Moodle security config.',
                                    VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'password' =>
                                new external_value(core_user::get_property_type('password'), 'Plain text password consisting of any characters', VALUE_OPTIONAL,
                                    '', NULL_NOT_ALLOWED),
                            'firstname' =>
                                new external_value(core_user::get_property_type('firstname'), 'The first name(s) of the user', VALUE_OPTIONAL, '',
                                    NULL_NOT_ALLOWED),
                            'lastname' =>
                                new external_value(core_user::get_property_type('lastname'), 'The family name of the user', VALUE_OPTIONAL),
                            'email' =>
                                new external_value(core_user::get_property_type('email'), 'A valid and unique email address', VALUE_OPTIONAL, '',
                                    NULL_NOT_ALLOWED),
                            'auth' =>
                                new external_value(core_user::get_property_type('auth'), 'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL, '',
                                    NULL_NOT_ALLOWED),
                            'idnumber' =>
                                new external_value(core_user::get_property_type('idnumber'), 'An arbitrary ID code number perhaps from the institution',
                                    VALUE_OPTIONAL),
                            'lang' =>
                                new external_value(core_user::get_property_type('lang'), 'Language code such as "en", must exist on server',
                                    VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'calendartype' =>
                                new external_value(core_user::get_property_type('calendartype'), 'Calendar type such as "gregorian", must exist on server',
                                    VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'theme' =>
                                new external_value(core_user::get_property_type('theme'), 'Theme name such as "standard", must exist on server',
                                    VALUE_OPTIONAL),
                            'timezone' =>
                                new external_value(core_user::get_property_type('timezone'), 'Timezone code such as Australia/Perth, or 99 for default',
                                    VALUE_OPTIONAL),
                            'mailformat' =>
                                new external_value(core_user::get_property_type('mailformat'), 'Mail format code is 0 for plain text, 1 for HTML etc',
                                    VALUE_OPTIONAL),
                            'description' =>
                                new external_value(core_user::get_property_type('description'), 'User profile description, no HTML', VALUE_OPTIONAL),
                            'city' =>
                                new external_value(core_user::get_property_type('city'), 'Home city of the user', VALUE_OPTIONAL),
                            'country' =>
                                new external_value(core_user::get_property_type('country'), 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                            'firstnamephonetic' =>
                                new external_value(core_user::get_property_type('firstnamephonetic'), 'The first name(s) phonetically of the user', VALUE_OPTIONAL),
                            'lastnamephonetic' =>
                                new external_value(core_user::get_property_type('lastnamephonetic'), 'The family name phonetically of the user', VALUE_OPTIONAL),
                            'middlename' =>
                                new external_value(core_user::get_property_type('middlename'), 'The middle name of the user', VALUE_OPTIONAL),
                            'alternatename' =>
                                new external_value(core_user::get_property_type('alternatename'), 'The alternate name of the user', VALUE_OPTIONAL),
                            'customfields' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                                    )
                                ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
                            'preferences' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preference'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the preference')
                                    )
                                ), 'User preferences', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    /**
     * Update users
     *
     * @param array $users
     * @return null
     * @since Moodle 2.2
     */
    public static function update_users($users) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); // Required for customfields related function.

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        require_capability('moodle/user:update', $context);
        self::validate_context($context);

        $params = self::validate_parameters(self::update_users_parameters(), array('users' => $users));

        $transaction = $DB->start_delegated_transaction();

        $userids = array();

        foreach ($params['users'] as $user) {

            $tmpUser = $DB->get_record('user',  array('username' => $user['username']));

            if(!is_object($tmpUser)){
                throw new invalid_parameter_exception('Usuario no existe: '.$user['username']);
            }

            $user = $tmpUser;

            user_update_user($user, true, false);
            // Update user custom fields.
            if (!empty($user['customfields'])) {

                foreach ($user['customfields'] as $customfield) {
                    // Profile_save_data() saves profile file it's expecting a user with the correct id,
                    // and custom field to be named profile_field_"shortname".
                    $user["profile_field_".$customfield['type']] = $customfield['value'];
                }
                profile_save_data((object) $user);
            }

            // Trigger event.
            \core\event\user_updated::create_from_userid($user['id'])->trigger();

            // Preferences.
            if (!empty($user['preferences'])) {
                foreach ($user['preferences'] as $preference) {
                    set_user_preference($preference['type'], $preference['value'], $user['id']);
                }
            }

            $userids[] = array('id' => $user['id'], 'username' => $user['username']);
        }

        $transaction->allow_commit();

        return $userids;
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 2.2
     */
    public static function update_users_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'       => new external_value(PARAM_INT, 'user id'),
                    'username' => new external_value(PARAM_USERNAME, 'user name'),
                )
            )
        );
    }

   

}
