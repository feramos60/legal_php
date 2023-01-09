<?php

namespace App\Controllers;

use \Core\View;
use App\Models\ReportesDb;
use App\Flash;

/**
 * Reportes controller
 *
 * PHP version 7.0
 */
class Reportes extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $year = date('Y');
        $_SESSION['inicio'] = $year.'-01-01';
        $_SESSION['fin'] = date('Y-m-d');
        $this->competencias = ReportesDb::findCompetencias();
        
        $this->ligas = ReportesDb::findLigas();

        $control = $_POST ?? false;

        

        if ($control) {
            $_SESSION['control'] = '';
            $_SESSION['genero'] = $_POST['genero'];
            $_SESSION['prueba'] = intval($_POST['prueba']);

            if ($_POST['inicio'] == '') {
                $_SESSION['inicio'] = date('Y-m-d');
            } else {
                $_SESSION['inicio'] = date('Y-m-d', strtotime($_POST['inicio']));
            }
            $_SESSION['categoria'] = Reportes::getCategoria($_POST['categoria']);

            if ($_POST['fin'] == '') {
                $_SESSION['fin'] = date('Y-m-d');
            } else {
                $_SESSION['fin'] = date('Y-m-d', strtotime($_POST['fin']));
            }

            $_SESSION['torneo'] = $_POST['torneo'] ?? false;
            $_SESSION['club_id'] = $_POST['club_id'] ?? false;
            $_SESSION['piscina'] = $_POST['piscina'] ?? false;
            $_SESSION['liga'] = $_POST['liga'] ?? false;

            if ($_POST['torneo'] ?? false) {
                $torneo = $_POST['torneo'];
            } else {
                $torneo = 'Todas';
            }

            if ($_POST['liga'] ?? false) {
                $nomliga = ReportesDb::findLiga($_POST['liga']);
                $liga = $nomliga['nombre_liga'];
            } else {
                $liga = 'Todas';
            }
            


            if (ReportesDb::findSelectEquipos()) {
                $club = ReportesDb::findSelectEquipos();
            } else {
                $club['nombre_club'] = 'Todos';
            }

            if (ReportesDb::findPrueba()) {
                $prueba = ReportesDb::findPrueba();
            } else {
                $prueba['nombre'] = '';
            }

            $arrData = ReportesDb::selectRanking();



            Flash::addMessage('<h4 class="alert-heading">Elementos de la Consulta</h4>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <p>Fecha de Inicio: ' . $_SESSION['inicio'] . '</p>            
                    <p>Categoría: ' . $_POST['categoria'] . ' años</p>                    
                    <p>Genero: ' . $_SESSION['genero'] . '</p>            
                </div>
                <div class="col-md-4">
                    <p>Fecha de Fin: ' . $_SESSION['fin'] . '</p>
                    <p>Liga: ' . $liga . '</p>
                    <p>Estilo: ' . $prueba['nombre'] . '</p>            
                </div>
                <div class="col-md-4">
                    <p>Competencia: ' . $torneo . '</p>
                    <p>Club: ' . $club['nombre_club'] . '</p>                
                    <p>Piscina: ' . $_SESSION['piscina'] . '</p>                            
                </div>
                
            </div>
            <hr>
            ', Flash::LIGHT);

            //echo ReportesDb::selectRanking();

            View::renderTemplate('Reportes/index.html', [
                'competencias' => $this->competencias,                   //llama al resultado del metodo before()
                'ligas' => $this->ligas,
                'control' => $control

            ]);
        } else {
            $_SESSION['control'] = 1;            
            View::renderTemplate('Reportes/index.html', [
                'competencias' => $this->competencias,                   //llama al resultado del metodo before()
                'ligas' => $this->ligas

            ]);
        }
    }

    /**
     * Metodo de consulta de par ala tabla
     *
     * @return void
     */
    public function getRanking()
    {          

        $arrData = ReportesDb::selectRanking();
        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['contador'] = $i + 1;
            $datos_puntos = ReportesDb::puntosFina($arrData[$i]['prueba_id']);
            $fechaTorneo = ReportesDb::fechaTorneo($arrData[$i]['identificacion'], $arrData[$i]['tiempo'], $arrData[$i]['prueba_id']);
            $arrData[$i]['fecha_torneo'] = $fechaTorneo['fecha_torneo'];
            switch ($arrData[$i]['genero']) {
                case 'F':
                    if ($arrData[$i]['piscina'] == 'LC') {
                        $record_Mundial = $datos_puntos['fina_tflc'];
                    } else {
                        $record_Mundial = $datos_puntos['fina_tfsc'];
                    }
                    break;

                case 'M':
                    if ($arrData[$i]['piscina'] == 'LC') {
                        $record_Mundial = $datos_puntos['fina_tmlc'];
                    } else {
                        $record_Mundial = $datos_puntos['fina_tmsc'];
                    }
                    break;
            }

            list($horas, $minutos, $segundos) = explode(':', $arrData[$i]['tiempo']);
            $t_segundos = ($horas * 3600) + ($minutos * 60) + $segundos;
            $finapts = round(1000 * pow(($record_Mundial / $t_segundos), 3));
            
            $arrData[$i]['puntos'] = $finapts;
            $arrData[$i]['nada'] = $arrData[$i]['nombre'] . ' ' . $arrData[$i]['apellido'];
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }


    /**
     * Metodo de consulta de par ala tabla
     *
     * @return void
     */
    public function getFiltro()
    {


        $arrData = ReportesDb::selectFiltro();
        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['contador'] = $i + 1;
            $datos_puntos = ReportesDb::puntosFina($arrData[$i]['prueba_id']);

            switch ($arrData[$i]['genero']) {
                case 'F':
                    if ($arrData[$i]['piscina'] == 'LC') {
                        $record_Mundial = $datos_puntos['fina_tflc'];
                    } else {
                        $record_Mundial = $datos_puntos['fina_tfsc'];
                    }
                    break;

                case 'M':
                    if ($arrData[$i]['piscina'] == 'LC') {
                        $record_Mundial = $datos_puntos['fina_tmlc'];
                    } else {
                        $record_Mundial = $datos_puntos['fina_tmsc'];
                    }
                    break;
            }

            list($horas, $minutos, $segundos) = explode(':', $arrData[$i]['tiempo']);
            $t_segundos = ($horas * 3600) + ($minutos * 60) + $segundos;
            $finapts = round(1000 * pow(($record_Mundial / $t_segundos), 3));

            $arrData[$i]['puntos'] = $finapts;
            $arrData[$i]['nada'] = $arrData[$i]['nombre'] . ' ' . $arrData[$i]['apellido'];
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();

        //echo ReportesDb::selectFiltro();

    }

    /**
     * Establece las categorias
     *
     * @return void
     */
    public static function getCategoria($rango)
    {
        switch ($rango) {
            case '16 - 17':
                return '"' . (date('Y') - 17) . '-01-01" AND "' . (date('Y') - 16) . '-12-31"';
                break;
            case '17 - 18':
                return '"' . (date('Y') - 18) . '-01-01" AND "' . (date('Y') - 17) . '-12-31"';
                break;
            case '18 - MÁS AÑOS':
                return '"' . (date('Y') - 109) . '-01-01" AND "' . (date('Y') - 18) . '-12-31"';
                break;
            case '19 - MÁS AÑOS':
                return '"' . (date('Y') - 109) . '-01-01" AND "' . (date('Y') - 19) . '-12-31"';
                break;
            case '25 - 29':
                return '"' . (date('Y') - 29) . '-01-01" AND "' . (date('Y') - 25) . '-12-31"';
                break;
            case '30 - 34':
                return '"' . (date('Y') - 34) . '-01-01" AND "' . (date('Y') - 30) . '-12-31"';
                break;
            case '35 - 39':
                return '"' . (date('Y') - 39) . '-01-01" AND "' . (date('Y') - 35) . '-12-31"';
                break;
            case '40 - 44':
                return '"' . (date('Y') - 44) . '-01-01" AND "' . (date('Y') - 40) . '-12-31"';
                break;
            case '45 - 49':
                return '"' . (date('Y') - 49) . '-01-01" AND "' . (date('Y') - 45) . '-12-31"';
                break;
            case '50 - 54':
                return '"' . (date('Y') - 54) . '-01-01" AND "' . (date('Y') - 50) . '-12-31"';
                break;
            case '55 - 59':
                return '"' . (date('Y') - 59) . '-01-01" AND "' . (date('Y') - 55) . '-12-31"';
                break;
            case '60 - 64':
                return '"' . (date('Y') - 64) . '-01-01" AND "' . (date('Y') - 60) . '-12-31"';
                break;
            case '65 - 69':
                return '"' . (date('Y') - 69) . '-01-01" AND "' . (date('Y') - 65) . '-12-31"';
                break;
            case '70 - 74':
                return '"' . (date('Y') - 74) . '-01-01" AND "' . (date('Y') - 70) . '-12-31"';
                break;
            case '75 - 79':
                return '"' . (date('Y') - 79) . '-01-01" AND "' . (date('Y') - 75) . '-12-31"';
                break;
            case '80 - MÁS AÑOS':
                return '"' . (date('Y') - 110) . '-01-01" AND "' . (date('Y') - 80) . '-12-31"';
                break;
            case 'ABIERTO/OPEN':
                return '"' . (date('Y') - 110) . '-01-01" AND "' . (date('Y') - 10) . '-12-31"';
                break;            
            default:
                $tyear = explode("-", $_SESSION['inicio']);
                //$anos = date('Y') - ((date('Y') - $tyear[0]) + $rango);
                $anos = date('Y') - $rango;
                return '"' . $anos . '-01-01" AND "' . $anos . '-12-31"';
                break;
        }
    }

    /**
     * Muestra la pagina de Historial
     * deportivo
     *
     * @return void
     */
    public function histdepAction()
    {
        
        $this->nadadores = ReportesDb::findNadadores();

        View::renderTemplate('Reportes/histdep.html', [
            'nadadores' => $this->nadadores,                   //monta los nadadores
            'mostrar' => false,
            
        ]);
    }

    /**
     * Show the filtro del historial
     *
     * @return void
     */
    public function histdepFilter()
    {
        $control = $_POST ?? false;

        if ($_POST['nadador']) {
            $this->nadadores = ReportesDb::findNadadores();
            $this->deportista = ReportesDb::findNadador($_POST['nadador']);

            $_SESSION['histo_nada'] = $_POST['nadador'];
            $_SESSION['prueba'] = $_POST['prueba'];
            $_SESSION['anual'] = $_POST['anual'];

            if ($_POST['inicio']) {
                $_SESSION['inicio'] = $_POST['inicio'];
            } else {
                $_SESSION['inicio'] = false;
            }

            if ($_POST['fin']) {
                $_SESSION['fin'] = $_POST['fin'];
            } else {
                $_SESSION['fin'] = false;
            }

            //$this->getHistorial();

            View::renderTemplate('Reportes/histdep.html', [
                'nadadores' => $this->nadadores,                   //monta los nadadores
                'deportista' => $this->deportista,
                'mostrar' => true,
                'control' => $control
            ]);
        } else {
            $this->nadadores = ReportesDb::findNadadores();

            Flash::addMessage('Por favor seleccione un nadador', Flash::DANGER);

            View::renderTemplate('Reportes/histdep.html', [
                'nadadores' => $this->nadadores,                   //monta los nadadores
                'mostrar' => false
            ]);
        }
    }

    /**
     * Metodo de consulta de par ala tabla
     *
     * @return void
     */
    public function getHistorial()
    {
        $cod = array_key_exists('id', $this->route_params) ? json_encode($this->route_params['id'], JSON_NUMERIC_CHECK) : 2;

        $arrData = ReportesDb::selectHistorial($cod);

        for ($i = 0; $i < count($arrData); $i++) {
            //Calculo de la edad            
            $anotorneo = strtotime($arrData[$i]['fecha_torneo']);
            $anonaci = strtotime($arrData[$i]['fecha_nac']);
            $arrData[$i]['edad'] = date("Y", $anotorneo) - date("Y", $anonaci);
            //Calculo de la diferencia
            $ant = $i - 1;
            if ($ant < 0) {
                $arrData[$i]['diferencia'] = '<span class="badge badge-success">El Mejor!!!</span>';
            } else {
                $tiempoant = explode(":", $arrData[$i - 1]['tiempo']);
                $tiempoact = explode(":", $arrData[$i]['tiempo']);
                $arrData[$i]['diferencia'] = round((($tiempoact[0] * 3600) + ($tiempoact[1] * 60) + $tiempoact[2]) - (($tiempoant[0] * 3600) + ($tiempoant[1] * 60) + $tiempoant[2]), 2);
            }
            //Calculo de los puntos fina
            $datos_puntos = ReportesDb::puntosFina($arrData[$i]['prueba_id']);

            switch ($arrData[$i]['genero']) {
                case 'F':
                    if ($arrData[$i]['piscina'] == 'LC') {
                        $record_Mundial = $datos_puntos['fina_tflc'];
                    } else {
                        $record_Mundial = $datos_puntos['fina_tfsc'];
                    }
                    break;

                case 'M':
                    if ($arrData[$i]['piscina'] == 'LC') {
                        $record_Mundial = $datos_puntos['fina_tmlc'];
                    } else {
                        $record_Mundial = $datos_puntos['fina_tmsc'];
                    }
                    break;
            }

            list($horas, $minutos, $segundos) = explode(':', $arrData[$i]['tiempo']);
            $t_segundos = ($horas * 3600) + ($minutos * 60) + $segundos;
            $finapts = round(1000 * pow(($record_Mundial / $t_segundos), 3));

            $arrData[$i]['segundos'] = $t_segundos;

            $arrData[$i]['puntos'] = $finapts;
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }


    /**
     * Muestra la pagina de Historial
     * deportivo Comparado
     *
     * @return void
     */
    public function histdepcAction()
    {
        $this->nadadores = ReportesDb::findNadadores();

        View::renderTemplate('Reportes/histdepc.html', [
            'nadadores' => $this->nadadores,                   //llama al resultado del metodo before()
            'mostrar' => false
        ]);
    }

    /**
     * Show the filtro del historial
     *
     * @return void
     */
    public function histdepcFilter()
    {

        if ($_POST['nadador'] and $_POST['nadador1']) {
            $this->nadadores = ReportesDb::findNadadores();
            $this->deportista = ReportesDb::findNadador($_POST['nadador']);
            $this->deportista1 = ReportesDb::findNadador($_POST['nadador1']);

            $_SESSION['nada1'] = $_POST['nadador'];
            $_SESSION['nada2'] = $_POST['nadador1'];

            $_SESSION['prueba'] = $_POST['prueba'];

            if ($_POST['ano']) {
                $_SESSION['ano'] = $_POST['ano'];
            } else {
                $_SESSION['ano'] = false;
            }
            //$this->getComparado();

            $stilo = ReportesDb::selectStilo($_POST['prueba']);
            $this->stilo = $stilo['nombre'];





            View::renderTemplate('Reportes/histdepc.html', [
                'nadadores' => $this->nadadores,                   //monta los nadadores
                'deportista' => $this->deportista,
                'deportista1' => $this->deportista1,
                'stilo' => $this->stilo,
                'mostrar' => true
            ]);
        } else {
            $this->nadadores = ReportesDb::findNadadores();

            Flash::addMessage('Por favor seleccione los nadadores', Flash::DANGER);

            View::renderTemplate('Reportes/histdepc.html', [
                'nadadores' => $this->nadadores,                   //monta los nadadores
                'mostrar' => false
            ]);
        }
    }

    /**
     * Metodo de consulta de par ala tabla
     *
     * @return void
     */
    public function getComparado()
    {
        $nada = array();
        $arrData = ReportesDb::selectComparado($_SESSION['nada1']);
        for ($i = 0; $i < count($arrData); $i++) {
            $anotorneo = strtotime($arrData[$i]['fecha_torneo']);
            $anonaci = strtotime($arrData[$i]['fecha_nac']);
            $edad = date("Y", $anotorneo) - date("Y", $anonaci);

            //Calculo de los puntos fina
            $datos_puntos = ReportesDb::puntosFina($_SESSION['prueba']);

            switch ($arrData[$i]['genero']) {
                case 'F':
                    if ($arrData[$i]['piscina'] == 'LC') {
                        $record_Mundial = $datos_puntos['fina_tflc'];
                    } else {
                        $record_Mundial = $datos_puntos['fina_tfsc'];
                    }
                    break;

                case 'M':
                    if ($arrData[$i]['piscina'] == 'LC') {
                        $record_Mundial = $datos_puntos['fina_tmlc'];
                    } else {
                        $record_Mundial = $datos_puntos['fina_tmsc'];
                    }
                    break;
            }

            list($horas, $minutos, $segundos) = explode(':', $arrData[$i]['tiempo']);
            $t_segundos = ($horas * 3600) + ($minutos * 60) + $segundos;
            $finapts = round(1000 * pow(($record_Mundial / $t_segundos), 3));

            $arrData[$i]['segundos'] = $t_segundos;
            $arrData[$i]['puntos'] = $finapts;

            $nada[$edad] = array(
                'edad' => $edad,
                'torneo' => $arrData[$i]['torneo'],
                'fecha' => $arrData[$i]['fecha_torneo'],
                'tiempo' => $arrData[$i]['tiempo'],
                'segundos' => $arrData[$i]['segundos'],
                'puntos' => $arrData[$i]['puntos'],
                'piscina' => $arrData[$i]['piscina'],
                'torneo1' => '',
                'fecha1' => '',
                'tiempo1' => '',
                'segundos1' => '',
                'puntos1' => '',
                'piscina1' => ''

            );
        }

        $arrData1 = ReportesDb::selectComparado($_SESSION['nada2']);
        for ($i = 0; $i < count($arrData1); $i++) {
            $anotorneo1 = strtotime($arrData1[$i]['fecha_torneo']);
            $anonaci1 = strtotime($arrData1[$i]['fecha_nac']);
            $edad1 = date("Y", $anotorneo1) - date("Y", $anonaci1);

            //Calculo de los puntos fina
            $datos_puntos = ReportesDb::puntosFina($_SESSION['prueba']);

            switch ($arrData1[$i]['genero']) {
                case 'F':
                    if ($arrData1[$i]['piscina'] == 'LC') {
                        $record_Mundial = $datos_puntos['fina_tflc'];
                    } else {
                        $record_Mundial = $datos_puntos['fina_tfsc'];
                    }
                    break;

                case 'M':
                    if ($arrData1[$i]['piscina'] == 'LC') {
                        $record_Mundial = $datos_puntos['fina_tmlc'];
                    } else {
                        $record_Mundial = $datos_puntos['fina_tmsc'];
                    }
                    break;
            }

            list($horas, $minutos, $segundos) = explode(':', $arrData1[$i]['tiempo']);
            $t_segundos = ($horas * 3600) + ($minutos * 60) + $segundos;
            $finapts = round(1000 * pow(($record_Mundial / $t_segundos), 3));

            $arrData[$i]['segundos1'] = $t_segundos;
            $arrData1[$i]['puntos'] = $finapts;

            if (array_key_exists($edad1, $nada)) {
                $reemplazos = array($edad1 => array(
                    'torneo1' => $arrData1[$i]['torneo'],
                    'fecha1' => $arrData1[$i]['fecha_torneo'],
                    'tiempo1' => $arrData1[$i]['tiempo'],
                    'segundos1' => $arrData[$i]['segundos1'],
                    'puntos1' => $arrData1[$i]['puntos'],
                    'piscina1' => $arrData1[$i]['piscina']
                ));
                $nada = array_replace_recursive($nada, $reemplazos);
            } else {
                $nada[$edad1] = array(
                    'edad' => $edad1,
                    'torneo' => '',
                    'fecha' => '',
                    'tiempo' => '',
                    'segundos' => '',
                    'puntos' => '',
                    'piscina' => '',
                    'torneo1' => $arrData1[$i]['torneo'],
                    'fecha1' => $arrData1[$i]['fecha_torneo'],
                    'tiempo1' => $arrData1[$i]['tiempo'],
                    'segundos1' => $arrData[$i]['segundos1'],
                    'puntos1' => $arrData1[$i]['puntos'],
                    'piscina1' => $arrData1[$i]['piscina']
                );
            }
        }


        echo json_encode(array_values($nada), JSON_UNESCAPED_UNICODE);

        die();
    }


    /**
     * Muestra la pagina de Record
     * 
     *
     * @return void
     */
    public function recordAction()
    {
        $control = $_POST ?? false;

        if ($control) {
            $_SESSION['control'] = '';
            $_SESSION['genero'] = $_POST['genero'] ?? false ;
            $_SESSION['prueba'] = $_POST['prueba'] ?? 1 ; 
            $_SESSION['inicio'] = date('Y-m-d');                       
            $_SESSION['categoria'] = Reportes::getCategoria($_POST['categoria']);

            

            //echo ReportesDb::selectRanking();

            View::renderTemplate('Reportes/record.html');
        } else {
            $_SESSION['control'] = 1;
            $_SESSION['genero'] = false ;
            $_SESSION['prueba'] =  1 ;  
            View::renderTemplate('Reportes/record.html');
        }
        

        
    }

    /**
     * Metodo de consulta de par ala tabla
     *
     * @return void
     */
    public function getRecord()
    {
       /*  $_SESSION['genero'] = $_POST['torneo'] ?? false;
        $_SESSION['prueba'] = $_POST['prueba'] ?? true;
        $_SESSION['categoria'] = '"' . (date('Y') - 17) . '-01-01" AND "' . (date('Y') - 16) . '-12-31"'; */

        if ($_SESSION['prueba'] != 1) {
            $arrData = array();
            if ($arrData = ReportesDb::selectRecord($_SESSION['prueba'])) {
                $stilo = ReportesDb::selectStilo($arrData['prueba_id']);
                $nadador = ReportesDb::findNadador($arrData['identificacion']);
                $arrData['stile'] = $stilo['nombre'];
                $arrData['identificacion'] = $nadador['nombre'] . ' ' . $nadador['apellido'];
                $arrData['id'] = $nadador['nombre_club'];
                //Calculo de los puntos fina
                $datos_puntos = ReportesDb::puntosFina($arrData['prueba_id']);

                switch ($nadador['genero']) {
                    case 'F':
                        if ($arrData['piscina'] == 'LC') {
                            $record_Mundial = $datos_puntos['fina_tflc'];
                        } else {
                            $record_Mundial = $datos_puntos['fina_tfsc'];
                        }
                        break;

                    case 'M':
                        if ($arrData['piscina'] == 'LC') {
                            $record_Mundial = $datos_puntos['fina_tmlc'];
                        } else {
                            $record_Mundial = $datos_puntos['fina_tmsc'];
                        }
                        break;
                }

                list($horas, $minutos, $segundos) = explode(':', $arrData['tiempo']);
                $t_segundos = ($horas * 3600) + ($minutos * 60) + $segundos;
                if ($t_segundos == 0){
                    $t_segundos = 1;
                }
                $finapts = round(1000 * pow(($record_Mundial / $t_segundos), 3)); 

                $arrData['puntos'] = $finapts;
                $record[] = $arrData;
            }
            
            echo json_encode(array_values($record), JSON_UNESCAPED_UNICODE);
            die();
        } else {
            $arrData = array();
            
            for ($i = 2; $i < 23; $i++) {
                if ($arrData = ReportesDb::selectRecord($i)) {
                    $stilo = ReportesDb::selectStilo($arrData['prueba_id']);
                    $nadador = ReportesDb::findNadador($arrData['identificacion']);
                    $arrData['stile'] = $stilo['nombre'];
                    $arrData['identificacion'] = $nadador['nombre'] . ' ' . $nadador['apellido'];
                    $arrData['id'] = $nadador['nombre_club'];
                    //Calculo de los puntos fina
                    $datos_puntos = ReportesDb::puntosFina($arrData['prueba_id']);
    
                    switch ($nadador['genero']) {
                        case 'F':
                            if ($arrData['piscina'] == 'LC') {
                                $record_Mundial = $datos_puntos['fina_tflc'];
                            } else {
                                $record_Mundial = $datos_puntos['fina_tfsc'];
                            }
                            break;
    
                        case 'M':
                            if ($arrData['piscina'] == 'LC') {
                                $record_Mundial = $datos_puntos['fina_tmlc'];
                            } else {
                                $record_Mundial = $datos_puntos['fina_tmsc'];
                            }
                            break;
                    }
    
                    list($horas, $minutos, $segundos) = explode(':', $arrData['tiempo']);
                    $t_segundos = ($horas * 3600) + ($minutos * 60) + $segundos;
                    if ($t_segundos == 0){
                        $t_segundos = 1;
                    }
                    $finapts = round(1000 * pow(($record_Mundial / $t_segundos), 3)); 
    
                    $arrData['puntos'] = $finapts;
                    $record[] = $arrData;          
    
                }
            }
          
            echo json_encode(array_values($record), JSON_UNESCAPED_UNICODE);
            die();
        }
        
        
      
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function findClubes()
    {
        //echo $_POST['id'];
        $id = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);
        
        echo json_encode(ReportesDb::findEquipos($id), JSON_UNESCAPED_UNICODE);

    }
}
