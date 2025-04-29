<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PetModel extends CI_Model {
    protected $table = 'mascota'; 
    public function __construct() {
        parent::__construct();
    }
  public function findIdentity($id) {
      return $this->db->get_where($this->table, ['id_mascota' => $id])->row();
  }
  public function getId($mascota) {
      return $mascota->id_mascota ?? null;
  }
  public function findAll() {
    $this->db->select("t.*, CONCAT(c.nombres, ' ', c.ap_paterno, ' ', c.ap_materno) AS cliente");
    $this->db->from($this->table . ' AS t'); 
    $this->db->join('cliente AS c', 't.id_cliente = c.id_cliente', 'inner'); 
    $query = $this->db->get(); // Ejecuta la consulta construida
    if ($query->num_rows() > 0) {
        return $query->result(); // Devuelve los resultados como un array de objetos
    } else {
        return array(); // Devuelve un array vacío si no hay resultados
    }
  }
  public function findActive(){
    $this->db->select("t.*, CONCAT(c.nombres, ' ', c.ap_paterno, ' ', c.ap_materno) AS cliente");
    $this->db->from($this->table . ' AS t'); 
    $this->db->join('cliente AS c', 't.id_cliente = c.id_cliente', 'inner');
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
    $data['fecha_alta'] = date('Y-md H:i:s');
    return $this->db->insert($this->table, $data);
    //return $this->db->insert_id();
  }
  public function delete($id) {
    $this->db->where('id_mascota', $id);
    return $this->db->update($this->table, ['estado'=>0]);
  }
  public function activate($id) {
    $this->db->where('id_mascota', $id);
    return $this->db->update($this->table, ['estado'=>'1']);
  }
  public function update($id, $data) {
    if (!$this->validate_pet_data($data, $id)) {
        return FALSE;
    }
    $this->db->where('id_mascota', $id);
    return $this->db->update($this->table, $data);
  }
  private function validate_pet_data($data, $id_mascota = 0) {
    $this->form_validation->set_data($data);
    $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
    //$this->form_validation->set_rules('id', 'id', 'max_length[15] |is_unique[mascotaes.id]');
    $this->form_validation->set_rules('sexo', 'Sexo', 'in_list[Macho,Hembra,Otro]');
    $this->form_validation->set_rules('color', 'Color', 'required|max_length[50]');
    $this->form_validation->set_rules('pelo', 'Pelo', 'required|max_length[50]');
    //$this->form_validation->set_rules('talla', 'Talla', 'required|max_length[50]');
    return $this->form_validation->run();
  }
}
