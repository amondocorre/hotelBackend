<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/
$hook['post_controller_constructor'][] = [
  'class'    => 'Cors',
  'function' => 'enable_cors',
  'filename' => 'cors.php',
  'filepath' => 'hooks'
];
$hook['pre_system'][] = array(
    'class'    => 'Nocache',
    'function' => 'set_no_cache_headers',
    'filename' => 'nocache.php',
    'filepath' => 'hooks'
);
