<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BoxMovement extends CI_Model {
  protected $table = 'movimientos_caja'; 
  public function __construct() {
      parent::__construct();
  }
  public function findIdentity($id) {
      return $this->db->get_where($this->table, ['id_movimientos_caja' => $id])->row();
  }
  public function getId($movimientos_caja) {
      return $movimientos_caja->id_movimientos_caja ?? null;
  }
  public function findAll() {
    $this->db->select("*");
    $this->db->from($this->table ); 
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array();
    }
  }
  public function findFilter(){
    $this->db->select("mc.*, nombre as usuario");
    $this->db->from($this->table.' as mc');
    $this->join('usuarios as u','u.id_usuario = mc.id_usuario','inner');
    $this->db->where('estado', 1);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result(); 
    } else {
        return array(); 
    }
  }
  public function create($data) {
    if (!$this->validate_movimientos_caja_data($data)) {
        return FALSE; 
    }
    $data['fecha_movimiento'] = date('Y-m-d H:i:s');
    return $this->db->insert($this->table, $data);
  }
  public function delete($id) {
    $this->db->where('id_movimientos_caja', $id);
    return $this->db->update($this->table, ['estado'=>0]);
  }
  public function activate($id) {
    $this->db->where('id_movimientos_caja', $id);
    return $this->db->update($this->table, ['estado'=>'1']);
  }
  public function update($id, $data) {
    if (!$this->validate_movimientos_caja_data($data, $id)) {
        return FALSE;
    }
    $this->db->where('id_movimientos_caja', $id);
    return $this->db->update($this->table, $data);
  }
  private function validate_movimientos_caja_data($data, $id_movimientos_caja = 0) {
    $this->form_validation->set_data($data);
    $this->form_validation->set_rules('id_usuario', 'id_usuario', 'required');
    $this->form_validation->set_rules('id_caja', 'id_caja', 'required');
    return $this->form_validation->run();
  }
}
