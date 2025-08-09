<?php namespace ZeeltePHP\Lib\Time;

class Watch {

     public $name;
     public $start;
     public $stop;
     public $labs = [];

     /**
      * 
      */
     function __construct($name = null, $start = false) {
          $this->name = $name; # todo: ?? uuid_tiny(); # generate a name
          return $start && $this->Start() ?? $start;
     }

     function Start() {
          return $this->start = microtime(true);
     }

     function Stop() {
          return $this->stop = microtime(true);
     }
     
     function AddLab($name = null) {
          $this->labs[] = new Lab($name);
     }
}

class Lab {
     public $name;
     public $time;
     function __construct($name = '') {
          $this->name = $name;
          $this->time = microtime(true);
     }
}

class StopWatch {

     public $watches = [];

     function start($name) {
          $this->watches[$name]['start'] = microtime(true);
     }

     function end($name) {
          $this->watches[$name]['end'] = microtime(true);
          return $this->getTimeDiff($name);
     }

     function endN($name) {
          $this->watches[$name]['end'] = microtime(true);
          return "TM $name: ". $this->getTimeDiff($name);
     }

     function getTimeDiff($name, $how = 'H:m:s') {
          $diff = $this->watches[$name]['end'] - $this->watches[$name]['start'];
          return $diff;
          $seconds = (int)$diff;
          $micro = (int)(($diff - $seconds) * 1e6);
          //return gmdate($how, (int)$diff);
     }
}

?>