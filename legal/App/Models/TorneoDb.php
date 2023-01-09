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
class TorneoDb extends \Core\Model
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
     * Items index
     *
     * @return void
     */
    public static function crearTorneo($file)
    {
        if (($gestor = fopen($file, "r")) !== FALSE) {
            if (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {

                $nombre = utf8_encode($datos[0]);
                $lugar = utf8_encode($datos[1]);
                $fecha_ini = date('Y-m-d', strtotime($datos[2]));
                $fecha_fin = date('Y-m-d', strtotime($datos[3]));
                $fecha_corte = date('Y-m-d', strtotime($datos[4]));
                $lo = $datos[5];
                $vacio_1 = $datos[6];
                $vacio_2 = $datos[7];
                $vacio_3 = $datos[8];
                $software = utf8_encode($datos[9]);
                $nombre_usu = utf8_encode($datos[10]);
                $tipo = utf8_encode($datos[11]);
                $fecha_semb = date('Y-m-d', strtotime($datos[12]));
                $vacio_4 = $datos[13];
                $vacio_5 = $datos[14];
                $edad_min_abie = $datos[15];
                $fecha_aper_insc = date('Y-m-d', strtotime($datos[16]));
                $vacio_6 = $datos[17];
                $max_ins_con_rel = $datos[18];
                $max_ins_indiv = $datos[19];
                $max_ins_rel = $datos[20];
                $vacio_7 = $datos[21];
                $vacio_8 = $datos[22];
                $fecha_cre = date('Y-m-d', strtotime($datos[23]));
                $vacio_9 = $datos[24];
                $vacio_10 = $datos[25];
                $ciudad = utf8_encode($datos[26]);
                $pais = utf8_encode($datos[27]);
                $codigo_post = $datos[28];
                $pais_1 = utf8_encode($datos[29]);
                $vacio_11 = $datos[30];
                $ene = $datos[31];
                $ene_1 = $datos[32];
                $fecha_final = date('Y-m-d', strtotime($datos[33]));
                $fin = $datos[34];
            }
            fclose($gestor);
        }

        $sql = 'INSERT INTO torneos (nombre, lugar, fecha_ini, fecha_fin, fecha_corte, lo, vacio_1, vacio_2, vacio_3, software, nombre_usu, tipo, fecha_semb, vacio_4, vacio_5, edad_min_abie, fecha_aper_insc, vacio_6, max_ins_con_rel, max_ins_indiv, max_ins_rel, vacio_7, vacio_8, fecha_cre, vacio_9, vacio_10, ciudad, pais, codigo_post, pais_1, vacio_11, ene, ene_1, fecha_final, fin, created_at, updated_at, limt, user)
        VALUES (:nombre, :lugar, :fecha_ini, :fecha_fin, :fecha_corte, :lo, :vacio_1, :vacio_2, :vacio_3, :software, :nombre_usu, :tipo, :fecha_semb, :vacio_4, :vacio_5, :edad_min_abie, :fecha_aper_insc, :vacio_6, :max_ins_con_rel, :max_ins_indiv, :max_ins_rel, :vacio_7, :vacio_8, :fecha_cre, :vacio_9, :vacio_10, :ciudad, :pais, :codigo_post, :pais_1, :vacio_11, :ene, :ene_1, :fecha_final, :fin, :created_at, :updated_at, :limt, :user)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':lugar', $lugar, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_ini', $fecha_ini, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_fin', $fecha_fin, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_corte', $fecha_corte, PDO::PARAM_STR);
        $stmt->bindValue(':lo', $lo, PDO::PARAM_STR);
        $stmt->bindValue(':vacio_1', $vacio_1, PDO::PARAM_INT);
        $stmt->bindValue(':vacio_2', $vacio_2, PDO::PARAM_INT);
        $stmt->bindValue(':vacio_3', $vacio_3, PDO::PARAM_INT);
        $stmt->bindValue(':software', $software, PDO::PARAM_STR);
        $stmt->bindValue(':nombre_usu', $nombre_usu, PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_semb', $fecha_semb, PDO::PARAM_STR);
        $stmt->bindValue(':vacio_4', $vacio_4, PDO::PARAM_INT);
        $stmt->bindValue(':vacio_5', $vacio_5, PDO::PARAM_INT);
        $stmt->bindValue(':edad_min_abie', $edad_min_abie, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_aper_insc', $fecha_aper_insc, PDO::PARAM_STR);
        $stmt->bindValue(':vacio_6', $vacio_6, PDO::PARAM_INT);
        $stmt->bindValue(':max_ins_con_rel', $max_ins_con_rel, PDO::PARAM_STR);
        $stmt->bindValue(':max_ins_indiv', $max_ins_indiv, PDO::PARAM_STR);
        $stmt->bindValue(':max_ins_rel', $max_ins_rel, PDO::PARAM_STR);
        $stmt->bindValue(':vacio_7', $vacio_7, PDO::PARAM_INT);
        $stmt->bindValue(':vacio_8', $vacio_8, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_cre', $fecha_cre, PDO::PARAM_STR);
        $stmt->bindValue(':vacio_9', $vacio_9, PDO::PARAM_INT);
        $stmt->bindValue(':vacio_10', $vacio_10, PDO::PARAM_INT);
        $stmt->bindValue(':ciudad', $ciudad, PDO::PARAM_STR);
        $stmt->bindValue(':pais', $pais, PDO::PARAM_STR);
        $stmt->bindValue(':codigo_post', $codigo_post, PDO::PARAM_STR);
        $stmt->bindValue(':pais_1', $pais_1, PDO::PARAM_STR);
        $stmt->bindValue(':vacio_11', $vacio_11, PDO::PARAM_INT);
        $stmt->bindValue(':ene', $ene, PDO::PARAM_STR);
        $stmt->bindValue(':ene_1', $ene_1, PDO::PARAM_STR);
        $stmt->bindValue(':fecha_final', $fecha_final, PDO::PARAM_STR);
        $stmt->bindValue(':fin', $fin, PDO::PARAM_STR);
        $stmt->bindValue(':created_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':limt', 0, PDO::PARAM_INT);
        $stmt->bindValue(':user', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->execute();

        $id_torneo = $db->lastInsertId();

        $fila = 2;
        if (($gestor = fopen($file, "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {
                $numero = count($datos);
                if ($numero < 31) {

                    $numero = $datos[0];
                    $nombre1 = $datos[1];
                    $clase = $datos[2];
                    $vacio = $datos[3];
                    $tipo1 = $datos[4];
                    $genero = $datos[5];
                    $categoria_1 = $datos[6];
                    $categoria_2 = $datos[7];
                    $metros = $datos[8];
                    $estilo = $datos[9];
                    $vacio_1 = $datos[10];
                    $vacio_2 = $datos[11];
                    $vacio_3 = $datos[12];
                    $vacio_4 = $datos[13];
                    $vacio_5 = $datos[14];
                    $vacio_6 = $datos[15];
                    $vacio_7 = $datos[16];
                    $vacio_8 = $datos[17];
                    $vacio_9 = $datos[18];
                    $vacio_10 = $datos[19];
                    $vacio_11 = $datos[20];
                    $sesion = $datos[21];
                    $prueba = $datos[22];
                    $jornada = $datos[23];
                    $hora = $datos[24];
                    $piscina = $datos[25];
                    $max_inc_rel = $datos[26];
                    $max_indiv = $datos[27];
                    $max_rel = $datos[28];
                    $vacio_12 = $datos[29];

                    $sql1 = 'INSERT INTO pruebas_torneo (id_torneo, numero, nombre, clase, vacio, tipo, genero, categoria_1, categoria_2, metros, estilo, vacio_1, vacio_2, vacio_3, vacio_4, vacio_5, vacio_6, vacio_7, vacio_8, vacio_9, vacio_10, vacio_11, sesion, prueba, jornada, hora, piscina, max_inc_rel, max_indiv, max_rel, vacio_12, created_at, updated_at)
                                                VALUES (:id_torneo, :numero, :nombre1, :clase, :vacio, :tipo1, :genero, :categoria_1, :categoria_2, :metros, :estilo, :vacio_1, :vacio_2, :vacio_3, :vacio_4, :vacio_5, :vacio_6, :vacio_7, :vacio_8, :vacio_9, :vacio_10, :vacio_11, :sesion, :prueba, :jornada, :hora, :piscina, :max_inc_rel, :max_indiv, :max_rel, :vacio_12, :created_at, :updated_at)';

                    $stmt1 = $db->prepare($sql1);

                    $stmt1->bindValue(':id_torneo', $id_torneo, PDO::PARAM_INT);
                    $stmt1->bindValue(':numero', $numero, PDO::PARAM_INT);
                    $stmt1->bindValue(':nombre1', $nombre1, PDO::PARAM_STR);
                    $stmt1->bindValue(':clase', $clase, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio', $vacio, PDO::PARAM_STR);
                    $stmt1->bindValue(':tipo1', $tipo1, PDO::PARAM_STR);
                    $stmt1->bindValue(':genero', $genero, PDO::PARAM_STR);
                    $stmt1->bindValue(':categoria_1', $categoria_1, PDO::PARAM_STR);
                    $stmt1->bindValue(':categoria_2', $categoria_2, PDO::PARAM_STR);
                    $stmt1->bindValue(':metros', $metros, PDO::PARAM_STR);
                    $stmt1->bindValue(':estilo', $estilo, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_1', $vacio_1, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_2', $vacio_2, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_3', $vacio_3, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_4', $vacio_4, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_5', $vacio_5, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_6', $vacio_6, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_7', $vacio_7, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_8', $vacio_8, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_9', $vacio_9, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_10', $vacio_10, PDO::PARAM_STR);
                    $stmt1->bindValue(':vacio_11', $vacio_11, PDO::PARAM_STR);
                    $stmt1->bindValue(':sesion', $sesion, PDO::PARAM_INT);
                    $stmt1->bindValue(':prueba', $prueba, PDO::PARAM_INT);
                    $stmt1->bindValue(':jornada', $jornada, PDO::PARAM_INT);
                    $stmt1->bindValue(':hora', $hora, PDO::PARAM_STR);
                    $stmt1->bindValue(':piscina', $piscina, PDO::PARAM_INT);
                    $stmt1->bindValue(':max_inc_rel', $max_inc_rel, PDO::PARAM_INT);
                    $stmt1->bindValue(':max_indiv', $max_indiv, PDO::PARAM_INT);
                    $stmt1->bindValue(':max_rel', $max_rel, PDO::PARAM_INT);
                    $stmt1->bindValue(':vacio_12', $vacio_12, PDO::PARAM_STR);
                    $stmt1->bindValue(':created_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
                    $stmt1->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);

                    $stmt1->execute();
                }
                $fila++;
            }
            fclose($gestor);
        }

        return true;
    }

    /**
     * Encontrar todos los torneos para mostrarlos
     *
     * 
     * 
     */
    public static function selectTorneos()
    {
        //$sql = 'SELECT * FROM torneos WHERE fecha_ini > CURDATE()'; //Si se quiere que se desaparezcan los torneos cuando ya paso el torneo
        $sql = 'SELECT * FROM torneos'; //Si se quiere que aparezcan siempre los torneos cuando ya paso el torneo
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todas las pruebas para mostrarlos
     *
     * 
     * 
     */
    public static function selectPruebas($genero1, $genero2, $edad)
    {

        $sql = 'SELECT * FROM pruebas_torneo WHERE id_torneo=' . $_SESSION['torneo'] . ' AND categoria_1 <=' . $edad . ' AND categoria_2 >=' . $edad . ' AND (genero ="' . $genero1 . '" OR genero ="' . $genero2 . '" OR genero = "X")';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Busca los datos del nadador
     *
     * 
     * 
     */
    public static function selectNada()
    {
        //buscar los datos del nadador
        $sql1 = 'SELECT * FROM nadadores WHERE id =' . $_SESSION['nadador'];
        $db = static::getDB();
        $stmt1 = $db->prepare($sql1);
        $stmt1->execute();

        return $stmt1->fetch();
    }

    /**
     * Busca los datos del nadador
     * se soloco este mientras se define como se busca
     * con fetchAll
     *
     * 
     * 
     */
    public static function selectNadapru()
    {
        //buscar los datos del nadador
        $sql1 = 'SELECT * FROM nadadores WHERE id =' . $_SESSION['nadador'];
        $db = static::getDB();
        $stmt1 = $db->prepare($sql1);
        $stmt1->execute();

        return $stmt1->fetch();
    }

    /**
     * Encontrar todas las pruebas para mostrarlos
     *
     * 
     * 
     */
    public static function selectNadadores()
    {
        if ($_SESSION['user_role'] != 3) {
            $sql = 'SELECT * FROM nadadores WHERE status=1';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql);
    
            $stmt->execute();
    
            return $stmt->fetchAll();
            
        } else {
            $sql = 'SELECT * FROM nadadores WHERE id_club=' . $_SESSION['user_club'] .' AND status=1';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql);
    
            $stmt->execute();
    
            return $stmt->fetchAll();
        }
        

    }

    /**
     * Encuentra los tiempos del nadador
     *
     * 
     * 
     */
    public static function selectNadador($id)
    {
        $datosTorneo = TorneoDb::torneo($_SESSION['torneo']);
        $_SESSION['piscina'] = $datosTorneo['lo'];

        if ($_SESSION['piscina'] === 'LO') {
            $_SESSION['piscina'] = 'LC';
        } else {
            $_SESSION['piscina'] = 'SC';
        }        

        $sql = 'SELECT fecha_torneo, min(tiempo) AS t, torneo, prueba_id 
        FROM resultados         
        WHERE fecha_torneo > DATE_SUB(CURDATE(),INTERVAL 1 YEAR) AND identificacion = ' . $id . ' AND piscina = "' . $_SESSION['piscina'] . '" 
        GROUP BY prueba_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encuentra el id del menor tiermpo
     *
     * 
     * 
     */
    public static function selectId($id, $tiempo, $prueba_id)
    {

        $sql = 'SELECT * FROM resultados WHERE tiempo ="' . $tiempo . '" AND prueba_id =' . $prueba_id . ' AND  identificacion = ' . $id;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todos los torneos para mostrarlos
     *
     * 
     * 
     */
    public static function crearInscripcion($id_n, $id_pru, $id_torneo)
    {
        //primero obtenemos el mejor tiempo.
        $bus_prueba = 'SELECT pruebas.id, min(resultados.tiempo) AS t  FROM pruebas 
        INNER JOIN pruebas_torneo ON pruebas.metros = pruebas_torneo.metros AND pruebas.estilo = pruebas_torneo.estilo
        INNER JOIN resultados ON pruebas.id = resultados.prueba_id
        INNER JOIN nadadores ON resultados.identificacion = nadadores.nuip
        WHERE pruebas_torneo.id =' . $id_pru . ' AND nadadores.id =' . $id_n .' AND resultados.piscina = "' . $_SESSION['piscina'] .'"';

        $db = static::getDB();
        $busq = $db->prepare($bus_prueba);

        $busq->execute();

        $tiempo = $busq->fetchAll();


        $menor_tiempo = $tiempo[0]['t'];

        //luego guardamos la i nscripción.        
        $sql = 'INSERT INTO inscripciones (id_torneo, id_club, id_nadador, id_pruebas_torneo, tiempo_1, created_at, updated_at)
        VALUES (:id_torneo, :id_club, :id_nadador, :id_pruebas_torneo, :tiempo_1, :created_at, :updated_at)';


        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id_torneo', $id_torneo, PDO::PARAM_INT);
        $stmt->bindValue(':id_club', $_SESSION['id_club'], PDO::PARAM_INT);
        $stmt->bindValue(':id_nadador', $id_n, PDO::PARAM_INT);
        $stmt->bindValue(':id_pruebas_torneo', $id_pru, PDO::PARAM_INT);
        $stmt->bindValue(':tiempo_1', $menor_tiempo, PDO::PARAM_STR);

        $stmt->bindValue(':created_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);

        $stmt->execute();

        return true;
    }

    /**
     * Encontrar el nombre de la prueba
     *
     * @return void
     */
    public static function nombrePrueba($id_prueba)
    {
        $sql = 'SELECT * FROM pruebas WHERE id=' . $id_prueba;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todos los inscritos en el torneo
     * para editar tiempos o eliminar
     *
     * 
     * 
     */
    public static function selectResumen()
    {

        if ($_SESSION['user_role'] != 3) {
            $sql = 'SELECT nadadores.id AS idn, nadadores.nuip, nadadores.nombre, nadadores.apellido, pruebas_torneo.metros, pruebas_torneo.estilo, pruebas_torneo.jornada, pruebas_torneo.sesion, pruebas_torneo.numero, pruebas_torneo.hora, inscripciones.id, inscripciones.tiempo_1, clubes.nombre_club 
            FROM nadadores 
            INNER JOIN inscripciones ON nadadores.id = inscripciones.id_nadador 
            INNER JOIN pruebas_torneo ON inscripciones.id_pruebas_torneo = pruebas_torneo.id
            INNER JOIN clubes ON inscripciones.id_club = clubes.id
            WHERE pruebas_torneo.id_torneo =' . $_SESSION['resumen'] . '
            ORDER BY nadadores.apellido ASC';
        } else {
            $sql = 'SELECT nadadores.id AS idn, nadadores.nuip, nadadores.nombre, nadadores.apellido, pruebas_torneo.metros, pruebas_torneo.estilo, pruebas_torneo.jornada, pruebas_torneo.sesion, pruebas_torneo.numero, pruebas_torneo.hora, inscripciones.id, inscripciones.tiempo_1, clubes.nombre_club 
            FROM nadadores 
            INNER JOIN inscripciones ON nadadores.id = inscripciones.id_nadador 
            INNER JOIN pruebas_torneo ON inscripciones.id_pruebas_torneo = pruebas_torneo.id
            INNER JOIN clubes ON inscripciones.id_club = clubes.id
            WHERE pruebas_torneo.id_torneo =' . $_SESSION['resumen'] . ' AND inscripciones.id_club =' . $_SESSION['user_club'] . '
            ORDER BY nadadores.apellido ASC';
        }

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar la cantidad de pruebas 
     * inscritas del nadador en el torneo seleccionado
     *
     * @return void
     */
    public static function maxTorneo($id_torneo, $id_n)
    {
        $sql = 'SELECT * FROM inscripciones WHERE id_torneo =' . $id_torneo . ' AND id_nadador=' . $id_n;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar la prueba en la que esta inscrito
     * en el
     * del torneo
     *
     * @return void
     */
    public static function maxPrueba($id_torneo, $id_n, $id_pru)
    {
        $sql = 'SELECT * FROM inscripciones WHERE id_torneo =' . $id_torneo . ' AND id_nadador=' . $id_n . ' AND id_pruebas_torneo=' . $id_pru;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function mostrar($id_pru)
    {
        $bus_prueba = 'SELECT pruebas.id, min(resultados.tiempo) AS t  FROM pruebas 
        INNER JOIN pruebas_torneo ON pruebas.metros = pruebas_torneo.metros AND pruebas.estilo = pruebas_torneo.estilo
        INNER JOIN resultados ON pruebas.id = resultados.prueba_id
        WHERE pruebas_torneo.id =' .  $id_pru;

        $db = static::getDB();
        $busq = $db->prepare($bus_prueba);

        $busq->execute();

        return $busq->fetchAll();
    }

    /**
     * Borra de la base de datos
     * la prueba seleccionada
     *
     * 
     * 
     */
    public static function borrarPrueba($id)
    {
        $sql = 'DELETE FROM inscripciones WHERE id =' . $id;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Borra de la base de datos
     * todas las pruebas del nadador
     * seleccionado 
     * 
     */
    public static function borrarTodas($id)
    {
        $sql = 'DELETE FROM inscripciones WHERE id_nadador =' . $id .' AND id_torneo ='. $_SESSION['resumen'];

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Encontrar la sesión del torneo
     * en donde esta inscritopara las
     * restricciones
     * por sesión
     *
     * @return void
     */
    public static function jornada($id_pru)
    {
        $sql = 'SELECT * FROM pruebas_torneo WHERE id =' . $id_pru;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Encontrar la cantidad de pruebas 
     * por sesion
     *
     * @return void
     */
    public static function maxJornada($id_n, $cantidad)
    {
        $sql = 'SELECT inscripciones.id_pruebas_torneo, pruebas_torneo.sesion FROM inscripciones 
        INNER JOIN pruebas_torneo ON inscripciones.id_pruebas_torneo = pruebas_torneo.id 
        WHERE inscripciones.id_nadador =' . $id_n . ' AND pruebas_torneo.sesion =' . $cantidad . ' AND inscripciones.id_torneo =' . $_SESSION['torneo'];

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar la cantidad de pruebas 
     * individual en la sesión
     *
     * @return void
     */
    public static function maxIndividual($id_n, $torneo_sesion, $id_torneo)
    {
        $sql = 'SELECT inscripciones.id_pruebas_torneo, pruebas_torneo.tipo FROM inscripciones 
        INNER JOIN pruebas_torneo ON inscripciones.id_pruebas_torneo = pruebas_torneo.id 
        WHERE inscripciones.id_nadador =' . $id_n . ' AND inscripciones.id_torneo =' . $id_torneo . ' AND pruebas_torneo.tipo = "I" AND pruebas_torneo.sesion =' . $torneo_sesion;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar la cantidad de pruebas 
     * de relevo en la sesión
     *
     * @return void
     */
    public static function maxRelevo($id_n, $torneo_sesion)
    {
        $sql = 'SELECT inscripciones.id_pruebas_torneo, pruebas_torneo.tipo FROM inscripciones 
        INNER JOIN pruebas_torneo ON inscripciones.id_pruebas_torneo = pruebas_torneo.id 
        WHERE inscripciones.id_nadador =' . $id_n . ' AND pruebas_torneo.tipo = "R" AND pruebas_torneo.sesion =' . $torneo_sesion;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Actualizar el tiempo del
     * NAdador inscrito
     * 
     * 
     */
    public static function updateTime($data)
    {
        $updated_at = time();

        $id = $data['id_inscripcion'];

        $tiempo = $data['hora'] . ':' . $data['minutos'] . ':' . $data['segundos'] . '.' . $data['centesimas'];

        $sql = 'UPDATE inscripciones
                    SET tiempo_1 = :tiempo_1,                        
                        updated_at = :updated_at
                    ';
        $sql .= "\nWHERE id = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':tiempo_1', $tiempo, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', $updated_at), PDO::PARAM_STR);

        //$stmt->debugDumpParams();

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Buscar las caracteristicas y 
     * activaciones del club
     *
     * 
     * 
     */
    public static function club($club)
    {

        $sql = 'SELECT * FROM clubes WHERE id=' . $club;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todos los inscritos en el torneo
     * para editar tiempos o eliminar
     *
     * 
     * 
     */
    public static function selectResumenada()
    {

        
        $sql = 'SELECT nadadores.nombre, nadadores.apellido, pruebas_torneo.metros, pruebas_torneo.estilo, pruebas_torneo.jornada, pruebas_torneo.sesion, pruebas_torneo.numero, pruebas_torneo.hora, inscripciones.id, inscripciones.tiempo_1, clubes.nombre_club 
        FROM nadadores 
        INNER JOIN inscripciones ON nadadores.id = inscripciones.id_nadador 
        INNER JOIN pruebas_torneo ON inscripciones.id_pruebas_torneo = pruebas_torneo.id
        INNER JOIN clubes ON inscripciones.id_club = clubes.id
        WHERE pruebas_torneo.id_torneo =' . $_SESSION['torneo'] . ' AND inscripciones.id_nadador =' . $_SESSION['nadador'] . '
        ORDER BY nadadores.apellido ASC';
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    /**
     * Encontrar cuantas pruebas 
     * se inscribieron en el troneo
     *
     * 
     * 
     */
    public static function resumenClub($control)
    {
        if ($control == 'admin') {
            $sql = 'SELECT * FROM inscripciones WHERE id_torneo ='.$_SESSION['resumen'];
        } else {
            $sql = 'SELECT * FROM inscripciones WHERE id_torneo ='.$_SESSION['resumen']. ' AND id_club =' . $_SESSION['user_club'];
        } 
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Encontrar cuantos nadadores se inscribieron 
     * en el torneo
     * 
     * 
     */
    public static function resumenNada($control)
    {

        if ($control == 'admin') {
            $sql = 'SELECT * FROM inscripciones WHERE id_torneo ='.$_SESSION['resumen']. ' GROUP BY id_nadador';
        } else {
            $sql = 'SELECT * FROM inscripciones WHERE id_torneo ='.$_SESSION['resumen']. ' AND id_club =' . $_SESSION['user_club']. ' GROUP BY id_nadador';
        } 
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Encontrar cuantas pruebas 
     * se inscribieron en el troneo
     *
     * 
     * 
     */
    public static function resumenClubes()
    {
        
        $sql = 'SELECT * FROM inscripciones WHERE id_torneo ='.$_SESSION['resumen']. ' GROUP BY id_club';
        
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Busca los datos del torneo especifico
     * se soloco este mientras se define como se busca
     * con fetchAll
     *
     * 
     * 
     */
    public static function torneo($id_torneo)
    {
        //buscar los datos del nadador
        $sql1 = 'SELECT * FROM torneos WHERE id =' . $id_torneo;
        $db = static::getDB();
        $stmt1 = $db->prepare($sql1);
        $stmt1->execute();

        return $stmt1->fetch();
    }

    /**
     * Update the torneo
     *
     * @param array $data Data from the edit profile form
     *
     * @return boolean  True if the data was updated, false otherwise
     */
    public static function updateTorneo($data, $fecha, $limite)
    {
        
        $updated_at = time();          

        $sql = 'UPDATE torneos
                    SET fecha_cre = :fecha_cre,                        
                        updated_at = :updated_at,
                        limt = :limt
                    ';
            $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $data, PDO::PARAM_INT);          
            $stmt->bindValue(':fecha_cre', $fecha, PDO::PARAM_STR);            
            $stmt->bindValue(':updated_at', date('Y-m-d H:i:s', $updated_at), PDO::PARAM_STR);
            $stmt->bindValue(':limt', $limite, PDO::PARAM_INT);
            

            return $stmt->execute();          
    }

    /**
     * Consulta cuantos nadadores hay
     * inscritos
     *
     * 
     * 
     */
    public static function torneoCantidad($id_torneo)
    {
        //buscar los datos del nadador
        $sql1 = 'SELECT * FROM inscripciones WHERE id_torneo =' . $id_torneo;
        $sql1 .= ' GROUP BY id_nadador';
        $db = static::getDB();
        $stmt1 = $db->prepare($sql1);
        $stmt1->execute();

        return $stmt1->fetchAll();
    }

    /**
     * Consulta si el nadador esta inscrito
     *
     * 
     * 
     */
    public static function nadadorInscrito($id_torneo, $id_n)
    {
        //buscar los datos del nadador
        $sql1 = 'SELECT * FROM inscripciones WHERE id_torneo =' . $id_torneo . ' AND id_nadador =' . $id_n;        
        $db = static::getDB();
        $stmt1 = $db->prepare($sql1);
        $stmt1->execute();

        return $stmt1->fetchAll();
    }

    /**
     * Borrar torneo
     *  
     * 
     */
    public static function borrarTorneo($id)
    {
        $sql = 'DELETE FROM torneos WHERE id =' . $id;

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute(); 

        if ($stmt->rowCount()>0) {
            return true;
        } else {
            return false;
        }
    }
}
