<?php namespace ZeeltePHP\Lib\Mail;

     require_once('lib/inc/keyValueListHelper.php');

     /**
      * 
      */
     function sendmail_prepareTextMessageFromKeyValueList($keyValueDataList) {
          return key_value_list_to_text_table_format($keyValueDataList);
     }


     function sendmail_text($sendTo, $subject, $textToSend, $headers = "") {
          if (is_array($textToSend)) {
               $textToSend = sendmail_prepareTextMessageFromKeyValueList($textToSend);
          }
          mail($sendTo, $subject, $textToSend, $headers);
     }

     function sendmail_html($sendTo, $subject, $keyValueDataList, $htmlTemplate, $headers = "") {
          // open $htmlTemplate
          // RegExp-replace key-value matching $htmlTemplate
          // get Text Content
          // prepare mail MIME type content-html including part text
          // send mail
          sendmail_text($sendTo, $subject, $keyValueDataList, $headers);
     }


?>