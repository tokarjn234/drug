<?php


namespace App\Lib;


class PushNotification
{
    // (Android)API access key from Google API's Console.
//    private static $API_ACCESS_KEY = 'AIzaSyD0TiDUpEkpyfE2CIzaURgUQEtSuFvQ4z8';
    // (iOS) Private key's passphrase.
    private static $passphrase = '';

    // Change the above three vriables as per your app.
    public function __construct()
    {
        exit('Init function is not allowed');
    }

    // Sends Push notification for Android users
    public static function android($data, $reg_id, $API_ACCESS_KEY = null)
    {
        $url = 'https://android.googleapis.com/gcm/send';
        $message = array(
            'title' => $data['mtitle'],
            'message' => $data['mdesc'],
            'subtitle' => '',
            'tickerText' => '',
            'msgcnt' => 1,
            'vibrate' => 1,
            'alias' => $data['alias'],
            'name' => $data['name'],
            'settings' => $data['settings']
        );

        $headers = array(
            'Authorization: key=' . $API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $fields = array(
            'registration_ids' => $reg_id,
            'data' => $message,
        );

        return self::useCurl($url, $headers, json_encode($fields));
    }

    // Sends Push notification for iOS users
    public static function iOS($data, $devicetoken, $filePush = null)
    {
//        $path = base_path('certs') . '/DONoPassPush.pem';
        $path = base_path('pushIos') . '/' . $filePush;
        $deviceToken = $devicetoken;
        $ctx = stream_context_create();
        // ck.pem is your certificate file
        stream_context_set_option($ctx, 'ssl', 'local_cert', $path); //PUT THIS ck.pem IN A DIRECTORY WHERE YOU EXECUTE TestNotifications.php
        stream_context_set_option($ctx, 'ssl', 'passphrase', self::$passphrase);
        // Open a connection to the APNS server
        //Use: ssl://gateway.push.apple.com:2195  for development mode
        $fp = stream_socket_client(
            'ssl://gateway.sandbox.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        // Create the payload body
        // Create the payload body
        $body['aps'] =
            [
                'alert' => $data['mtitle'],
                [
                    'title' => $data['mtitle'],
                    'body' => $data['mtitle'],
                ],
                'sound' => 'request.wav',
                'badge' => 1, //or your custom number
            ];
        $body['content'] = $data['mdesc'];
        $body['settings'] = $data['settings'];
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        // Close the connection to the server
        fclose($fp);
        if (!$result)
            return 'Message not delivered' . PHP_EOL;
        else
            return 'Message successfully delivered' . PHP_EOL;
    }

    // Curl
    private static function useCurl($url, $headers, $fields = null)
    {
        // Open connection
        $ch = curl_init();
        if ($url) {
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if ($fields) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }

            // Execute post
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }

            // Close connection
            curl_close($ch);

            return $result;
        }
    }

}