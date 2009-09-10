<?php
    
    // Array of all the authorized page to display and their name for a kind of user
    $validPages = array(
        'login' => 'include/login.php',
        'logout' => 'include/logout.php',
        'credits' => 'include/credits.php');
    
    if($_SESSION['type'] == STUDENT_TYPE)
    {
        $validPages = array_merge($validPages, array(
				    'student_home' => 'include/student/home.php',
				    'student_history' => 'include/student/history.php',
				    'student_export_ical' => 'include/student/export_ical.php',
				    'student_email_groups' => 'include/student/email_groups.php',
				    'student_email_students' => 'include/student/email_students.php',
				    'student_list_deliveries' => 'include/student/list_deliveries.php',
				    'student_display_delivery' => 'include/student/display_delivery.php',
				    'student_create_deliverygroup' => 'include/student/create_deliverygroup.php',
				    'student_display_deliverygroup' => 'include/student/display_deliverygroup.php'));
    }
    else if($_SESSION['type'] == TEACHER_TYPE)
	{			    
		$validPages = array_merge($validPages, array(		
				    'teacher_home' => 'include/teacher/home.php',
				    'teacher_history' => 'include/teacher/history.php',
				    'teacher_list_subjects' => 'include/teacher/list_subjects.php',
				    'teacher_create_subject_name' => 'include/teacher/create_subject_name.php',
				    'teacher_create_subject_teachers' => 'include/teacher/create_subject_teachers.php',
				    'teacher_create_subject_groups' => 'include/teacher/create_subject_groups.php',
				    'teacher_subject_settings_name' => 'include/teacher/subject_settings_name.php',
				    'teacher_subject_settings_teachers_add' => 'include/teacher/subject_settings_teachers_add.php',
				    'teacher_subject_settings_teachers_remove' => 'include/teacher/subject_settings_teachers_remove.php',
				    'teacher_list_works' => 'include/teacher/list_works.php',
				    'teacher_create_work_name_constraints' => 'include/teacher/create_work_name_constraints.php',
				    'teacher_create_work_files' => 'include/teacher/create_work_files.php',
				    'teacher_create_work_assocs' => 'include/teacher/create_work_assocs.php',
				    'teacher_work_settings' => 'include/teacher/work_settings.php',
				    'teacher_email_groups' => 'include/teacher/email_groups.php',
				    'teacher_email_students' => 'include/teacher/email_students.php',
				    'teacher_list_groups' => 'include/teacher/list_groups.php',
				    'teacher_load_group' => 'include/teacher/load_group.php',
				    'teacher_create_group_name' => 'include/teacher/create_group_name.php',
				    'teacher_create_group_students' => 'include/teacher/create_group_students.php',
				    'teacher_group_settings_name' => 'include/teacher/group_settings_name.php',
				    'teacher_group_settings_student' => 'include/teacher/group_settings_student.php',
				    'teacher_group_settings_students_add' => 'include/teacher/group_settings_students_add.php',
				    'teacher_group_settings_students_remove' => 'include/teacher/group_settings_students_remove.php',
				    'teacher_group_settings_students_edit' => 'include/teacher/group_settings_students_edit.php',
				    'teacher_group_settings_student_edit' => 'include/teacher/group_settings_student_edit.php',
				    'teacher_list_deliveries' => 'include/teacher/list_deliveries.php',
				    'teacher_delivery_settings' => 'include/teacher/delivery_settings.php',
				    'teacher_deliveries_settings' => 'include/teacher/deliveries_settings.php',
				    'teacher_display_board' => 'include/teacher/display_board.php',
				    'teacher_create_deliverygroup' => 'include/teacher/create_deliverygroup.php',
				    'teacher_list_groups_deliveries' => 'include/teacher/list_groups_deliveries.php',
				    'teacher_list_group_deliveries' => 'include/teacher/list_group_deliveries.php',
				    'teacher_download_work' => 'include/teacher/download_work.php',
				    'teacher_download_delivery' => 'include/teacher/download_delivery.php',
				    
				    'teacher_help_home' => 'include/teacher/help/home.php',
				    'teacher_help_list_subjects' => 'include/teacher/help/list_subjects.php',
				    'teacher_help_create_subject_name' => 'include/teacher/help/create_subject_name.php',
				    'teacher_help_list_groups' => 'include/teacher/help/list_groups.php'));
    }			    
    else if($_SESSION['type'] == ADMIN_TYPE)
    {
        $validPages = array_merge($validPages, array(		
				    'admin_home' => 'include/admin/home.php',
                    'admin_database_management' => 'include/admin/database_management.php',
                    'admin_create_teacher' => 'include/admin/create_teacher.php',
                    'admin_edit_teachers' => 'include/admin/edit_teachers.php',
                    'admin_edit_teacher' => 'include/admin/edit_teacher.php',
                    'admin_load_teachers' => 'include/admin/load_teachers.php',
                    'admin_log_management' => 'include/admin/log_management.php',));
    }
?>
