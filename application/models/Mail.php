<?php

/**
 * Model_Mail
 *
 * Enviador de Mails del Sistema
 * @package Model
 * @subpackage Mail
 * @author Martin Alejandro Santangelo
 */
class Model_Mail
{
    /**
     * @var string Cuerpo del correo
     */
    protected $_cuerpo;
    /**
     * @var string url del server smtp
     */
    protected $_smtp;
    /**
     * @var string direccion destino del correo
     */
    protected $_destino;
    /**
     * @var string Alias del remitente
     */
    protected $_remitente;
    /**
     * @var string Asunto
     */
    protected $_asunto;
    /**
     * @var string correo de origen
     */
    protected $_origen;
    /**
     * @var string nombre de usuario para la autenticacion
     */
    protected $_usuario;
    /**
     * @var string contraseña para la autenticacion
     */
    protected $_password;

    public function perform()
    {
        $this->send();
    }

    /**
     * Constructor de la clase
     * 
     */
    public function __construct($destino, $asunto, $cuerpo, $remitente)
    {
        $config = Rad_Cfg::get();

        $this->_destino = $destino;
        $this->_cuerpo  = $cuerpo;
        $this->_asunto  = $asunto;

        $this->_origen    = $config->Mail->usuario;
        $this->_usuario   = $config->Mail->usuario;
        $this->_password  = $config->Mail->password;
        $this->_smtp      = $config->Mail->smtp;
        $this->_remitente = $remitente;

        $this->mail = new Zend_Mail('UTF-8');

    }

    public function getMsg()
    {
        return $this->mail;
    }

    public function attach($binary, $type, $fileName)
    {
        $at = $this->mail->createAttachment(
            $binary,
            $type,
            Zend_Mime::DISPOSITION_ATTACHMENT,
            Zend_Mime::ENCODING_BASE64,
            $fileName
        );
        return $at;
    }

    public function clearAttachement()
    {
        $this->mail->createAttachment();
    }

    /**
     * Envia el Correo
     * 
     * @param string $destino direccion de emial a la que se enviara el correo
     * @param string $asunto  asunto del correo
     * @param string $cuerpo  cuerpo del correo
     */
    public function send()
    {
        // Creamos el Layer de Transporte
        $config = array(
            'auth' => 'login',
            'ssl'  => 'tls',
            'port' => 587,
            'username' => $this->_usuario,
            'password' => $this->_password
        );
        
        $transport = new Zend_Mail_Transport_Smtp($this->_smtp, $config);

        Zend_Mail::setDefaultTransport($transport);
        
        $this->mail->clearRecipients();
        $this->mail->clearFrom();

        $this->mail->setFrom($this->_destino, $this->_remitente)
                ->setBodyHtml($this->_cuerpo)
                ->setSubject($this->_asunto);
        $destinosArray = explode(',', $this->_destino);
		
        foreach ($destinosArray as $dest) {
            $this->mail->addTo($this->_destino);
        }
		
        $this->mail->send();
    }
}