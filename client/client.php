<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once('./curl.php');
        global $CFG, $DB;

	$item = new stdClass();
	$item->fullname = 'uXYOu86etsbYkeUp8XTT';
	$item->shortname = '1000004';
	$item->idnumber_course = '32';
	$item->idnumber_category = 'cat3';

	$data = array($item);


	$curl = new curl;

    $token = "46d591f0365ae9eef0b77cff1c3ef4a3";
    $domainname = $CFG->wwwroot;

    $functionname = 'local_wscibertec_create_course';

    $serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token.'&wsfunction='.$functionname;
    //$data = array('optional'=>'1');
    $post = array('courses' => $data);
    $format = 'json';
    $format = ($format == 'json')?'&moodlewsrestformat=' . $format:'';
    $resp = $curl->post($serverurl.$format, $post);

    echo "<pre>";
    print_r(json_decode($resp));
    echo "</pre>";
