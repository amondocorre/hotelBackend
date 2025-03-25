<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    protected $table = 'usuarios'; // Tabla asociada al modelo

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation'); // Cargar la librería de validación de formularios
    }

    // Encuentra un usuario por ID
    public function findIdentity($id) {
        return $this->db->get_where($this->table, ['id_usuario' => $id])->row();
    }

    // Encuentra un usuario por token de acceso (si lo usas)
    public function findIdentityByAccessToken($token) {
        $user = $this->db->get_where($this->table, ['access_token' => $token])->row();
        if ($user) {
            $user->access_token = null;
            return $user;
        }
        return null;
    }

    // Obtiene el ID del usuario (este método puede estar en un controlador en lugar de aquí)
    public function getId($user) {
        return $user->id_usuario ?? null;
    }

    // Valida contraseña
    public function validatePassword($password, $passwordHash) {
        return password_verify($password, $passwordHash);
    }

    // Encuentra un usuario por nombre de usuario
    public function findByUsername($username) {
        return $this->db->get_where($this->table, ['usuario' => $username])->row();
    }
    public function create($data) {
        $data['password'] = 'admin';
        if (!$this->validate_user_data($data)) {
            return FALSE; 
        }
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
    public function update($id, $data) {
      if (!$this->validate_user_data($data, true)) {
          return FALSE;
      }
      $this->db->where('id_usuario', $id);
      return $this->db->update($this->table, $data);
    }
    public function updateFoto($url,$id){
      $this->db->where('id_usuario', $id);
      return $this->db->update($this->table, ['foto'=>$url]);
    }
    public function createAccessUser($id_usuario,$id_perfil){
      $this->db->select("id_acceso, estado, $id_usuario as id_usuario");
      $this->db->from('acceso_perfil');
      $this->db->where('id_perfil', $id_perfil);
      $this->db->where('estado',1);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          $accesos = $query->result_array(); 
          if ($this->db->insert_batch('acceso_usuario', $accesos)) {
              return true; 
          } else {
              return false;
          }
      } else {
          return false; 
      }
    }
    public function createAccessBottons($id_usuario,$id_perfil){
      $this->db->select("id_acceso, id_boton, estado, $id_usuario as id_usuario");
      $this->db->from('acceso_boton_perfil');
      $this->db->where('id_perfil', $id_perfil);
      $this->db->where('estado',1);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
          $accesos = $query->result_array(); 
          if ($this->db->insert_batch('acceso_boton_usuario', $accesos)) {
              return true; 
          } else {
              return false;
          }
      } else {
          return false; 
      }
    }
    private function validate_user_data($data, $is_update = false) {
      $this->form_validation->set_data($data);
      $this->form_validation->set_rules('id_perfil', 'Perfil', 'required|max_length[20]|perfil_existe');
      $this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
      $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]'.($is_update ? '' : '|is_unique[usuarios.email]'));
      $this->form_validation->set_rules('telefono', 'Teléfono', 'max_length[15]');
      $this->form_validation->set_rules('celular', 'Celular', 'max_length[15]');
      $this->form_validation->set_rules('estado', 'Estado', 'in_list[Activo,Inactivo]');
      //$this->form_validation->set_rules('fecha_ingreso', 'Fecha Ingreso', 'valid_date_format[Y-m-d]');
      //$this->form_validation->set_rules('fecha_baja', 'Fecha Baja', 'valid_date');
      $this->form_validation->set_rules('sueldo', 'Sueldo', 'decimal');
      $this->form_validation->set_rules('usuario', 'Usuario', 'max_length[15]' . ($is_update ? '' : '|is_unique[usuarios.usuario]'));
      //$this->form_validation->set_rules('foto', 'Foto');
      //$this->form_validation->set_rules('password','Contraseña','min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]).*$/]');

      return $this->form_validation->run();
  }
}
?>