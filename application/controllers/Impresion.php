<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Impresion extends CI_Controller {
  public function __construct() {
    parent::__construct();
    $this->load->database(); 
    $this->load->model('configurations/Company');
    $this->load->model('configurations/PaymentMethod');
    $this->load->model('caja/CajaModel');
    $this->load->model('auth/User_model');
    $this->load->model('caja/BoxMovement');
  } 
  public function imprimirMovimientoCaja() {
   /* if (!validate_http_method($this, ['POST'])) return; 
    $res = verifyTokenAccess();
    if(!$res) return; */
    $data = json_decode(file_get_contents('php://input'), true);
    $idMovimiento = 12;
    $movi = $this->BoxMovement->findIdentity($idMovimiento);
    if(!$movi){
      return _send_json_response($this, 204 , ['status' => 'error','message'=>'No se encotro el movimiento.']);
    }
    $idUSer = $movi->id_usuario;
    $user = $this->User_model->findIdentity($idUSer);
    $company = $this->Company->findIdentity(1);
    $objeto = new stdClass();
    $objeto->empresa = strtoupper($company->nombre.'');//S.R.L.
    $objeto->usuario = $user->nombre;
    $objeto->tipoMovimieno = strtoupper($movi->tipo);
    $objeto->descripcionIE = "";
    $objeto->monto = $movi->monto.' Bs.';
    $objeto->descripcion = ucfirst(strtolower($movi->descripcion));
    $objeto->fecha = date('d-m-Y', strtotime($movi->fecha_movimiento));
    $objeto->hora = date('H:i:s A', strtotime($movi->fecha_movimiento));;
    $datos['json'] = json_encode($objeto);
    $this->load->view('impresion/movimientoCaja', $datos, FALSE);    
    $response = ['status' => 'success','data'=>$company];
    //return _send_json_response($this, 200, $response);
  }
}