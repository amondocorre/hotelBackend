<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class DaycareController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database(); 
        $this->load->model('DaycareModel');
    } 
    public function create() {
      if (!validate_http_method($this, ['POST'])) {
        return;
      }
      $res = verifyTokenAccess();
      if(!$res){
        return;
      }
      $user = $res->user;
      $idUser = $user->id_usuario;
      $turno = $this->DaycareModel->findActive($idUser);
      if ($turno) {
        $response = ['status' => 'error','message'=>'Existe un turno abierto.'];
        return _send_json_response($this, 400, $response);
      }
      $data = json_decode(file_get_contents('php://input'), true);
      $data['id_usuario'] = $idUser;
      $id = $this->DaycareModel->create($data);
      if ($id) {
          $response = ['status' => 'success','message'=>'Se aperturo con éxito el Turno.'];
          return _send_json_response($this, 200, $response);
      } else {
        $response = ['status' => 'error', 'message' =>  array_values($this->form_validation->error_array())];
        return _send_json_response($this, 400, $response);
      }
    }
    public function update($id) {
      if (!validate_http_method($this, ['POST'])) {
        return; 
      }
      $res = verifyTokenAccess();
      if(!$res){
        return;
      } 
      $user = $res->user;
      $idUser = $user->id_usuario;
      $turno = $this->DaycareModel->findActive($idUser);
      if (!$turno) {
        $response = ['status' => 'error','message'=>'No se encontro ningun turno abierto.'];
        return _send_json_response($this, 400, $response);
      }
      if (!$turno->myTurno) {
        $response = ['status' => 'error','message'=>'El turno solo puede cerrar el usuario que aperturo el turno.'];
        return _send_json_response($this, 400, $response);
      }
      $data = json_decode(file_get_contents('php://input'), true);
      if ($this->DaycareModel->update($id, $data)) {
          $response = ['status' => 'success','message'=>'Se cerro el turno con éxito.'];
          return _send_json_response($this, 200, $response);
      } else {
        $response = ['status' => 'error', 'message' =>  array_values($this->form_validation->error_array())];
        return _send_json_response($this, 400, $response);
      }
    }
    public function delete($id) {
      if (!validate_http_method($this, ['DELETE'])) {
        return; 
      }
      $res = verifyTokenAccess();
      if(!$res){
        return;
      } 
      if ($this->DaycareModel->delete($id)) {
          $response = ['status' => 'success','message'=>'Metodo de pago eliminado con éxito.'];
          return _send_json_response($this, 200, $response);
      } else {
        $response = ['status' => 'error', 'message' =>  'Ocurrio un eror al internatar eliminar el metodo de pago.'];
        return _send_json_response($this, 400, $response);
      }
    }
    public function activate($id) {
      if (!validate_http_method($this, ['PUT'])) {
        return; 
      }
      $res = verifyTokenAccess();
      if(!$res){
        return;
      } 
      if ($this->DaycareModel->activate($id)) {
          $response = ['status' => 'success','message'=>'Metodo de pago Habilitado con éxito.'];
          return _send_json_response($this, 200, $response);
      } else {
        $response = ['status' => 'error', 'message' => 'Ocurrio un eror al internatar Habilitar el metodo de pago.'];
        return _send_json_response($this, 400, $response);
      }
    }
    public function findActive() {
      if (!validate_http_method($this, ['GET'])) return; 
      $res = verifyTokenAccess();
      if(!$res) return; 
      $user = $res->user;
      $idUser = $user->id_usuario;
      $Mascotaes = $this->DaycareModel->findActive($idUser);
      $response = ['status' => 'success','data'=>$Mascotaes];
      return _send_json_response($this, 200, $response);
    }
    public function findAll() {
      if (!validate_http_method($this, ['GET'])) return; 
      $res = verifyTokenAccess();
      if(!$res) return; 
      $Mascotaes = $this->DaycareModel->findAll();
      $response = ['status' => 'success','data'=>$Mascotaes];
      return _send_json_response($this, 200, $response);
    }
    public function findPetByClient($idClient) {
      if (!validate_http_method($this, ['GET'])) return; 
      $res = verifyTokenAccess();
      if(!$res) return; 
      $Mascotas = $this->DaycareModel->findPetByClient($idClient);
      $response = ['status' => 'success','data'=>$Mascotas];
      return _send_json_response($this, 200, $response);
    }
}
