<?php
interface Rad_Observable
{
    function notifyObservers($event, $args);
	function addObserver($observer);
	function removeObserver($observer);
}
