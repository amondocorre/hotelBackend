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

if (!function_exists('validate_http_method')) {
  function validate_http_method(&$CI, $allowed_methods) {
      $method = $CI->input->server('REQUEST_METHOD');
      if (!in_array($method, $allowed_methods)) {
          _send_json_response($CI, 405, ['message' => 'Método no permitido']);
          return false;
      }
      return true;
  }
}
if(!function_exists('getDirectorio')){
	function getDirectorio(){
		$nombreCarpeta =  explode('/',$_SERVER['REQUEST_URI'])[1];
		$ruta = $_SERVER['DOCUMENT_ROOT']."/".$nombreCarpeta."/" ;
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
			$ruta = $_SERVER['DOCUMENT_ROOT']."/";
		}
		return $ruta;
	}
}
if(!function_exists('getHttpHost')){
	function getHttpHost(){
		$nombreCarpeta =  explode('/',$_SERVER['REQUEST_URI'])[1];
		$url = "http://" .$_SERVER['HTTP_HOST']."/".$nombreCarpeta."/" ;
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
			$url = "https://" .$_SERVER['HTTP_HOST']."/";
		}
		return $url;
	}
}
if (!function_exists('perfil_existe')) {
  function perfil_existe($id_perfil) {
      $CI =& get_instance();
      $CI->db->where('id', $id_perfil);
      $query = $CI->db->get('perfiles');
      return $query->num_rows() > 0;
  }
}

if(!function_exists('guardarArchivo')){
	function guardarArchivo($nombre,$file,$direcion)
	{
		$url = getHttpHost();
    $ruta = getDirectorio();
    $fileTmpPath = $file['tmp_name'];
    $fileName = $file['name'];
    $fileType = $file['type'];
    $ext = pathinfo($fileName, PATHINFO_EXTENSION) ;
    $destinationPath = $ruta.'/'.$direcion.'/'.$nombre.'.'.$ext;
    if (move_uploaded_file($fileTmpPath, $destinationPath)) {
        return $direcion.'/'.$nombre.'.'.$ext;
    } else {
        return false;
    }
	}
  if (!function_exists('email_unique_current')) {
    function email_unique_current($email, $user_id) {
        $CI =& get_instance();
        $CI->db->where('email', $email);
        $CI->db->where('id_usuario !=', $user_id);
        $query = $CI->db->get('usuarios');
        return $query->num_rows() === 0; 
    }
  }
  if (!function_exists('usuario_unique_current')) {
    function usuario_unique_current($usuario, $user_id) {
      $CI =& get_instance();
      $CI->db->where('usuario', $usuario);
      $CI->db->where('id_usuario !=', $user_id); 
      $query = $CI->db->get('usuarios');
      return $query->num_rows() === 0; 
    }
  }
}