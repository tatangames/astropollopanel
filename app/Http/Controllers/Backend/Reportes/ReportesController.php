<?php

namespace App\Http\Controllers\Backend\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Servicios;
use Illuminate\Http\Request;
use Carbon\Carbon;


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


        //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf']);

       // $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Ordenes Calificadas');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $tabla = "<h1>Ejemplo</h1>";

        $stylesheet = file_get_contents('css/reporteestilo1.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();

    }





}
