<?php

class Server
{
	protected $_mapper;

	public function __construct ()
	{
		$this->_mapper = new Model_Stock();
	}

	public function date( $format )
	{
		ExtDirect::fire('error','url '.$_SERVER['REQUEST_URI']);
		return date( $format );
	}

	public function estado ($a, $b)
	{
		return array (
			'success' => false,
			'msg' 	  => 'error'
		);
	}

	public function __call($name, $arguments) {
		// prepare caller //
		$arrCaller = array( $this->_mapper , $name );
		// return the result of the method into the object  //
		$rtn = call_user_func_array( $arrCaller, $arguments );
		return $rtn;
	}
}