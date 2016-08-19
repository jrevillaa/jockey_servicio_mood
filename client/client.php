<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once('./curl.php');
        global $CFG, $DB;

	//id = 58

	//$datap = array('user' => array('userid' => 58));

    $us->id = 76;
    $us->username = $username;
    $us->password = $password;
    $us->firstname = isset($datos->NOMBRE)? $datos->NOMBRE : 'NULL';
    $us->lastname = isset($datos->APELLIDO_PATERNO)? $datos->APELLIDO_PATERNO : 'NULL';
    $us->email = $username.'@test.test';
    $us->auth = 'drupalservices';


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
/*

    $cats = $DB->get_records('course_categories',array('parent' => 0));

    $cats_hijos = $cats

    foreach($cats as $value){

        $DB->get_records('courses',  array('categoryid' => $value->id));
        echo "<pre>";
        print_r($value);
        echo "</pre>";

        $catis = $DB->get_records('course_categories',  array('parent' => $value->parent));


    }*/

/*
    $courses = enrol_get_users_courses(234);

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

        $cateogires[$root_category->name][] = $course;

    $output = array();
    foreach ($cateogires as $key => $value) {
        $tmpp = new stdClass();
        $tmpp->categoria = $key;
        $tmpp->cursos = $value;
        $output[] = $tmpp;
    }
        
        echo "<pre>";
        print_r($output);
        echo "</pre>";*/