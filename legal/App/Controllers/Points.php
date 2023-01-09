<?php

namespace App\Controllers;

use \Core\View;
use App\Models\PuntajesDb;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class points extends \Core\Controller
{
    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('/Points/index.html', [
            'tabla' => 1
        ]);
    }    

    /**
     * Consulta en puntajes para obtener el puntaje 
     * General
     *
     * @return void
     */
    public function getGeneral()
    {

        $arrData = PuntajesDb::selectGeneral();

        for ($i = 0; $i < count($arrData); $i++) {

            $arrData[$i]['posicion'] = $i + 1;

            $arrData[$i]['detalle'] = '<a class="btn btn-info btn-sm" id="btn-tod" href="' . $arrData[$i]['id'] . '/club"><i class="fas fa-archive"></i> Ver </a>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Consulta en puntajes para obtener el puntaje 
     * General
     *
     * @return void
     */
    public function club()
    {
        $cod = array_key_exists('id', $this->route_params) ? json_encode($this->route_params['id'], JSON_NUMERIC_CHECK) : '';       

        $club = PuntajesDb::selectClub($cod);
        $_SESSION['puntaje'] = $club['club'];

        if ($seg = array_key_exists('idp', $this->route_params) ? json_encode($this->route_params['idp'], JSON_NUMERIC_CHECK) : FALSE) {
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/points/'.$seg.'/club');
        } else {
            View::renderTemplate('/Points/index.html', [
                'tabla' => 2
            ]);
        }       
    }

    /**
     * Consulta en puntajes para obtener el puntaje 
     * General
     *
     * @return void
     */
    public function getClub()
    {
        $arrData = PuntajesDb::selectPuntos($_SESSION['puntaje']);       

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }    

    /**
     * Consulta en puntajes para obtener el puntaje 
     * Promedio de los clubes
     *
     * @return void
     */
    public function getPromedio()
    {
        $arrData = PuntajesDb::selectPromedio();

        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['posicion'] = $i + 1;
            $this->id = PuntajesDb::idClub($arrData[$i]['club']);

            if ($this->id) {
                $cantidad = PuntajesDb::calcularNadadores($this->id['id']);
                if ($cantidad) {
                    $arrData[$i]['suma'] = round(($arrData[$i]['suma'] / ($cantidad['total'] - $cantidad['exterior']['exte'] - $cantidad['seleccion']['sel_col'] - $cantidad['inactivo']['inact'])), 2);
                } else {
                    $arrData[$i]['suma'] = 'NO hubo en la segunda';
                }
            } else {
                $arrData[$i]['suma'] = 'NO hubo';
            }

            $arrData[$i]['detalle'] = '<a class="btn btn-info btn-sm" id="btn-tod" href="' . $arrData[$i]['id'] . '/detalleprom"><i class="fas fa-archive"></i> Ver </a>';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }    

    /**
     * Consulta en puntajes para obtener el detalle
     * de puntaje Promedio
     *
     * @return void
     */
    public function detallepromAction()
    {
        $cod = array_key_exists('id', $this->route_params) ? json_encode($this->route_params['id'], JSON_NUMERIC_CHECK) : '';

        $club = PuntajesDb::selectClub($cod);
        $_SESSION['puntaje'] = $club['club'];
        $this->id = PuntajesDb::idClub($club['club']);

        if ($this->id) {
            $this->cantidad = PuntajesDb::calcularNadadores($this->id['id']);            
        } else {
            $this->cantidad = [];
        }

        if ($seg = array_key_exists('idp', $this->route_params) ? json_encode($this->route_params['idp'], JSON_NUMERIC_CHECK) : FALSE) {
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/points/'.$seg.'/detalleprom');
        } else {
            View::renderTemplate('/Points/prom.html', [            
                'cantidades' => $this->cantidad,
                'prom' => 2            
            ]);
        }

                
    }

    /**
     * Consulta en puntajes para obtener el puntaje 
     * General
     *
     * @return void
     */
    public function getDetalle()
    {

        $arrData = PuntajesDb::selectPuntosProm($_SESSION['puntaje']);

        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['prom'] = round(($arrData[$i]['suma'] / ($arrData[$i]['liga'] - $arrData[$i]['inactivos'] - $arrData[$i]['exterior'] - $arrData[$i]['seleccion'])), 2);
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }
}
