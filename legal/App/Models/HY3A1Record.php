<?php

namespace App\Models;

use PDO;


ini_set('date.timezone', 'America/Bogota');

/**
 * HY3 A1 record
 *
 * @author Mike Walsh <mpwalsh8@gmail.com>
 * @access public
 * @see HY3Record
 */
class   HY3A1Record extends \Core\Model
{
    /**
     * Class constructor para la consulta de las inscripociones
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
     * Encontrar todos los clubes para  mostrarlos
     *
     * 
     * 
     */
    public static function selectClubes($id_torneo)
    {
        $sql = 'SELECT clubes.nombre_club, clubes.id  FROM clubes 
        INNER JOIN inscripciones ON clubes.id = inscripciones.id_club        
        WHERE inscripciones.id_torneo =' .  $id_torneo . '
        GROUP BY clubes.id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todos los clubes para  mostrarlos
     *
     * 
     * 
     */
    public static function selectB1($id_torneo)
    {
        $sql = 'SELECT *  FROM torneos 
        WHERE id =' .  $id_torneo;
        

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todos los clubes para  mostrarlos
     *
     * 
     * 
     */
    public static function selectC1($id_torneo)
    {
        $sql = 'SELECT *  FROM clubes 
        WHERE id =' .  $id_torneo;
        

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
    public static function selectNadadores()
    {
       
        $sql = 'SELECT nadadores.id, nadadores.genero, nadadores.nombre, nadadores.apellido, nadadores.nuip, nadadores.fecha_nac 
        FROM nadadores 
        INNER JOIN inscripciones ON nadadores.id = inscripciones.id_nadador 
        WHERE inscripciones.id_torneo =' . $_SESSION['torneo_club'] .' AND inscripciones.id_club =' . $_SESSION['id_club'] . '
        GROUP BY nadadores.id
        ORDER BY nadadores.apellido ASC';

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
    public static function selectPruebas($id_nada)
    {
       
        $sql = 'SELECT nadadores.genero, nadadores.id, nadadores.apellido, pruebas_torneo.nombre, pruebas_torneo.categoria_1, pruebas_torneo.categoria_2, pruebas_torneo.metros, pruebas_torneo.estilo, pruebas_torneo.genero AS gp, torneos.lo, inscripciones.tiempo_1, torneos.id 
        FROM nadadores 
        INNER JOIN inscripciones ON nadadores.id = inscripciones.id_nadador 
        INNER JOIN pruebas_torneo ON inscripciones.id_pruebas_torneo = pruebas_torneo.id
        INNER JOIN torneos ON pruebas_torneo.id_torneo = torneos.id 
        WHERE inscripciones.id_nadador ='. $id_nada .' AND inscripciones.id_torneo ='.$_SESSION['torneo_club'] .'
        ORDER BY nadadores.apellido ASC';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Encontrar todos los clubes para  mostrarlos
     *
     * 
     * 
     */
    public static function cantidadGenero($torneo, $idClub, $genero)
    {
        $sql = 'SELECT count(*)  FROM nadadores 
        INNER JOIN inscripciones ON nadadores.id = inscripciones.id_nadador       
        WHERE nadadores.genero ="' .  $genero . '" AND inscripciones.id_club =' .  $idClub . ' AND inscripciones.id_torneo =' .  $torneo . '
        GROUP BY nadadores.id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->rowCount();
    }

}

?>