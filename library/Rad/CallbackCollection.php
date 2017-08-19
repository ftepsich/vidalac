<?php

/**
 * Rad_CallbackCollection
 *
 * Collecion de callbacks
 *
 * <code>
 * <?php
 * $c = new CallbackCollection();
 *
 * $c->append(function($a){echo ($a*2).PHP_EOL;});
 * $c->append(function($a){echo ($a*4).PHP_EOL;});
 *
 * $c(4);
 * ?>
 * </code>
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage PubSub
 * @author Martin Alejandro Santangelo
 */
class Rad_CallbackCollection extends ArrayObject
{
   function __invoke()
   {
      $args = func_get_args();
      foreach ($this as $callback) {
         call_user_func_array($callback, $args);
      }
   }
}