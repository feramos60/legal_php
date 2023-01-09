<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\RolesDb;
use App\Models\User;
use \App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Usuarios extends Authenticated
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
        $this->authorizationRequired(permission: 2);
    }

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('/Usuarios/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }
    
    /**
     * Listar los usuarios
     *
     * @return void
     */
    public function getUsuarios()
    {
        $arrData = RolesDb::selectUsers();
        
        for ($i = 0; $i < count($arrData); $i++) {
            
            $arrData[$i]['accion'] = '<a href="' . $arrData[$i]['id'] . '/asignar"><span class="badge badge-success">Editar Usuario</span></a>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Listar los usuarios
     *
     * @return void
     */
    public function asignarAction()
    {
        if (array_key_exists('id', $this->route_params)) {
            $_SESSION['entrenador'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK); //se recoge la variable que se pasa por parametro
            $this->entrenador = User::findByID($_SESSION['entrenador']);
            $this->club1 = RolesDb::findClubByID($this->entrenador->club_id); 
            $this->clubes = RolesDb::selectClubes();        
            View::renderTemplate('Usuarios/asignar.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
                'entrenador' => $this->entrenador,                   //llama al resultado del metodo before()
                'clubes' => $this->clubes,
                'clubinfo' => $this->club1

            ]);
        } else {
            $this->redirect('/torneos/dashboard/index');
        }
    }

    /**
     * Asigna el club al entrenador
     *
     * @return void
     */
    public function updateAction()
    {
        $img = '';
        $this->entrenador = User::findByID($_SESSION['entrenador']);
        if ($this->entrenador->updateProfile($_POST, $img)) {   //llama al resultado del metodo before()

            Flash::addMessage('Se actualizo la información del usuario con éxito!');            

            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/usuarios/'. $_SESSION['entrenador'] . '/asignar');

        } else {

            Flash::addMessage('Algo fallo en la actualización por favor revisar que la identificación o correo electrónico no exista!', Flash::DANGER);            

            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/usuarios/club');

        }
    }

    /**
     * Muestra la página que par crear un club
     *
     * @return void
     */
    public function nuevoclubAction()
    {
        $this->ligas = RolesDb::selectLigas();
        View::renderTemplate('Usuarios/nuevoclub.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()
            'ligas' => $this->ligas
        ]);
    }

    /**
     * Metodo que guarda los datos recibidos del usuario
     * @return void
     */
    public function create()
    {  
        $club = new RolesDB($_POST);      


        if ($club->crearClub()) {
            Flash::addMessage('El club fue creado con éxito!'); 
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Usuarios/nuevoclub');            
        } else {
            Flash::addMessage('El club no fue creado, por favor revise que el club no exista o no se haya usado la misma abreviatura.', Flash::DANGER); 
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Usuarios/nuevoclub');
        }
        
    }

    /**
     * Muestra la página que par crear un nadador
     *
     * @return void
     */
    public function nuevonadaAction()
    {
        $this->clubes = RolesDb::selectClubes(); 
        View::renderTemplate('/Usuarios/nuevonada.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()
            'clubes' => $this->clubes
        ]);
    }

    /**
     * Metodo que guarda los datos recibidos del 
     * nadador
     * @return void
     */
    public function createnada()
    {  
        $nada = new RolesDB($_POST);
        if ($nada->crearNada()) {            
            Flash::addMessage('El nadador fue creado con éxito!'); 
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Usuarios/nuevonada');            
        } else {            
            Flash::addMessage('El nadador no fue creado, por favor revise que el nadador no exista.', Flash::DANGER); 
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/Usuarios/nuevonada');
        }
        
    }

    /**
     * Editar el club
     *
     * @return void
     */
    public function editclubAction()
    {
        View::renderTemplate('Usuarios/editclub.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Buscar los clubes
     *
     * @return void
     */
    public function getClubes()
    {

        $arrData = RolesDb::selectClubes();

        for ($i = 0; $i < count($arrData); $i++) {
            if ($arrData[$i]['id']==19) {
                $arrData[$i]['accion'] = '<a href="#"><span class="badge badge-danger">No Editar Club</span></a>';
            } else {
                $arrData[$i]['accion'] = '<a href="' . $arrData[$i]['id'] . '/cambiar"><span class="badge badge-success">Editar Club</span></a>';
            }
                        
            
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();        
    }

    /**
     * Edición del club
     *
     * @return void
     */
    public function cambiarAction()
    {
        if (array_key_exists('id', $this->route_params)) {
            $_SESSION['club'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK); //se recoge la variable que se pasa por parametro
            $this->clubes = RolesDb::findClubByID($_SESSION['club']);
            $this->ligas = RolesDb::selectLigas();             
            View::renderTemplate('Usuarios/cambiar.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()                
                'clubes' => $this->clubes,
                'ligas' => $this->ligas
            ]);
        } else {
            $this->redirect('/torneos/dashboard/index');
        }
    }

    /**
     * Asigna el club al entrenador
     *
     * @return void
     */
    public function updateclubAction()
    {
        $this->club = RolesDb::findClubByID($_SESSION['club']);
        if ($this->club->updateClub($_POST)) {   //llama al resultado del metodo before()

            Flash::addMessage('Se edito la información del club con éxito!');            

            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/usuarios/editclub');

        } else {

            Flash::addMessage('Algo fallo en la actualización por favor revisar que el club no exista!', Flash::DANGER);            

            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/usuarios/editclub');

        }
    }

    /**
     * Mostrar los nadadores para editarlos
     *
     * @return void
     */
    public function editnadaAction()
    {
        View::renderTemplate('Usuarios/editnada.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Buscar los nadadores
     *
     * @return void
     */
    public function getNadadores()
    {

        $arrData = RolesDb::selectNadadores();

        for ($i = 0; $i < count($arrData); $i++) {
            
                $arrData[$i]['accion'] = '<a href="' . $arrData[$i]['id'] . '/nadar"><span class="badge badge-success">Editar Nadador</span></a>';  
                                    
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();        
    }

    /**
     * Edición del club
     *
     * @return void
     */
    public function nadarAction()
    {
        if (array_key_exists('id', $this->route_params)) {
            $_SESSION['nadador'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK); //se recoge la variable que se pasa por parametro
            $nadador = RolesDb::findNadaByID($_SESSION['nadador']);                   
            $this->club1 = RolesDb::findClubByID($nadador->id_club);
            $this->club = RolesDb::selectClubes();       
            View::renderTemplate('Usuarios/nadar.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()                
                'clubes' => $this->club,
                'club' => $this->club1,
                'nadador' => $nadador
            ]);
        } else {
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/dashboard/index');
        }
    }

    /**
     * Editar la información general
     * del nadador
     *
     * @return void
     */
    public function updatenadaAction()
    {
        $this->swimmer = new RolesDB($_POST);        
        if ($this->swimmer->updateNada()) {   //llama al resultado del metodo before()

            Flash::addMessage('El nadador se actualizo con éxito!');            

            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/usuarios/'. $_SESSION['nadador'] . '/nadar');

        } else {

            Flash::addMessage('Algo fallo en la actualización por favor revisar que la identificación no exista!', Flash::DANGER);            

            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/usuarios/editnada');

        }
    }

    /**
     * Sube en masa los naddores en archivo Excel
     *
     * @return void
     */
    public function competenciaAction()
    {        
        View::renderTemplate('Usuarios/importnada.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }


}