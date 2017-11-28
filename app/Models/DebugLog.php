<?php


namespace App\Models;

/**
 * Class DebugLog
 * @package App\Models
 */
class DebugLog extends AppModel
{
    public $table = 'debug_logs';
    public $timestamps = false;

    /**
     * @return DebugLog
     */
    public static function init() {
        $entry = new DebugLog();


        if (!empty ($_SERVER['REQUEST_URI'])) {
            $entry->request_uri =  $_SERVER['REQUEST_URI'];
            $entry->http_method = $_SERVER['REQUEST_METHOD'];
            $entry->headers = json_encode(getallheaders());
            if ( $entry->http_method == 'GET') {
                $entry->parameters = json_encode($_GET);
            } else if ($entry->http_method == 'POST') {
                $entry->parameters = json_encode($_POST);
            } else if ($entry->http_method == 'PUT') {
                $_put = [];
                parse_str(file_get_contents("php://input"), $_put);
                $entry->parameters = json_encode($_put);
            }

            $currentRoute = app('request')->route();

            if ($currentRoute !== null) {
                $routeAction = $currentRoute->getAction();

                list($controller, $action) = explode('@', class_basename($routeAction['controller']));

                $entry->controller = $controller;
                $entry->action = $action;
            }



            $entry->user_agent = $_SERVER['HTTP_USER_AGENT'];
            $entry->client_ip_address = $_SERVER['REMOTE_ADDR'];
            $entry->client_platform = self::getOS();

            $entry->browser = self::getBrowser();
        } else {
            $entry->browser = 'Command line';
        }

        $entry->timestamp = current_timestamp();


        return $entry;
    }

    /**
     * Write info debug
     * @param $message
     * @return bool
     */
    public static function info($message = null) {
        $entry = self::init();
        $entry->event_type = 'info';
        $entry->message = $message;
        return $entry->save();
    }

    /**
     * @return string
     */
    public static function error(\Exception $e) {

        $entry = self::init();
        $entry->event_type = 'error';
        $entry->message = $e->getMessage();
        $entry->exception = get_class($e);
        $entry->stack_trace = $e->getTraceAsString();

        return $entry->save();
    }


    /**
     * Gets client OS
     * @param $checkMobile
     * @return string
     */
    public static function getOS ($checkMobile = null) {

        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $osPlatform    =   "Unknown OS Platform";

        $osArray       =   array(
            '/windows nt 10/i'     =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($osArray as $regex => $value) {

            if (preg_match($regex, $userAgent)) {
                $osPlatform    =   $value;
            }

        }

        $is_mobile = [
            'iPhone',
            'iPod',
            'iPad',
            'Android',
            'BlackBerry',
            'Mobile'
        ];

        if ($checkMobile) {
            return in_array($osPlatform, $is_mobile);
        }

        return $osPlatform;

    }

    /**
     * Gets client browser
     * @return string
     */
    public static function getBrowser() {

        $userAgent = @$_SERVER['HTTP_USER_AGENT'];

        $browser        =   "Unknown Browser";

        $browserArray  =   array(
            '/msie/i'       =>  'Internet Explorer',
            '/firefox/i'    =>  'Firefox',
            '/safari/i'     =>  'Safari',
            '/chrome/i'     =>  'Chrome',
            '/edge/i'       =>  'Edge',
            '/opera/i'      =>  'Opera',
            '/netscape/i'   =>  'Netscape',
            '/maxthon/i'    =>  'Maxthon',
            '/konqueror/i'  =>  'Konqueror',
            '/mobile/i'     =>  'Handheld Browser'
        );

        foreach ($browserArray as $regex => $value) {

            if (preg_match($regex, $userAgent)) {
                $browser    =   $value;
            }

        }

        return $browser;

    }




}