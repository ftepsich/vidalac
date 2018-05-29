<?php

/**
 * Controlador Encargado de Enviar Reportes
 * 
 * @class Window_BirtReporterController
 * @extend Rad_Window_Controller
 */
class Window_BirtReporterController extends Rad_Window_Controller_Action
{
    /*
    // IMPLEMENTAR!
     * 
    public function authorize ($request)
    {
        $db = Zend_Registry::get('db');

        $acl = new Rad_ModelAcl($db);

        if (!$acl->allowView($this->modelClass)) {
            return false;
        } else {
            return true;
        }
    }
    */
    
    public function authorize ($request)
    {
         return true;
    }

    /** 
     * Reporteador generico
     * 
     */
    public function reportAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $rq       = $this->getRequest();
        
        $template = $rq->template;
        $id       = $rq->id;
        $output   = ($rq->output) ? $rq->output : 'pdf';

        // if (!$template || !$id) {
        if (!$template) {
            throw new Rad_Db_Table_Exception('Faltan los parametros requeridos.');
        }

        $report = new Rad_BirtEngine();

        if ($rq->id) $report->setParameter('Id', $id, 'int');
        //$report->setParameter('Id', $id, 'int');   
	
	if ($rq->params) {
		if (is_array($rq->params)) {
			foreach ($rq->params as $p) {
			    $param = explode(',', $p);
			    $report->setParameter($param[0], $param[2], $param[1]);
			}
		} else if (is_string($rq->params)) {
			$param = explode(',', $rq->params);
			$report->setParameter($param[0], $param[2], $param[1]);

		}
	}        

        $report->renderFromFile(APPLICATION_PATH . "/../birt/Reports/$template.rptdesign", $output);
        $report->sendStream();
    }
    
    /**
     * Envia un reporte por mail
     * 
     */
    public function mailreportAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $rq = $this->getRequest();

        $template = $rq->template;
        $id       = $rq->id;
        $destino  = $rq->Destino;
        $asunto   = $rq->Asunto;
        $cuerpo   = $rq->Cuerpo;
        $output   = ($rq->output) ? $rq->output : 'pdf';

        if (!in_array($output, array('pdf', 'msword', 'xls'))) {
            throw new Rad_Db_Table_Exception('Solo se pueden enviar por mail reportes en Word, Excel o PDF');
        }

        if (!$template || !$id) {
            throw new Rad_Db_Table_Exception('Faltan los parametros requeridos.');
        }

        $report = new Rad_BirtEngine();
        $report->setParameter('Id', $id, 'int');
        $report->renderFromFile(APPLICATION_PATH . "/../birt/Reports/$template.rptdesign", 'pdf');
        $attach = $report->getStream();
        try {
            $mail = new Model_Mail($destino, $asunto, $cuerpo);
            $mail->attach($attach, 'application/pdf', 'adjunto.pdf');

            Rad_Jobs::enqueue($mail, 'mail');

            $msg['success'] = true;
            $this->_helper->json->sendJson($msg);
        } catch (Exception $e) {
            $msg['success'] = false;
            $msg['msg'] = $e->getMessage();
            $this->_helper->json->sendJson($msg);
        }
    }
    
    /**
     * Imprime una serie de cheques consecutivos
     * 
     */
    public function reportchequesAction ()
    {
        $db = Zend_Registry::get('db');
        try {
            $db->beginTransaction();
            $Cheques = $this->getRequest()->id;

            $ids = explode(',', $Cheques);

            $M_ChP = new Base_Model_DbTable_ChequesPropios();
            /**
             * 0: No impreso
             * 1: Impreso
             * 2: Para impresion
             */
            // Revisa que todos los cheques que se quieren imprimir
            // no se encuentren impresos todavia
            $yaImpresos = $M_ChP->fetchAll("Id IN ($Cheques) AND Impreso = 1");

            if (count($yaImpresos)) {
                throw new Rad_Exception('Algunos de los cheques ya se encuentra impresos');
            }
            $chequesRows = $M_ChP->fetchAll("Id IN ($Cheques)",'Numero asc');

            foreach ($chequesRows as $cheque) {
                if ($chequeAnt && $chequeAnt->Numero + 1 != $cheque->Numero)
                    throw new Rad_Exception('Los cheques deben ser correlativos');
                $chequeAnt = $cheque;
            }

            $this->_helper->viewRenderer->setNoRender(true);

            $report = new Rad_BirtEngine();
            $file = APPLICATION_PATH . '/../birt/Reports/Cheques.rptdesign';

            $where = "Where Id IN ($Cheques)";
            

            $report->renderFromFile($file, 'pdf', array('WHERE' =>  $where));
            $report->sendStream();
        
            $db->commit();
        } catch (Exception $e) {
            $db->update('Cheques', array('Impreso' => 0), "Id IN ($Cheques)");
            Rad_Log::crit($e->getMessage());
            $db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Reporte de ventas
     * 
     */
    public function reportventasAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $anio = $this->getRequest()->anio;
        $mes = $this->getRequest()->mes;

        $output = ($this->getRequest()->output) ? $this->getRequest()->output : 'pdf';

        if (!$anio || !$mes)
            throw new Rad_Db_Table_Exception('Faltan los parametros requeridos.');

        $report = new Rad_BirtEngine();
        $report->setParameter('Mes', $mes, 'Int');
        $report->setParameter('anio', $anio, 'Int');
        $report->renderFromFile(APPLICATION_PATH . "/../birt/Reports/ListadoCantidadArticulosVendidos.rptdesign", $output);
        $report->sendStream();
    }
    
    /**
     * Inicializa el contador con el titulo
     */
    public function initWindow ()
    {
        $this->title = "Reportes";
    }
    
}
