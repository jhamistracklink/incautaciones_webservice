<?php

namespace App\Controllers\Service;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Model;
use CodeIgniter\RESTful\ResourceController;

class ConsultingData extends ResourceController
{
    public $db;

    public function __construct()
    {
        $this->db      = \Config\Database::connect();
        // $this->builder = $db->table('infoIncautaciones');
    }

    public function index()
    {
        // $model = model('ConsultingDataModel');

        // $F1 = $this->request->getPost('f1');
        // $F2 = $this->request->getPost('f2');

        $F1 = $_GET['fechaIni'];
        $F2 = $_GET['fechaFin'];

        // Consulta para convertir la fecha F1
        $sqlF1 = "SELECT CONVERT(float, CONVERT(datetime, ?)) + 2 AS fechaInicio";
        $dataF1 = $this->db->query($sqlF1, [$F1]);
        $respuestaF1 = $dataF1->getRow();

        // Consulta para convertir la fecha F2
        $sqlF2 = "SELECT CONVERT(float, CONVERT(datetime, ?)) + 2 AS fechaFin";
        $dataF2 = $this->db->query($sqlF2, [$F2]);
        $respuestaF2 = $dataF2->getRow();


        $var1 = '20525107915';
        $var2 = '01';
        $var3 = '001';
        $var4 = '';
        $var5 = '';
        $var6 = '';
        $var7 = '';
        $var8 = '001';
        $var9 = '';
        $var10 = $respuestaF1->fechaInicio;
        $var11 = $respuestaF2->fechaFin;

        // $this->model->sp($var1, $var2, $var3, $var4, $var5, $var6, $var7, $var8, $var9, $var10);

        // $data = $this->execute_sp($var1, $var2, $var3, $var4, $var5, $var6, $var7, $var8, $var9, $var10, $var11);

        $data = $this->db->query("exec sp_Rep_AlmacenSalidas_MTC
        @Empresa  = '$var1',  
        @Localidad ='$var2',
        @Almacen ='$var3',     
        @transaccion ='$var4',
          @Entidad ='$var5',
          @OPSerie ='$var6',    
          @OPNumero ='$var7',    
          @Linea ='$var8',       
          @Producto ='$var9',    
          @FechaI='$var10',      
          @FechaF='$var11'");

        $respuestaBSOFT = $data->getResultArray();

        return $this->respond($respuestaBSOFT, 200);
    }
}
