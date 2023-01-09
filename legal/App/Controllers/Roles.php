<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\RolesDb;
use App\Models\User;
use \App\Flash;

/**
 * Manejo de todos los roles y permisos en el sistema
 *
 * PHP version 7.0
 */
class Roles extends Authenticated
{    

    /**
     * Before filter - called before each action method
     * Hace que se requiera loguearse para poder acceder a estos metodos
     * con $this->user se llama este metodo before
     * además se controla si hay permiso para este modulo
     *
     * @return void
     */
    protected function before()
    {
        parent::before();           //Llama el metodo before() de la clase Authenticated

        $this->user = Auth::getUser();
        $this->authorizationRequired(permission: 8);
    }

    /**
     * Muestra el listado de Roles que se
     * pueden asignar a un usaurio
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('/Roles/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Se encarga de traer los datos para la tabla 
     * que muestra los roles
     *
     * @return void
     */
    public function getRoles()
    {

        $arrData = RolesDb::selectRoles();

        for ($i = 0; $i < count($arrData); $i++) {
            if ($arrData[$i]['status'] == 1) {
                if ($arrData[$i]['id'] == 1) {
                    $arrData[$i]['status'] = '<a class="btn btn-success btn-sm disabled" id="btn-sm" href="' . $arrData[$i]['id'] . '/modulos" ><i class="fas fa-pencil-alt"></i> Módulo</a>';
                } else {
                    $arrData[$i]['status'] = '<a class="btn btn-success btn-sm" id="btn-sm" href="' . $arrData[$i]['id'] . '/modulos" ><i class="fas fa-pencil-alt"></i> Módulo</a>';
                }
                          
                
            } else {
                $arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
            }
            $arrData[$i]['permisos'] = '<a class="btn btn-info btn-sm" id="btn-sm" href="' . $arrData[$i]['id'] . '/permisos" ><i class="fas fa-eye"></i> Permisos</a>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();        
    }

    /**
     * Muestra el listado de módulos para
     * asignar a un al rol seleccionado
     *
     * @return void
     */
    public function modulosAction()
    {
        if (array_key_exists('id', $this->route_params)) {
            $_SESSION['idRol'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK); //se recoge la variable que se pasa por parametro            
            View::renderTemplate('Roles/modulos.html', [
                'user' => $this->user                   //llama al resultado del metodo before()
            ]);
        } else {
            $this->redirect('/torneos/dashboard/index');
        }
    }

    /**
     * Se encarga de traer los datos para la tabla 
     * que muestra los roles
     *
     * @return void
     */
    public function getModulos()
    {
        $modulosActivos = RolesDb::permisosRol($_SESSION['idRol']);
        $arrData = RolesDb::selectModulos();

        for ($i = 0; $i < count($arrData); $i++) {
            $existePermiso = array_search($arrData[$i]['id'], array_column($modulosActivos, 'module_id'));
            if ($existePermiso) {
                $arrData[$i]['accion'] = '<a class="btn btn-danger btn-sm" id="btn-sm" href="' . $arrData[$i]['id'] . '/quitar" ><i class="fas fa-pencil-alt"></i> Quitar</a>';               
            } else {
                if ($existePermiso === 0) {
                    $arrData[$i]['accion'] = '<a class="btn btn-danger btn-sm" id="btn-sm" href="' . $arrData[$i]['id'] . '/quitar" ><i class="fas fa-pencil-alt"></i> Quitar</a>';
                } else {
                    $arrData[$i]['accion'] = '<a class="btn btn-success btn-sm" id="btn-sm" href="' . $arrData[$i]['id'] . '/asignar" ><i class="fas fa-pencil-alt"></i> Asignar</a>';
                }                
            }         
                     
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();        
    }

    /**
     * Muestra los permisos para ese rol
     *
     * @return void
     */
    public function permisosAction()
    {
        if (array_key_exists('id', $this->route_params)) {
            $_SESSION['idRol'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK); //se recoge la variable que se pasa por parametro            
            View::renderTemplate('Roles/permisos.html', [
                'user' => $this->user                   //llama al resultado del metodo before()
            ]);
        } else {
            $this->redirect('/torneos/dashboard/index');
        }
    }    

    /**
     * Se encarga de traer los datos para la tabla 
     * que muestra los roles
     *
     * @return void
     */
    public function getPermisos()
    {        
        $arrData = RolesDb::permisosRol($_SESSION['idRol']);
        $modulos = RolesDb::selectModulos();

        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['numero'] = $i + 1;
            
        }       

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();  
    }

    /**
     * Se asigna un modulo a un rol
     *
     * @return void
     */
    public function asignar()
    {        
        if (array_key_exists('id', $this->route_params) and array_key_exists('idp', $this->route_params)) {
            $id_modulo = json_encode($this->route_params['idp'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero id del modulo
            $id_rol = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero el id del rol

            $asignarModulo = RolesDb::asignarRol($id_modulo, $id_rol);

            if ($asignarModulo) {
                Flash::addMessage('Módulo Asignado', Flash::SUCCESS);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/roles/' . $id_rol . '/modulos');
            } else {
                Flash::addMessage('No se pudo asignar el Módulo', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/roles/' . $id_rol . '/modulos');
            }
            

            
        }
    }

    /**
     * Se quita un modulo a un rol
     *
     * @return void
     */
    public function quitar()
    {        
        if (array_key_exists('id', $this->route_params) and array_key_exists('idp', $this->route_params)) {
            $id_modulo = json_encode($this->route_params['idp'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero id del modulo
            $id_rol = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero el id del rol

            $asignarModulo = RolesDb::quitarRol($id_modulo, $id_rol);

            if ($asignarModulo) {
                Flash::addMessage('Módulo Desactivado', Flash::SUCCESS);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/roles/' . $id_rol . '/modulos');
            } else {
                Flash::addMessage('No se pudo desactivar el Módulo', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/roles/' . $id_rol . '/modulos');
            }
            

            
        }
    }
}