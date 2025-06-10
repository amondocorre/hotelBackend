<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CajaModel extends CI_Model {
  protected $table = 'caja'; 
  public function __construct() {
      parent::__construct();
  }
  public function findIdentity($id) {
      return $this->db->get_where($this->table, ['id' => $id])->row();
  }
  public function getId($caja) {
      return $caja->id ?? null;
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
    $this->db->insert($this->table, $data);
    return $this->db->insert_id();
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
}
