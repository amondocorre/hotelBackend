<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceModel extends CI_Model {
    protected $table = 'servicio'; 
    public function __construct() {
        parent::__construct();
    }
  public function findIdentity($id) {
      return $this->db->get_where($this->table, ['id_servicio' => $id])->row();
  }
  public function getId($servicio) {
      return $servicio->id_servicio ?? null;
  }
  public function findAll() {
    $this->db->select("s.*, ts.descripcion as tipo");
    $this->db->from($this->table . ' AS s'); 
    $this->db->join('tipo_servicio ts', 'ts.id_tipo_servicio = s.id_tipo', 'inner'); 
    $query = $this->db->get(); 
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array(); 
    }
  }
  public function findActive(){
    $this->db->select("s.*, ts.descripcion as tipo,unico");
    $this->db->from($this->table . ' AS s'); 
    $this->db->join('tipo_servicio ts', 'ts.id_tipo_servicio = s.id_tipo', 'inner'); 
    $this->db->where('s.estado', 1);
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
    //return $this->db->insert_id();
  }
  public function delete($id) {
    $this->db->where('id_servicio', $id);
    return $this->db->update($this->table, ['estado'=>0]);
  }
  public function activate($id) {
    $this->db->where('id_servicio', $id);
    return $this->db->update($this->table, ['estado'=>'1']);
  }
  public function update($id, $data) {
    if (!$this->validate_pet_data($data, $id)) {
        return FALSE;
    }
    $this->db->where('id_servicio', $id);
    return $this->db->update($this->table, $data);
  }
  private function validate_pet_data($data, $id_servicio = 0) {
    $this->form_validation->set_data($data);
    $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
    $this->form_validation->set_rules('descripcion', 'Descripcion', 'required|max_length[100]');
    $this->form_validation->set_rules('id_tipo', 'Tipo', 'required|numeric');
    $this->form_validation->set_rules('dias_incluidos', 'Dias incluidas', 'required|numeric');
    $this->form_validation->set_rules('horas_dia', 'Hopras por dia', 'required|numeric');
    $this->form_validation->set_rules('precio', 'Precio', 'required|numeric');
    $this->form_validation->set_rules('precio_excedente_diurno', 'Precio excedente diurno', 'required|numeric');
    $this->form_validation->set_rules('precio_excedente_nocturno', 'Precio excedente nocturno', 'required|numeric');
    $this->form_validation->set_rules('dias_duracion', 'Duracion', 'required|numeric');
    return $this->form_validation->run();
  }
}