<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_model extends CI_Model {
    protected $table = 'cliente'; 
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation'); 
    }
    public function findIdentity($id) {
        return $this->db->get_where($this->table, ['id_cliente' => $id])->row();
    }
    public function getId($cliente) {
        return $user->id_cliente ?? null;
    }
    public function findAll() {
      return $this->db->get($this->table)->result();
  }
    public function findActive(){
      $this->db->select("*,CONCAT(nombres, ' ', ap_paterno, ' ', ap_materno) AS nombre_completo"); 
      $this->db->from($this->table); 
      $this->db->where('activo', 1); 
      $this->db->order_by('nombre_completo', 'ASC');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          return $query->result(); 
      } else {
          return array(); 
      }
    }
    public function create($data) {
      if (!$this->validate_pefil_data($data)) {
          return FALSE; 
      }
      $data['activo'] = '1';
      return $this->db->insert($this->table, $data);
      //return $this->db->insert_id();
    }
  public function update($id, $data) {
    if (!$this->validate_pefil_data($data, $id)) {
        return FALSE;
    }
    $this->db->where('id_cliente', $id);
    return $this->db->update($this->table, $data);
  }
  public function delete($id) {
    $this->db->where('id_cliente', $id);
    return $this->db->update($this->table, ['activo'=>'0']);
  }
  public function activate($id) {
    $this->db->where('id_cliente', $id);
    return $this->db->update($this->table, ['activo'=>'1']);
  }
  private function validate_pefil_data($data, $id_client = 0) {
    $this->form_validation->set_data($data);
    $this->form_validation->set_rules('nombres', 'Nombre', 'required|max_length[100]');
    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]'.($id_client>0 ? '|email_unique_client['.$id_client.']' : '|is_unique[cliente.email]'));
    $this->form_validation->set_rules('telefono', 'Teléfono', 'max_length[15]');
    $this->form_validation->set_rules('celular', 'Celular', 'max_length[15]');
    //$this->form_validation->set_rules('id', 'id', 'max_length[15] |is_unique[perfiles.id]');
    return $this->form_validation->run();
  }
}