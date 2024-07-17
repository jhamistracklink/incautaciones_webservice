<?php

namespace App\Models;

use CodeIgniter\Model;

class InfoRutasModel extends Model
{
    protected $DBGroup          = 'trackdb';
    protected $table            = 'infoRutas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['gestion', 'placa', 'marca', 'modelo', 'color', 'distrito', 'domicilio', 'link', 'ccc2', 'rq', 'empresa', 'update_at', 'created_at', 'latitud', 'longitud', 'responsable', 'ubicado', 'capturado', 'observaciones', 'ruta', 'prioridad'];

}
