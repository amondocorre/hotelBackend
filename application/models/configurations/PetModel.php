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
  public function getVaccinePet($id_mascota) {
    $this->db->select('v1.*,(to_days(cast(v1.fecha_vigente as date))-to_days(curdate())) as dias_vigencia');
    $this->db->from('v_mascota_vacuna v1');
    $this->db->join(
        '(SELECT id_vacuna, MAX(fecha_vigente) AS max_fecha_vigente
          FROM v_mascota_vacuna
          WHERE id_mascota = ' . $this->db->escape($id_mascota) . '
          GROUP BY id_vacuna) v2',
        'v1.id_vacuna = v2.id_vacuna AND v1.fecha_vigente = v2.max_fecha_vigente'
    );
    $this->db->where('v1.id_mascota', $id_mascota);
    $query = $this->db->get();
    return $query->result();
}
  public function findAll() {
    $this->db->select("t.*, CONCAT(c.nombres, ' ', c.ap_paterno, ' ', c.ap_materno) AS cliente");
    $this->db->from($this->table . ' AS t'); 
    $this->db->join('cliente AS c', 't.id_cliente = c.id_cliente', 'inner'); 
    $query = $this->db->get(); 
    if ($query->num_rows() > 0) {
      $pets = $query->result();
      foreach($pets as $key => $pet){
        $pet->vacunas = $this->getVaccinePet($pet->id_mascota);
      }
      return $pets;
    } else {
        return array();
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
  public function addVaccines($id, $vaccines) {
    $stateVaccine = false;
    foreach ($vaccines as $key => $vaccine) {
      $id_mascota_vacuna = $vaccine['id_mascota_vacuna'];
      if($id_mascota_vacuna ==0){
        $niewData = new stdClass();
        $niewData->id_mascota = $id;
        $niewData->id_vacuna = $vaccine['id_vacuna'];
        $this->db->insert('mascota_vacuna', $niewData);
        $idMV = $this->db->insert_id();
        if($idMV){
          $niewData2 = new stdClass();
          $niewData2->id_mascota_vacuna = $idMV;
          $niewData2->fecha_registro = date('Y-m-d H:i:s');
          $niewData2->fecha_aplicacion = $vaccine['fecha_aplicacion'];
          $niewData2->fecha_vigente = $vaccine['fecha_vigente'];
          $niewData2->estado = 1;
          $this->db->insert('mascota_vacuna_detalle', $niewData2);
        }
      }
    }
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
