<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\PuntajesDb;
use App\Models\User;
use \App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class puntajes extends Authenticated
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
        $this->authorizationRequired(permission: 7);
    }

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('/Puntajes/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Método que lee el archivo de excel
     * y sube los nadadores
     *
     * @return void
     */
    public function importAction()
    {
        $ds          = DIRECTORY_SEPARATOR;  //1
        $storeFolder = 'uploads';   //2

        $nom = $_FILES["file"]["name"]; //se agarra el nombre del archivo
        $arrayString = explode(".", $nom); //array(archivo1, extension)
        $extension = end($arrayString); //se obtiene la extension en una variable

        if ($extension === 'csv' || $extension === 'CSV') {
            if (!empty($_FILES)) {

                $tempFile = $_FILES['file']['tmp_name'];          //3             

                $targetPath = dirname(__DIR__) . $ds . $storeFolder . $ds;  //4

                $targetFile =  $targetPath . $_FILES['file']['name'];  //5

                move_uploaded_file($tempFile, $targetFile); //6

                if (PuntajesDb::subirPuntajes($targetFile)) {
                    Flash::addMessage('Se subieron los puntajes con éxito!!! ', Flash::INFO);
                } else {
                    Flash::addMessage('Se presentó un error al subir el archivo!', Flash::DANGER);
                }
            }
        } else {
            Flash::addMessage('La extensión del archivo no corresponde a CSV!!!', Flash::DANGER);
        }

        View::renderTemplate('/Puntajes/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Lista los puntajes Generales
     *
     * @return void
     */
    public function listarAction()
    {
        View::renderTemplate('/Puntajes/general.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
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
    public function clubAction()
    {
        $cod = array_key_exists('id', $this->route_params) ? json_encode($this->route_params['id']) : '';

        $club = PuntajesDb::selectClub($cod);
        $_SESSION['puntaje'] = $club['club'];

        View::renderTemplate('/Puntajes/detalle.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
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

        for ($i = 0; $i < count($arrData); $i++) {

            $arrData[$i]['posicion'] = $i + 1;

            $arrData[$i]['detalle'] = '<a class="btn btn-warning btn-sm" id="btn-tod" href="' . $arrData[$i]['id'] . '/edit"><i class="fas fa-pencil-alt"></i> Editar </a>';
            $arrData[$i]['accion'] = '<a class="btn btn-danger btn-sm" id="btn-tod" href="' . $arrData[$i]['id'] . '/eliminar"><i class="fas fa-trash"></i> Eliminar</a>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Lista los puntajes Promedio
     *
     * @return void
     */
    public function promedioAction()
    {
        View::renderTemplate('/Puntajes/promedio.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
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
     * Muestra formulario de edición de registro
     * delpuntaje
     *
     * @return void
     */
    public function editAction()
    {
        if (array_key_exists('id', $this->route_params) and array_key_exists('idp', $this->route_params)) {
            $idReg = json_encode($this->route_params['idp'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero id de la prueba en ese torneo

            $_SESSION['idClub'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);
            $this->registro = PuntajesDb::selectRegistros($idReg);

            View::renderTemplate('/Puntajes/editPuntaje.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
                'registro' => $this->registro
            ]);
        } else {
            Flash::addMessage('Vuelva a intentarlo.', Flash::DANGER);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Puntajes/general.html');
        }
    }

    /**
     * Metodo que guarda los datos recibidos del usuario
     * @return void
     */
    public function update()
    {
        $editRegistro = new PuntajesDb($_POST);

        if ($editRegistro->updateRegistro()) {
            Flash::addMessage('El registro fue actualizado con éxito!');
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/puntajes/' . $_SESSION['idClub'] . '/club');
        } else {
            Flash::addMessage('No se pudo actualizar el registro.', Flash::DANGER);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/puntajes/' . $_SESSION['idClub'] . '/club');
        }
    }

    /**
     * Borra el registro seleccinado por
     * el usuario
     *
     * @return void
     */
    public function eliminar()
    {
        if (array_key_exists('id', $this->route_params) and array_key_exists('idp', $this->route_params)) {
            $idReg = json_encode($this->route_params['idp'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero id de la prueba en ese torneo

            if (PuntajesDB::deleteReg($idReg)) {
                Flash::addMessage('Registro Borrado', Flash::SUCCESS);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Puntajes/listar');
            } else {
                Flash::addMessage('No se pudo borrar el registro', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Puntajes/listar');
            }
        } else {
            Flash::addMessage('Vuelva a intentarlo.', Flash::DANGER);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Puntajes/listar');
        }
    }

    /**
     * Consulta en puntajes para obtener el detalle
     * de puntaje Promedio
     *
     * @return void
     */
    public function detallepromAction()
    {
        $cod = array_key_exists('id', $this->route_params) ? json_encode($this->route_params['id']) : '';

        $club = PuntajesDb::selectClub($cod);
        $_SESSION['puntaje'] = $club['club'];
        $this->id = PuntajesDb::idClub($club['club']);

        if ($this->id) {
            $this->cantidad = PuntajesDb::calcularNadadores($this->id['id']);
        } else {
            $this->cantidad = [];
        }

        View::renderTemplate('/Puntajes/detalleProm.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()
            'cantidades' => $this->cantidad
        ]);
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
