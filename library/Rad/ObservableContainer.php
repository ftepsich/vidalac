<?php
class Rad_ObservableContainer
{
	protected $_observers = array();
	protected $observableObj;
	
	public function notifyObservers ($event, $args) 
	{
        foreach ($this->_observers as $key => $observer) {
			if (is_string($observer)) {
				if (class_exists($observer) || Zend_Loader::loadClass($observer)) {
					$this->_observers[$key] = new $observer(array(),false);
				} else {
					unset($this->_observers[$key]);
				}
			}
            if ($this->_observers[$key]) {
				$this->_observers[$key]->notify($this->observableObj, $event, $args);
			}
        }
    }

	public function addObserver ($observer) 
	{
        $this->_observers[]=& $observer;
    }
	
	public function removeObserver ($observer)
	{
		if(in_array($observer, $this->_observers)){
		    $key = array_search($observer, $this->_observers);
		    unset($this->_observers[$key]);
		}
	}

	public function __set($var, $value)
	{
		$this->observableObj->$var = $value;
	}
   
	public function __get($var)
	{
		return $this->observableObj->$var;
	}
    public function __call( $strMethod, $arrParams )
    {
	
		// get the class of the element //
		$strClass = get_class( $this->observableObj );
		// get all methods of the class  //
		$arrMethods = get_class_methods( $strClass );
		// case the method exists into this class  //
		if( in_array( $strMethod , $arrMethods ) )
		{
			// prepare caller //
			$arrCaller = Array( $this->observableObj , $strMethod );
			// return the result of the method into the object  //
			$rtn = call_user_func_array( $arrCaller, $arrParams );
			$this->notifyObservers($strMethod, $arrParams);
			return $rtn;
		}
       
        // any object has the method //
        // throw a exception //
        throw new Exception( " Method " . $strMethod . " not exist in this class " . get_class( $this ) . "." );
    }
}