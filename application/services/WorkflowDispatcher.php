<?php

/**
 * Service_WorkflowDispatcher
 *
 * Despacha eventos a los workflows atachados a los mismos
 * 
 * @package     Aplicacion
 * @subpackage  Service
 * @author Martin Alejandro Santangelo
 */
class Service_WorkflowDispatcher
{
    /**
     * @var array 
     */
    protected static $_pubsubWorkflows = array();

    private static $_loaded = false;
    private static $_db;

    /**
     * Carga el array de workflows que estan atachados a eventos desde la DB
     */
    public static function init($db)
    {
        self::$_db = $db;
    }

    private static function _load()
    {
        if (self::$_loaded) return;

        $workflows = self::$_db->fetchAll("SELECT we.Event, w.* FROM WorkflowsEvents we left join 
            WorkflowsPubSub wp on we.Id = wp.Event left join
            Workflows w on w.Id = wp.Workflow order by Event");

        foreach ($workflows as $row) {
            self::$_pubsubWorkflows[$row['Event']][] = array(
                'Id' => $row['Id'], 
                'TipoEntrada' => $row['TipoEntrada']
            );
        }
        self::$_loaded = true;
    }


    public function dispatch($topic, $argv = null)
    {
        self::_load();
        
        $argv   = func_get_args();
        array_shift($argv);

        if (count($argv) != 1) {
            throw new Exception("Los Workflows solo pueden estar encadenados a eventos que envien solo un parametro y el evento $topic envia ".count($argv));
        }

        foreach (self::$_pubsubWorkflows[$topic] as $workflow) {
            $wf = $this->getWorkflow($workflow);
            $wf->procesar($argv[0]);
        }
    }

    public function getWorkflow($cfg)
    {
        $wv = new Model_DbTable_WorkflowsVersiones();

        $ultimaVersion = $wv->fetchRow("Workflow = ".$cfg['Id'], "Version desc");

        if (!$ultimaVersion) {
            throw new Exception("El workflow {$cfg['Id']} no tiene implementacion");
        }

        $wfCfg = unserialize(stripslashes($ultimaVersion->Logica));

        $workflow = new Rad_Workflow();
        $workflow->cargar($wfCfg);
        $workflow->setTipoEntrada($cfg['TipoEntrada']);
        return $workflow;
    }
} 