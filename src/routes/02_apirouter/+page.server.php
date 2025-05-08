<?php

      function load() {
            global $jsonResponse, $zpAR;
            return [
                  'zpAR' => $zpAR,
                  'data' => [
                        [ 'id'=>'0', 'label'=>'Santa' ],
                        [ 'id'=>'1', 'label'=>'Clause' ]
                  ]
            ];
      }

      function actions($action, $value) {
            global $jsonResponse, $zpAR;
            $data = null;
            switch ($action) {
                  case 'example_1':
                                    return [
                                          'zpAR' => $zpAR,
                                          'data' => [ 'name1'=>'Jingle', 'name2'=>'Bells', 'value'=>$value ]
                                    ];
                              break;
                  default: 
                        return [
                              'zpAR' => $zpAR,
                              'data' => "msg: actions/default $action:$value"
                        ];
            }
      }

      function action_example_2($value) {
            return [
                  'data' => 'hello from action_example_2('.$value.')'
            ];
      }

      function action_go($value) {
            return 'hello from action_go('.$value.')';
      }

      function action_example3($value) {
            return 'tbd';
      }
      
      function action_GoGo($value) {
            global $jsonResponse, $zpAR;
            $zpAR->data = "Hello from action_GoGo";
            return [
                  'ok' => false,
                  'data' => $zpAR
            ];
      }


?>