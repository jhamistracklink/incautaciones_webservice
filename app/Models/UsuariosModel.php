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

    // public function getRutasPorUsuario($idUsuario, $rolUsuario, $rutaUsuario)
    // {
    //     $infoRutasModel = model('InfoRutasModel');
        
    //     if ($rolUsuario == 1) {
    //         // Administradores ven todas las rutas
    //         $result = $infoRutasModel->findAll();
    //     } elseif ($rolUsuario == 2) {
    //         // Motorizados ven solo sus rutas
    //         $result = $infoRutasModel->where('ruta', $rutaUsuario)->findAll();
    //     } else {
    //         return ['status' => 'error', 'msg' => 'Rol no autorizado'];
    //     }

    //     return ['status' => 'ok', 'data' => $result];
    // }

    public function getRutasPorUsuario($idUsuario)
    {
        $usuario = $this->find($idUsuario);
    
        if (!$usuario) {
            log_message('error', 'Usuario no encontrado');
            return ['status' => 'error', 'msg' => 'Usuario no encontrado'];
        }
    
        $rolUsuario = $usuario['rol'];
        $rutaUsuario = $usuario['ruta'];
        
        log_message('debug', 'Rol del usuario: ' . $rolUsuario);
        log_message('debug', 'Ruta del usuario: ' . $rutaUsuario);
    
        $rutasDetalleModel = model('RutasDetalleModel');
        
        if ($rolUsuario == 1) {
            // Administradores ven todas las rutas
            $result = $rutasDetalleModel->findAll();
            log_message('debug', 'Administrador - Total rutas encontradas: ' . count($result));
        } elseif ($rolUsuario == 2) {
            // Motorizados ven solo sus rutas
            if (empty($rutaUsuario)) {
                log_message('error', 'Usuario no tiene una ruta asignada');
                return ['status' => 'error', 'msg' => 'Usuario no tiene una ruta asignada'];
            }
            $result = $rutasDetalleModel->where('ruta', $rutaUsuario)->findAll();
            log_message('debug', 'Motorizado - Total rutas encontradas para ruta ' . $rutaUsuario . ': ' . count($result));
        } else {
            log_message('error', 'Rol no autorizado');
            return ['status' => 'error', 'msg' => 'Rol no autorizado'];
        }
    
        return ['status' => 'ok', 'data' => $result];
    }
    

}



