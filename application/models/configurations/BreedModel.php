<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BreedModel extends CI_Model {
    protected $table = 'razas'; 
    public function __construct() {
        parent::__construct();
    }
  public function findIdentity($id) {
      return $this->db->get_where($this->table, ['id_raza' => $id])->row();
  }
  public function getId($mascota) {
      return $mascota->id_raza ?? null;
  }
  public function findAll() {
    return $this->db->get($this->table)->result();
  }
  public function findActive(){
    $this->db->select("t.*,concat(especie,' > ',raza) as especie_raza");
    $this->db->from($this->table . ' AS t'); 
    $this->db->where('t.estado', 1);
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
    $this->db->where('id_raza', $id);
    return $this->db->update($this->table, ['estado'=>0]);
  }
  public function activate($id) {
    $this->db->where('id_raza', $id);
    return $this->db->update($this->table, ['estado'=>'1']);
  }
  public function update($id, $data) {
    if (!$this->validate_pet_data($data, $id)) {
        return FALSE;
    }
    $this->db->where('id_raza', $id);
    return $this->db->update($this->table, $data);
  }
  private function validate_pet_data($data, $id_raza = 0) {
    $this->form_validation->set_data($data);
    $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
    $this->form_validation->set_rules('especie', 'Especie', 'max_length[15]');
    return $this->form_validation->run();
  }
}