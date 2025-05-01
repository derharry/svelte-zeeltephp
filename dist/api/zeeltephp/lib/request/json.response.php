<?php

     /**
     * Default JSON Respone definition
     * 
     */
    class JSONResponse {
        public $ok       =  false;
        public $code     =  500;
        public $message  =  ""; //null;
        public $error    =  ""; //null;
        public $data     =  ""; //null;

        public function dump($debug = false) {
            $this->set_send_state();
            //error_log(' JSONResponse @@ ');
            //error_log('   ok      : ' . ($this->ok       ? 'true':'false') );
            //error_log('   success : ' . ($this->success  ? 'true':'false') );
            //error_log('   code    : ' . $this->code     );
            //error_log('   message : ' . $this->message  );
            //error_log('   error   : ' . $this->error    );
            debug_vardump($debug,'data ', $this->data    );
        }

        public function send() {
            $this->set_send_state();
            http_response_code($this->code);
            echo json_encode($this);
        }

        private function set_send_state() {
            try {
                if ($this->ok) {
                    $this->ok      = true;
                    $this->code = $this->code == 500 ? 200 : $this->code;
                }
            } 
            catch (\Throwable $th) {
                zp_error_handler($th);
            }
        }
    }

?>