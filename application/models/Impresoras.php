<?php
require_once 'PrintIpp/CupsPrintIPP.php';

/**
 * Administrador de trabajos de CUPS
 * 
 * @package Rad
 * @subpackage Impresoras
 * @author Martin Alejandro Santangelo
 */
class Model_Impresoras
{
    private $_ipp;

    public function __construct($server)
    {
        $this->_ipp = new CupsPrintIPP();

        $this->_ipp->with_exceptions = true;
 
        $this->_ipp->handle_http_exceptions = true;
        
        $this->_ipp->setHost($server);
    }

    public function getImpresoras()
    {
        $cache = Zend_Registry::get('fastCache');

        if ($cache) {
            $impresoras = $cache->load('Model_Impresoras_impresoras');
            if ($impresoras) {
                return $impresoras;
            }
        }

        $this->_ipp->getPrinters();

        $imp = $this->_ipp->available_printers;
        
        $impresoras = array();

        foreach ($imp as $value) {
            $impresoras[] = array(
                $value, 
                substr($value, strrpos($value,'/')+1)
            );
        }
                        
        if ($cache) {
            $cache->save($impresoras,'Model_Impresoras_impresoras');    
        }
        return $impresoras;
    }

    /**
     * Retorna una la lista de trabajos de una impresora
     * 
     * @param string $printer
     */      
    public function getJobs($printer)
    {
        $this->_ipp->setPrinterURI($printer);

        $this->_ipp->getJobs(true, 0);

        $rows = array();

        foreach ($this->_ipp->jobs_attributes as $value) {
            $rows[] = array(
                'descripcion' => $value->document_name->_value0,
                'fecha'       => date('Y-m-d H:i:s',$value->time_at_creation->_value0),
                'estado'      => $value->job_state->_value0,
                'uri'         => $value->job_uri->_value0,
                'id'          => $value->job_id->_value0
            );
        }

        return $rows;
    }

    public function cancelJob($uri)
    {
        $this->_ipp->setAuthentication('smartsoftware','motorola007');
        $r = $this->_ipp->cancelJob($uri);
    }
}