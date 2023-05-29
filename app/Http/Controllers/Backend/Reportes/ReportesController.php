<?php

namespace App\Http\Controllers\Backend\Reportes;

use App\Http\Controllers\Controller;
use App\Models\MotoristasServicios;
use App\Models\Ordenes;
use App\Models\Servicios;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ReportesController extends Controller
{

    public function vistaReporteOrdenesCalificadas(){

        // RESTAURANTES
        $restaurantes = Servicios::orderBy('nombre')->get();


        return view('backend.admin.reportes.ordenescalificadas.vistaordenescalificadas', compact('restaurantes'));
    }


    public function pdfOrdenesCalificadas($idservicio, $desde, $hasta){

        // ESTO ES PARA HACER LA CONSULTA
        $date1 = Carbon::parse($desde)->format('Y-m-d');
        $date2 = Carbon::parse($hasta)->addDays(1)->format('Y-m-d');

        // ESTO PARA MOSTRAR EN EL REPORTE YA FORMATEADO
        $f1 = Carbon::parse($desde)->format('d-m-Y');
        $f2 = Carbon::parse($hasta)->format('d-m-Y');


        $infoServicio = Servicios::where('id', $idservicio)->first();


        // AQUI APARECEN SOLO SI EL CLIENTE COMPLETO LA CALIFICACION

        $arrayOrdenes =  DB::table('ordenes_motoristas AS om')
            ->join('ordenes AS o', 'om.id_ordenes', '=', 'o.id')
            ->select('o.id', 'o.id_servicio', 'o.fecha_orden', 'om.id_motorista', 'om.experiencia', 'om.mensaje')
            ->where('om.experiencia', '=!', null)
            ->where('o.id_servicio', $idservicio)
            ->whereBetween('o.fecha_orden', array($date1, $date2))
            ->get();

        $sumaExperiencia = 0;
        $contador = 0;
        foreach ($arrayOrdenes as $dato){
            $contador = $contador + 1;
            $sumaExperiencia = $sumaExperiencia + $dato->experiencia;

            $infoMotorista = MotoristasServicios::where('id', $dato->id_motorista)->first();
            $dato->nombremotorista = $infoMotorista->nombre;


            $dato->fecha_orden = date("h:i A d-m-Y", strtotime($dato->fecha_orden));
        }

        if($contador != 0){
            $promedio = $sumaExperiencia / $contador;
            $promedio = number_format((float)$promedio, 2, '.', ',');
        }else{
            $promedio = "";
        }



        // # de orden
        // # fecha orden
        // # motorista
        // calificacion
        // mensaje

        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf']);

       // $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Ordenes Calificadas');

        // mostrar errores
        $mpdf->showImageErrors = false;


        $tabla = "<div class='content'>
            <p id='titulo'>$infoServicio->nombre<br>
            Reporte de Ordenes Calificadas<br>
            Fecha: $f1   /   $f2 </p>
            </div>";

        $tabla .= "<p style='font-weight: bold; font-size: 18px'>Promedio: $promedio </p>";


        $tabla .= "<table width='100%' id='tablaFor'>
                <tbody>";

            $tabla .= "<tr>
                <td width='6%'># Orden</td>
                <td width='7%'>Fecha Orden</td>
                <td width='10%'>Motorista</td>
                <td width='10%'>Nota</td>
                <td width='10%'>Mensaje</td>
            </tr>";


        foreach ($arrayOrdenes as $info) {

            $tabla .= "<tr>
                    <td width='6%'>$info->id</td>
                    <td width='7%'>$info->fecha_orden</td>
                    <td width='10%'>$info->nombremotorista</td>
                    <td width='10%'>$info->experiencia</td>
                    <td width='10%'>$info->mensaje</td>
                </tr>";
        }


        $tabla .= "</tbody></table>";



        $stylesheet = file_get_contents('css/reporteestilo1.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }



    //****************

    // ORDENES ENTREGADAS POR MOTORISTAS POR RESTAURANTE Y MOTORISTA

    public function vistaReporteOrdenesEntregadas(){

        $restaurantes = Servicios::orderBy('nombre')->get();

        // se selecciona el restaurante y cargara los motoristas

        return view('backend.admin.reportes.ordenesentregadas.vistaordenesentregadas', compact('restaurantes'));
    }



    public function buscarMotoristaPorRestaurante(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        if($infoServicio = Servicios::where('id', $request->id)->first()){

            $motoristas = MotoristasServicios::where('id_servicios', $infoServicio->id)->get();

            return ['success' => 1, 'lista' => $motoristas];
        }else{
            return ['success' => 2];
        }
    }



    public function pdfOrdenesEntregadas($idmotorista, $idservicio, $desde, $hasta){


        // ESTO ES PARA HACER LA CONSULTA
        $date1 = Carbon::parse($desde)->format('Y-m-d');
        $date2 = Carbon::parse($hasta)->addDays(1)->format('Y-m-d');

        // ESTO PARA MOSTRAR EN EL REPORTE YA FORMATEADO
        $f1 = Carbon::parse($desde)->format('d-m-Y');
        $f2 = Carbon::parse($hasta)->format('d-m-Y');

        $infoServicio = Servicios::where('id', $idservicio)->first();

        $infoMoto = MotoristasServicios::where('id', $idmotorista)->first();


        $arrayOrdenes =  DB::table('ordenes_motoristas AS om')
            ->join('ordenes AS o', 'om.id_ordenes', '=', 'o.id')
            ->select('o.id', 'o.fecha_orden', 'o.estado_entregada', 'om.id_motorista', 'om.fecha')
            ->where('o.estado_entregada', 1)
            ->where('om.id_motorista', $idmotorista)
            ->whereBetween('o.fecha_orden', array($date1, $date2))
            ->get();

        $contador = 0;

        foreach ($arrayOrdenes as $dato){
            $contador = $contador + 1;

            $infoMotorista = MotoristasServicios::where('id', $dato->id_motorista)->first();
            $dato->nombremotorista = $infoMotorista->nombre;

            $dato->fecha_orden = date("h:i A d-m-Y", strtotime($dato->fecha_orden));

            $dato->fechaentrego = date("h:i A d-m-Y", strtotime($dato->fecha));
        }


        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf']);

        // $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Ordenes Entregadas');

        // mostrar errores
        $mpdf->showImageErrors = false;


        $tabla = "<div class='content'>
            <p id='titulo'>$infoServicio->nombre<br>
            Motorista: $infoMoto->nombre<br>
            Reporte de Ordenes Entregadas<br><br>
            Fecha: $f1   /   $f2 </p>
            </div>";

        $tabla .= "<p style='font-weight: bold; font-size: 18px'>Total: $contador </p>";


        $tabla .= "<table width='100%' id='tablaFor'>
                <tbody>";

        $tabla .= "<tr>
                <td width='6%'># Orden</td>
                <td width='7%'>Fecha Orden</td>
                <td width='10%'>Motorista</td>
                <td width='7%'>Fecha Entrego</td>
            </tr>";


        foreach ($arrayOrdenes as $info) {

            $tabla .= "<tr>
                    <td width='6%'>$info->id</td>
                    <td width='7%'>$info->fecha_orden</td>
                    <td width='10%'>$info->nombremotorista</td>
                    <td width='10%'>$info->fechaentrego</td>
                </tr>";
        }


        $tabla .= "</tbody></table>";



        $stylesheet = file_get_contents('css/reporteestilo1.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }




}
