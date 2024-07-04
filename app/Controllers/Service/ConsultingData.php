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

        // $data = $this->db->query("exec sp_Rep_AlmacenSalidas_MTC
        // @Empresa  = '$var1',  
        // @Localidad ='$var2',
        // @Almacen ='$var3',     
        // @transaccion ='$var4',
        //   @Entidad ='$var5',
        //   @OPSerie ='$var6',    
        //   @OPNumero ='$var7',    
        //   @Linea ='$var8',       
        //   @Producto ='$var9',    
        //   @FechaI='$var10',      
        //   @FechaF='$var11'");

        $data = $this->db->query("SELECT C.Anexo,A.Descripcion As AnexoDescripcion,A.Direccion,C.OP_Numero_Doc, IH.imeiExtrae, IH.marca , IH.modelo ,IH.codHomologacion
        FROM TransDet D Inner Join TransCab      C On D.Id_Transaccion=C.Id_Transaccion
                         Left Join Transacciones T On C.Empresa=T.Empresa And C.TipoTransaccion=T.TipoTransaccion And C.Transaccion=T.Transaccion
                         Left Join Anexos        A On C.Id_Anexo=A.Id_Anexo
                         Left Join Productos     P On D.Empresa=P.Empresa And D.Producto=P.Producto
                         Left Join Almacen       U On D.Empresa=U.Empresa And C.Localidad=U.Localidad and D.Almacen=U.Almacen
                         Left Join Lineas        L On P.Empresa=L.Empresa And P.Linea=L.Linea
                         INNER JOIN InfoImeiHomologacion IH On SUBSTRING(C.OP_Numero_Doc, 1,4) = IH.imeiExtrae
       WHERE D.Empresa='20525107915' 
         And C.TipoTransaccion='2'
         And D.TipoRegistro='M'
         And C.Serie_Doc in ('001','003')
         And Case When '01'=''   Then '01'   else C.Localidad end='01'
         And Case When '001'=''     Then '001'     else D.Almacen end='001'
         And Case When ''='' Then '' else C.Transaccion end=''
         And Case When ''=''     Then ''     else C.Anexo end=''
         And Case When ''=''     Then ''     else C.OP_Serie_Doc end=''
         And Case When ''=''    Then ''     else C.OP_Numero_Doc end=''
         And Case When '001'=''       Then '001'       else P.Linea end='001'
         And Case When ''=''    Then ''    else D.Producto end=''
         And Fecha Between 45413-2 And 45443-2
         AND (A.Direccion is NULL or A.Direccion = '')");

        $respuestaBSOFT = $data->getResultArray();

        $resultFinal = [];
        $contador = 0;

        $modelOnyx = model('ConsultingDataOnyxModel');

        foreach ($respuestaBSOFT as $key => $value) {

            $contador++;

            $resultadoParams = [
                'nombre_equipo' => $value['modelo'],
                'marca' => $value['marca'],
                'modelo' => $value['modelo'],
                'nserie' => $value['OP_Numero_Doc'],
                'codHomologacion' => $value['codHomologacion'],
                'comprador' => $value['AnexoDescripcion'],
                'documento' => $value['Anexo'],
            ];

            $resultadoParams['nro'] = $contador;

            //CONSULTA AL ONYX

            if (empty($value['Direccion'])) {
                $dataFinalOnyx = $modelOnyx->where("identdoc", $value['Anexo'])->first();

                if (empty($dataFinalOnyx)) {
                    $resultadoParams['direccion'] = 'SIN DIRECCIÓN';
                } else {
                    $resultadoParams['direccion'] = $dataFinalOnyx['addressmain'];
                }
            } else {
                $resultadoParams['direccion'] = $value['Direccion'];
            }

            $resultFinal[] = $resultadoParams;
        }

        
        return $this->respond($respuestaBSOFT, 200);
    }
}
