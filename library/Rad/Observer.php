<?php
interface Rad_Observer 
{
	function notify($sender, $event, $params);
}
