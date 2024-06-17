<?php

namespace App\Controllers\Service;

use CodeIgniter\RESTful\ResourceController;

class Usuarios extends ResourceController
{
    public function index()
    {

        $model =  model('UsuariosModel');
        $lista = $model->findAll();

        return $this->respond($lista);
    }

    public function show($id = null)
    {

        $model =  model('UsuariosModel');
        $lista = $model->where('id_usuario', $id)->first();

        return $this->respond($lista);
    }

    public function update($id = null)
    {

        $data = [
            'id_usuario' => $id,
            'password' => $this->request->getPost('password')
        ];

        $model =  model('UsuariosModel');

        if (!$model->save($data)) {
            return $this->respond('Hubo un error: ' . $model);
        }

        $lista = $model->where('id_usuario', $id)->first();

        return $this->respond($lista);
    }

    public function signin()
    {

        $user = $this->request->getPost('user');
        $pass = $this->request->getPost('password');

        $model =  model('UsuariosModel');

        $searchUser = $model->where('correo', $user)->first();

        if (empty($searchUser)) {
            return $this->respond(['msg'=>'El usuario no se encuentra registrado', 'status'=>'error']);
        }

        if (!password_verify($pass,$searchUser['contra'])) {
            return $this->respond(['msg'=>'La contraseña es incorrecta', 'status'=>'error']);
        }

        return $this->respond(['msg'=>'Acceso correcto', 'status'=>'ok', 'usuario'=>$searchUser]);
    }
}
