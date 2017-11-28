<?php
use App\Models\Certificate;
use App\Models\Staff;
use App\Models\Company;
/**
 * Prints object to debug
 */
function pr()
{
    $numArgs = func_num_args();
    $argList = func_get_args();
    echo "<meta charset=\"utf-8\">";

    echo '<pre>';

    for ($i = 0; $i < $numArgs; $i++) {
        echo "@param $i:<br>";
        print_r($argList[$i]);
        echo "<br>--------------<br>";
    }

    echo '</pre>';

}

function pr_log()
{
    $numArgs = func_num_args();
    $argList = func_get_args();

    for ($i = 0; $i < $numArgs; $i++) {
        echo sprintf('<script>console.log(JSON.parse(\'%s\'));</script>', addslashes(json_encode($argList[$i])));
    }
}

/**
 * Prints object to debug then die;
 */
function pd()
{
    call_user_func_array('pr', func_get_args());
    die;
}


/**
 * Returns an API success
 * @param $data
 * @param string $message
 * @param int $err_code
 * @return array
 */
function r_ok($data, $message = '', $err_code = 0)
{
    if (empty ($data)) {
        $data = [];
    }

    if (!is_string($message)) {
        if (is_array($message)) {
            $message = implode(' ', $message);
        } else {
            $message = 'Invalid message';
        }
    }

    return [
        'status' => 'ok',
        'error' => 0,
        'data' => $data,
        'msg' => $message,
        'err_code' => $err_code,
        'timestamp' => current_timestamp()
    ];
}

/**
 * Returns an API error
 * @param $data
 * @param string $message
 * @param int $err_code
 * @return array
 */
function r_err($data, $message = '', $err_code = 0)
{
    if (!is_array($data)) {
        $data = [$data];
    }

    if (!is_string($message)) {
        if (is_array($message)) {
            $message = implode(' ', $message);
        } else {
            $message = 'Invalid message';
        }
    }

    return [
        'status' => 'ok',
        'error' => count($data),
        'data' => $data,
        'msg' => $message,
        'err_code' => $err_code,
        'timestamp' => current_timestamp(),
    ];
}

function replace_url($url)
{
    $parsed_url = parse_url($url);
    if ($parsed_url['scheme'] == 'https') {
        $url = preg_replace("/^https:/i", "http:", $url);
    }
    return $url;
}

function r_winapp($data, $message = '', $code = '')
{
    if (!is_array($data)) {
        $data = [$data];
    }

    return [
        'status' => 'ok',
        'error' => count($data),
        'data' => $data,
        'msg' => $message,
        'timestamp' => current_timestamp(),
        'code' => $code
    ];
}

function __($id = null)
{
    static $translator = null;

    if ($translator === null) {
        $translator = app('translator');
    };

    return $translator->trans('msg.' . $id, [], 'msg', null);
}

function current_timestamp()
{
    return date('Y-m-d H:i:s');
}

function parse_start_date($startDate, $startTime = '')
{
    $receivedDateStart = null;

    if ($startDate) {
        $receivedDateStart = trim(preg_replace('/年|月|日/', '-', $startDate), '-');

        if ($startDate) {
            $receivedDateStart .= ' ' . $startTime . ':00';
        } else {
            $receivedDateStart .= ' 00:00:00';
        }
    }

    return $receivedDateStart;
}

function parse_end_date($endDate, $endTime = '')
{
    $receivedDateEnd = null;

    if ($endDate) {
        $receivedDateEnd = trim(preg_replace('/年|月|日/', '-', $endDate), '-');
        if ($endTime) {
            $receivedDateEnd .= ' ' . $endTime . ':00';
        } else {
            $receivedDateEnd .= ' 23:59:59';
        }
    }

    return $receivedDateEnd;
}

/**
 * Determines if the browser provided a valid SSL client certificate
 *
 * @return boolean True if the client cert is there and is valid
 */
function has_valid_cert()
{
    if (session('SSL_CLIENT') === true) {
        return true;
    }

    if (!isset($_SERVER['SSL_CLIENT_M_SERIAL'])
        || !isset($_SERVER['SSL_CLIENT_V_END'])
        || !isset($_SERVER['SSL_CLIENT_VERIFY'])
        || $_SERVER['SSL_CLIENT_VERIFY'] !== 'SUCCESS'
        || !isset($_SERVER['SSL_CLIENT_I_DN'])
    ) {
        return false;
    }

    if ($_SERVER['SSL_CLIENT_V_REMAIN'] <= 0) {
        return false;
    }

    return true;
}

/**
 * @return boolean True if the client cert is there and is valid
 */
function has_valid_cert_win_app()
{
    if (!isset($_SERVER['SSL_CLIENT_M_SERIAL'])
        || !isset($_SERVER['SSL_CLIENT_V_END'])
        || !isset($_SERVER['SSL_CLIENT_VERIFY'])
        || $_SERVER['SSL_CLIENT_VERIFY'] !== 'SUCCESS'
        || !isset($_SERVER['SSL_CLIENT_I_DN'])
    ) {
        return false;
    }

    if ($_SERVER['SSL_CLIENT_V_REMAIN'] <= 0) {
        return false;
    }

    return true;
}

function is_https()
{
    return
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443;
}

function get_reverted_index($string, $delimiters = ' ;-,') {
    if (empty ($string)) {
        return '';
    }

    $delimiters = preg_quote($delimiters);

    $keywords = preg_split( "/[$delimiters]/", $string );

    $indexedKeywords =  array_map(function($value) {return md5(strtolower($value));}, $keywords);
    $indexedKeywords[] = md5(preg_replace("/[$delimiters]/", '', $string));
    return implode(' ', $indexedKeywords);
}

/**
 * Encrypts data without exception
 */
function encrypt_data($s) {
    if (empty ($s)) {
        return $s;
    }


    try {
        return \Crypt::encrypt($s);
    } catch (\Exception $e) {
        //\App\Models\DebugLog::error($e);
    }

    return $s;
}

/**
 * Decrypts data without exception
 */
function decrypt_data($s) {
    if (empty ($s)) {
        return $s;
    }

    try {
        return \Crypt::decrypt($s);
    } catch (\Exception $e) {
        //\App\Models\DebugLog::error($e);
    }

    return $s;
}

/**
 * Gets days between 2 dates
 */
function get_days_between($date1, $date2) {
    $date1 = new \DateTime($date1);
    $date2 = new \DateTime($date2);
    $interval = $date1->diff($date2);
    return $interval->days;
}

/**
 * Gets minutes between 2 dates
 */
function get_minutes_between($date1, $date2) {
    $date1 = new \DateTime($date1);
    $date2 = new \DateTime($date2);
    $interval = $date1->diff($date2);

    $minutes = $interval->days * 24 * 60;
    $minutes += $interval->h * 60;
    $minutes += $interval->i;

    return $minutes;
}

/**
 * Determines certificate is active or inactive.
 *
 * @return boolean True if the cert is active.
 */
function has_active_cert()
{
    if(!empty($_SERVER['SSL_CLIENT_S_DN_CN'])){
        $sslClientSDnCn = $_SERVER['SSL_CLIENT_S_DN_CN'];
        $staff = Auth::user();
        $company = Company::find($staff->company_id);
        $cert = Certificate::whereCompanyId($company->id)
            ->where('ssl_client_s_dn_cn', '=', $sslClientSDnCn)
            ->first();

        if($cert){
            if($cert->status == Certificate::STATUS_INACTIVE){
                return false;
            }
        }
    }

    return true;
}


/**
 * Determines certificate of mediaid is active or inactive.
 *
 * @return boolean True if the cert is active.
 */
function has_active_mediaid_cert()
{
    if(!empty($_SERVER['SSL_CLIENT_S_DN_CN'])){
        $sslClientSDnCn = $_SERVER['SSL_CLIENT_S_DN_CN'];
        $cert = Certificate::whereIsMediaid(Certificate::IS_MEDIAID)
            ->where('ssl_client_s_dn_cn', '=', $sslClientSDnCn)
            ->first();

        if($cert){
            if($cert->status == Certificate::STATUS_INACTIVE){
                return false;
            }
        }else{
            return false;
        }
    }

    return true;
}