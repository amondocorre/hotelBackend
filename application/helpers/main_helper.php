<?php

use LDAP\Result;
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('test')) {
	function test(){
    var_dump('main_helper');
	}
}
if (!function_exists('_send_json_response')) {
  function _send_json_response(&$CI, $status, $data) {
      $CI->output
          ->set_status_header($status)
          ->set_content_type('application/json')
          ->set_output(json_encode($data));
      return;
  }
}
if (!function_exists('verifyTokenAccess')) {
    function verifyTokenAccess() {
        $CI = &get_instance();
        $authHeader = $CI->input->get_request_header('Authorization');
        if (!$authHeader) {
            return _send_unauthorized_response($CI, 'Token no proporcionado');
        }
        $token = str_replace('Bearer ', '', $authHeader);
        if (!$token) {
            return _send_unauthorized_response($CI, 'Token no proporcionado');
        }
        try {
            $decoded = $CI->jwthandler->decode($token);
            if ($decoded) {
                return $decoded;
            }
            return _send_unauthorized_response($CI, 'Token inválido o expirado');
        } catch (Exception $e) {
          var_dump($e);
            return _send_unauthorized_response($CI, 'Token inválido o expirado');
        }
    }
}

if(!function_exists('_send_unauthorized_response')){
    function _send_unauthorized_response(&$CI, $message){
        $CI->output
            ->set_status_header(401)
            ->set_content_type('application/json')
            ->set_output(json_encode(['message' => $message]));
        return null;
    }
}


