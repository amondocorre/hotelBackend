<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class daycareModel extends CI_Model {
  protected $table = 'ingreso_salida'; 
  public function __construct() {
      parent::__construct();
      $this->load->model('configurations/ServiceModel');
      $this->load->model('Client_model');
  }
  public function findIdentity($id) {
      return $this->db->get_where($this->table, ['id_ingreso_salida' => $id])->row();
  }
  public function getId($daycare) {
      return $daycare->id_ingreso_salida ?? null;
  }
  public function findAll() {
    $this->db->select("*");
    $this->db->from($this->table ); 
    $this->db->where('estado', 0); 
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array();
    }
  }
  public function findActive($id_usuario){
    $this->db->select("c.*,u.nombre as usuario");
    $this->db->from($this->table . ' as c');
    $this->db->join('usuarios as u', 'u.id_usuario = c.id_usuario', 'inner');
    $this->db->where('c.estado', 'Abierta'); 
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        $turno =$query->result()[0];
        $turno->myTurno = ($turno->id_usuario===$id_usuario);
        return $turno; 
    } else {
        return array(); 
    }
  }
  public function create($data) {
    $data['estado'] = 'Abierta';
    $data['fecha_apertura'] = date('Y-m-d H:i:s');
    return $this->db->insert($this->table, $data);
  }
  public function delete($id) {
    $this->db->where('id', $id);
    return $this->db->update($this->table, ['estado'=>0]);
  }
  public function activate($id) {
    $this->db->where('id', $id);
    return $this->db->update($this->table, ['estado'=>'1']);
  }
  public function registerIngreso($data,$turno,$idUsuario){
    //$this->db->trans_rollback();
    $this->db->trans_start();
    $fechaActual = date('Y-m-d H:i:s');
    $idTurno = $turno->id;
    $idCliente = $data->cliente->id_cliente;
    $aCuenta = $data->a_cuenta;
    $totalPagar = $data->total_pagar;
    $total = $data->total;
    $horaIngreso = $data->hora_ingreso;
    $fechaIngreso = $data->fecha_ingreso;
    $mascotas = $data->mascotas;
    $descuento = $data->descuento;
    $deudaVen = $data->deudaVen;
    $observaciones = '';
    $idFormaPago = $data->id_forma_pago;
    $idPago = 0;
    if($aCuenta>0){
      $idPago = $this->insertPago($idCliente,$idUsuario,$idTurno,$aCuenta,$descuento,$observaciones,$idFormaPago,$fechaActual);
      if($deudaVen>0){
        $deudas  = $this->getDeudasPasadas($idCliente);
        foreach($deudas as $deuda){
          $idContrato = $deuda->id_paquete_contratado;
          $subTotal = $deuda->saldo_pagar;
            if($aCuenta>0){
              $monto = round(($subTotal>=$aCuenta?$aCuenta:$subTotal),2);
              $aCuenta = round(($subTotal>=$aCuenta?0:$aCuenta-$subTotal),2);
              $this->insertPagoDetalle($monto,$idPago,$idContrato,0);
            }
        }
      }
    }
    $idIngreso =0;
    foreach($mascotas as $key=>$mascota){
      $idMascota = $mascota->id_mascota;
      $total = 0;
      $servicios = $mascota->serviciosSeleccionadas;
      $idIngreso = $this->insertIngreso($idTurno,$idMascota,$idCliente,$fechaIngreso.' '.$horaIngreso,$total);
      if($idIngreso){
        foreach ($servicios as $keyServicio => $servicio){
          $idServicio = $servicio->id_servicio;
          $subTotal = $servicio->sub_total;
          $precio = $servicio->precio;
          $subDescuento = $servicio->descuento;
          $observacion = $servicio->detalle??'';
          $dataServicio = $this->ServiceModel->findIdentity($idServicio);
          $diasIncluidos = (int)$dataServicio->dias_incluidos;
          $diasDisponibles = $diasIncluidos;
          if($servicio->editable ==1){
            $idContrato = $this->insertContrado($idTurno,$idCliente,$idMascota,$idServicio,$fechaActual,$diasDisponibles,$precio,$subDescuento,$subTotal,$observacion);
            if($idContrato){
              $this->insertContradoDetalle($idContrato,$idIngreso);
              if($aCuenta>0){
                $monto = round(($subTotal>=$aCuenta?$aCuenta:$subTotal),2);
                $aCuenta = round(($subTotal>=$aCuenta?0:$aCuenta-$subTotal),2);
                $this->insertPagoDetalle($monto,$idPago,$idContrato,0);
              }
            }
          }else{
            $idContrato = $servicio->id_paquete_contratado;
            $this->insertContradoDetalle($idContrato,$idIngreso);
            if($aCuenta>0){
              $monto = round(($subTotal>=$aCuenta?$aCuenta:$subTotal),2);
              $aCuenta = round(($subTotal>=$aCuenta?0:$aCuenta-$subTotal),2);
              $this->insertPagoDetalle($monto,$idPago,$idContrato,0);
            }
          }
        }
      }else;
    }
    $this->db->trans_complete();
    return $idIngreso?$idIngreso:true;;
  }
  public function registerSalida($data,$turno,$idUsuario){
    //$this->db->trans_rollback();
    $this->db->trans_start();
    $fechaActual = date('Y-m-d H:i:s');
    $idTurno = $turno->id;
    $idCliente = $data->cliente->id_cliente;
    $aCuenta = $data->a_cuenta;
    $totalPagar = $data->total_pagar;
    $total = $data->total;
    $horaSalida = $data->hora_salida;
    $fechaSalida = $data->fecha_salida;
    $mascotas = $data->mascotas;
    $descuento = $data->descuento;
    $observaciones = '';
    $idFormaPago = $data->id_forma_pago;
    $idPago = 0;
    $totalExcPagsado = 0;
    if($aCuenta>0){
      $idPago = $this->insertPago($idCliente,$idUsuario,$idTurno,$aCuenta,$descuento,$observaciones,$idFormaPago,$fechaActual);
    }
    foreach($mascotas as $key=>$mascota){
      $idMascota = $mascota->id_mascota;
      $idIngreso = $mascota->id_ingreso_salida;
      $total = 0;
      $servicios = $mascota->serviciosSeleccionadas;
      $saldoExsedidoDiu = 0;
      $saldoExsedidoNot = 0;
      foreach ($servicios as $keyServicio => $servicio){
        $idServicio = $servicio->id_servicio;
        $horaExsedidoDiu = $servicio->horaExsedidoDiu;
        $horaExsedidoNot = $servicio->horaExsedidoNot;
        $saldoDiu = floatval($servicio->precio_exc_dia);
        $saldoNot = floatval($servicio->precio_exc_noche);
        $saldoExsedidoDiu += $saldoDiu;
        $saldoExsedidoNot += $saldoNot;
        $totalExc = $saldoDiu+$saldoNot;
        if($totalExc>0){
          $idContrato = $servicio->id_paquete_contratado;
          $idIngresoDetalle = $this->getIdIngesoDetalle($idIngreso,$idContrato);
          $resSalidaDetalle  = $this->registerSalidaDetalle($idIngresoDetalle,$horaExsedidoDiu,$horaExsedidoNot,$saldoDiu,$saldoNot);
          if($resSalidaDetalle){  
            if($aCuenta>0){
              $monto = round(($totalExc>=$aCuenta?$aCuenta:$totalExc),2);
              $aCuenta = round(($totalExc>=$aCuenta?0:$aCuenta-$totalExc),2);
              $totalExcPagsado += $monto; 
              $this->insertPagoDetalle($monto,$idPago,0,$idIngresoDetalle);
            }
          }
        }
      }
      $mascota->saldoExsedidoDiu = $saldoExsedidoDiu;
      $mascota->saldoExsedidoNot = $saldoExsedidoNot; 
    }
    foreach($mascotas as $key=>$mascota){
      $idMascota = $mascota->id_mascota;
      $total = 0;
      $servicios = $mascota->serviciosSeleccionadas;
      $idIngreso = $mascota->id_ingreso_salida;
      $horaExsedidoDiu = $mascota->horaExsedidoDiu;
      $horaExsedidoNot = $mascota->horaExsedidoNot;
      $saldoExsedidoDiu = $mascota->saldoExsedidoDiu;
      $saldoExsedidoNot =  $mascota->saldoExsedidoNot;
      $resSalida = $this->insertSalida($idIngreso,$idTurno,$fechaSalida.' '.$horaSalida,$horaExsedidoDiu,$horaExsedidoNot,$saldoExsedidoDiu,$saldoExsedidoNot);
      if($resSalida){
        foreach ($servicios as $keyServicio => $servicio){
          $idServicio = $servicio->id_servicio;
          $idContrato = $servicio->id_paquete_contratado;
          $precio = $servicio->precio;
          $subDescuento = $servicio->descuento;
          $montoPagado = $servicio->monto_pagado;
          $subTotal = $precio-($montoPagado+$subDescuento);
          $observacion = $servicio->detalle;
          $dataServicio = $this->ServiceModel->findIdentity($idServicio);
          $diasIncluidos = (int)$dataServicio->dias_incluidos;
          $diasDisponibles = $diasIncluidos;
          if(true){
            $resContrato = $this->updateContrado($idContrato);
            if($resContrato){
              if($subTotal>0 && $aCuenta>0){
                $monto = round(($subTotal>=$aCuenta?$aCuenta:$subTotal),2);
                $aCuenta = round(($subTotal>=$aCuenta?0:$aCuenta-$subTotal),2);
                $this->insertPagoDetalle($monto,$idPago,$idContrato,0);
              }
            }
          }
        }
      }else;
    }
    $this->db->trans_complete();
    return $idIngreso?$idIngreso:true;
  }
  public function insertIngreso($idCaja,$idMascota,$idCliente,$fechaIngreso,$total) {
    $niewData = new stdClass();
    $niewData->id_caja = $idCaja;
    $niewData->id_mascota = $idMascota;
    $niewData->id_cliente = $idCliente;
    $niewData->fecha_ingreso = $fechaIngreso;
    $niewData->horas_excedidas = 0;
    $niewData->cobro_excedente = 0;
    $niewData->total = $total;
    $niewData->estado = 'En Estancia';    
    $this->db->insert('ingreso_salida', $niewData);
    return $this->db->insert_id();
  }
  public function insertSalida($idIngreso,$idCaja,$fechaSalida,$horaExsedidoDiu,$horaExsedidoNot,$saldoExsedidoDiu,$saldoExsedidoNot) {
    $niewData = new stdClass();
    $niewData->id_caja_salida = $idCaja;
    $niewData->fecha_salida = $fechaSalida;
    $niewData->horas_excedidas = $horaExsedidoDiu + $horaExsedidoNot;
    $niewData->cobro_excedente = $saldoExsedidoDiu+$saldoExsedidoNot;
    $niewData->total = $saldoExsedidoDiu+$saldoExsedidoNot;
    $niewData->hora_exedente_diurno = $horaExsedidoDiu;
    $niewData->hora_exedente_nocturno = $horaExsedidoNot;
    $niewData->cobro_exedente_diurno = $saldoExsedidoDiu;
    $niewData->cobro_exedente_nocturno = $saldoExsedidoNot;
    $niewData->estado = 'Finalizado';    
    $this->db->where('id_ingreso_salida', $idIngreso);
    $this->db->update('ingreso_salida', $niewData);
    return $this->db->affected_rows(); 
  }
  public function insertContrado($idCaja,$idCliente,$idMascota,$idServicio,$fechaCompra,$diasDisponibles,$precio,$descuento,$subTotal,$observacion) {
    $niewData = new stdClass();
    $niewData->id_caja = $idCaja;
    $niewData->id_cliente = $idCliente;
    $niewData->id_mascota = $idMascota;
    $niewData->id_servicio = $idServicio;
    $niewData->fecha_compra = $fechaCompra;
    $niewData->dias_disponibles = $diasDisponibles;
    $niewData->precio_servicio = $precio;
    $niewData->descuento = $descuento;
    $niewData->total_pagar = $subTotal;
    $niewData->observacion = $observacion;
    $niewData->estado = 'Activo';
    $this->db->insert('paquete_contratado', $niewData);
    return $this->db->insert_id();
  }
  public function updateContrado($idContrato) {
    $sql = "UPDATE paquete_contratado 
        SET dias_disponibles=dias_disponibles-1,
            estado = CASE 
            WHEN dias_disponibles = 0 THEN 'Finalizado' 
            ELSE 'Activo' 
        END 
        WHERE id_paquete_contratado = ?";
    $this->db->query($sql, array($idContrato));
    return $this->db->affected_rows(); 
  }
  public function insertContradoDetalle($idContrato,$idIngreso) {
    $niewData = new stdClass();
    $niewData->id_ingreso_salida = $idIngreso;
    $niewData->id_paquete_contratado = $idContrato;
    $this->db->insert('ingreso_salida_paquete', $niewData);
    return $this->db->insert_id();
  }
  public function getIdIngesoDetalle($idIngreso,$idContrato) {
    $ingresoDetalle = $this->db->get_where('ingreso_salida_paquete', ['id_ingreso_salida' => $idIngreso,'id_paquete_contratado'=>$idContrato])->row();
    return $ingresoDetalle->id_ingreso_salida_paquete??0;
  }
  public function registerSalidaDetalle($idIngresoDetalle,$horaExsedidoDiu,$horaExsedidoNot,$saldoExsedidoDiu,$saldoExsedidoNot) {
    $niewData = new stdClass();
    $niewData->total_excedente = $saldoExsedidoDiu+$saldoExsedidoNot;
    $niewData->hora_exedente_diurno = $horaExsedidoDiu;
    $niewData->hora_exedente_nocturno = $horaExsedidoNot;
    $niewData->cobro_exedente_diurno = $saldoExsedidoDiu;
    $niewData->cobro_exedente_nocturno = $saldoExsedidoNot;
    $this->db->where('id_ingreso_salida_paquete', $idIngresoDetalle);
    $this->db->update('ingreso_salida_paquete', $niewData);
    return $this->db->affected_rows(); 
  }
  public function insertPagoDetalle($monto,$idPago,$idContrato,$idIngresoDetalle) {
    $niewData = new stdClass();
    $niewData->monto = $monto;
    $niewData->id_pago = $idPago;
    $niewData->id_paquete_contratado = $idContrato;
    $niewData->id_ingreso_salida_paquete = $idIngresoDetalle;
    $this->db->insert('paquete_contratado_detalle', $niewData);
    return $this->db->insert_id();
  }
  public function insertPago($idCliente,$idUsuario,$idCaja,$monto,$descuento,$observaciones,$idFormaPago,$fechaPago) {
    $niewData = new stdClass();
    $niewData->id_cliente = $idCliente;
    $niewData->id_usuario = $idUsuario;
    $niewData->id_caja = $idCaja;
    $niewData->monto = $monto;
    //$niewData->descuento = $descuento;
    $niewData->anulado = 'no';
    $niewData->observaciones = $observaciones;
    $niewData->id_forma_pago = $idFormaPago;
    $niewData->fecha_pago = $fechaPago;
    $this->db->insert('pago', $niewData);
    return $this->db->insert_id();
  }
  public function findPetByClient($idClient) {
    $this->db->select("m.*");
    $this->db->from('mascota as m'); 
    $this->db->join("ingreso_salida as is2", "is2.id_mascota = m.id_mascota AND is2.estado = 'En Estancia'", "left");
    $this->db->where('is2.id_mascota IS NULL');
    $this->db->where('m.estado', 1); 
    $this->db->where('m.id_cliente', $idClient); 
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        $pets = $query->result();
        foreach($pets as $key => $pet){
          $services = $this->getServisesByPet($pet->id_mascota);
          $saldoDeudaVen =0;
          $saldoDeudaVig =0;
          $idServices =[];
          foreach($services as $service){
            $service->historial = $service->historial?json_decode($service->historial):[];
            if($service->disponible == 0){
              $saldoDeudaVen += $service->saldo_pagar;
              array_push($idServices,$service->id_servicio);
            }else{
              $saldoDeudaVig += $service->saldo_pagar;
            }
          } 
          $pet->saldoDeudaVen = $saldoDeudaVen;
          $pet->saldoDeudaVig = $saldoDeudaVig;
          $pet->servicios = $services;
          $pet->idServicios = $idServices;
        }
        return $pets;
    } else {
        return array();
    }
  }
  public function getServisesByPet($idMascota) {
    $this->db->select('vpc.*');
    $this->db->select("JSON_ARRAYAGG(JSON_OBJECT('id_ingreso_salida', is2.id_ingreso_salida,'fecha_ingreso', is2.fecha_ingreso,'fecha_salida', is2.fecha_salida)) AS historial", false);
    $this->db->from('v_paquete_contratado vpc');
    $this->db->join('ingreso_salida is2', 'is2.id_mascota = vpc.id_mascota', 'left');
    $this->db->join('ingreso_salida_paquete isp', 'isp.id_ingreso_salida = is2.id_ingreso_salida', 'left');
    $this->db->where('(vpc.disponible = 1 OR vpc.saldo_pagar > 0)');
    $this->db->where('vpc.id_mascota', $idMascota);
    $this->db->group_by('vpc.id_paquete_contratado'); 
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array();
    }
  }
  public function getServisesContratoByPet2($idMascota,$idIngresoSalida) {
    $this->db->select('vpc.*,isp.hora_exedente_nocturno as horaExsedidoNot,isp.hora_exedente_diurno as horaExsedidoDiu,isp.cobro_exedente_nocturno as saldoExsedidoNot,isp.cobro_exedente_diurno as saldoExsedidoDiu');
    $this->db->from('v_paquete_contratado as vpc'); 
    $this->db->join('ingreso_salida_paquete as isp', 'isp.id_paquete_contratado = vpc.id_paquete_contratado', 'inner');
    $this->db->where('vpc.id_mascota', $idMascota);
    $this->db->where('isp.id_ingreso_salida', $idIngresoSalida);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array();
    }
  }
  public function getServisesContratoByPet($idMascota,$idIngresoSalida) {
    $this->db->select('vpc.*');
    $this->db->select('vpc.*,isp.hora_exedente_nocturno as horaExsedidoNot,isp.hora_exedente_diurno as horaExsedidoDiu,isp.cobro_exedente_nocturno as saldoExsedidoNot,isp.cobro_exedente_diurno as saldoExsedidoDiu');
    $this->db->select("JSON_ARRAYAGG(JSON_OBJECT('id_ingreso_salida', is2.id_ingreso_salida,'fecha_ingreso', is2.fecha_ingreso,'fecha_salida', is2.fecha_salida)) AS historial", false);
    $this->db->from('v_paquete_contratado vpc');
    $this->db->join('ingreso_salida is2', 'is2.id_mascota = vpc.id_mascota', 'left');
    $this->db->join('ingreso_salida_paquete isp2', 'isp2.id_ingreso_salida = is2.id_ingreso_salida', 'left');
    $this->db->join('ingreso_salida_paquete as isp', 'isp.id_paquete_contratado = vpc.id_paquete_contratado', 'inner');
    $this->db->where('vpc.id_mascota', $idMascota);
    $this->db->where('isp.id_ingreso_salida', $idIngresoSalida);
    $this->db->group_by('vpc.id_paquete_contratado'); 
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array();
    }
  } 
  public function getDeudasPasadas($idClient) {
    $this->db->select('vpc.*');
    $this->db->from('v_paquete_contratado as vpc'); 
    $this->db->where('vpc.id_cliente', $idClient);
    $this->db->where('vpc.disponible', 0);
    $this->db->where('vpc.saldo_pagar >0');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array();
    }
  } 
  public function getPagos($idsIngreso) {
    $this->db->distinct();
    $this->db->select('pcd.id_pago, p.fecha_pago');
    $this->db->from('ingreso_salida_paquete isp'); 
    $this->db->join('paquete_contratado_detalle pcd', 'pcd.id_paquete_contratado = isp.id_paquete_contratado OR pcd.id_ingreso_salida_paquete = isp.id_ingreso_salida_paquete', 'inner');
    $this->db->join('pago p', 'p.id_pago = pcd.id_pago', 'inner');
    $this->db->where_in('isp.id_ingreso_salida', $idsIngreso);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result();
    } else {
        return array();
    }
  }
  function calcularDiferenciaHoras($hora1, $hora2) {
    $timestamp1 = strtotime($hora1);
    $timestamp2 = strtotime($hora2);
    $diferenciaSegundos = abs($timestamp1 - $timestamp2);
    return round($diferenciaSegundos / 3600, 2);
  }
  public function getDaycare($estado,$i_fecha,$f_fecha) {
    $sql = "CALL getClientesMascotasInHotel('$estado','$i_fecha','$f_fecha');";
    $query = $this->db->query($sql);
    $clientes = $query->result_array();
    $query->free_result(); 
    $this->db->close();
    $this->db->initialize();
    foreach($clientes as $key=>$cliente){
      $mascotas = !empty($cliente['mascotas']) ? json_decode($cliente['mascotas'], true) : [];
      $saldoDeudaTVen = 0;
      $saldoDeudaTVig = 0;
      $saldoDeuda = 0;
      $mascotasAux =array();
      $idsIngreso =array();
      foreach($mascotas as $key2 => $mascota){
        $idMascota =(int)$mascota['id_mascota'];
        $idIngresoSalida =(int)$mascota['id_ingreso_salida'];
        array_push($mascotasAux,$mascota['nombre']);
        array_push($idsIngreso,$idIngresoSalida);
        $services = $this->getServisesContratoByPet($idMascota,$idIngresoSalida);
        $saldoDeudaVen = 0;
        $saldoDeudaVig = 0;
        foreach($services as $service){
          if($service->disponible ==0){
            $saldoDeudaVen += $service->saldo_pagar;
          }else{
            $saldoDeudaVig += $service->saldo_pagar;
          }
        }
        $saldoDeudaTVen +=$saldoDeudaVen;
        $saldoDeudaTVig +=$saldoDeudaVig;
      }
      $clientes[$key]['saldoDeudaVen']=$saldoDeudaTVen;
      $clientes[$key]['saldoDeudaVig']=$saldoDeudaTVig;
      $clientes[$key]['saldoDeuda']=($saldoDeudaTVen+$saldoDeudaTVig);
      $clientes[$key]['mascotas']=$mascotas;
      $clientes[$key]['nombreMascotas']=$mascotasAux;
      $clientes[$key]['idsIngreso']=$idsIngreso;
      $clientes[$key]['idsPagos']= $this->getPagos($idsIngreso);
      
    }
    return $clientes;
  }
  public function getDaycareById($idsIngreso,$idClient) {
    $mascotas=[];
    if($idsIngreso){
      $this->db->select("is2.id_ingreso_salida, is2.id_mascota, is2.id_cliente, is2.fecha_ingreso, m.nombre, m.notas, m.sexo, m.color,is2.estado");
      $this->db->from("ingreso_salida as is2");
      $this->db->join("mascota as m", "m.id_mascota = is2.id_mascota", "inner");
      $this->db->where_in("is2.id_ingreso_salida", $idsIngreso);
      $query = $this->db->get();
      $mascotas = $query->result();
    }
    $fechaActual = date('Y-m-d H:i:s');
    $horaActual = date('H:i:s');
    $data = new stdClass();
    $saldoExsedidoDiuT = 0;
    $saldoExsedidoNotT = 0;
    $horaExsedidoDiuT = 0;
    $horaExsedidoNotT = 0;
    $estado = '';
    foreach($mascotas as $key2 => $mascota){
      $idMascota =(int)$mascota->id_mascota;
      $estado = $mascota->estado;
      $idIngresoSalida =(int)$mascota->id_ingreso_salida;
      $services = $this->getServisesContratoByPet($idMascota,$idIngresoSalida);
      $saldoDeudaVen = 0;
      $saldoDeudaVig = 0;
      $saldoExsedidoDiu = 0;
      $saldoExsedidoNot = 0;
      $horaExsedidoDiu = 0;
      $horaExsedidoNot = 0;
      foreach($services as $service){
        $service->historial = $service->historial?json_decode($service->historial):[];
        if($service->disponible ==1 && $estado === 'Finalizado'){
          $service->disponible = 0;
        }
        if($service->disponible ==1){
          $saldoDeudaVen += $service->saldo_pagar;
          $horaExDiu =  $service->hora_exedente_diurno;
          $horaExNot = $service->hora_exedente_nocturno;
          $precioExDiu = $service->precio_excedente_diurno;
          $precioExNot = $service->precio_excedente_nocturno;
          if($horaExDiu && $horaExDiu<$horaActual){
            $hoaraAxu = $horaActual>$horaExNot?$horaExNot:$horaActual;
            $difHora = $this->calcularDiferenciaHoras($hoaraAxu, $horaExDiu);
            $saldoExsedidoDiu += round((($difHora)*$precioExDiu),2);
            $service->saldoExsedidoDiu = round((($difHora)*$precioExDiu),2);
            $service->horaExsedidoDiu = $difHora;
            $horaExsedidoDiu += $difHora;
          }else{
            $service->saldoExsedidoDiu = 0;
            $service->horaExsedidoDiu = 0;
          }
          if($horaExNot && $horaExNot<$horaActual){
            $difHora = $this->calcularDiferenciaHoras($horaActual, $horaExNot);
            $saldoExsedidoNot += round((($difHora)*$precioExNot),2);
            $service->saldoExsedidoNot = round((($difHora)*$precioExNot),2);
            $service->horaExsedidoNot = $difHora;
            $horaExsedidoNot += $difHora;
          }else{
            $service->saldoExsedidoNot = 0;
            $service->horaExsedidoNot = 0;
          }
        }else{
          $saldoDeudaVig += $service->saldo_pagar;
        }
      }
      $mascota->servicios = $services;
      $mascota->saldoExsedidoDiu = $saldoExsedidoDiu;
      $mascota->saldoExsedidoNot = $saldoExsedidoNot;
      $mascota->horaExsedidoDiu = $horaExsedidoDiu;
      $mascota->horaExsedidoNot = $horaExsedidoNot;
      
      $saldoExsedidoDiuT +=$saldoExsedidoDiu;
      $saldoExsedidoNotT +=$saldoExsedidoNot;
      $horaExsedidoDiuT +=$horaExsedidoDiu;
      $horaExsedidoNotT +=$horaExsedidoNot;
      /*
      $saldoDeudaTVen +=$saldoDeudaVen;
      $saldoDeudaTVig +=$saldoDeudaVig;*/
    }
    $data->saldoExsedidoDiu = $saldoExsedidoDiuT;
    $data->saldoExsedidoNot = $saldoExsedidoNotT;
    $data->horaExsedidoDiu = $horaExsedidoDiuT;
    $data->horaExsedidoNot = $horaExsedidoNotT;
    $data->mascotas = $mascotas;
    $data->estado = $estado;
    $cliente = $this->Client_model->findIdentity($idClient);
    $cliente->nombre_completo = $cliente->nombres.' '.$cliente->ap_paterno.' '.$cliente->ap_materno;
    $data->cliente = $cliente;
    return $data;
  }

}
