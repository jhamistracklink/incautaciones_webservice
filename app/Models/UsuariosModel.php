<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $DBGroup          = 'trackdb';
    protected $table            = 'usuariosIncautaciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nombres', 'apellidos', 'correo', 'contra', 'fecha_creacion', 'estado',  'ruta', 'rol'];

    public function getRutasPorUsuario($idUsuario, $rolUsuario, $rutaUsuario)
    {
        $infoRutasModel = model('InfoRutasModel');
        
        if ($rolUsuario == 1) {
            // Administradores ven todas las rutas
            $result = $infoRutasModel->findAll();
        } elseif ($rolUsuario == 2) {
            // Motorizados ven solo sus rutas
            $result = $infoRutasModel->where('ruta', $rutaUsuario)->findAll();
        } else {
            return ['status' => 'error', 'msg' => 'Rol no autorizado'];
        }

        return ['status' => 'ok', 'data' => $result];
    }
}



