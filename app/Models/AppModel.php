<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppModel extends Model
{
    const ACCESS_ADD_ACTION = 1;
    const ACCESS_EDIT_ACTION = 2;
    const ACCESS_DELETE_ACTION = 3;
    const ACCESS_SHOW_ACTION = 4;

    /**
     * Finds record by alias
     * @param $alias string
     * @param null $field
     * @return null
     */
    public static function findByAlias($alias, $field = null)
    {
        $item = self::where('alias', '=', $alias)->first();

        if (!$item) {
            return null;
        }

        if (!$field) {
            return $item;
        }

        return @$item->{$field};

    }

    /**
     * Search query scope
     * @param string $field
     * @param string $keyword
     * @param string $delimiters
     */
    public function scopeSearch($query, $field, $keyword = '', $delimiters = ' ;-,')
    {
        $keyword = trim($keyword, $delimiters);

        $delimiters = preg_quote($delimiters);
        $keyword = preg_replace("/[$delimiters]+/", ' ', $keyword);
        $keywords = preg_split("/[$delimiters]/", $keyword);

        for ($i = 0; $i < count($keywords); $i++) {
            if (trim($keywords[$i])) {
                $query->where($field, 'LIKE', "%$keywords[$i]%");
            }

        }
        return $query;
    }

    /**
     * OrSearch query scope
     * @param string $field
     * @param string $keyword
     * @param string $delimiters
     */
    public function scopeOrSearch($query, $field, $field2, $keyword = '', $delimiters = ' ;-,')
    {
        $keyword = trim($keyword, $delimiters);
        $delimiters = preg_quote($delimiters);
        $keyword = preg_replace("/[$delimiters]+/", ' ', $keyword);
        $keywords = preg_split("/[$delimiters]/", $keyword);

        for ($i = 0; $i < count($keywords); $i++) {
            if (trim($keywords[$i])) {
                $key = $keywords[$i];
                $query->where(function ($q) use ($field, $field2, $key) {
                    $q->where($field2, 'LIKE', "%$key%")->OrWhere($field, 'LIKE', "%$key%");
                });

            }

        }
        return $query;
    }


    /**
     * Search encrypt data query scope
     * @param string $field
     * @param string $keyword
     * @param string $delimiters
     */
    public function scopeSearchEncrypted($query, $field, $keyword = '', $delimiters = ' ;-,')
    {
        $keyword = trim($keyword, $delimiters);

        $delimiters = preg_quote($delimiters);

        $keyword = preg_replace("/[$delimiters]+/", ' ', $keyword);
        $keywords = preg_split("/[$delimiters]/", $keyword);

        $keywords = array_map(function ($value) {
            return md5(strtolower($value));
        }, $keywords);

        for ($i = 0; $i < count($keywords); $i++) {
            if ($keywords[$i]) {
                $query->where($field, 'LIKE', "%$keywords[$i]%");
            }

        }
        return $query;
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
            '/windows nt 10/i'      =>  'Windows 10',
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
            '/iphone/i'             =>  'iPhone端末',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android端末',
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