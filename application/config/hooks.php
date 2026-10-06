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

$hook['post_controller'][] = function () {
    $CI =& get_instance();
    if (isset($CI->security) && $CI->security instanceof CI_Security) {
        header('X-CSRF-Hash: ' . $CI->security->get_csrf_hash());
    }
};
