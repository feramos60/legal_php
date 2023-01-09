<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\RolesDb;
use App\Models\TorneoDb;
use App\Flash;

/**
 * Administración del Sistema (example)
 *
 * PHP version 7.0
 */
class Admin extends Authenticated
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
        $this->authorizationRequired(permission: 3);
    }

    /**
     * Muestrala pagina para subir el torneo en 
     * archivo hyv
     *
     * @return void
     */
    public function indexAction()
    {        
        View::renderTemplate('Admin/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }   

    /**
     * Sube en masa los tiempos en archivo Excel
     *
     * @return void
     */
    public function resultadosAction()
    {        
        View::renderTemplate('Admin/importresult.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Lista los tiempos del sistema
     *
     * @return void
     */
    public function tiemposAction()
    {        
        View::renderTemplate('Admin/tiempos.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Conseguir los tiempos para colocarlos
     * en la tabla de edición y eliminación de tiempos
     *
     * @return void
     */
    public function getTiempos()
    {
        
        $arrData = RolesDb::selectTiempos();

        for ($i=0; $i < count($arrData); $i++) {
            $arrData[$i]['nadador'] = $arrData[$i]['apellido']. ' ' .$arrData[$i]['nada'];
            $arrData[$i]['edicion'] = '<a class="btn btn-info btn-sm" id="btn-sm" data-toggle="modal" data-target="#modal-sm-t" data-dest="tiempitos" data-nom="' . $arrData[$i]['id'] . '" href="#" ><i class="fas fa-pencil-alt"></i> Editar</a>';  
            $arrData[$i]['accion'] = '<a class="btn btn-danger btn-sm" id="btn-tod" href="' . $arrData[$i]['id'] . '/eliminar"><i class="fas fa-trash"></i> Eliminar</a>';           
        }
        
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE); 
        die();        
    }

    /**
     * Borra el tiempo seleccinado por
     * el usuario
     *
     * @return void
     */
    public function eliminarAction()
    {
        if (array_key_exists('id', $this->route_params)) {
            $id = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero id de la prueba en ese torneo
            
            if (RolesDb::deleteTime($id)){
                Flash::addMessage('Tiempo borrado', Flash::SUCCESS);
                $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Admin/tiempos');
            }            
        } else {
            Flash::addMessage('No se pudo borrar el tiempo', Flash::DANGER);
            View::renderTemplate('Admin/tiempos.html', [
                'user' => $this->user                   //llama al resultado del metodo before()
            ]);
        }
    }

    /**
     * Actualiza el tiempo del nadador
     * para el ranking
     *
     * @return void
     */
    public function tiempoGeneral()
    {

        if ($_POST['destino_pag'] == 'tiempitos') {
            if (RolesDb::updateTimeGeneral($_POST)) {
                Flash::addMessage('El tiempo fue actualizado con éxito!');
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/tiempos');
            } else {
                Flash::addMessage('No fue posible actualizar el tiempo, por favor intente de nuevo', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/tiempos');
            }
        } else {            
                Flash::addMessage('No fue posible actualizar el tiempo, por favor intente de nuevo', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/tiempos');            
        }
    }

    /**
     * Lista los torneos para editar
     *
     * @return void
     */
    public function listarAction()
    {

        View::renderTemplate('Admin/listaTorneos.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()

        ]);
    }

    /**
     * Muestra el formulario para hacer la edición 
     * del torneo
     *
     * @return void
     */
    public function editAction()
    {
        if (array_key_exists('id', $this->route_params)) {
            $_SESSION['idT'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK); //se recoge la variable que se pasa por parametro
            $this->torneo = TorneoDb::torneo($_SESSION['idT']);
            View::renderTemplate('Admin/editTorneos.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
                'torneo' => $this->torneo,
            ]);
        } else {
            $this->redirect('/compa/dashboard/index');
        }
    }

    /**
     * Edita el Torneo seleccionado
     *
     * @return void
     */
    public function updateTAction()
    {
        $fecha = $_POST['fecha_cre'];
        $limite = $_POST['limite'];
        if (TorneoDb::updateTorneo($_SESSION['idT'], $fecha, $limite)) {
            Flash::addMessage('Se actualizo la información del Torneo con éxito!');            
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/admin/'. $_SESSION['idT'] .'/edit');
        } else {
            Flash::addMessage('Algo fallo en la actualización por favor revisar la información!', Flash::DANGER);
            unset($_SESSION["idT"]);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/admin/listar');
        }
    }

    /**
     * Borra el torneo seleccionado
     * esta acción no se puede 
     * deshacer.
     *
     * @return void
     */
    public function deleteTor()
    {
        if (TorneoDb::borrarTorneo($_POST['torneo_borrar'])) {
            Flash::addMessage('Se borró el torneo seleccionado', Flash::SUCCESS);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/admin/listar');
        } else {
            Flash::addMessage('No se pudo borrar el torneo, por favor intente de nuevo', Flash::DANGER);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/admin/'. $_SESSION['idT'] .'/edit');
        }
    }
}