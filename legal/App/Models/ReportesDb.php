<?php

namespace App\Models;

use PDO;


ini_set('date.timezone', 'America/Bogota');

/**
 * Clase para Cargar en la base de datos
 * los datos del torneo
 *
 * PHP version 7.0
 */
class ReportesDb extends \Core\Model
{

    /**
     * Class constructor para la consulta de los torneos
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
     * Selecciona los Rankin de acuerdo a la entrada
     * Si se hace filtro busca y clasifica los ranking 
     * de acuerdo con el filtro 
     * 
     */
    public static function selectRanking()
    {

        if ($_SESSION['control'] == 1) {
            //Los resultados por defecto
            $anos = date('Y') - 10;
            $sql = 'SELECT identificacion, pruebas.nombre as prueba, clubes.nombre_club, min(resultados.tiempo) as tiempo, nadadores.fecha_nac, nadadores.nombre, nadadores.apellido, resultados.prueba_id, resultados.piscina, nadadores.genero, ligas.nombre_liga FROM resultados 
            INNER JOIN nadadores  ON resultados.identificacion = nadadores.nuip
            INNER JOIN clubes ON nadadores.id_club = clubes.id
            INNER JOIN pruebas ON resultados.prueba_id = pruebas.id
            INNER JOIN ligas ON clubes.id_liga = ligas.id
            WHERE resultados.prueba_id = 2 AND nadadores.genero = "F" AND nadadores.fecha_nac BETWEEN "' . $anos . '-01-01" AND "' . $anos . '-12-31" AND resultados.piscina = "LC" 
             AND resultados.fecha_torneo BETWEEN "' . $_SESSION['inicio'] . '" AND "' . $_SESSION['fin'] . '" 
            GROUP BY resultados.identificacion  
            ORDER BY `tiempo`  ASC';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        } else {
            $sql = 'SELECT identificacion, MIN(tiempo) AS tiempo, prueba_id, piscina, nadadores.genero, nadadores.apellido, nadadores.nombre, nadadores.fecha_nac, clubes.nombre_club, pruebas.nombre AS prueba, ligas.nombre_liga
            FROM resultados 
            INNER JOIN nadadores ON resultados.identificacion = nadadores.nuip
            INNER JOIN clubes ON nadadores.id_club = clubes.id
            INNER JOIN pruebas ON resultados.prueba_id = pruebas.id
            INNER JOIN ligas ON clubes.id_liga = ligas.id 
            WHERE prueba_id = ' . $_SESSION['prueba'] . ' AND nadadores.fecha_nac BETWEEN ' . $_SESSION['categoria'] . 
                ' AND resultados.fecha_torneo BETWEEN "' . $_SESSION['inicio'] . '" AND "' . $_SESSION['fin'] . '" ';

            if ($_SESSION['torneo'] ?? false) {
                $sql .= ' AND resultados.torneo = "' . $_SESSION['torneo'] . '" ';
            }

            if ($_SESSION['club_id'] ?? false) {
                $sql .= ' AND clubes.id = "' . $_SESSION['club_id'] . '" ';
            }

            if ($_SESSION['liga'] ?? false) {
                $sql .= ' AND ligas.id = "' . $_SESSION['liga'] . '" ';
            }

            if ($_SESSION['genero']  != 'X') {
                $sql .= ' AND nadadores.genero = "' . $_SESSION['genero'] . '" ';
            }
            $sql .= '
            AND resultados.piscina = "' . $_SESSION['piscina'] . '"
            GROUP BY resultados.identificacion  
            ORDER BY `tiempo`  ASC';            

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->execute();

            

            return $stmt->fetchAll();

            //return $sql;
        }
    }

    /**
     * Selecciona los Rankin de acuerdo a la entrada
     * Si se hace filtro busca y clasifica los ranking 
     * de acuerdo con el filtro 
     * 
     */
    public static function selectFechaTorneo($identificacion, $prueba, $tiempo)
    {

       
            $sql = 'SELECT fecha_torneo FROM resultados 
            WHERE identificacion = ' . $identificacion . ' AND prueba_id = ' . $prueba . ' AND tiempo = "' . $tiempo .'"'; 
             

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->execute();

            return $stmt->fetch();
        
    }

    /**
     * Encontrar todos los torneos para mostrarlos
     *
     * 
     * 
     */
    public static function puntosFina($data)
    {
        $sql = 'SELECT * FROM pruebas            
            WHERE id = ' . $data;

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Encontrar todos los torneos para mostrarlos
     *
     * 
     * 
     */
    public static function fechaTorneo($identi, $tiempo, $prueba) // ya no se usa porque la fecha se obtiene directo
    {
        $sql = 'SELECT * FROM resultados            
            WHERE identificacion = ' . $identi . ' AND tiempo = "' . $tiempo . '" AND prueba_id = ' . $prueba;

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Encontrar todos los torneos para mostrarlos
     *
     * 
     * 
     */
    public static function selectFiltro()
    {
        $anos = date('Y') - $_SESSION['categoria'];

        $sql = 'SELECT pruebas.nombre as prueba, clubes.nombre_club, resultados.fecha_torneo, min(resultados.tiempo) as tiempo, nadadores.fecha_nac, nadadores.nombre, nadadores.apellido, resultados.prueba_id, resultados.piscina, nadadores.genero FROM resultados 
            INNER JOIN nadadores  ON resultados.identificacion = nadadores.nuip
            INNER JOIN clubes ON nadadores.id_club = clubes.id
            INNER JOIN pruebas ON resultados.prueba_id = pruebas.id
            WHERE resultados.prueba_id = ' . $_SESSION['prueba'] . ' AND nadadores.genero = "' . $_SESSION['genero'] . '" AND nadadores.fecha_nac BETWEEN "' . $anos . '-01-01" AND "' . $anos . '-12-31"
            GROUP BY resultados.identificacion  
            ORDER BY `tiempo`  ASC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todos los competenicas
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findCompetencias()
    {
        $sql = 'SELECT * FROM resultados
        GROUP BY torneo
        ORDER BY torneo ASC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todas las ligas
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findLigas()
    {
        $sql = 'SELECT * FROM ligas';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todos los equipos
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findEquipos($id)
    {
        $sql = 'SELECT * FROM clubes 
        WHERE NOT (nombre_club = "no club") AND id_liga = ' . $id .' ORDER BY nombre_club ASC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar el nombre del club 
     * que selecciono
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findSelectEquipos()
    {

        if ($_SESSION['club_id']) {
            $sql = 'SELECT * FROM clubes
            WHERE id = ' . $_SESSION['club_id'];
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->execute();

            return $stmt->fetch();
        } else {
            return false;
        }
    }

    /**
     * Encontrar el nombre de la 
     * prueba
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findPrueba()
    {

        if ($_SESSION['prueba']) {
            $sql = 'SELECT * FROM pruebas
            WHERE id = ' . $_SESSION['prueba'];
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->execute();

            return $stmt->fetch();
        } else {
            return false;
        }
    }

    /**
     * Encontrar todos los nadadores para los
     * listados de busqueda si se requiere mostrar todos se cambia
     * el sql para que liste a todos 
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findNadadores()
    {


        $sql = 'SELECT *, ligas.nombre_liga FROM nadadores
        INNER JOIN clubes  ON nadadores.id_club = clubes.id
        INNER JOIN ligas  ON clubes.id_liga = ligas.id
        WHERE nadadores.status = 1            
        ORDER BY apellido ASC';
        //Este es cuando la liga decida que aparezca solo los nadadores de la LIga de Bogota.
        /* $sql = 'SELECT * FROM nadadores
        INNER JOIN clubes  ON nadadores.id_club = clubes.id
        INNER JOIN ligas  ON clubes.id_liga = ligas.id            
        WHERE ligas.id = 1 
        ORDER BY apellido ASC'; */

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar el nadador seleccionado
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findNadador($id)
    {

        $sql = 'SELECT * FROM nadadores
            INNER JOIN clubes ON nadadores.id_club = clubes.id
            INNER JOIN ligas ON clubes.id_liga = ligas.id
            WHERE nadadores.nuip=' . $id;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Selecciona los datos a filtrar 
     * 
     */
    public static function selectHistorial($cod)
    {
        $sql = 'SELECT resultados.prueba_id, resultados.fecha, resultados.tiempo, resultados.torneo, resultados.fecha_torneo, resultados.piscina, nadadores.genero, nadadores.fecha_nac  FROM resultados
            INNER JOIN nadadores  ON resultados.identificacion = nadadores.nuip            
            WHERE resultados.identificacion =' . $_SESSION['histo_nada'] . ' AND resultados.prueba_id =' . $cod; 
        if ($_SESSION['inicio'] AND $_SESSION['fin']) {
            $sql .= ' AND resultados.fecha_torneo BETWEEN "' . $_SESSION['inicio'] . '" AND "' . $_SESSION['fin'] . '"';
        }
        if ($_SESSION['anual'] == 1) {
            $sql .= ' GROUP BY YEAR(resultados.fecha_torneo)';
        }
        $sql .= ' ORDER BY resultados.tiempo  ASC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
        //return $sql;
    }

    /**
     * Selecciona los datos a filtrar 
     * 
     */
    public static function selectComparado($nada)
    {
        $sql = 'SELECT resultados.torneo, resultados.fecha_torneo, resultados.tiempo, resultados.piscina, nadadores.fecha_nac, nadadores.nombre, nadadores.genero 
        FROM resultados JOIN nadadores ON resultados.identificacion = nadadores.nuip 
        WHERE resultados.identificacion = ' . $nada . ' AND resultados.prueba_id=' . $_SESSION['prueba'];
        if ($_SESSION['ano']) {
            $sql .= ' AND YEAR(resultados.fecha_torneo) = "' . $_SESSION['ano'] . '"';
        }
        $sql .= ' GROUP BY YEAR(resultados.fecha_torneo) AND resultados.tiempo = (SELECT MIN(resultados.tiempo) FROM resultados WHERE resultados.identificacion = ' . $nada . ' )';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();

        //return array_merge($stmt->fetchAll(), $stmt1->fetchAll());
        //return $sql;
    }

    /**
     * Selecciona los datos a filtrar 
     * 
     */
    public static function selectRecord($id)
    {
        $sql = 'SELECT * FROM resultados ';        

        if ($_SESSION['prueba'] != 1) {
            $sql .= 'INNER JOIN nadadores  ON resultados.identificacion = nadadores.nuip  WHERE resultados.tiempo = (SELECT MIN(resultados.tiempo) FROM resultados INNER JOIN nadadores  ON resultados.identificacion = nadadores.nuip 
            WHERE prueba_id=' . $_SESSION['prueba'] . ' AND nadadores.genero = "' . $_SESSION['genero'] . '" AND nadadores.fecha_nac BETWEEN ' . $_SESSION['categoria'] . ') 
            AND prueba_id=' . $_SESSION['prueba'] .' AND nadadores.genero = "' . $_SESSION['genero'] . '" AND nadadores.fecha_nac BETWEEN ' . $_SESSION['categoria'];            
        } elseif($_SESSION['genero']) {
            $sql .= 'INNER JOIN nadadores  ON resultados.identificacion = nadadores.nuip  WHERE resultados.tiempo = (SELECT MIN(resultados.tiempo) FROM resultados INNER JOIN nadadores  ON resultados.identificacion = nadadores.nuip 
            WHERE prueba_id=' . $id . ' AND nadadores.genero = "' . $_SESSION['genero'] . '" AND nadadores.fecha_nac BETWEEN ' . $_SESSION['categoria'] . ') 
            AND prueba_id=' . $id .' AND nadadores.genero = "' . $_SESSION['genero'] . '" AND nadadores.fecha_nac BETWEEN ' . $_SESSION['categoria'];
        } else {
            $sql .= 'INNER JOIN nadadores  ON resultados.identificacion = nadadores.nuip WHERE tiempo = (SELECT MIN(resultados.tiempo) FROM resultados WHERE prueba_id=' . $id . ') AND prueba_id=' . $id;
        }

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();

        //return array_merge($stmt->fetchAll(), $stmt1->fetchAll());
        //return $sql;
    }

    /**
     * Selecciona los datos a filtrar 
     * 
     */
    public static function selectStilo($st)
    {
        $sql = 'SELECT * FROM pruebas WHERE id = ' . $st;

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();

        //return array_merge($stmt->fetchAll(), $stmt1->fetchAll());
        //return $sql;
    }

    /**
     * Encontrar la liga
     *
     * 
     * 
     */
    public static function findLiga($data)
    {
        $sql = 'SELECT * FROM ligas            
            WHERE id = ' . $data;

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }
}
