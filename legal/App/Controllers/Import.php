<?php

namespace App\Controllers;

use \App\Auth;
use App\Models\ImportDb;
use \Core\View;
use App\Flash;


/**
 * Subida de los archivos Excel (example)
 *
 * PHP version 7.0
 */
class Import extends Authenticated
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
     * Método que lee el archivo de excel
     * y sube los nadadores
     *
     * @return void
     */
    public function indexAction()
    {
        $ds          = DIRECTORY_SEPARATOR;  //1 Lo usamos para utilizarlo luego en la dirección
        $storeFolder = 'uploads';   //2 esta es la carpeta en donde se guardarán los archivos

        $nom = $_FILES["file"]["name"]; //archivo1.jpg
        $arrayString = explode(".", $nom); //array(archivo1, jpg)
        $extension = end($arrayString); //jpg

        if ($extension == 'xls' or $extension == 'xlsx') {
            if (!empty($_FILES)) {

                $archivo = $_FILES['file']['tmp_name'];

                $targetPath = dirname(__DIR__) . $ds . $storeFolder . $ds;  //4

                $targetFile =  $targetPath . $_FILES['file']['name'];  //5

                if (move_uploaded_file($archivo, $targetFile)) {
                    $error = ImportDb::subirNadadores($targetFile);
                    if ($error) {
                        foreach ($error as $value) {
                            Flash::addMessage('Se presentó error en el registro ' . $value, Flash::DANGER);
                        }
                        $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/competencia');
                    } else {
                        Flash::addMessage('No se presentaron errores', Flash::SUCCESS);
                        $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/competencia');
                    }
                } else {
                    Flash::addMessage('Se presentó un problema al subir el archivo, por favor intente de nuevo', Flash::DANGER);
                    $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/competencia');
                }
            } else {
                Flash::addMessage('Se presentó un problema al subir el archivo, por favor intente de nuevo', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/competencia');
            }
        } else {
            Flash::addMessage('Se presentó un problema al subir el archivo, por favor revisar que sea un archivo valido de Excel', Flash::DANGER);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/competencia');
        }
    }

    /**
     * Método que lee el archivo de excel
     * y sube los resultados
     *
     * @return void
     */
    public function resultadosAction()
    {
        $ds          = DIRECTORY_SEPARATOR;  //1 Lo usamos para utilizarlo luego en la dirección
        $storeFolder = 'uploads';   //2 esta es la carpeta en donde se guardarán los archivos

        $nom = $_FILES["file"]["name"]; //archivo1.jpg
        $arrayString = explode(".", $nom); //array(archivo1, jpg)
        $extension = end($arrayString); //jpg

        if ($extension == 'xls' or $extension == 'xlsx') {
            if (!empty($_FILES)) {

                $archivo = $_FILES['file']['tmp_name'];
    
                $targetPath = dirname(__DIR__) . $ds . $storeFolder . $ds;  //4
    
                $targetFile =  $targetPath . $_FILES['file']['name'];  //5
    
                if (move_uploaded_file($archivo, $targetFile)) {
                    $error = ImportDb::subirResultados($targetFile);
    
                    if ($error) {
                        foreach ($error as $value) {
                            Flash::addMessage('Se presentó error en el registro ' . $value, Flash::DANGER);
                        }
                        $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/resultados');
                    } else {
                        Flash::addMessage('No se presentaron errores al subir los resultados de los nadadores', Flash::SUCCESS);
                        $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/resultados');
                    }
                } else {
                    Flash::addMessage('Se presentó un problema al subir el archivo, por favor intente de nuevo', Flash::DANGER);
                    $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/resultados');
                }
            } else {
                Flash::addMessage('Se presentó un problema al subir el archivo, por favor intente de nuevo, adjuntelo por favor', Flash::DANGER);
                $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/resultados');
            }
        } else {
            Flash::addMessage('Se presentó un problema al subir el archivo, por favor revisar que sea un archivo valido de Excel', Flash::DANGER);
            $this->redirect(dirname($_SERVER["PHP_SELF"]) . '/Admin/resultados');
        }
        

        
    }
}
