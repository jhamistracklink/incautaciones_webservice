<?php

namespace App\Models;

use CodeIgniter\Model;

class RutasDetalleModel extends Model
{
    protected $DBGroup          = 'trackdb'; 
    protected $table            = 'rutas_detalle';
    protected $primaryKey       = 'id'; 
    protected $useAutoIncrement = true; 
    protected $returnType       = 'array'; 
    protected $useSoftDeletes   = false; 
    protected $protectFields    = true; 
    protected $allowedFields    = ['placa', 'distrito', 'domicilio', 'link', 'ccc2', 'update_at', 'created_at', 'latitud', 'longitud', 'ruta', 'prioridad', 'idRuta'
    ]; 
    
}
