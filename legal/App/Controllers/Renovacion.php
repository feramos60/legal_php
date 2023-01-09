<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\RenovaDb;
use App\Flash;

/**
 * Renovacion Registro Por parte de la
 * Liga
 *
 * PHP version 7.0
 */
class Renovacion extends Authenticated
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
        $this->authorizationRequired(permission: 1);
    }

    /**
     * Muestra el listado de años
     * para activar la renovación
     *
     * @return void
     */
    public function indexAction()
    {                
        View::renderTemplate('Renovacion/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Muestra los años para registros
     * con datos
     *
     * @return void
     */
    public function getRegistro()
    {
        
        $arrData = RenovaDb::selectReg();

        if ($arrData) {            
            for ($i=0; $i < count($arrData); $i++) {
                if ($arrData[$i]['status'] == 1) {
                    $arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
                } else {
                    $arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
                }
                if ($arrData[$i]['year'] == date('Y')) {
                    $arrData[$i]['accion']= '<a class="btn btn-info btn-sm" id="btn-sm" href="' . $arrData[$i]['id'] . '/info" ><i class="fas fa-pencil-alt"></i> Editar</a>';
                } else {
                    $arrData[$i]['accion']= '<a class="btn btn-danger btn-sm disabled" id="btn-sm" href="#" ><i class="fas fa-pencil-alt"></i> Editar</a>';
                }                
            }
        }
        
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE); 
        die();        
    }

    /**
     * Muestra el formulario de edición de
     * registro donde se activa y se colocan los valores
     *
     * @return void
     */
    public function infoAction()
    {
        if (array_key_exists('id', $this->route_params)) {
            $id = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);
            $this->renova = RenovaDb::RegById($id);
            View::renderTemplate('Renovacion/info.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
                'renova' => $this->renova
            ]);
        } else {
            Flash::addMessage('No seleccionó un año para editar', Flash::DANGER);
            View::renderTemplate('Renovacion/index.html', [
                'user' => $this->user                   //llama al resultado del metodo before()
            ]);
        }        
    }

    /**
     * Metodo modifica los datos de renovacion
     * en el año actual
     * 
     * @return void
     */
    public function updateYear()
    {  
        $registro = new RenovaDb($_POST);

        if ($registro->updateRegistros()) {
            Flash::addMessage('El registro fue actualizado con éxito!'); 
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Renovacion/index');            
        } else {
            Flash::addMessage('El registro no fue actualizado, por favor revise los datos e intente de nuevo.', Flash::DANGER); 
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Renovacion/index');
        }
        
    }

    /**
     * Muestra el listado de clubes para
     * renovar registro por parte de la LIga
     *
     * @return void
     */
    public function registroAction()
    {        
        View::renderTemplate('Renovacion/registro.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    

    

    

}