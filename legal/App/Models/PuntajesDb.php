<?php

namespace App\Models;

use PDO;
use Exception;

ini_set('date.timezone', 'America/Bogota');

/**
 * Example roles model
 *
 * PHP version 7.0
 */
class PuntajesDb extends \Core\Model
{
    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Lee el archivo CSV con los
     * Puntajes y lo sube a la base
     * de datos
     *
     * @return void
     */
    public static function subirPuntajes($file)
    {
        if (($gestor = fopen($file, "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, 500, ",", '"')) !== FALSE) {

                $torneo = utf8_encode(iconv("UTF-8", "ISO-8859-1//IGNORE", $datos[2]));
                $gender = $datos[6];
                $club = utf8_encode(iconv("UTF-8", "ISO-8859-1//IGNORE", $datos[11]));
                $entero = str_replace(".", "", $datos[13]);
                $points = floatval($entero . "." . $datos[14]);
                $year = date('Y');

                $id_club = PuntajesDb::idClub($club);

                if ($id_club) {
                    $cantidad = PuntajesDb::calcularNadadores($id_club['id']);
                    $liga = $cantidad['total'];
                    $inactivos = $cantidad['inactivo']['inact'];
                    $exterior = $cantidad['exterior']['exte'];
                    $seleccion = $cantidad['seleccion']['sel_col'];
                } else {
                    $liga = 1;
                    $inactivos = 0;
                    $exterior = 0;
                    $seleccion = 0;
                }

                $sql1 = 'INSERT INTO puntajes (year, tournament, gender, club, points, liga, inactivos, exterior, seleccion, created_at, updated_at, created_by_id, updated_by_id)
                            VALUES (:year, :tournament, :gender, :club, :points, :liga, :inactivos, :exterior, :seleccion, :created_at, :updated_at, :created_by_id, :updated_by_id)';

                $db = static::getDB();
                $stmt1 = $db->prepare($sql1);

                $stmt1->bindValue(':year', $year, PDO::PARAM_INT);
                $stmt1->bindValue(':tournament', $torneo, PDO::PARAM_STR);
                $stmt1->bindValue(':gender', $gender, PDO::PARAM_STR);
                $stmt1->bindValue(':club', $club, PDO::PARAM_STR);
                $stmt1->bindValue(':points', $points, PDO::PARAM_STR);
                $stmt1->bindValue(':liga', $liga, PDO::PARAM_INT);
                $stmt1->bindValue(':inactivos', $inactivos, PDO::PARAM_INT);
                $stmt1->bindValue(':exterior', $exterior, PDO::PARAM_INT);
                $stmt1->bindValue(':seleccion', $seleccion, PDO::PARAM_INT);
                $stmt1->bindValue(':created_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
                $stmt1->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
                $stmt1->bindValue(':created_by_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt1->bindValue(':updated_by_id', $_SESSION['user_id'], PDO::PARAM_INT);

                try {
                    $stmt1->execute();
                } catch (Exception $th) {
                    fclose($gestor);
                    return false;
                }
            }
            fclose($gestor);
        }
        return true;
    }

    /**
     * Calcula los puntajes generales de la
     * Base de Datos y tabla Puntajes 
     * 
     */
    public static function selectGeneral()
    {
        $year = date('Y');

        $sql = 'SELECT *, SUM(points) AS suma FROM puntajes WHERE year =' . $year . ' GROUP BY club ORDER BY suma DESC'; //Si se quiere que aparezcan siempre los torneos cuando ya paso el torneo
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Busca el club 
     * 
     */
    public static function selectClub($club)
    {
        $sql = 'SELECT * FROM puntajes WHERE id =' . $club; //Si se quiere que aparezcan siempre los torneos cuando ya paso el torneo
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Busca los puntajes del club 
     * 
     */
    public static function selectPuntos($club)
    {
        $year = date('Y');
        $sql = 'SELECT * FROM puntajes WHERE club ="' . $club . '" AND year =' . $year; //Si se quiere que aparezcan siempre los torneos cuando ya paso el torneo
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Busca los puntajes del club 
     * 
     */
    public static function selectPuntosProm($club)
    {
        $year = date('Y');
        $sql = 'SELECT *, sum(points) AS suma FROM puntajes WHERE club ="' . $club . '" AND year =' . $year . ' GROUP BY tournament'; //Si se quiere que aparezcan siempre los torneos cuando ya paso el torneo
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Calcula los puntajes promedio de la
     * Base de Datos y tabla Puntajes 
     * 
     */
    public static function selectPromedio()
    {
        $year = date('Y');

        $sql = 'SELECT *, SUM(points) AS suma FROM puntajes WHERE year =' . $year . ' GROUP BY club'; //Si se quiere que aparezcan siempre los torneos cuando ya paso el torneo
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Busca el id del club 
     * 
     */
    public static function idClub($club)
    {
        // Encontramos el id del club
        $sql = 'SELECT id FROM clubes WHERE nombre_club LIKE "%' . $club . '%"';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }



    /**
     * Calcula la cantidad de nadadores del 
     * club 
     * 
     */
    public static function calcularNadadores($club)
    {
        $cantidades = array();
        // Calculamos la cantidad de nadadores
        $sql = 'SELECT * FROM nadadores WHERE id_club = ' . $club . ' AND act_liga = 1';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $cantidades['total'] = $stmt->rowCount();

        //Calculamos los que están en el extranjero
        $sql1 = 'SELECT count(exterior) as exte FROM nadadores WHERE id_club = ' . $club;
        $stmt1 = $db->prepare($sql1);
        $stmt1->execute();
        $exterior = $stmt1->fetch();

        $cantidades['exterior'] = $exterior;


        //Calculamos los que están en selección Colombia
        $sql2 = 'SELECT count(sel_col) as sel_col FROM nadadores WHERE id_club = ' . $club;
        $stmt2 = $db->prepare($sql2);
        $stmt2->execute();
        $selCol = $stmt2->fetch();

        $cantidades['seleccion'] = $selCol;

        //Calculamos los que están inactivos en el club
        $sql3 = 'SELECT count(inactivo) as inact FROM nadadores WHERE id_club = ' . $club;
        $stmt3 = $db->prepare($sql3);
        $stmt3->execute();
        $inactivo = $stmt3->fetch();

        $cantidades['inactivo'] = $inactivo;

        return $cantidades;
    }

    /**
     * Busca los puntajes del club 
     * 
     */
    public static function selectRegistros($id)
    {
        $sql = 'SELECT * FROM puntajes WHERE id = ' . $id;
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Update 
     * El registro seleccionado con los datos
     * del puntaje
     * 
     */
    public function updateRegistro()
    {

        $id_club = PuntajesDb::idClub($this->club);

        if ($id_club) {
            $cantidad = PuntajesDb::calcularNadadores($id_club['id']);
            $liga = $cantidad['total'];
            $inactivos = $cantidad['inactivo']['inact'];
            $exterior = $cantidad['exterior']['exte'];
            $seleccion = $cantidad['seleccion']['sel_col'];
        } else {
            $liga = 1;
            $inactivos = 0;
            $exterior = 0;
            $seleccion = 0;
        }

        $sql = 'UPDATE puntajes
                SET year = :year,
                    tournament = :tournament,
                    club = :club,
                    points = :points,
                    liga = :liga,
                    inactivos = :inactivos,
                    exterior = :exterior,
                    seleccion = :seleccion,
                    updated_at = :updated_at,
                    updated_by_id = :updated_by_id
                    WHERE id = :id';


        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':year', $this->year, PDO::PARAM_INT);
        $stmt->bindValue(':tournament', $this->torneo, PDO::PARAM_STR);
        $stmt->bindValue(':club', $this->club, PDO::PARAM_STR);
        $stmt->bindValue(':points', $this->puntos, PDO::PARAM_STR);
        $stmt->bindValue(':liga', $liga, PDO::PARAM_INT);
        $stmt->bindValue(':inactivos', $inactivos, PDO::PARAM_INT);
        $stmt->bindValue(':exterior', $exterior, PDO::PARAM_INT);
        $stmt->bindValue(':seleccion', $seleccion, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':updated_by_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * Elimina el registro de puntaje
     * seleccionado
     *
     * @return void
     */
    public static function deleteReg($id)
    {
        $sql = 'DELETE FROM puntajes
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);


        return $stmt->execute();
    }
}
