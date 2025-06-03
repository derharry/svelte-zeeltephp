<?php
#
# TEST File for ZP_DB
#

function load() {
      global $zpAR, $env, $db;

      $res = null;
      
      if ($db->connect())     $res = $db->query('select * from og_companies');
      //if ($db->isConnected()) $res2 = $db->query('select * from og_companies');
      //$res = $db->query('select * from og_companies');

      $insertData['name'] = 'Harry vvv';
      $insertData['phone'] = '+433224';
      $insertData['message'] = 'xxds weffe fasfwex';
      $insertData['email'] = 'xxx@me.com';
      $res = $db->insert('og_contact_form', $insertData);

      $updateData = $insertData;
      $updateData['name'] = 'Harry zzssdffssz';
      $res = $db->update('og_contact_form', ['id'=>1], $updateData);
      
      $res = $db->delete('og_contact_form', ['id'=>3]);


      return [
            '$_REQUEST'   => $_POST,
            'res'   => $res,
            'zpAR'  => $zpAR,
            'zp_DB' => $db,
            //'zpEnv' => $end
      ];
}

?>