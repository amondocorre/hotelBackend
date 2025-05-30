<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class DaycareController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database(); 
        $this->load->model('DaycareModel');
        $this->load->model('configurations/PaymentMethod');
        $this->load->model('configurations/ServiceModel');
        $this->load->model('configurations/ServiceTypeModel');
        $this->load->model('caja/CajaModel');
        $this->load->library('pdf');
    } 
    
    public function registerIngreso() {
      if (!validate_http_method($this, ['POST'])) {
        return; 
      }
      $res = verifyTokenAccess();
      if(!$res){
        return;
      } 
      $user = $res->user;
      $idUser = $user->id_usuario;
      $turno = $this->CajaModel->findActive($idUser);
      if (!$turno) {
        $response = ['status' => 'error','message'=>'No se encontro ningun turno abierto.'];
        return _send_json_response($this, 400, $response);
      }
      if (!$turno->myTurno) {
        $response = ['status' => 'error','message'=>'Solo el usuario que aperturo puede realizar el ingreso.'];
        return _send_json_response($this, 400, $response);
      }
      $data = json_decode(file_get_contents('php://input'), false);
      $idIngreso = $this->DaycareModel->registerIngreso($data,$turno,$idUser);
      if ($idIngreso) {
          $idIngreso = $idIngreso==true?0:$idIngreso;
          $response = ['status' => 'success','message'=>'Se registro con éxito el ingreso.','idIngreso'=>$idIngreso];
          return _send_json_response($this, 200, $response);
      } else {
        $response = ['status' => 'error', 'message' =>  'Ocurrio un error al intentar registrar el ingreso.'];
        return _send_json_response($this, 400, $response);
      }
    }
    public function registerSalida() {
      if (!validate_http_method($this, ['POST'])) {
        return; 
      }
      $res = verifyTokenAccess();
      if(!$res){
        return;
      } 
      $user = $res->user;
      $idUser = $user->id_usuario;
      $turno = $this->CajaModel->findActive($idUser);
      if (!$turno) {
        $response = ['status' => 'error','message'=>'No se encontro ningun turno abierto.'];
        return _send_json_response($this, 400, $response);
      }
      if (!$turno->myTurno) {
        $response = ['status' => 'error','message'=>'Solo el usuario que aperturo puede registrar la salida.'];
        return _send_json_response($this, 400, $response);
      }
      $data = json_decode(file_get_contents('php://input'), false);
      $idIngreso = $this->DaycareModel->registerSalida($data,$turno,$idUser);
      if ($idIngreso) {
          $idIngreso = $idIngreso==true?0:$idIngreso;
          $response = ['status' => 'success','message'=>'Se registro con éxito la salida.','idIngreso'=>$idIngreso];
          return _send_json_response($this, 200, $response);
      } else {
        $response = ['status' => 'error', 'message' =>  'Ocurrio un error al intentar registrar la salida.'];
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
    public function getDaycare() {
      if (!validate_http_method($this, ['POST'])) return; 
      $res = verifyTokenAccess();
      if(!$res) return; 
      $data = json_decode(file_get_contents('php://input'), true);
      $estado = $data['estado']??'All';
      $i_fecha = $data['i_fecha']??'';
      $f_fecha = $data['f_fecha']??'';
      $data = $this->DaycareModel->getDaycare($estado,$i_fecha,$f_fecha);
      $response = ['status' => 'success','data'=>$data];
      return _send_json_response($this, 200, $response);
    }
    public function getDaycareById($idClient) {
      if (!validate_http_method($this, ['POST'])) return; 
      $res = verifyTokenAccess();
      if(!$res) return; 
      $data = json_decode(file_get_contents('php://input'), true);
      $idsIngreso = $data['idsIngreso']??[];
      $data = $this->DaycareModel->getDaycareById($idsIngreso,$idClient);
      $response = ['status' => 'success','data'=>$data];
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
    public function getDataRequerid() {
      if (!validate_http_method($this, ['GET'])) return; 
      $res = verifyTokenAccess();
      if(!$res) return; 
      $response = new stdClass();
      $response->status = 'success';
      $response->services = $this->ServiceModel->findActive();
      $response->servicesType = $this->ServiceTypeModel->findActive();
      $response->paymentMethods = $this->PaymentMethod->findActive();
      return _send_json_response($this, 200, $response);
    }
    public function getDaycareById2($idClient) {
      if (!validate_http_method($this, ['POST'])) return; 
      $res = verifyTokenAccess();
      if(!$res) return; 
      $data = json_decode(file_get_contents('php://input'), true);
      $idsIngreso = $data['idsIngreso']??[];
      $data = $this->DaycareModel->getDaycareById($idsIngreso,$idClient);
      $response = ['status' => 'success','data'=>$data];

      $pdf = new TCPDF();
        $pdf->SetTitle('Comprobante de Pago');

        // Agregar una página
        $pdf->AddPage();

        // Datos del cliente
        $cliente = [
            'Nombre' => 'Carlos Gutiérrez López',
            'CI' => '6543210 CB',
            'Teléfono' => '70712345',
            'Email' => 'carlos.gutierrez@gmail.com'
        ];

        // Datos de las mascotas
        $mascotas = [
            [
                'nombre' => 'Firulais',
                'servicio' => 'PAQ DIARIO',
                'precio' => 100.00,
                'descuento' => 20.00,
                'total_pagar' => 80.00
            ],
            [
                'nombre' => 'Pelusa',
                'servicio' => 'PAQ DIARIO',
                'precio' => 100.00,
                'descuento' => 10.00,
                'total_pagar' => 90.00
            ]
        ];

        // Crear contenido
        $html = "<h2>Comprobante de Pago</h2>";
        $html .= "<strong>Cliente:</strong> {$cliente['Nombre']}<br>";
        $html .= "<strong>CI:</strong> {$cliente['CI']}<br>";
        $html .= "<strong>Teléfono:</strong> {$cliente['Teléfono']}<br>";
        $html .= "<strong>Email:</strong> {$cliente['Email']}<br><br>";

        foreach ($mascotas as $m) {
            $html .= "<strong>Mascota:</strong> {$m['nombre']}<br>";
            $html .= "<strong>Servicio:</strong> {$m['servicio']}<br>";
            $html .= "<strong>Precio:</strong> {$m['precio']} Bs<br>";
            $html .= "<strong>Descuento:</strong> {$m['descuento']} Bs<br>";
            $html .= "<strong>Total a Pagar:</strong> {$m['total_pagar']} Bs<br><br>";
        }

        // Agregar HTML al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Descargar el archivo PDF
        $pdf->Output('comprobante_pago.pdf', 'D');

      return _send_json_response($this, 200, $response);
    }

}
