<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class daycareModel extends CI_Model {
  protected $table = 'ingreso_salida'; 
  public function __construct() {
      parent::__construct();
  }
  public function findIdentity($id) {
      return $this->db->get_where($this->table, ['id_ingreso_salida' => $id])->row();
  }
  public function getId($daycare) {
      return $daycare->id_ingreso_salida ?? null;
  }
  public function findAll() {
    $this->db->select("*");
    $this->db->from($this->table ); 
    $this->db->where('estado', 0); 
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array();
    }
  }
  public function findActive($id_usuario){
    $this->db->select("c.*,u.nombre as usuario");
    $this->db->from($this->table . ' as c');
    $this->db->join('usuarios as u', 'u.id_usuario = c.id_usuario', 'inner');
    $this->db->where('c.estado', 'Abierta'); 
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        $turno =$query->result()[0];
        $turno->myTurno = ($turno->id_usuario===$id_usuario);
        return $turno; 
    } else {
        return array(); 
    }
  }
  public function create($data) {
    $data['estado'] = 'Abierta';
    $data['fecha_apertura'] = date('Y-m-d H:i:s');
    return $this->db->insert($this->table, $data);
  }
  public function delete($id) {
    $this->db->where('id', $id);
    return $this->db->update($this->table, ['estado'=>0]);
  }
  public function activate($id) {
    $this->db->where('id', $id);
    return $this->db->update($this->table, ['estado'=>'1']);
  }
  public function update($id, $data){
    $data['estado'] = 'Cerrada';
    $data['fecha_cierre'] = date('Y-m-d H:i:s');
    $this->db->where('id', $id);
    return $this->db->update($this->table, $data);
  }
  public function findPetByClient($idClient) {
    $this->db->select("*");
    $this->db->from('mascota as m' ); 
    $this->db->where('estado', 1); 
    $this->db->where('id_cliente', $idClient); 
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        $pets = $query->result();
        foreach($pets as $key => $pet){
          $services = $this->getServisesByPet($pet->id_mascota);
          $saldoDeudaVen =0;
          $saldoDeudaVig =0;
          $idServices =[];
          foreach($services as $service){
            if($service->disponible ===1){
              $saldoDeudaVen += $service->saldo_pagar;
              array_push($idServices,$service->id_servicio);
            }else{
              $saldoDeudaVig += $service->saldo_pagar;
            }
          } 
          $pet->saldoDeudaVen = $saldoDeudaVen;
          $pet->saldoDeudaVig = $saldoDeudaVig;
          $pet->servicios = $services;
          $pet->idServicios = $idServices;
        }
        return $pets;
    } else {
        return array();
    }
  }

   public function getServisesByPet($idMascota) {
    $this->db->select('*');
    $this->db->from('v_paquete_contratado');
    $this->db->where('(disponible = 1 OR saldo_pagar > 0)'); 
    $this->db->where('id_mascota', $idMascota);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array();
    }
  }
}
