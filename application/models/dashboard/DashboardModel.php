<?php
class DashboardModel extends CI_Model {

  public function fetch_arrivals_departures() {
    return $this->db->query("
      select sexo, count(sexo) as cantidad from cliente group by sexo
      
      
    ")->result();
  }

  public function fetch_occupation() {
    return $this->db->query("
      SELECT count(nombre) from mascota
    ")->result();
  }

   public function get_total_clientes() {
    $query = $this->db->query("
        SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN sexo = 'M' THEN 1 ELSE 0 END) AS masculino,
        SUM(CASE WHEN sexo = 'F' THEN 1 ELSE 0 END) AS femenino
        FROM cliente;
    ");
    return $query->row(); // Devuelve un solo objeto con ->total
  }
}
