<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\TorneoDb;
use App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Torneo extends Authenticated
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

        $this->authorizationRequired(permission: 5);
    }

    /**
     * Inicio de la inscriopción al torneo
     * nos muestra los torneos disponibles
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Torneo/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Función que consulta todos los torneos vigentes
     * y luego coloca un botón para mostrar los nadadores pertenecientes
     * al Club del entrenador logueado.
     *
     * @return void
     */
    public function getTorneos()
    {

        $arrData = TorneoDb::selectTorneos();

        for ($i = 0; $i < count($arrData); $i++) {

            if ($arrData[$i]['fecha_semb'] < date('Y-m-d', strtotime('+1 day')) && $arrData[$i]['fecha_cre'] > date('Y-m-d', strtotime('-1 day'))) {
                $arrData[$i]['seleccionar'] = '<a href="' . $arrData[$i]['id'] . '/' . $arrData[$i]['max_ins_indiv'] . '/nadadores"><button type="button" class="btn btn-block btn-success btn-sm">Inscribir Nadadores</button></a>';
            } else {
                $arrData[$i]['seleccionar'] = '<a href="#"><button type="button" class="btn btn-block btn-success btn-sm" disabled>Inscribir Nadadores</button></a>';
            }
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Muestra la pagina con los nadadores del club para ser
     * inscritos en el torneo seleccionado.
     * Con un botón para inscribir al nadador en las pruebas.
     *
     * @return void
     */
    public function nadadoresAction()
    {


        if (array_key_exists('id', $this->route_params) and array_key_exists('idp', $this->route_params)) {
            $_SESSION['max_torneo'] = json_encode($this->route_params['idp'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero id de la prueba en ese torneo
            $_SESSION['torneo'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero el id del nadador
            View::renderTemplate('Torneo/nadadores.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
            ]);
        } else {
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Torneo/index');
        }
    }

    /**
     * Consulta la base de datos TorneoDb para listar
     * los nadadores del club del entrenador logueado.
     * El botón lleva consigo el id del nadador.
     *
     * @return void
     */
    public function getNadadores()
    {

        $arrData = TorneoDb::selectNadadores();

        for ($i = 0; $i < count($arrData); $i++) {

            switch ($arrData[$i]['genero']) {
                case 'M':
                    $arrData[$i]['genero']  = 'MASCULINO';
                    break;
                case 'F':
                    $arrData[$i]['genero']  = 'FEMENINO';
                    break;
            }
            $fch = explode("-", $arrData[$i]['fecha_nac']);
            $tfecha = $fch[0];
            /* $tfecha = $fch[2] . "-" . $fch[1] . "-" . $fch[0];
            $dias = explode("-", $tfecha, 3);
            $dias = mktime(0, 0, 0, $dias[1], $dias[0], $dias[2]);
            $edad = (int)((time() - $dias) / 31556926); */
            $ahora = date('Y');

            $edad = $ahora - $tfecha;


            $arrData[$i]['edad'] = $edad;

            $arrData[$i]['inscribir'] = '<a href="../../' . $arrData[$i]['id'] . '/pruebas"><button type="button" class="btn btn-block btn-success btn-sm">Incribirse en prueba</button></a>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Muestra la pagina con el listado de los nadadores 
     * guarda en una variable global el id el nadador
     * pasada por parametro.
     * Muestra el mejor tiempo del nadador en cada prueba en
     * el último año
     *
     * @return void
     */
    public function pruebasAction()
    {


        if (array_key_exists('id', $this->route_params)) {
            $_SESSION['nadador'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK); //se recoge la variable que se pasa por parametro
            $this->nadador = TorneoDb::selectNadapru();
            View::renderTemplate('Torneo/pruebas.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
                'nadador' => $this->nadador,
            ]);
        } else {
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/dashboard/index');
        }
    }

    /**
     * Consulta en la BD TorneBd el nadador selecionado
     * con el mejor tiempo por prueba en el último año.
     *
     * @return void
     */
    public function getTiempos()
    {
        $nadador = TorneoDb::selectNada();

        $id = $nadador['nuip'];

        $arrData = TorneoDb::selectNadador($id);


        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['nombre'] = $nadador['nombre'];
            $arrData[$i]['apellido'] = $nadador['apellido'];
            $otros = TorneoDb::selectId($id, $arrData[$i]['t'], $arrData[$i]['prueba_id']);
            for ($j = 0; $j < count($otros); $j++) {
                $arrData[$i]['torneo'] = $otros[$j]['torneo'];
                $arrData[$i]['fecha_torneo'] = $otros[$j]['fecha_torneo'];
            }
            $prueba = TorneoDb::nombrePrueba($arrData[$i]['prueba_id']);
            $arrData[$i]['prueba_id'] = $prueba[0]['nombre'];
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Consulta en la BD TorneBd el nadador selecionado
     * con el mejor tiempo por prueba en el último año.
     *
     * @return void
     */
    public function getNadador()
    {
        $nadador = TorneoDb::selectNada();

        $id = $nadador[0]['nuip'];

        $arrData = TorneoDb::selectNadador($id);


        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['nombre'] = $nadador[0]['nombre'];
            $arrData[$i]['apellido'] = $nadador[0]['apellido'];
            $otros = TorneoDb::selectId($id, $arrData[$i]['t'], $arrData[$i]['prueba_id']);
            for ($j = 0; $j < count($otros); $j++) {
                $arrData[$i]['torneo'] = $otros[$j]['torneo'];
                $arrData[$i]['fecha_torneo'] = $otros[$j]['fecha_torneo'];
            }
            $prueba = TorneoDb::nombrePrueba($arrData[$i]['prueba_id']);
            $arrData[$i]['prueba_id'] = $prueba[0]['nombre'];
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Consulta las pruebas según la categoria del nadador
     * coloca un botón para inscribir el nadador.
     *
     * @return void
     */
    public function getPruebas()
    {

        $nadador = TorneoDb::selectNada(); //Busco el nadador seleccionado para ponerle pruebas
        
        //es cargar una variable para saber el genero
        switch ($nadador['genero']) {
            case 'M':
                $genero1  = 'M';
                $genero2 = 'B';
                break;
            case 'F':
                $genero1  = 'W';
                $genero2  = 'G';
                break;
        }

        //Monto en una global el id del club del nadador
        $_SESSION['id_club'] = $nadador['id_club'];

        //cargamos en $fch los datos de la fecha de nacimiento
        $fch = explode("-", $nadador['fecha_nac']);
        /* $tfecha = $fch[2] . "-" . $fch[1] . "-" . $fch[0];
        $dias = explode("-", $tfecha, 3);
        $dias = mktime(0, 0, 0, $dias[1], $dias[0], $dias[2]);
        $edad = (int)((time() - $dias) / 31556926); */

        $tfecha = $fch[0]; //cargamos el año de nacimiento del nadador

        //$ahora = date('Y');

        $edad = date('Y') - $tfecha;



        $arrData = TorneoDb::selectPruebas($genero1, $genero2, $edad);

        for ($i = 0; $i < count($arrData); $i++) {

            switch ($arrData[$i]['estilo']) {
                case 'A':
                    $arrData[$i]['estilo']  = 'Libre/Free';
                    break;
                case 'B':
                    $arrData[$i]['estilo']  = 'Espalda/Back';
                    break;
                case 'C':
                    $arrData[$i]['estilo']  = 'Pecho/Breast';
                    break;
                case 'D':
                    $arrData[$i]['estilo']  = 'Mariposa/Fly';
                    break;
                case 'E':
                    $arrData[$i]['estilo']  = 'Combinado/Medley';
                    break;
                case 'F':
                    $arrData[$i]['estilo']  = 'Libre/Free';
                    break;
            }

            switch ($arrData[$i]['tipo']) {
                case 'R':
                    $relevo = 'disabled';
                    break;

                case 'I':
                    $relevo = '';
                    break;
            }




            $arrData[$i]['inscribir'] = '<a style="text-decoration:none" href="' . $arrData[$i]['id'] . '/inscripcion"><button type="button" class="btn btn-block btn-success btn-sm"' . $relevo . '>Inscribir Nadador</button></a>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Realiza la inscriopción del nadador en la 
     * prueba específica.
     *
     * @return void
     */
    public function inscripcion()
    {
        if (array_key_exists('id', $this->route_params) and array_key_exists('idp', $this->route_params)) {
            $id_pru = json_encode($this->route_params['idp'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero id de la prueba en ese torneo
            $id_n = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero el id del nadador
            $id_torneo = $_SESSION['torneo'];


            $chequeo = TorneoDb::maxTorneo($id_torneo, $id_n);  //averiguo la cantidad de pruebas inscritas en el torneo

            $checnada = TorneoDb::maxPrueba($id_torneo, $id_n, $id_pru); //Consulto si ya esta inscrito en la prueba

            $checjornada = TorneoDb::jornada($id_pru);  //Consulta la sesión de la prueba

            $maxjornadas = TorneoDb::maxJornada($id_n, $checjornada['sesion']);

            $cantindividual = TorneoDb::maxIndividual($id_n, $checjornada['sesion'], $id_torneo); //Consulta la cantidad de pruebas individuales

            $cantirel = TorneoDb::maxRelevo($id_n, $checjornada['sesion']);  //Consulta la cantidad de relevos

            $datosTorneo = TorneoDb::torneo($id_torneo);  //Traigo los datos del torneo

            $sinadadorInscrito = TorneoDb::nadadorInscrito($id_torneo, $id_n);

            $cantidadInscritos = TorneoDb::torneoCantidad($id_torneo);  //Traigo los cantidad de inscritos en el torneo

            

            if (!$sinadadorInscrito && $datosTorneo['limt'] != 0) {
                if (count($cantidadInscritos) >= $datosTorneo['limt']) {
                    Flash::addMessage('Nadador no puede ser inscrito porque se llego al limite de inscritos', Flash::DANGER);
                    $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $id_n . '/pruebas');                    
                }                
            } 
            

            if ($checnada) {
                Flash::addMessage('Nadador ya esta inscripto en esa prueba', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $id_n . '/pruebas');
            } else {
                if (count($chequeo) < $_SESSION['max_torneo']) {
                    if ($checjornada['tipo'] == "R") {
                        if (count($cantirel) >= $checjornada['max_rel']) {
                            Flash::addMessage('Nadador sobrepasa la cantidad de pruebas por Relevo por Sesión', Flash::DANGER);
                            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $id_n . '/pruebas');
                        } else {
                            if (TorneoDb::crearInscripcion($id_n, $id_pru, $id_torneo)) {
                                Flash::addMessage('Nadador Inscrito en la prueba de Relevo', Flash::SUCCESS);
                                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $id_n . '/pruebas');
                            } else {
                                Flash::addMessage('No se pudo inscribir al nadador en la prueba, por favor intente de nuevo', Flash::DANGER);
                            }
                        }
                    } else {
                        if (count($cantindividual) >= $checjornada['max_indiv']) {
                            Flash::addMessage('Nadador sobrepasa la cantidad de pruebas Individual por Sesión', Flash::DANGER);
                            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $id_n . '/pruebas');
                        } else {
                            if (TorneoDb::crearInscripcion($id_n, $id_pru, $id_torneo)) {
                                Flash::addMessage('Nadador Inscrito en la prueba de Individual', Flash::SUCCESS);
                                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $id_n . '/pruebas');
                            } else {
                                Flash::addMessage('No se pudo inscribir al nadador en la prueba, por favor intente de nuevo', Flash::DANGER);
                            }
                        }
                    }
                } else {
                    Flash::addMessage('Nadador Sobrepasa la cantidad de pruebas por torneo', Flash::DANGER);
                    $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $id_n . '/pruebas');
                }
            }
        } else {
            $this->redirect(dirname($_SERVER['PHP_SELF']) . '/dashboard/index');
        }
    }

    /**
     * Muestra los torneos para que los entrenadores
     * puedan ver el resumen de la inscripción
     *
     * @return void
     */
    public function resumenAction()
    {
        View::renderTemplate('Torneo/resumen.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Consulta en la Base de Datos los torneos vigentes
     * para mostrar el resumen a cada entrenador.
     *
     * @return void
     */
    public function getResumen()
    {

        $arrData = TorneoDb::selectTorneos();

        for ($i = 0; $i < count($arrData); $i++) {

            $arrData[$i]['seleccionar'] = '<a href="' . $arrData[$i]['id'] . '/consolidado"><span class="badge badge-success">Ver Nadadores Inscritos</span></a>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Muestra la página que tiene a todos los
     * nadadores inscritos en la prueba seleccionada
     *
     * @return void
     */
    public function consolidadoAction()
    {

        if (array_key_exists('id', $this->route_params)) {
            $_SESSION['resumen'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro
            $control = 'admin';
            $control1 = 'club';
            $this->resumen = TorneoDb::resumenClub($control);
            $this->cant_nada = TorneoDb::resumenNada($control);
            $this->cant_clubes = TorneoDb::resumenClubes();
            $this->resumenC = TorneoDb::resumenClub($control1);
            $this->cant_nadaC = TorneoDb::resumenNada($control1);
            View::renderTemplate('Torneo/consolidado.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
                'cant_pruebas' => $this->resumen,
                'cant_nadadores' => $this->cant_nada,
                'cant_clubes' => $this->cant_clubes,
                'cant_pruebasC' => $this->resumenC,
                'cant_nadadoresC' => $this->cant_nadaC
            ]);
        } else {
            $this->redirect(dirname($_SERVER['PHP_SELF']) . '/dashboard/index');
        }
    }

    /**
     * Consulta en la tabla los nadadores según el torneo
     * y el club al cuakl poertenece el nadador que esta
     * consultando.
     *
     * @return void
     */
    public function getConsolidado()
    {

        if ($_SESSION['user_role'] == 3) {
            $op_eliminar = TorneoDb::torneo($_SESSION['resumen']);

            if ($op_eliminar['fecha_cre'] < date('Y-m-d')) {
                $act_del = 'disabled';
            } else {
                $act_del = '';
            }

            $ed_time = TorneoDb::club($_SESSION['user_club']);

            if ($ed_time[0]['time'] == 0) {
                $activar = 'disabled';
            } else {
                $activar = '';
            }
        } else {
            if ($_SESSION['user_role'] == 4) {
                $activar = 'disabled';
                $act_del = 'disabled';
            } else {
                $activar = '';
                $act_del = '';
            }            
        }




        $arrData = TorneoDb::selectResumen();

        for ($i = 0; $i < count($arrData); $i++) {

            switch ($arrData[$i]['estilo']) {
                case 'A':
                    $arrData[$i]['estilo']  = 'Libre/Free';
                    break;
                case 'B':
                    $arrData[$i]['estilo']  = 'Espalda/Back';
                    break;
                case 'C':
                    $arrData[$i]['estilo']  = 'Pecho/Breast';
                    break;
                case 'D':
                    $arrData[$i]['estilo']  = 'Mariposa/Fly';
                    break;
                case 'E':
                    $arrData[$i]['estilo']  = 'Combinado/Medley';
                    break;
                case 'F':
                    $arrData[$i]['estilo']  = 'Libre/Free';
                    break;
            }
            $arrData[$i]['ingresar'] = '<a class="btn btn-info btn-sm ' . $activar . '" id="btn-sm" data-toggle="modal" data-target="#modal-sm" data-dest="general" data-nom="' . $arrData[$i]['id'] . '" href="#" ><i class="fas fa-pencil-alt"></i> Editar</a>';
            $arrData[$i]['eliminar'] = '<a class="btn btn-danger btn-sm ' . $act_del . '" id="btn-tod" data-toggle="modal" data-target="#modal-sm3" data-idn="' . $arrData[$i]['idn'] . '" href="#"><i class="fas fa-trash"></i> Eliminar</a>';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Borra la prueba seleccionada
     * prueba específica.
     *
     * @return void
     */
    public function borrar()
    {
        if (TorneoDb::borrarTodas($_POST['nadador'])) {
            Flash::addMessage('Se borraron todas las pruebas para este nadador', Flash::SUCCESS);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $_SESSION['resumen'] . '/consolidado');
        } else {
            Flash::addMessage('No se pudo borrar la prueba, por favor intente de nuevo', Flash::DANGER);
        }
    }

    /**
     * Borra la prueba seleccionada
     * prueba específica.
     *
     * @return void
     */
    public function tiempo()
    {

        if ($_POST['destino_pag'] == 'especifico') {
            if (TorneoDb::updateTime($_POST)) {
                Flash::addMessage('El tiempo fue actualizado con éxito!');
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $_SESSION['nadador'] . '/pruebas');
            } else {
                Flash::addMessage('No fue posible actualizar el tiempo, por favor intente de nuevo', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $_SESSION['nadador'] . '/pruebas');
            }
        } else {
            if (TorneoDb::updateTime($_POST)) {
                Flash::addMessage('El tiempo fue actualizado con éxito!');
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $_SESSION['resumen'] . '/consolidado');
            } else {
                Flash::addMessage('No fue posible actualizar el tiempo, por favor intente de nuevo', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $_SESSION['resumen'] . '/consolidado');
            }
        }
    }


    /**
     * Este metodo permite buscar las pruebas 
     * que tiene el nadador inscritas en el
     * torneo que se esta inscribiendo
     *
     * @return void
     */
    public function getConsolinada()
    {

        if ($_SESSION['user_role'] == 3) {
            $ed_time = TorneoDb::club($_SESSION['user_club']);

            if ($ed_time[0]['time'] == 0) {
                $activar = 'disabled';
            } else {
                $activar = '';
            }
        } else {
            $activar = '';
        }

        $arrData = TorneoDb::selectResumenada();

        for ($i = 0; $i < count($arrData); $i++) {

            switch ($arrData[$i]['estilo']) {
                case 'A':
                    $arrData[$i]['estilo']  = 'Libre/Free';
                    break;
                case 'B':
                    $arrData[$i]['estilo']  = 'Espalda/Back';
                    break;
                case 'C':
                    $arrData[$i]['estilo']  = 'Pecho/Breast';
                    break;
                case 'D':
                    $arrData[$i]['estilo']  = 'Mariposa/Fly';
                    break;
                case 'E':
                    $arrData[$i]['estilo']  = 'Combinado/Medley';
                    break;
                case 'F':
                    $arrData[$i]['estilo']  = 'Libre/Free';
                    break;
            }
            $arrData[$i]['ingresar'] = '<a class="btn btn-info btn-sm ' . $activar . '" id="btn-sm" data-toggle="modal" data-target="#modal-sm" data-dest="especifico" data-nom="' . $arrData[$i]['id'] . '" href="#" ><i class="fas fa-pencil-alt"></i> Editar</a>';
            $arrData[$i]['eliminar'] = '<a class="btn btn-danger btn-sm" id="btn-del" data-toggle="modal" data-target="#modal-sm2" data-id="' . $arrData[$i]['id'] . '" href="#" ><i class="fas fa-trash"></i>Eliminar</a>';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Borra la prueba seleccionada
     * prueba específica.
     *
     * @return void
     */
    public function borrare()
    {

        if (TorneoDb::borrarPrueba($_POST['id'])) {
            Flash::addMessage('Prueba borrada', Flash::SUCCESS);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/torneo/' . $_SESSION['nadador'] . '/pruebas');
        } else {
            Flash::addMessage('No se pudo borrar la prueba, por favor intente de nuevo', Flash::DANGER);
        }
    }

    /**
     * Función que consulta todos los torneos vigentes
     * y luego coloca un botón para editar el torneo
     * 
     *
     * @return void
     */
    public function getLista()
    {

        $arrData = TorneoDb::selectTorneos();

        for ($i = 0; $i < count($arrData); $i++) {

            $arrData[$i]['seleccionar'] = '<a href="' . $arrData[$i]['id'] . '/edit"><button type="button" class="btn btn-block btn-success btn-sm">Editar</button></a>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }
    
}