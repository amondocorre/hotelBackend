<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AccessMenu_model extends CI_Model {
    protected $table = 'menu_acceso'; 
    public function __construct() {
        parent::__construct();
    }
    public function findIdentity($id) {
        return $this->db->get_where($this->table, ['id_menu_acceso' => $id])->row();
    }
    public function getId($user) {
        return $user->id_menu_acceso ?? null;
    }
    public function findAllIdUser($idUser) {
        $access = $this->db->get_where($this->table, ['estado' => 1])->result();
        $resAccess =  $this->getSubMenu($access,'0');
        return $resAccess;
    }
    function getSubMenu($access,$idSubMenu){
      $resAccess = array();
      foreach ($access as $key => $acces) {
        if($acces->nivel_superior === $idSubMenu){
          $acces->subMenu = $this->getSubMenu($access,$acces->id_menu_acceso);
          array_push($resAccess,$acces);
        }
      }
      return $resAccess;
    }
}