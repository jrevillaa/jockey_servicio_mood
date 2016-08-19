<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once('./curl.php');
        global $CFG, $DB;
require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); 

	//id = 58


    $us = new stdClass();
    $us->id = 73;
    $us->firstname = 'Jair Edson';
    $us->lastname = 'Revilla arroyo';
    $us->username = 'consultor';
    $us->password = '123456.C';
    $us->email = 'consultor@test.test';
    //$us->auth = 'manual';


	$curl = new curl;

    $token = "648734155c457b0607e298959363c7c9";
    $domainname = $CFG->wwwroot;

    $functionname = 'local_wsjockey_update_users';

    $serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token.'&wsfunction='.$functionname;
    $data[] = $us;
    $post = array('users' => $data);
    $format = 'json';
    $format = ($format == 'json')?'&moodlewsrestformat=' . $format:'';
    $resp = $curl->post($serverurl.$format, $post);

    echo "<pre>";
    print_r(json_decode($resp));
    echo "</pre>";





       /* $output = array();


        $user = array();
        $user['username'] = 'consultor';

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
            $output[] = array( 'id'=> $user['id'], 'username' => $user['username']);
        }

        //$transaction->allow_commit();

        return $output;*/