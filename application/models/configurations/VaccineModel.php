<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VaccineModel extends CI_Model {
    protected $table = 'vacuna'; 
    public function __construct() {
        parent::__construct();
    }
  public function findIdentity($id) {
      return $this->db->get_where($this->table, ['id_vacuna' => $id])->row();
  }
  public function getId($vacuna) {
      return $vacuna->id_vacuna ?? null;
  }
  public function findAll() {
    $this->db->select("*");
    $this->db->from($this->table); 
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array();
    }
  }
  public function findActive(){
    $this->db->select("*");
    $this->db->from($this->table); 
    $this->db->where('estado', 1);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result(); 
    } else {
        return array(); 
    }
  }
  public function create($data) {
    if (!$this->validate_pet_data($data)) {
        return FALSE; 
    }
    $data['estado'] = '1';
    return $this->db->insert($this->table, $data);
  }
  public function delete($id) {
    $this->db->where('id_vacuna', $id);
    return $this->db->update($this->table, ['estado'=>0]);
  }
  public function activate($id) {
    $this->db->where('id_vacuna', $id);
    return $this->db->update($this->table, ['estado'=>'1']);
  }
  public function update($id, $data) {
    if (!$this->validate_pet_data($data, $id)) {
        return FALSE;
    }
    $this->db->where('id_vacuna', $id);
    return $this->db->update($this->table, $data);
  }
  private function validate_pet_data($data, $id_vacuna = 0) {
    $this->form_validation->set_data($data);
    $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
    $this->form_validation->set_rules('duracion', 'Duracion', 'required|integer|greater_than[0]|less_than[1000]');
    return $this->form_validation->run();
  }
}
