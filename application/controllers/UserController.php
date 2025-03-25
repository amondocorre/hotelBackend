<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class UserController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database(); 
        $this->load->model('auth/User_model');
        $this->load->model('auth/Usuario_model');
        $this->load->model('auth/AccessMenu_model');
    }
    public function index() {
      echo 'Hello from UserController!';
    } 
    public function create_user() {
      if (!validate_http_method($this, ['POST'])) {
        return;
      }
      $res = verifyTokenAccess();
      if(!$res){
        return;
      }
      $data = $this->input->post();
      $file = $_FILES['file']??null;
      //$data = json_decode(file_get_contents('php://input'), true);
      $id_usuario = $this->User_model->create($data);
      if ($id_usuario) {
          if($file){
            $url = guardarArchivo($id_usuario,$file,'assets/user/');
            if(!$url){
              $response = ['status' => 'success','message'=>'Ocurrio un error al guardar la foto.'];
              return _send_json_response($this, 200, $response);
            }
            $this->User_model->updateFoto($url,$id_usuario);
          }
          if(!$this->User_model->createAccessUser($id_usuario,$data['id_perfil'])){
            //return;
          }
          if(!$this->User_model->createAccessBottons($id_usuario,$data['id_perfil'])){
            //return;
          }
          $response = ['status' => 'success','message'=>'Usuario creado con éxito.'];
          return _send_json_response($this, 200, $response);
      } else {
        $response = ['status' => 'error', 'message' =>  array_values($this->form_validation->error_array())];
        return _send_json_response($this, 400, $response);
      }
  }
  public function update_user($id) {
      if (!validate_http_method($this, ['PUT',])) {
        return; 
      }
      $data = $this->input->post();
      if ($this->User_model->update($id, $data)) {
          $response = ['status' => 'success','message'=>'Usuario actualizado con éxito.'];
          return _send_json_response($this, 200, $response);
      } else {//echo validation_errors(); 
        $response = ['status' => 'error', 'message' =>  array_values($this->form_validation->error_array())];
        return _send_json_response($this, 400, $response);
      }
  }
  public function login() {
    $body = json_decode(file_get_contents('php://input'), true);
    $username = isset($body['username']) ? $body['username'] : null; // Corregido el nombre del campo
    $password = isset($body['password']) ? $body['password'] : null;
    if (!$username || !$password) {
        return _send_json_response($this, 400, ['message' => 'Username and password are required']);
    }
    $user = $this->User_model->findByUsername($username);
    if (!$user) {
        return _send_json_response($this, 401, ['message' => 'Incorrect username/password']);
    }
    if (!$user->estado) {
        return _send_json_response($this, 403, ['message' => 'Inactive account. Access denied']);
    }
    if (!password_verify($password, $user->password_hash)) {
        return _send_json_response($this, 401, ['message' => 'Incorrect username/password']);
    }
    unset($user->password_hash);
    $payload = ['user' => $user];
    $token = $this->jwthandler->encode($payload);
    $data = ['user' => $user, 'token' => $token];

    return _send_json_response($this, 200, $data);
  }
  public function logout(){
    $res = verifyTokenAccess();
    if(!$res){
      return;
    }
    $response = ['message' => 'success','data'=>$res];
    return _send_json_response($this, 200, $response);
  }
  public function getMenuAccess(){
    $res = verifyTokenAccess();
    if(!$res){
      return;
    }
    
    $access = $this->AccessMenu_model->findAllIdUser(0);
    $response = ['message' => 'success','menu'=>$access];
    return _send_json_response($this, 200, $response);
  }
}
