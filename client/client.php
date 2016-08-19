<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once('./curl.php');
        global $CFG, $DB, $OUTPUT;
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

    $userids = array('username' => 'Estudiante');

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

        //if(is_object($image)){
            //$uri = "$CFG->wwwroot/pluginfile.php" . '/' . $image->contextid . '/' . $image->component . '/' . $image->filearea . $image->filepath . $image->filename;
            /*$uri = file_encode_url("$CFG->wwwroot/pluginfile.php",
                '/'. $image->contextid. '/'. $image->component. '/'.
                $image->filearea. $image->filepath. $image->filename, false);*/
        //}else{
            $uri = $OUTPUT->pix_url('default-course', 'theme');
        //}
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
