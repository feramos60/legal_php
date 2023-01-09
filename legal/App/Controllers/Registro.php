<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\TorneoDb;
use App\Models\RenovaDb;
use App\Flash;
use Mpdf\Mpdf;

/**
 * Renovacion Registro para los administrativos
 * del Club
 * 
 *
 * PHP version 7.0
 */
class Registro extends Authenticated
{
    /**
     * Before filter - called before each action method
     * Hace que se requiera loguearse para poder acceder a estos metodos
     * con $this->user se llama este metodo before
     *
     * @return void
     */
    protected function before()
    {
        parent::before();           //Llama el metodo before() de la clase Authenticated

        $this->user = Auth::getUser();
        $this->authorizationRequired(permission: 9);
    }

    /**
     * Muestrala pagina con el listado de los nadadores
     * asociados al club o todos los nadadores en el caso
     * de ser administrador
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Registro/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()            
        ]);
    }

    /**
     * Consulta la base de datos TorneoDb para listar
     * los nadadores del club del entrenador logueado.
     * El botón lleva consigo el id del nadador.
     *
     * @return void
     */
    public function getRenovacion()
    {

        $arrData = TorneoDb::selectNadadores();
        $activarReg = RenovaDb::registro();
        
        for ($i = 0; $i < count($arrData); $i++) {            
            $fch = explode("-", $arrData[$i]['fecha_nac']);
            $tfecha = $fch[0];
            /* $tfecha = $fch[2] . "-" . $fch[1] . "-" . $fch[0];
            $dias = explode("-", $tfecha, 3);
            $dias = mktime(0, 0, 0, $dias[1], $dias[0], $dias[2]);
            $edad = (int)((time() - $dias) / 31556926); */
            $ahora = date('Y');

            $edad = $ahora - $tfecha;

            $arrData[$i]['edad'] = $edad;

            $arrData[$i]['hv'] = '<a href="' . $arrData[$i]['id'] . '/updateHv" class="btn btn-info btn-sm" id="btn-sm"><i class="fas fa-address-card"></i> HV</a>';

            $actualizada = RenovaDb::hojaVida($arrData[$i]['id']);
            
            if ($activarReg AND $activarReg['status'] == 1 AND $actualizada > 0) {
                $arrData[$i]['renovar'] = '<div class="icheck-primary d-inline">
                <input type="checkbox" id="checkboxPrimary'. $i+1 .'" name="vehicle'. $i+1 .'" value="' . $arrData[$i]['id'] . '">
                <label for="checkboxPrimary'. $i+1 .'"></label></div>';
            } else {
                if ($activarReg AND $activarReg['status'] == 1) {
                    $arrData[$i]['renovar'] = 'Debe Actualizar HV';                                        
                } else {
                    $arrData[$i]['renovar'] = 'Proceso no Activo';                                       
                }               
                
            }
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Guarda los seleccionados para renovar registo
     * y luego muestra el PDF para el pago
     * 
     * @return void
     */
    public function procesarAction()
    {
        //var_dump($_POST);

        
        $this->nadadores = $_POST;
        $mensaje = 'HOla Mundo';
        $html = View::getTemplate('Registro/resumenRenovacion.html', [
            'nadadores' => $this->nadadores,                   //llama al resultado del metodo before()
            'mensaje' => $mensaje
        ]);
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    /**
     * Mustra el formulario para diligenciar
     * la hoja de vida del naadador muestra 
     * si el nadador tiene datos o en blanco 
     * si nunca la han llenado
     * 
     * @return void
     */
    public function updateHvAction()
    {
        if (array_key_exists('id', $this->route_params)) {
            $id = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);
            $this->deportista = RenovaDb::datosNadador($id);
            View::renderTemplate('Registro/hojaVida.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
                'deportista' => $this->deportista
            ]);
        } else {
            Flash::addMessage('No seleccionó un año para editar', Flash::DANGER);
            View::renderTemplate('Renovacion/index.html', [
                'user' => $this->user                   //llama al resultado del metodo before()
            ]);
        }

    }
}