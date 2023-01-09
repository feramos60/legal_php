<?php

namespace App\Controllers;

use \App\Auth;
use App\Models\TorneoDb;

/**
 * Subida de los archivos Excel (example)
 *
 * PHP version 7.0
 */
class Upload extends Authenticated
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
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
        $ds          = DIRECTORY_SEPARATOR;  //1

        $storeFolder = 'uploads';   //2

        if (!empty($_FILES)) {

            $tempFile = $_FILES['file']['tmp_name'];          //3             

            $targetPath = dirname(__DIR__) . $ds . $storeFolder . $ds;  //4

            $targetFile =  $targetPath . $_FILES['file']['name'];  //5

            move_uploaded_file($tempFile, $targetFile); //6

            TorneoDb::crearTorneo($targetFile);
        }    
    }
    
}
