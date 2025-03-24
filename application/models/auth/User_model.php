<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    protected $table = 'usuarios'; // Tabla asociada al modelo
    public function __construct() {
        parent::__construct();
    }
    // Encuentra un usuario por ID
    public function findIdentity($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }
    // Encuentra un usuario por token de acceso
    public function findIdentityByAccessToken($token) {
        $user = $this->db->get_where($this->table, ['access_token' => $token])->row();
        if ($user) {
            $user->access_token = null;
            return $user;
        }
        return null; // Si no se encuentra, retorna null
    }
    // Obtiene el ID del usuario (este método puede estar en un controlador en lugar de aquí)
    public function getId($user) {
        return $user->id ?? null;
    }
    // Valida contraseña
    public function validatePassword($password, $passwordHash) {
        return password_verify($password, $passwordHash);
    }
    // Encuentra un usuario por nombre de usuario
    public function findByUsername($username) {
        $user = new stdClass();
        $user->password_hash ='$2y$10$plLOGQNVtpnl1XlR9tOU4eam88M/Td9hKNb3JBwHAyBbaRm2W/qwe'; 
        $user->user_name ='testt'; 
        $user->nombres ='test' ;
        $user->estado =1;
        return $this->db->get_where($this->table, ['usuario' => $username])->row();
    }
}/*
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('jwt'); // Biblioteca para manejar JWT (debe incluirse)
    }

    public function login() {
        // Obtener los datos enviados en el cuerpo de la solicitud
        $body = json_decode(file_get_contents('php://input'), true);
        $email = isset($body['email']) ? $body['email'] : null;
        $password = isset($body['password']) ? $body['password'] : null;
        // Buscar usuario por email
        $userFound = $this->User_model->findByEmail($email);
        if (!$userFound) {
            $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'El email no existe']));
            return;
        }
        // Validar la contraseña
        if (!password_verify($password, $userFound->password)) {
            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(['message' => 'La contraseña es incorrecta']));
            return;
        }
        // Obtener roles del usuario (dependiendo de cómo se relacionen los roles)
        $roles = $this->User_model->getRoles($userFound->id); // Supongamos que es un método adicional
        $rolesIds = array_map(function($role) {
            return $role['id'];
        }, $roles);

        // Crear el payload para el token
        $payload = [
            'id' => $userFound->id,
            'name' => $userFound->name,
            'roles' => $rolesIds,
            'iat' => time(), // Fecha de emisión
            'exp' => time() + 3600 // Expiración (1 hora)
        ];
        $token = $this->jwt->encode($payload, 'TU_CLAVE_SECRETA'); // Asegúrate de incluir una clave segura

        // Preparar la respuesta
        unset($userFound->password); // Eliminar la contraseña del objeto usuario
        $data = [
            'user' => $userFound,
            'token' => 'Bearer ' . $token
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
*/