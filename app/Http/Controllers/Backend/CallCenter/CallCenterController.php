<?php

namespace App\Http\Controllers\Backend\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\DireccionCliente;
use Illuminate\Http\Request;

class CallCenterController extends Controller
{


    public function indexGenerarOrden(){



        return view('backend.admin.callcenter.generarorden.vistagenerarorden');
    }

    // RECIBO EL TELEFONO, Y DIGO QUE TIENE DIRECCIONES, PARA QUE DESPUES MANDE A BUSCAR

    // ** USUARIO DE CALL CENTER SERA EL ID 1 FIJO **
    public function listaDireccionTelefono(Request $request){



    }


}
