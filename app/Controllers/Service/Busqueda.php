<?php

namespace App\Controllers\Service;

use App\Models\InfoRutasModel;
use CodeIgniter\RESTful\ResourceController;
use App\Models\RutasDetalleModel;



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
        $data = $modelParaPlaca->where(['placa' => $placa, 'estado' => '1'])->first();

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

    // public function listaRutas($idUsuario)
    // {
    //     $modelUsuario = model('UsuariosModel');
    //     $usuario = $modelUsuario->find($idUsuario);

    //     if (!$usuario) {
    //         return $this->respond(['msg' => 'Usuario no encontrado', 'status' => 'error'], 404);
    //     }

    //     $rutaUsuario = $usuario['ruta'];

    //     $infoRutasModel = model('InfoRutasModel');
    //     $lista = $infoRutasModel->where('ruta', $rutaUsuario)->findAll();

    //     if (empty($lista)) {
    //         return $this->respond(['msg' => 'No se encontraron rutas para el usuario especificado', 'status' => 'error'], 404);
    //     }

    //     return $this->respond(['data' => $lista, 'status' => 'ok']);
    // }

    public function listaRutas($idUsuario)
    {
        $modelUsuario = model('UsuariosModel');
        $usuario = $modelUsuario->find($idUsuario);

        if (!$usuario) {
            return $this->respond(['msg' => 'Usuario no encontrado', 'status' => 'error'], 404);
        }

        $rolUsuario = $usuario['rol'];
        $rutaUsuario = $usuario['ruta'];

        $infoRutasModel = model('InfoRutasModel');

        // Verificar el rol del usuario
        if ($rolUsuario == 1) {
            // Administrador (rol 1) puede ver todas las rutas, sin importar si tiene ruta asignada o no
            $lista = $infoRutasModel->findAll();
        } elseif ($rolUsuario == 2) {
            // Motorizado (rol 2) solo puede ver las rutas si tiene una asignada
            if (empty($rutaUsuario)) {
                return $this->respond(['msg' => 'Usuario no tiene una ruta asignada', 'status' => 'error'], 403);
            }
            $lista = $infoRutasModel->where('ruta', $rutaUsuario)->findAll();
        } else {
            return $this->respond(['msg' => 'Rol no autorizado', 'status' => 'error'], 403);
        }

        if (empty($lista)) {
            return $this->respond(['msg' => 'No se encontraron rutas para el usuario especificado', 'status' => 'error'], 404);
        }

        return $this->respond(['data' => $lista, 'status' => 'ok']);
    }

    // public function listaRutasDetalle($idUsuario)
    // {
    //     $modelRutasDetalle = model('RutasDetalleModel');

    //     // Unir las tablas rutas_detalle e infoRutas
    //     $rutasDetalle = $modelRutasDetalle->select('rutas_detalle.*, infoRutas.gestion')
    //                                       ->join('infoRutas', 'infoRutas.id = rutas_detalle.idRuta')
    //                                       ->join('usuariosIncautaciones', 'usuariosIncautaciones.ruta = rutas_detalle.ruta')
    //                                       ->where('usuariosIncautaciones.id', $idUsuario) // Filtrar por idUsuario en la tabla de usuarios
    //                                       ->findAll();

    //     if (empty($rutasDetalle)) {
    //         return $this->respond(['msg' => 'No se encontraron rutas', 'status' => 'error'], 404);
    //     }

    //     return $this->respond(['data' => $rutasDetalle, 'status' => 'ok']);
    // }

    // lista ruttasv2
    // public function getListaPlacasConDirecciones()
    // {
    //     $modelRutasDetalle = model('RutasDetalleModel');

    //     $arr = ['gestion !=' => 'SI', 'capturado !=' => 'si'];
    //     // Obtener todas las placas, distritos y domicilios desde rutas_detalle
    //     $rutasDetalle = $modelRutasDetalle->select('rutas_detalle.id, rutas_detalle.placa, rutas_detalle.distrito, rutas_detalle.domicilio, rutas_detalle.latitud, rutas_detalle.longitud')
    //         ->join('infoRutas', 'infoRutas.id = rutas_detalle.idRuta', 'inner')
    //         ->where($arr)
    //         ->findAll();

    //     if (empty($rutasDetalle)) {
    //         return $this->respond(['msg' => 'No se encontraron rutas', 'status' => 'error'], 404);
    //     }

    //     // Concatenar distrito y domicilio para la dirección
    //     foreach ($rutasDetalle as &$ruta) {
    //         $ruta['direccion'] = $ruta['distrito'] . ', ' . $ruta['domicilio'];
    //     }

    //     return $this->respond(['data' => $rutasDetalle, 'status' => 'ok']);
    // }

    public function getListaPlacasConDirecciones($idUsuario)
    {
        $modelRutasDetalle = model('RutasDetalleModel');
        $modelUsuario = model('UsuariosModel');

        // Obtener la información del usuario
        $usuario = $modelUsuario->find($idUsuario);

        if (!$usuario) {
            return $this->respond(['msg' => 'Usuario no encontrado', 'status' => 'error'], 404);
        }

        $rolUsuario = $usuario['rol'];
        $rutaUsuario = $usuario['ruta'];

        if (empty($rutaUsuario)) {
            $arr = ['infoRutas.gestion !=' => 'SI', 'infoRutas.capturado !=' => 'si'];
        } else {
            $arr = ['infoRutas.gestion !=' => 'SI', 'infoRutas.capturado !=' => 'si', 'rutas_detalle.ruta' => $rutaUsuario];
        }

        $arr = ['gestion !=' => 'SI', 'capturado !=' => 'si'];
        // Obtener todas las placas, distritos y domicilios desde rutas_detalle
        $rutasDetalleQuery = $modelRutasDetalle->select('rutas_detalle.id, rutas_detalle.placa, rutas_detalle.distrito, rutas_detalle.domicilio, rutas_detalle.latitud, rutas_detalle.longitud, concat(rutas_detalle.domicilio, " ", rutas_detalle.distrito) AS direccion')
            ->join('infoRutas', 'infoRutas.id = rutas_detalle.idRuta', 'inner')
            ->where($arr)
            ->findAll();

        // // Condición para rol = 1 (Administrador)
        // if ($rolUsuario == 1) {
        //     // Administrador sin una ruta asignada o con cualquier ruta asignada ve todos los puntos
        //     $rutasDetalle = $rutasDetalleQuery->findAll();
        // }
        // // Condición para rol = 2 (Usuario con Ruta Específica)
        // elseif ($rolUsuario == 2) {
        //     // Verificar que el usuario tenga una ruta asignada
        //     if (empty($rutaUsuario)) {
        //         return $this->respond(['msg' => 'Usuario no tiene una ruta asignada', 'status' => 'error'], 403);
        //     }

        //     // Filtrar solo por la ruta asignada
        //     $rutasDetalle = $rutasDetalleQuery->where('rutas_detalle.ruta', $rutaUsuario)->findAll();
        // } else {
        //     // En caso de rol no identificado
        //     return $this->respond(['msg' => 'Rol no autorizado', 'status' => 'error'], 403);
        // }


        if (empty($rutasDetalle)) {
            return $this->respond(['msg' => 'No se encontraron rutas', 'status' => 'error'], 404);
        }

        // Concatenar distrito y domicilio para la dirección
        foreach ($rutasDetalle as &$ruta) {
            $ruta['direccion'] = $ruta['distrito'] . ', ' . $ruta['domicilio'];
        }

        return $this->respond(['data' => $rutasDetalle, 'status' => 'ok']);
    }

    //MAPA - ruttasv2
    // public function listaRutasDetalle($idUsuario)
    // {
    //     $modelRutasDetalle = model('RutasDetalleModel');
    //     $modelUsuario = model('UsuariosModel');

    //     // Obtener la información del usuario
    //     $usuario = $modelUsuario->find($idUsuario);

    //     if (!$usuario) {
    //         return $this->respond(['msg' => 'Usuario no encontrado', 'status' => 'error'], 404);
    //     }

    //     $rolUsuario = $usuario['rol'];
    //     $rutaUsuario = $usuario['ruta'];

    //     // Inicializar la consulta
    //     $rutasDetalleQuery = $modelRutasDetalle->select('rutas_detalle.*, infoRutas.gestion,rutas_detalle.id as idRutaDetalle')
    //         ->join('infoRutas', 'infoRutas.id = rutas_detalle.idRuta');

    //     // Condición para rol = 1 (Administrador)
    //     if ($rolUsuario == 1) {
    //         // Administrador sin una ruta asignada o con cualquier ruta asignada ve todos los puntos
    //         $rutasDetalle = $rutasDetalleQuery->findAll();
    //     }
    //     // Condición para rol = 2 (Usuario con Ruta Específica)
    //     elseif ($rolUsuario == 2) {
    //         // Verificar que el usuario tenga una ruta asignada
    //         if (empty($rutaUsuario)) {
    //             return $this->respond(['msg' => 'Usuario no tiene una ruta asignada', 'status' => 'error'], 403);
    //         }

    //         // Filtrar solo por la ruta asignada
    //         $rutasDetalle = $rutasDetalleQuery->where('rutas_detalle.ruta', $rutaUsuario)->findAll();
    //     } else {
    //         // En caso de rol no identificado
    //         return $this->respond(['msg' => 'Rol no autorizado', 'status' => 'error'], 403);
    //     }

    //     if (empty($rutasDetalle)) {
    //         return $this->respond(['msg' => 'No se encontraron rutas', 'status' => 'error'], 404);
    //     }

    //     return $this->respond(['data' => $rutasDetalle, 'status' => 'ok']);
    // }


    
    public function listaRutasDetalle($idUsuario)
    {
        $modelRutasDetalle = model('RutasDetalleModel');
        $modelUsuario = model('UsuariosModel');

        // Obtener la información del usuario
        $usuario = $modelUsuario->find($idUsuario);

        if (!$usuario) {
            return $this->respond(['msg' => 'Usuario no encontrado', 'status' => 'error'], 404);
        }

        $rolUsuario = $usuario['rol'];
        $rutaUsuario = $usuario['ruta'];

        if (empty($rutaUsuario) || $rutaUsuario == NULL) {
            $arr = "(infoRutas.capturado!= 'si' OR infoRutas.capturado IS NULL)";
        } else {
            $arr = "(infoRutas.capturado!= 'si' OR infoRutas.capturado IS NULL) AND rutas_detalle.ruta ='$rutaUsuario'";
        }

        // $arr = "(infoRutas.capturado!= 'si' OR infoRutas.capturado IS NULL) AND infoRutas.gestion != 'SI'";
        // Obtener todas las placas, distritos y domicilios desde rutas_detalle
        $rutasDetalleQuery = $modelRutasDetalle->select('rutas_detalle.id, rutas_detalle.placa, rutas_detalle.distrito, rutas_detalle.domicilio, rutas_detalle.latitud, rutas_detalle.longitud, concat(rutas_detalle.domicilio, " ", rutas_detalle.distrito) AS direccion')
            ->join('infoRutas', 'infoRutas.id = rutas_detalle.idRuta', 'inner')
            ->where($arr)
            ->findAll();
            
        if (empty($rutasDetalleQuery)) {
            return $this->respond(['msg' => 'No se encontraron rutas', 'status' => 'error'], 404);
        }

        return $this->respond(['data' => $rutasDetalleQuery, 'status' => 'ok']);
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

        $latitud = $this->request->getPost('latitud');
        $longitud = $this->request->getPost('longitud');

        $idDetalle = $this->request->getPost('idDetalle');


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
            'observaciones' => $observaciones,
            'ruta' => $rutaz,
            'prioridad' => $prioridad
        ];

        if ($model->update($ruta['id'], $updateData)) {

            //de rutas_seguimiento
            $registerData = [
                'id_info_ruta' => $ruta['id'],
                'id_ruta_detalle' => $idDetalle,
                'gestion' => $gestion,
                'rq' => $rq,
                'responsable' => $resposable,
                'register_at' => date('Y-m-d H:i:s'),
                'ubicado' => $ubicado,
                'capturado' => $capturado,
                'observaciones' => $observaciones,
                'ruta' => $rutaz,
                'prioridad' => $prioridad,
                'latitud' => $latitud,
                'longitud' => $longitud
            ];
            $modelSeguimiento = model('RutasSeguimientoModel');
            $modelSeguimiento->save($registerData);

            return $this->respond(['msg' => 'Actualización exitosa', 'status' => 'ok']);
        } else {
            return $this->respond(['msg' => 'Error al actualizar el registro', 'status' => 'error'], 500);
        }
    }

    // public function insertarRuta()
    // {
    //     $data = $this->request->getPost();

    //     // Validar que todos los campos requeridos estén presentes
    //     $requiredFields = ['gestion', 'placa', 'marca', 'modelo', 'color', 'distrito', 'domicilio', 'ccc2', 'rq', 'empresa', 'latitud', 'longitud', 'responsable', 'ubicado', 'capturado', 'observaciones', 'ruta', 'prioridad'];
    //     foreach ($requiredFields as $field) {
    //         if (!isset($data[$field])) {
    //             return $this->respond(['msg' => "El campo $field es requerido", 'status' => 'error'], 400);
    //         }
    //     }

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

    public function insertarRuta()
    {
        $data = $this->request->getPost();

        // Validar que todos los campos requeridos estén presentes
        $requiredFields = ['gestion', 'placa', 'marca', 'modelo', 'color', 'distrito', 'domicilio', 'ccc2', 'rq', 'empresa', 'latitud', 'longitud', 'responsable', 'ubicado', 'capturado', 'observaciones', 'ruta', 'prioridad'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->respond(['msg' => "El campo $field es requerido", 'status' => 'error'], 400);
            }
        }

        $modelInfoRutas = model('InfoRutasModel');

        // Verificar si la placa ya existe en infoRutas
        $existingRecord = $modelInfoRutas->where('placa', $data['placa'])->first();

        if ($existingRecord) {
            // Si existe, usamos su ID para insertar en rutas_detalle
            $idRuta = $existingRecord['id'];
        } else {
            // Si no existe, insertamos un nuevo registro en infoRutas
            $dataInfoRutas = [
                'gestion' => $data['gestion'],
                'placa' => $data['placa'],
                'marca' => $data['marca'],
                'modelo' => $data['modelo'],
                'color' => $data['color'],
                'rq' => $data['rq'],
                'empresa' => $data['empresa'],
                'update_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'observaciones' => $data['observaciones'],
                'prioridad' => $data['prioridad']
            ];

            // Insertar en infoRutas
            if (!$modelInfoRutas->insert($dataInfoRutas)) {
                return $this->respond(['msg' => 'Error al insertar en infoRutas', 'status' => 'error'], 500);
            }

            // Obtener el ID del registro insertado
            $idRuta = $modelInfoRutas->getInsertID();
        }

        // Preparar los datos para rutas_detalle
        $dataRutasDetalle = [
            'placa' => $data['placa'],
            'distrito' => $data['distrito'],
            'domicilio' => $data['domicilio'],
            'link' => $data['link'] ?? '', // Asegurarse de que link no sea nulo
            'ccc2' => $data['ccc2'],
            'update_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'latitud' => $data['latitud'],
            'longitud' => $data['longitud'],
            'ruta' => $data['ruta'],
            'prioridad' => $data['prioridad'],
            'idRuta' => $idRuta // Usar el ID de infoRutas, existente o nuevo
        ];

        $modelRutasDetalle = model('RutasDetalleModel');

        // Insertar en rutas_detalle
        if (!$modelRutasDetalle->insert($dataRutasDetalle)) {
            return $this->respond(['msg' => 'Error al insertar en rutas_detalle', 'status' => 'error'], 500);
        }

        return $this->respond(['msg' => 'Registro insertado exitosamente', 'status' => 'ok']);
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



    // public function getDetalleVehiculoDireccion($idDetalle)
    // {
    //     $modelRutasDetalle = model('RutasDetalleModel');

    //     $detalle = $modelRutasDetalle->select('rutas_detalle.*, infoRutas.*')
    //                                  ->join('infoRutas', 'infoRutas.id = rutas_detalle.idRuta')
    //                                  ->where('rutas_detalle.id', $idDetalle)
    //                                  ->first();

    //     if (!$detalle) {
    //         return $this->respond(['msg' => 'No se encontró la información.', 'status' => 'error'], 404);
    //     }

    //     return $this->respond(['data' => $detalle, 'status' => 'ok']);
    // }

    public function detalleVehiculoDireccion($placa = null)
    {
        $idDetalle = $this->request->getGet('id');

        if (is_null($placa) || is_null($idDetalle)) {
            return $this->respond(['msg' => 'Placa o ID no proporcionado', 'status' => 'error'], 400);
        }

        $modelInfoRutas = model('InfoRutasModel');
        $modelRutasDetalle = model('RutasDetalleModel');

        // Obtener información de infoRutas
        $infoRutas = $modelInfoRutas->where('placa', $placa)->first();

        // Obtener información de rutas_detalle
        $rutaDetalle = $modelRutasDetalle->where('id', $idDetalle)->first();

        if (!$infoRutas || !$rutaDetalle) {
            return $this->respond(['msg' => 'Información no encontrada', 'status' => 'error'], 404);
        }

        // Combinar la información en una sola respuesta
        $resultado = array_merge($infoRutas, $rutaDetalle);

        return $this->respond(['data' => $resultado, 'status' => 'ok']);
    }



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
            return $this->respond(['msg' => 'Usuario no encontrado', 'status' => 'error'], 404);
        }

        if (!isset($usuario['rol']) || !isset($usuario['ruta'])) {
            log_message('debug', 'Usuario no tiene rol o ruta asignada');
            return $this->respond(['msg' => 'Usuario no tiene rol o ruta asignada', 'status' => 'error'], 400);
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
            return $this->respond(['msg' => 'Rol no autorizado', 'status' => 'error'], 403);
        }

        if (empty($result)) {
            log_message('debug', 'No se encontraron rutas para el usuario especificado');
            return $this->respond(['msg' => 'No se encontraron rutas para el usuario especificado', 'status' => 'error'], 404);
        }

        log_message('debug', 'Rutas obtenidas: ' . json_encode($result));
        return $this->respond(['data' => $result, 'status' => 'ok']);
    }
}
