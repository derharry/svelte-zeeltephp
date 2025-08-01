<?php namespace ZeeltePHP\Lib\Time;


class StopWatch {

     public $timediff_register = [];


     function start($title) {
          $this->timediff_register[$title]['start'] = microtime(true);
     }

     function end($title) {
          $this->timediff_register[$title]['end'] = microtime(true);
          return $this->getTimeDiff($title);
     }

     function endN($title) {
          $this->timediff_register[$title]['end'] = microtime(true);
          return "TM $title: ". $this->getTimeDiff($title);
     }

     function getTimeDiff($title, $how = 'H:m:s') {
          $diff = $this->timediff_register[$title]['end'] - $this->timediff_register[$title]['start'];
          return $diff;
          $seconds = (int)$diff;
          $micro = (int)(($diff - $seconds) * 1e6);
          //return gmdate($how, (int)$diff);
     }
}

?>