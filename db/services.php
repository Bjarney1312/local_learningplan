<?php

$functions = [
    'local_learningplan_save_section_data' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'save_section_data',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Save section data',
        'type' => 'write',
        'ajax' => true
    ],
    'local_learningplan_delete_section_data' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'delete_section_data',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Delete section data',
        'type' => 'write',
        'ajax' => true
    ],
    'local_learningplan_check_section_data' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'check_section_data',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Checks if section data already in database',
        'type' => 'read',
        'ajax' => true
    ],
    'local_learningplan_update_deadline' => [
        'classname' => 'local_learningplan\external\learningplan_service',
        'methodname' => 'update_deadline',
        'classpath' => 'local/learningplan/externallib.php',
        'description' => 'Checks if section data already in database',
        'type' => 'read',
        'ajax' => true
    ]
];

