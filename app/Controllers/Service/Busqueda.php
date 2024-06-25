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

        //$data = $this->model->where('placa', $placa)->first();
        $data = $this->model->searchByPlaca($placa);

        if (!$data) {
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

        // $modelBusq = model('BusquedaModel');

        //$update = $this->model->update($data,'placa="AMX636"');

        $this->builder->where('placa', $placa);

        $this->builder->update($data);

        // if (!$update) {
        //     return $this->respond(['msg'=>'Los campos rq y/o ubicado no se actualizaron', 'status'=>'error']);
        // } 

        return $this->respond(['msg' => 'Actualización exitosa', 'status' => 'ok']);

        // return $this->respond($data);

    }

    public function listaRutas()
    {
        $model = model('InfoRutasModel');
        $lista = $model->findAll();
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

        if (!$placa || !$rq) {
            return $this->respond(['msg' => 'Placa y RQ son requeridos', 'status' => 'error'], 400);
        }

        $model = model('InfoRutasModel');
        $ruta = $model->where('placa', $placa)->first();

        if (!$ruta) {
            return $this->respond(['msg' => 'No se encontró la ruta con esa placa', 'status' => 'error'], 404);
        }

        $updateData = [
            'gestion' => $gestion,
            'rq' => $rq,
            'update_at' => date('Y-m-d H:i:s'),
            'responsable' => $resposable,
            'ubicado' => $ubicado,
            'capturado' => $capturado,
            'observaciones' => $observaciones
        ];

        if ($model->update($ruta['id'], $updateData)) {
            return $this->respond(['msg' => 'Actualización exitosa', 'status' => 'ok']);
        } else {
            return $this->respond(['msg' => 'Error al actualizar el registro', 'status' => 'error'], 500);
        }
    }

    public function insertarRuta()
    {
        $data = $this->request->getPost();
    
        // Validar que todos los campos requeridos estén presentes
        $requiredFields = ['gestion', 'placa', 'marca', 'modelo', 'color', 'distrito', 'domicilio', 'ccc2', 'rq', 'empresa', 'latitud', 'longitud', 'responsable', 'ubicado', 'capturado', 'observaciones'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->respond(['msg' => "El campo $field es requerido", 'status' => 'error'], 400);
            }
        }
    
        // Verificar si la placa ya existe
        $model = model('InfoRutasModel');
        $existingRecord = $model->where('placa', $data['placa'])->first();
        if ($existingRecord) {
            return $this->respond(['msg' => 'La placa ya existe en el registro', 'status' => 'error'], 400);
        }
    
        // Agregar la fecha y hora de creación
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['update_at'] = date('Y-m-d H:i:s'); // También puede ser conveniente inicializar update_at aquí
    
        if ($model->insert($data)) {
            return $this->respond(['msg' => 'Registro insertado exitosamente', 'status' => 'ok']);
        } else {
            return $this->respond(['msg' => 'Error al insertar el registro', 'status' => 'error'], 500);
        }
    }

    public function listaRutasPlacas()
    {
        $model = model('InfoRutasModel');
        $lista = $model->select("placa")->findAll();
        return $this->respond($lista);
    }
    
}
