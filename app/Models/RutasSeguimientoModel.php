<?php

namespace App\Models;

use CodeIgniter\Model;

class RutasSeguimientoModel extends Model
{
    protected $DBGroup          = 'trackdb'; 
    protected $table            = 'rutas_seguimiento';
    protected $primaryKey       = 'id'; 
    protected $useAutoIncrement = true; 
    protected $returnType       = 'array'; 
    protected $useSoftDeletes   = false; 
    protected $protectFields    = true; 
    protected $allowedFields    = ['id_info_ruta', 'id_ruta_detalle', 'gestion', 'rq', 'responsable', 'register_at', 'ubicado', 'capturado', 'observaciones', 'ruta', 'prioridad', 'latitud', 'longitud'
    ]; 
    
}
