<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use App\Models\RolesDb;
use App\Models\TorneoDb;
use SplFileInfo;

/**
 * Profile controller
 *
 * PHP version 7.0
 */
class Profile extends Authenticated
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
    }

    /**
     * Show the profile
     *
     * @return void
     */
    public function showAction()
    {
        $this->club = RolesDb::selectClub($_SESSION['user_club']);
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()
            'club' => $this->club

        ]);
    }

    /**
     * Show the form for editing the profile
     *
     * @return void
     */
    public function editAction()
    {
        View::renderTemplate('Profile/edit.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Update the profile
     *
     * @return void
     */
    public function updateAction()
    {
        $fotico = '';

        if (!empty($_FILES["foto"]["tmp_name"])) {  //Se revisa que no venga vacia la foto
            $mTmpFile = $_FILES["foto"]["tmp_name"]; //se toma el nombre y la ruta temporal
            $mTipo = exif_imagetype($mTmpFile);  //Se revisa si es imagen

            $ds          = DIRECTORY_SEPARATOR;  //1 es un backslash para utilizarlo luego en la dirección
            $storeFolder = 'user';   //2 esta es la carpeta en donde se guardarán los archivos

            if (($mTipo != IMAGETYPE_JPEG) and ($mTipo != IMAGETYPE_PNG)) {
                Flash::addMessage('Por favor revisar que el tipo de archivo sea una imagen JPG o PNG', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/show');                
            } else {
                $targetPath = dirname(__DIR__) . $ds . $storeFolder . $ds;  //4 La ruta donde se guardara
                $file = new SplFileInfo($_FILES['foto']['name']);  //se averigua la extensión del archivo
                $extension  = $file->getExtension();      //se guarda la extensión del archivo         
                $fotico = $_SESSION['user_id'].'_'.basename($mTmpFile).'.'.$extension ; //el nombre con el que va a ir a labase de datos
                $targetFile =  $targetPath . $fotico;  //5  a donde se va a mover con todos los datos
                if (move_uploaded_file($mTmpFile, $targetFile)) {
                    if ($this->user->updateProfile($_POST, $fotico)) {   //llama al resultado del metodo before()
                        Flash::addMessage('Los cambios fueron realizados con éxito!', Flash::SUCCESS);
                        $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/show');
                    } else {
                        Flash::addMessage('No se pudo actualizar la información del usuario, por favor intente de nuevo', Flash::DANGER);
                        $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/show');
                    }                    
                } else {
                    Flash::addMessage('Por favor revisar que el tipo de archivo sea una imagen JPG o PNG', Flash::DANGER);
                    $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/show');                    
                }
                
            }
        } else {
            if ($this->user->updateProfile($_POST, $fotico)) {   //llama al resultado del metodo before()
                Flash::addMessage('Los cambios fueron realizados con éxito!', Flash::SUCCESS);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/show');
            } else {
                Flash::addMessage('No se pudo actualizar la información del usuario, por favor intente de nuevo', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/show');
            }
        }



        
    }

    /**
     * Show the club profile
     *
     * @return void
     */
    public function clubAction()
    {
        $this->club = RolesDb::selectClub($_SESSION['user_club']);
        View::renderTemplate('Profile/clubprofile.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()
            'club' => $this->club
        ]);
    }

    /**
     * Muestra los nadadores del club
     * en caso de SuperAdmin le muestra
     * todo
     *
     * @return void
     */
    public function listadoAction()
    {
        $this->club = RolesDb::selectClub($_SESSION['user_club']);
        View::renderTemplate('Profile/listado.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()
            'club' => $this->club
        ]);
    }

    /**
     * Consulta la base de datos TorneoDb para listar
     * los nadadores del club del entrenador logueado.
     * El botón lleva consigo el id del nadador.
     *
     * @return void
     */
    public function getListado()
    {

        $arrData = TorneoDb::selectNadadores();

        for ($i = 0; $i < count($arrData); $i++) {

            if ($arrData[$i]['nadaimg']) {
                $arrData[$i]['nadaimg'] = '<ul class="list-inline"><li class="list-inline-item"><img alt="Avatar Nada" class="table-avatar" src="/liga/App/nadadores/' . $arrData[$i]['nadaimg'] . '" ></li></ul>';
            } else {
                $arrData[$i]['nadaimg'] = '<ul class="list-inline"><li class="list-inline-item"><img alt="Avatar Nada" class="table-avatar" src="../dist/img/white.png"></li></ul>';
            }

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

            if ($arrData[$i]['status'] == 1) {
                $arrData[$i]['status'] = '<a class="btn btn-info btn-sm" id="btn-sm1" data-toggle="modal" data-target="#modal-sm1" data-id="' . $arrData[$i]['id'] . '" href="#"><i class="fas fa-pencil-alt"></i>Activo</a>';
            } else {
                $arrData[$i]['status'] = '<a class="btn btn-danger btn-sm disabled" href="#"><i class="fas fa-pencil-alt"></i>Inactivo</a>';
            }
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Update the profile
     *
     * @return void
     */
    public function upnadadorAction()
    {
       
        if (!empty($_FILES["foton"]["tmp_name"])) {  //Se revisa que no venga vacia la foto
            $mTmpFile = $_FILES["foton"]["tmp_name"]; //se toma el nombre y la ruta temporal
            $mTipo = exif_imagetype($mTmpFile);  //Se revisa si es imagen

            $ds          = DIRECTORY_SEPARATOR;  //1 es un backslash para utilizarlo luego en la dirección
            $storeFolder = 'nadadores';   //2 esta es la carpeta en donde se guardarán los archivos

            if (($mTipo != IMAGETYPE_JPEG) and ($mTipo != IMAGETYPE_PNG)) {
                Flash::addMessage('Por favor revisar que el tipo de archivo sea una imagen JPG o PNG', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/listado');                
            } else {
                $targetPath = dirname(__DIR__) . $ds . $storeFolder . $ds;  //4 La ruta donde se guardara
                $file = new SplFileInfo($_FILES['foton']['name']);  //se averigua la extensión del archivo
                $extension  = $file->getExtension();      //se guarda la extensión del archivo         
                $fotico = $_POST['id_nadador'].'_'.basename($mTmpFile).'.'.$extension ; //el nombre con el que va a ir a labase de datos
                $targetFile =  $targetPath . $fotico;  //5  a donde se va a mover con todos los datos
                if (move_uploaded_file($mTmpFile, $targetFile)) {
                    if ($this->user->updateNadador($_POST, $fotico)) {   //llama al resultado del metodo before()
                        Flash::addMessage('La imagen se subió con éxito!', Flash::SUCCESS);
                        $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/listado');
                    } else {
                        Flash::addMessage('No se pudo actualizar la información del usuario, por favor intente de nuevo', Flash::DANGER);
                        $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/listado');
                    }                    
                } else {
                    Flash::addMessage('Por favor revisar que el tipo de archivo sea una imagen JPG o PNG', Flash::DANGER);
                    $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/listado');                    
                }
                
            }
        } else {
            Flash::addMessage('No se pudo actualizar la foto del nadador, por favor adjunte una foto', Flash::DANGER);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/profile/listado');            
        }



        
    }




}
