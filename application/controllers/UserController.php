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
    public function create() {
        $body = json_decode(file_get_contents('php://input'), true);
        $newUser = [
            'username' => $body['username'],
            'password_hash' => password_hash($body['password'], PASSWORD_BCRYPT),
            'access_token' => bin2hex(random_bytes(16)),
        ];

        if ($this->Usuario_model->create($newUser)) {
            $this->output
                ->set_status_header(201)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'User was created',
                    'data' => ['user' => $newUser]
                ]));
        } else {
            $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'User wasn\'t registered',
                ]));
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
