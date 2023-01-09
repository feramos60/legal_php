<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Models\TorneoDb;
use App\Models\HY3A1Record;

require_once('Constantes.php');


/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Hygen extends Authenticated
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
     * hy3 data
     */
    var $__hy3Data;

    /**
     * Inicio de generación del Hy3
     * nos muestra los torneos disponibles
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Hygen/index.html', [
            'user' => $this->user                   //llama al resultado del metodo before()
        ]);
    }

    /**
     * Función que consulta todos los torneos vigentes
     * y luego coloca un botón para descargar el Hy3
     *
     * @return void
     */
    public function getTorneos()
    {

        $arrData = TorneoDb::selectTorneos();

        for ($i = 0; $i < count($arrData); $i++) {
            //$_SESSION['torneo'] = $arrData[$i]['id'];
            $arrData[$i]['seleccionar'] = '<a href="' . $arrData[$i]['id'] . '/club"><span class="badge badge-success">Listar Clubes para descarga</span></a>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Inicio de generación del Hy3
     * nos muestra los torneos disponibles
     *
     * @return void
     */
    public function clubAction()
    {
        if (array_key_exists('id', $this->route_params)) {
            $_SESSION['torneo_club'] = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero id de la prueba en ese torneo
            View::renderTemplate('Hygen/club.html', [
                'user' => $this->user                   //llama al resultado del metodo before()
            ]);
        } else {
            $this->redirect(dirname($_SERVER["PHP_SELF"]).'/dashboard/index');
        }
    }

    /**
     * Lista los clubes inscritos
     *
     * @return string - HY3 file name
     */
    public function getClubes()
    {

        $arrData = HY3A1Record::selectClubes($_SESSION['torneo_club']);

        for ($i = 0; $i < count($arrData); $i++) {

            $arrData[$i]['seleccionar'] = '<a href="' . $arrData[$i]['id'] . '/generate"><span class="badge badge-success">Descargar HY3</span></a>';

            $hombres = HY3A1Record::cantidadGenero($_SESSION['torneo_club'], $arrData[$i]['id'], 'M');
            $arrData[$i]['hombres']= $hombres;

            $mujeres = HY3A1Record::cantidadGenero($_SESSION['torneo_club'], $arrData[$i]['id'], 'F');;
            $arrData[$i]['mujeres']= $mujeres;

            $total = $hombres + $mujeres;
            $arrData[$i]['total']= $total;

        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }



    /**
     * Get HY3 file name
     *
     * @return string - HY3 file name
     */
    function getHY3File()
    {
        return $this->__hy3File;
    }

    /**
     * Set HY3 file name
     *
     * @param string - HY3 file name
     */
    function setHY3File($f)
    {
        $this->__hy3File = $f;
    }



    /**
     * Función que consulta todos los torneos vigentes
     * y luego coloca un botón para descargar el Hy3
     *
     * @return void
     */
    function generate()
    {
        if (array_key_exists('id', $this->route_params) and array_key_exists('idp', $this->route_params)) {
            $_SESSION['id_club'] = json_encode($this->route_params['idp'], JSON_NUMERIC_CHECK);  //se recoge la variable que se pasa por parametro y se convierte en numero id de la prueba en ese torneo


            $club = HY3A1Record::selectC1($_SESSION['id_club']);

            $nom_club = $club[0]['nombre_club'];



            $A1 = $this->A1();
            $B1 = $this->B1();
            $B2 = $this->B2();
            $C1 = $this->C1();
            $C2 = $this->C2();
            $C3 = $this->C3();
            $__hy3Data = HY3A1Record::selectNadadores();

            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';


            //  Generate a temporary file to hold the data?
            $this->setHY3File(dirname(dirname(__DIR__)).'/tmp/'.substr(str_shuffle($permitted_chars), 0, 16).'.hy3');

            $f = fopen($this->getHY3File(), 'w+');

            if ($f !== false) {
                fwrite($f, $A1 . WPST_HY3_RECORD_TERMINATOR);
                fwrite($f, $B1 . WPST_HY3_RECORD_TERMINATOR);
                fwrite($f, $B2 . WPST_HY3_RECORD_TERMINATOR);
                fwrite($f, $C1 . WPST_HY3_RECORD_TERMINATOR);
                fwrite($f, $C2 . WPST_HY3_RECORD_TERMINATOR);
                fwrite($f, $C3 . WPST_HY3_RECORD_TERMINATOR);

                for ($i = 0; $i < count($__hy3Data); $i++) {
                    $edad = $this->CalculateAge($__hy3Data[$i]['fecha_nac']);

                    $D1 = $this->D1($__hy3Data[$i]['genero'], $__hy3Data[$i]['id'], $__hy3Data[$i]['apellido'], $__hy3Data[$i]['nombre'], $__hy3Data[$i]['nuip'], date('mdY', strtotime($__hy3Data[$i]['fecha_nac'])), $edad);
                    fwrite($f, $D1 . WPST_HY3_RECORD_TERMINATOR);

                    $pruebas = HY3A1Record::selectPruebas($__hy3Data[$i]['id']);
                    for ($j = 0; $j < count($pruebas); $j++) {

                        $E1 = $this->E1($pruebas[$j]['genero'], $__hy3Data[$i]['id'], $pruebas[$j]['apellido'], $pruebas[$j]['gp'], $pruebas[$j]['metros'], $pruebas[$j]['estilo'], $pruebas[$j]['categoria_1'], $pruebas[$j]['categoria_2'], $pruebas[$j]['nombre'], $pruebas[$j]['lo'], $pruebas[$j]['tiempo_1']);
                        fwrite($f, $E1 . WPST_HY3_RECORD_TERMINATOR);
                    }
                }
                fclose($f);
                if (file_exists($this->getHY3File())) {
                    // Define headers
                    header("Cache-Control: public");
                    header("Content-Description: File Transfer");
                    header("Content-Disposition: attachment; filename= $nom_club.hy3");
                    header("Content-Type: application/zip");
                    header("Content-Transfer-Encoding: binary");

                    // Read the file
                    readfile($this->getHY3File());

                    exit;
                } else {
                    echo 'El archivo no se pudo generar.';
                }
            } else {
                echo 'hecho nada' . $this->setTempFileError(true);
            }
        } else {
            $this->redirect(dirname($_SERVER['PHP_SELF']) . '/dashboard/index');
        }
    }

    /**
     * Función que crea el A1
     *
     * @return void
     */
    function A1()
    {
        $hy3 = sprintf(
            WPST_HY3_A1_RECORD,
            WPST_HY3_FTC_MEET_ENTRIES_VALUE,
            WPST_HY3_FTC_MEET_ENTRIES_LABEL,
            WPST_HY3_SOFTWARE_NAME,
            WPST_HY3_SOFTWARE_VERSION,
            date('mdY'),
            WPST_HY3_UNUSED,
            date('g:i A'),
            WPST_HY3_TEAM_LIGA,
            WPST_HY3_NO_VALUE
        );



        return $this->CalculateHy3Checksum($hy3);
    }

    /**
     * Generate B1 Record
     */
    function B1()
    {
        $arrData = HY3A1Record::selectB1($_SESSION['torneo_club']);




        $hy3 = sprintf(
            WPST_HY3_B1_RECORD,
            $arrData[0]['nombre'],
            $arrData[0]['lugar'],
            date('mdY', strtotime($arrData[0]['fecha_ini'])),
            date('mdY', strtotime($arrData[0]['fecha_fin'])),
            date("mdY", mktime(0, 0, 0, 12, 31, date('Y'))),
            WPST_HY3_UNUSED,
            WPST_HY3_NO_VALUE
        );

        return $this->CalculateHy3Checksum($hy3);
    }

    /**
     * Generate Record B2
     */
    function B2()
    {
        $arrData = HY3A1Record::selectB1($_SESSION['torneo_club']);


        $hy3 = sprintf(
            WPST_HY3_B2_RECORD,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            substr($arrData[0]['lo'], -2, 1),
            WPST_HY3_CEROS,
            $arrData[0]['lo'],
            WPST_HY3_UNUSED,
            WPST_HY3_NO_VALUE
        );

        return $this->CalculateHy3Checksum($hy3);
    }

    /**
     * Generate Record
     *
     * @return string - C1 HY3 record
     */
    function C1()
    {
        $arrData = HY3A1Record::selectC1($_SESSION['id_club']);

        $hy3 = sprintf(
            WPST_HY3_C1_RECORD,
            $arrData[0]['abreviatura'],
            $arrData[0]['nombre_club'],
            $arrData[0]['abreviatura'],
            WPST_HY3_UNUSED,
            WPST_HY3_TTC_AGE_GROUP_VALUE,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED
        );

        return $this->CalculateHy3Checksum($hy3);
    }

    /**
     * Generate Record
     *
     * @return string - C2 HY3 record
     */
    function C2()
    {
        $hy3 = sprintf(
            WPST_HY3_C2_RECORD,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            WPST_HY3_TTC_COLLEGE_VALUE,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED
        );

        return $this->CalculateHy3Checksum($hy3);
    }



    /**
     * Generate Record
     *
     * @return string - C3 HY3 record
     */
    function C3()
    {

        $arrData = HY3A1Record::selectC1($_SESSION['id_club']);


        $hy3 = sprintf(
            WPST_HY3_C3_RECORD,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            $arrData[0]['email'],
            WPST_HY3_UNUSED
        );

        return $this->CalculateHy3Checksum($hy3);
    }

    /**
     * Generate Record
     *
     * @return string - D1 HY3 record
     */
    function D1($genero, $id, $apellido, $nombre, $nuip, $fecha, $edad)
    {
        $hy3 = sprintf(
            WPST_HY3_D1_RECORD,
            $genero,
            $id,
            $apellido,
            $nombre,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED,
            $nuip,
            WPST_HY3_UNUSED,
            $fecha,
            WPST_HY3_UNUSED,
            $edad,
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED
        );

        return $this->CalculateHy3Checksum($hy3);
    }

    /**
     * Generate Record
     *
     * @return string - E1 HY3 record
     */
    function E1($genero, $id, $apellido, $gp, $metros, $estilo, $categoria_1, $categoria_2, $nombre, $lo, $tiempo)
    {
        if (is_null($tiempo)) {
            $t_segundos = WPST_HY3_CEROS_P;
        } else {
            list($horas, $minutos, $segundos) = explode(':', $tiempo);
            $t_segundos = ($horas * 3600) + ($minutos * 60) + $segundos;
        }



        $hy3 = sprintf(
            WPST_HY3_E1_RECORD,
            $genero,
            $id,
            $apellido,
            $genero,
            $gp,
            $metros,
            $estilo,
            $categoria_1,
            $categoria_2,
            WPST_HY3_UNUSED,
            0,
            $nombre,
            0,
            substr($lo, -2, 1),
            $t_segundos,
            substr($lo, -2, 1),
            WPST_HY3_UNUSED,
            WPST_HY3_UNUSED
        );

        return $this->CalculateHy3Checksum($hy3);
    }

    /**
     * Calculate HY3 Checksum
     *
     * Adapted from Troy Delano's example PHP code.  Credit to Joe
     * Hance for decipering the goofy Hy-tek checksum.  Joe is the man!
     *
     * @param string - HY3 record
     */
    function CalculateHy3Checksum($hy3)
    {
        // Ensure the string is 128 bytes in length and padded with whitespace

        $hy3 = str_pad($hy3, 128, ' ', STR_PAD_RIGHT);

        $sumEvn = 0;
        $sumOdd = 0;

        //  Loop through 128 characters, two at a time

        for ($i = 0; $i < 64; $i++) {
            $sumEvn = $sumEvn + ord($hy3[(2 * $i)]);
            $sumOdd = $sumOdd + (2 * ord($hy3[(2 * $i) + 1]));
        }

        //  Calculate the checksum and save the ones and tens digits

        $chksum = (floor(($sumEvn + $sumOdd) / 21)) + 205;
        $ones = $chksum/1 % 10;
        $tens = (int) abs($chksum/10) % 10;

        return sprintf(WPST_HY3_CHECKSUM_RECORD, $hy3, $ones, $tens);
    }

    /**
     * Calculate la edad del nadador
     *
     * Adapted from Troy Delano's example PHP code.  Credit to Joe
     * Hance for decipering the goofy Hy-tek checksum.  Joe is the man!
     *
     * @param string - HY3 record
     */
    function CalculateAge($birth)
    {
        $fch = explode("-", $birth);
        $anio = $fch[0];
        $edad = date('Y') - $anio;

        /* $fch = explode("-", $birth);
        $tfecha = $fch[2] . "-" . $fch[1] . "-" . $fch[0];
        $dias = explode("-", $tfecha, 3);
        $dias = mktime(0, 0, 0, $dias[1], $dias[0], $dias[2]);
        $edad = (int)((time() - $dias) / 31556926); */
        return $edad;
    }

    public function mostrar()
    {
        foreach ($_SESSION as $index => $value) {
            echo __FILE__ . __LINE__ . " $index: $value<br>";
        }

        $id_torneo = json_encode($this->route_params['id'], JSON_NUMERIC_CHECK);
        echo $sql = 'SELECT clubes.nombre_club, clubes.id  FROM clubes 
        INNER JOIN inscripciones ON clubes.id = inscripciones.id_club        
        WHERE inscripciones.id_torneo =' .  $id_torneo . '
        GROUP BY clubes.id';
        echo '<br>';

        echo date("mdY", mktime(0, 0, 0, 12, 31, date('Y')));
        echo '<br>';
        echo date("M-d-Y", mktime(0, 0, 0, 13, 1, 1997));
        echo '<br>';
        echo date("M-d-Y", mktime(0, 0, 0, 1, 1, 1998));
        echo '<br>';
        echo date("M-d-Y", mktime(0, 0, 0, 1, 1, 98));

        echo '<br>';
        echo $rest = substr("LO", -2, 1);
        echo '<br>';


        echo $id_torneo;
        echo '<br>';
        $array = array(1, 2, 3, 4);
        foreach ($array as &$valor) {
            echo $valor = $valor * 2;
            echo '<br>';
        }

        $arrData = HY3A1Record::selectClubes($id_torneo);

        var_dump($arrData);
    }
}