<?php

namespace App\Models;

use CodeIgniter\Model;

class BusquedaModel extends Model
{
    protected $DBGroup          = 'trackdbLocal';
    protected $table            = 'infoIncautaciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id', 'fecha_update', 'periodo_recepcion_oc', 'fecha_recepcion_oficio_orden_captura',
        'nro_expediente_incautacion', 'divpoltran_general', 'ident_operacion', 'dni',
        'cliente', 'estudio', 'placa', 'marca', 'modelo', 'anio_vehiculo', 'fase',
        'correo', 'region', 'departamento', 'provincia', 'distrito', 'domicilio',
        'dir_laboral', 'mejor_direccion', 'tipo_mora', 'status_gps', 'coordenadas',
        'fec_ult_transmision_fin', 'n_motor', 'moneda', 'capital', 'capital_soles',
        'dias_atraso', 'tramo_mora', 'cuotas_pagadas', 'cuotas_pendientes',
        'cuotas_atrasadas', 'dias_transcurridos', 'rango_dias_of_oc',
        'rng_antiguedad_oficio_aju', 'gama_marca', 'gama_precio', 'telef_1', 'telef_2',
        'telef_3', 'telef_4', 'telef_5', 'rq', 'ubicado','idusuario', 'estado', 'responsable_cambio_estado', 'fecha_cambio_estado'
    ];

    public function searchByPlaca($placa)
    {
        //return $this->where('placa', $placa)->findAll();
        return $this->where(['placa'=> $placa, 'estado' => 1])->first();
    }

}
