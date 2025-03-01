<?php

class ZP_ActionDetails {

    public $route  = null;
    public $action = null;
    public $value  = null;
    public $params = [];

    
    function __construct() {
        try {
            $details = zp_getActionDetails();
            $this->route  = $details['route'];
            $this->action = $details['action'];
            $this->value  = $details['action_value'];
            $this->params = $details['queryParams'];
        }
        catch (Exception $exp) {
            error_log($exp);
        }
    }

}

function zp_getActionDetails() {
    $res = [
        "route"        => null,
        "action"       => null,
        "action_value" => null,
        "queryParams"  => []
    ];
    try {
        // Use the actual query string from the client
        $queryString = "route1/route2&/&btn=hi?var1=hi&foo=bar";
        $queryString = $_SERVER['QUERY_STRING'];
        
        // Split the query string into parts
        $parts = explode('&', $queryString);
        
        // Process each part of the query string
        foreach ($parts as $index => $part) {
            if ($index === 0) {
                // The first part is the route
                $res['route'] = $part;
            } elseif ($index === 1 && strpos($part, '/') !== false) {
                // The second part is the action (if it contains "=")
                list($res['action'], $res['action_value']) = explode('=', $part, 2);
                // Remove the leading slash if present
                $res['action'] = ltrim($res['action'], '/');
            } else {
                // Remaining parts are query parameters
                parse_str($part, $param);
                $res['queryParams'] = array_merge($res['queryParams'], $param);
            }
        }
    } 
    catch (Exception $exp) {
        error_log($exp->getMessage());
    }
    return $res;
}

?>