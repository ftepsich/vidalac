<?php


class Model_DbTable_CaracteristicasValores extends Rad_Db_Table
{
    protected $_name = 'CaracteristicasValores';

    protected $_referenceMap    = array(
        'CaracteristicasModelos' => array(
            'columns'       => 'CaracteristicaModelo',
            'refTableClass' => 'Model_DbTable_CaracteristicasModelos',
            'refTable'      => 'CaracteristicasModelos',
            'refColumns'    => 'Id'
        )
    );

    protected $_dependentTables = array();

    /**
     * Retorna un array con las caracteristicas y sus respectivos valores dado el id del modelo
     */
    public static function getCaracteristicasValores($modelo, $id)
    {
        $db = Zend_Registry::get('db');

        if (!is_numeric($modelo)) {
            //$modelo = $db->quote($modelo);
            if (!is_string($modelo)) throw new Rad_Db_Table_Exception('El parametro modelo debe ser númerico o un string con la clase del modelo');

            // Para que exista para el mensaje de error
            $m = $modelo;

            // Recupero el id del modelo
            $modelo = $db->fetchOne("SELECT Id From Modelos where Descripcion = '$modelo'");

            if (!$modelo) throw new Rad_Db_Table_Exception('No se encontro el modelo '.$m);
        }

        $valores = $db->fetchPairs("
            SELECT  C.Descripcion, CV.Valor
            FROM    Caracteristicas C
                    inner join CaracteristicasModelos CM on C.Id = CM.Caracteristica and CM.Modelo = $modelo
                    inner join CaracteristicasValores CV on CM.Id = CV.CaracteristicaModelo
            WHERE   CV.IdModelo = $id;
        ");

        return $valores;
    }
    /**
     * [getValor description]
     * @param  [type] $idDato         [description]
     * @param  [type] $caracteristica [description]
     * @param  [type] $modelo         [description]
     * @return [type]                 [description]
     */
    public function getValor($idDato, $caracteristica, $modelo) {

        // throw new Rad_Db_Table_Exception(" ".$idDato." ". $caracteristica." ". $modelo);

        // guardo el nombre del modeo para que exista para el mensaje de error
        $m = $modelo;

        $db = Zend_Registry::get('db');

        // Recupero el id del modelo
        if (!is_numeric($modelo)) {
            if (!is_string($modelo)) throw new Rad_Db_Table_Exception('El parametro modelo debe ser númerico o un string con la clase del modelo');
            // Recupero el id del modelo
            //$modelo = $db->quote($modelo);
            $sql    = "SELECT Id From Modelos where Descripcion = '$modelo'";
        } else {
            $sql    = "SELECT Id From Modelos where Id = $modelo";
        }
        $modelo = $db->fetchOne($sql);
        if (!$modelo) throw new Rad_Db_Table_Exception('No se encontro el modelo '.$m);

        /*
        $M          = new Model_DbTable_Modelos;
        $R_M        = $M->fetchRow("Descripcion = '$modelo'");
        if (!count($R_M)) throw new Rad_Db_Table_Exception("No existe el modelo donde aplica la caracteristica.");
        $idModelo   = $R_M->Id;
        */

        $sql            = "SELECT TipoDeCampo FROM Caracteristicas WHERE Id = $caracteristica";
        $tipoDeCampo    = $db->fetchOne($sql);

        // Recupero el valor Id de la Caracteristica Modelo
        $sql                    = "SELECT Id From CaracteristicasModelos where Modelo = $modelo and Caracteristica = $caracteristica";
        $caracteristcaModelo    = $db->fetchOne($sql);
        if (!$caracteristcaModelo) throw new Rad_Db_Table_Exception("La caracteristica no se aplica al modelo informado.");

        /*
        $CM         = new Model_DbTable_CaracteristicasModelos;
        $R_CM       = $CM->fetchRow("Modelo = $idModelo and Caracteristica = $caracteristica");
        if (!count($R_CM)) throw new Rad_Db_Table_Exception("La caracteristica no se aplica al modelo informado.");
        $idCaracteristicaModelo   = $R_CM->Id;
        */

        // Recupero el valor de la CaracteristicaValor
        $sql    = " SELECT  CV.Id, CV.Valor, C.TipoDeCampo, CL.ValorReferencia
                    FROM    CaracteristicasValores CV
                    INNER JOIN CaracteristicasModelos CM    on CM.Id    = CV.CaracteristicaModelo
                    INNER JOIN Caracteristicas C            on C.Id     = CM.Caracteristica
                    LEFT JOIN  CaracteristicasListas CL     on CL.Valor = CV.Valor
                    WHERE   CV.CaracteristicaModelo = $caracteristcaModelo
                    AND     CV.IdModelo             = $idDato ";

        Rad_Log::debug($sql);

        // throw new Rad_Db_Table_Exception($sql);

        // print_r($sql);

        $R_CV   = $db->fetchRow($sql);

        /*
        $R_CV   = $this->fetchRow("CaracteristicaModelo = $idCaracteristicaModelo and IdModelo = $idDato");
        //Rad_Log::debug("CaracteristicaModelo = $idCaracteristicaModelo and IdModelo = $idDato"  );
        $caracteristicaValor = (count($R_CV)) ? 1 : 0;
        */

        $caracteristicaValor = null;

        if (count($R_CV)) {
            //Rad_Log::debug($sql);
            //Rad_Log::debug($R_CV);
            //throw new Rad_Db_Table_Exception(count($R_CV));
            switch ($tipoDeCampo) {
                case 1: case 2: case 3: case 4: case 7: // Valores directos
                    $caracteristicaValor = $R_CV['Valor'];
                    break;
                case 5: // Listas
                    $caracteristicaValor = $R_CV['ValorReferencia'];
                    break;
                case 6: // Booleanos
                    $caracteristicaValor = ($R_CV['Valor'] == 'Si') ? 1 : 0;
                    break;
                default:
                    throw new Rad_Db_Table_Exception("El tipo de dato de la caracteristica no se encuentra soportado. ".$R_CV['TipoDeCampo']);
                    break;
            }

            //Rad_Log::debug($caracteristicaValor);
            return $caracteristicaValor;

        } else {
            // si es booleano devuelvo 0

            if ($tipoDeCampo == 6) {
                return 0;
            } else {
                return 2;
            }
        }

    }

}