<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Flash;
use App\Models\ReportesDb;
use App\Controllers\Reportes;


/**
 * Ranking controller
 * Permite Mostrar los datos del
 * Ranking para usuarios logueados
 * 
 *
 * PHP version 8.0
 */
class Ranking extends Authenticated
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
     * Inicio del Ranking para usuarios
     * Logueados, con los botones activados para
     * descargar PDF, Excel y Copiar los
     * Datos.
     * 
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



            Flash::addMessage('<h4 class="alert-heading">Elementos de la Consulta</h4>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <p>Fecha de Inicio: ' . $_POST['inicio'] . '</p>            
                    <p>Categoría: ' . $_POST['categoria'] . ' años</p>                    
                    <p>Genero: ' . $_SESSION['genero'] . '</p>            
                </div>
                <div class="col-md-4">
                    <p>Fecha de Fin: ' . $_POST['fin'] . '</p>
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

        View::renderTemplate('Ranking/resultados.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()
            'competencias' => $this->competencias,                   //llama al resultado del metodo before()
            'ligas' => $this->ligas,
            'control' => $control
        ]);

        } else {
            $_SESSION['control'] = 1;
            View::renderTemplate('Ranking/resultados.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
                'competencias' => $this->competencias,                   //llama al resultado del metodo before()
                'ligas' => $this->ligas

            ]);
        }
    }

    /**
     * Muestra la pagina de Historial
     * deportivo
     *
     * @return void
     */
    public function historialAction()
    {
        
        $this->nadadores = ReportesDb::findNadadores();

        View::renderTemplate('Ranking/historial.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()
            'nadadores' => $this->nadadores,                   //monta los nadadores
            'mostrar' => false,
            
        ]);
    }

    /**
     * Show the filtro del historial
     *
     * @return void
     */
    public function historialFilter()
    {
        $control = $_POST ?? false;
        $this->user = Auth::getUser();

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

            View::renderTemplate('Ranking/historial.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
                'nadadores' => $this->nadadores,                   //monta los nadadores
                'deportista' => $this->deportista,
                'mostrar' => true,
                'control' => $control
            ]);
        } else {
            $this->nadadores = ReportesDb::findNadadores();

            Flash::addMessage('Por favor seleccione un nadador', Flash::DANGER);

            View::renderTemplate('Ranking/historial.html', [
                'user' => $this->user,                   //llama al resultado del metodo before()
                'nadadores' => $this->nadadores,                   //monta los nadadores
                'mostrar' => false
            ]);
        }
    }

    /**
     * Muestra la pagina de Historial
     * deportivo Comparado
     *
     * @return void
     */
    public function historiadoAction()
    {
        $this->nadadores = ReportesDb::findNadadores();

        View::renderTemplate('Ranking/historiado.html', [
            'user' => $this->user,                   //llama al resultado del metodo before()
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

            View::renderTemplate('Ranking/historiado.html', [
                'nadadores' => $this->nadadores,                   //monta los nadadores
                'deportista' => $this->deportista,
                'deportista1' => $this->deportista1,
                'stilo' => $this->stilo,
                'mostrar' => true
            ]);
        } else {
            $this->nadadores = ReportesDb::findNadadores();

            Flash::addMessage('Por favor seleccione los nadadores', Flash::DANGER);

            View::renderTemplate('Ranking/historiado.html', [
                'nadadores' => $this->nadadores,                   //monta los nadadores
                'mostrar' => false
            ]);
        }
    }

    /**
     * Muestra la pagina de Record
     * 
     *
     * @return void
     */
    public function recordarAction()
    {
        $control = $_POST ?? false;

        if ($control) {
            $_SESSION['control'] = '';
            $_SESSION['genero'] = $_POST['genero'] ?? false ;
            $_SESSION['prueba'] = $_POST['prueba'] ?? 1 ; 
            $_SESSION['inicio'] = date('Y-m-d');                       
            $_SESSION['categoria'] = Reportes::getCategoria($_POST['categoria']);

            

            //echo ReportesDb::selectRanking();

            View::renderTemplate('Ranking/recordar.html');
        } else {
            $_SESSION['control'] = 1;
            $_SESSION['genero'] = false ;
            $_SESSION['prueba'] =  1 ;  
            View::renderTemplate('Ranking/recordar.html');
        }
        

        
    }

    

}
