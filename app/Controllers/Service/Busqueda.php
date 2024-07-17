<?php

namespace App\Controllers\Service;

use App\Models\InfoRutasModel;
use CodeIgniter\RESTful\ResourceController;


class Busqueda extends ResourceController
{
    protected $modelName = 'App\Models\BusquedaModel'; // Asegúrate de tener este modelo
    protected $format    = 'json';

    public $builder;

    public function __construct()
    {
        $db      = \Config\Database::connect();
        $this->builder = $db->table('infoIncautaciones');
    }
    // Método para buscar la información de la incautación por placa
    public function buscar_por_placa($placa = null)
    {
        if (!$placa) {
            return $this->respond(['msg' => 'Placa no proporcionada', 'status' => 'error']);
        }

        $modelParaPlaca = model('BusquedaModel');

        //$data = $this->model->where('placa', $placa)->first();
        $data = $modelParaPlaca->where(['placa'=> $placa, 'estado' => '1'])->first();

        if (empty($data)) {
            return $this->respond(['msg' => 'No se encontró la incautación con esa placa', 'status' => 'error']);
        }

        return $this->respond($data);
    }

    public function update_rq_ubi()
    {

        // return $this->respond($this->request->getPost());
        //$pass = $this->request->getPost('password');
        $placa = $this->request->getPost('placa');
        $rq = $this->request->getPost('rq');
        $ubicado = $this->request->getPost('ubicado');
        $idusuario = $this->request->getPost('idusuario');

        $data = ['rq' => $rq, 'ubicado' => $ubicado, 'idusuario' => $idusuario];

        $modelBusq = model('BusquedaModel');

        //Se obtiene el id
        $selectIdPlaca = $modelBusq->where('placa', $placa)->first();

        $modelBusq->update($selectIdPlaca['id'], $data);

        //$update = $this->model->update($data,'placa="AMX636"');

        // $this->builder->where('placa', $placa);

        // $this->builder->update($data);

        // if (!$update) {
        //     return $this->respond(['msg'=>'Los campos rq y/o ubicado no se actualizaron', 'status'=>'error']);
        // } 

        return $this->respond(['msg' => 'Actualización exitosa', 'status' => 'ok']);

        // return $this->respond($data);

    }

    // public function listaRutas($id)
    // {
    //     $model = model('InfoRutasModel');
    //     //$lista = $model->findAll();

    //     $modelUsuario = model('UsuarioModel');
    //     // $rutasPorUsuario = $modelUsuario->where('id', $id)->first();
    //     $rutasPorUsuario = $modelUsuario->find($id);

    //     if (empty($rutasPorUsuario['ruta'])) {
    //         $lista = $model->findAll();
    //     } else{
    //         $lista = $model->where('ruta', $rutasPorUsuario['ruta'])->findAll();
    //     }

    //     return $this->respond(['data' => $lista, 'status' => 'ok']);
    // }

    public function listaRutas($idUsuario)
    {
        $modelUsuario = model('UsuariosModel');
        $usuario = $modelUsuario->find($idUsuario);
    
        if (!$usuario) {
            return $this->respond(['msg' => 'Usuario no encontrado', 'status' => 'error']);
        }
    
        $infoRutasModel = model('InfoRutasModel');
        // $lista = $infoRutasModel->where('ruta', $rutaUsuario)->findAll();

        if (empty($usuario['ruta'])) {
            $lista = $infoRutasModel->findAll();
        } else{
            $lista = $infoRutasModel->where('ruta',$usuario['ruta'])->findAll();
        }
    
        if (empty($lista)) {
            return $this->respond(['msg' => 'No se encontraron rutas para el usuario especificado', 'status' => 'error']);
        }
    
        return $this->respond(['data' => $lista, 'status' => 'ok']);
    }

    public function actualizar_rq()
    {
        $gestion = $this->request->getPost('gestion');
        $placa = $this->request->getPost('placa');
        $rq = $this->request->getPost('rq');
        $resposable = $this->request->getPost('responsable');

        $ubicado = $this->request->getPost('ubicado');
        $capturado = $this->request->getPost('capturado');
        $observaciones = $this->request->getPost('observaciones');

        $rutaz = $this->request->getPost('ruta');
        $prioridad = $this->request->getPost('prioridad');

        if (!$placa || !$rq) {
            return $this->respond(['msg' => 'Placa y RQ son requeridos', 'status' => 'error']);
        }

        $model = model('InfoRutasModel');
        $ruta = $model->where('placa', $placa)->first();

        if (!$ruta) {
            return $this->respond(['msg' => 'No se encontró la ruta con esa placa', 'status' => 'error']);
        }

        $updateData = [
            'gestion' => $gestion,
            'rq' => $rq,
            'update_at' => date('Y-m-d H:i:s'),
            'responsable' => $resposable,
            'ubicado' => $ubicado,
            'capturado' => $capturado,
            'observaciones' => $observaciones,
            'ruta' => $rutaz,
            'prioridad' => $prioridad
        ];

        if ($model->update($ruta['id'], $updateData)) {
            return $this->respond(['msg' => 'Actualización exitosa', 'status' => 'ok']);
        } else {
            return $this->respond(['msg' => 'Error al actualizar el registro', 'status' => 'error']);
        }
    }

    public function insertarRuta()
    {
        $data = $this->request->getPost();

        // Validar que todos los campos requeridos estén presentes
        $requiredFields = ['gestion', 'placa', 'marca', 'modelo', 'color', 'distrito', 'domicilio', 'ccc2', 'rq', 'empresa', 'latitud', 'longitud', 'responsable', 'ubicado', 'capturado', 'observaciones', 'ruta', 'prioridad'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->respond(['msg' => "El campo $field es requerido", 'status' => 'error']);
            }
        }

        // Verificar si la placa ya existe
        $model = model('InfoRutasModel');
        $existingRecord = $model->where('placa', $data['placa'])->first();
        if ($existingRecord) {
            return $this->respond(['msg' => 'La placa ya existe en el registro', 'status' => 'error']);
        }

        // Agregar la fecha y hora de creación
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['update_at'] = date('Y-m-d H:i:s'); // También puede ser conveniente inicializar update_at aquí

        if ($model->insert($data)) {
            return $this->respond(['msg' => 'Registro insertado exitosamente', 'status' => 'ok']);
        } else {
            return $this->respond(['msg' => 'Error al insertar el registro', 'status' => 'error']);
        }
    }

    // public function insertarRuta()
    // {
    //     $data = $this->request->getPost();
    
    //     // Debugging: Log the received data
    //     log_message('info', 'Datos recibidos: ' . json_encode($data));
    
    //     // Validar que todos los campos requeridos estén presentes
    //     $requiredFields = ['gestion', 'placa', 'marca', 'modelo', 'color', 'distrito', 'domicilio', 'ccc2', 'rq', 'empresa', 'latitud', 'longitud', 'responsable', 'ubicado', 'capturado', 'observaciones', 'ruta', 'prioridad'];
    //     foreach ($requiredFields as $field) {
    //         if (!isset($data[$field])) {
    //             return $this->respond(['msg' => "El campo $field es requerido", 'status' => 'error'], 400);
    //         }
    //     }
    
    //     // // Validar que ruta sea un número
    //     // if (!is_numeric($data['ruta'])) {
    //     //     return $this->respond(['msg' => 'El campo ruta debe ser un número', 'status' => 'error'], 400);
    //     // }
    
    //     // // Validar que prioridad sea "alta" o "baja"
    //     // if (!in_array(strtolower($data['prioridad']), ['alta', 'baja'])) {
    //     //     return $this->respond(['msg' => 'El campo prioridad debe ser "alta" o "baja"', 'status' => 'error'], 400);
    //     // }
    
    //     // Verificar si la placa ya existe
    //     $model = model('InfoRutasModel');
    //     $existingRecord = $model->where('placa', $data['placa'])->first();
    //     if ($existingRecord) {
    //         return $this->respond(['msg' => 'La placa ya existe en el registro', 'status' => 'error'], 400);
    //     }
    
    //     // Agregar la fecha y hora de creación
    //     $data['created_at'] = date('Y-m-d H:i:s');
    //     $data['update_at'] = date('Y-m-d H:i:s'); // También puede ser conveniente inicializar update_at aquí
    
    //     if ($model->insert($data)) {
    //         return $this->respond(['msg' => 'Registro insertado exitosamente', 'status' => 'ok']);
    //     } else {
    //         return $this->respond(['msg' => 'Error al insertar el registro', 'status' => 'error'], 500);
    //     }
    // }
    
    
    


    public function listaRutasPlacas()
    {
        $model = model('InfoRutasModel');
        $lista = $model->select("placa")->findAll();
        return $this->respond($lista);
    }

    public function getRutasPorUsuario($idUsuario, $rolUsuario, $rutaUsuario)
    {
        $usuarioModel = model('UsuariosModel');
        $usuario = $usuarioModel->find($idUsuario);
    
        if (!$usuario) {
            log_message('debug', 'Usuario no encontrado');
            return $this->respond(['msg' => 'Usuario no encontrado', 'status' => 'error']);
        }
    
        if (!isset($usuario['rol']) || !isset($usuario['ruta'])) {
            log_message('debug', 'Usuario no tiene rol o ruta asignada');
            return $this->respond(['msg' => 'Usuario no tiene rol o ruta asignada', 'status' => 'error']);
        }
    
        $infoRutasModel = model('InfoRutasModel');
    
        if ($rolUsuario == 1) {
            log_message('debug', 'Admin viendo todas las rutas');
            $result = $infoRutasModel->findAll();
        } elseif ($rolUsuario == 2) {
            log_message('debug', 'Motorizado viendo sus rutas: ' . $rutaUsuario);
            $result = $infoRutasModel->where('ruta', $rutaUsuario)->findAll();
            log_message('debug', 'Consulta ejecutada: ' . $infoRutasModel->getLastQuery());
        } else {
            log_message('debug', 'Rol no autorizado');
            return $this->respond(['msg' => 'Rol no autorizado', 'status' => 'error']);
        }
    
        if (empty($result)) {
            log_message('debug', 'No se encontraron rutas para el usuario especificado');
            return $this->respond(['msg' => 'No se encontraron rutas para el usuario especificado', 'status' => 'error']);
        }
    
        log_message('debug', 'Rutas obtenidas: ' . json_encode($result));
        return $this->respond(['data' => $result, 'status' => 'ok']);
    }
    
    
    
    
    
    
    
    
}
