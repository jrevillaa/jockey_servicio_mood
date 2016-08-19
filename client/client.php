<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once('./curl.php');
        global $CFG, $DB;
require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); 

	//id = 58

/*
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
    echo "</pre>";*/

