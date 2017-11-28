<?php


namespace App\Console\Commands;
use Illuminate\Console\Command;
use GuzzleHttp;
use App\Models\User;
use Hash;

class ApiTool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apitool {action} {baseUrl?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $action = $this->argument('action');

        $this->{$action}();

    }

    /**
     * Send order
     */
    public function test() {
        $logFile = fopen(app_path('Console/Commands/ApiTool.log'), 'a+');

        $apiConfig = json_decode(file_get_contents(app_path('Console/Commands/ApiTool.json')));
        $loginUrl = '/api/oauth/getAccessToken';


        $this->info('Logging in...');

        $client = new GuzzleHttp\Client(['base_uri' => $apiConfig->baseUrl, 'verify' => false, 'headers' => ['Company' => $apiConfig->company]]);

        $loginFormParams = [
            'grant_type' => 'password',
            'device_code' => str_random(20)
        ];

        if (!empty ($apiConfig->email) && !empty ($apiConfig->password)) {
            $loginFormParams['email'] = $apiConfig->email;
            $loginFormParams['password'] = $apiConfig->password;
        }

        $res = $client->request('POST', $loginUrl, [
            'auth' => $apiConfig->auth,
            'form_params' => $loginFormParams

        ]);

        $response = json_decode($res->getBody());

        $this->info($response->msg);
        fwrite($logFile, $res->getBody() . "\n");

        if ($response->error > 0) {

            return;
        }

        $token = $response->data->access_token;
        $keyCode = sha1($response->data->app_keycode);

        $newHeaders = [
            'Company' => $apiConfig->company,
            'AppKey' => $keyCode,
            'Authorization' => "DrugOrder $token"
        ];

        foreach ($apiConfig->requests as $request) {
            if (empty ($request->name)) {
                $request->name = $request->uri;
            }

            if (empty ($request->loop)) {
                $request->loop = 1;
            }

            for ($i = 0; $i <  $request->loop; $i++) {
                $this->info('[' . $i . '][' . $request->method . ']' . $request->name);

                $options = [
                    'headers' => $newHeaders
                ];

                if ($request->type === 'multipart') {
                    $options['multipart'] = $this->parseFormParams($request->formParams, $i);


                } else {
                    foreach ($request->formParams as  &$v) {
                        $v = sprintf($v, $i);
                    }

                    $options['form_params'] =  $request->formParams;
                }

                $res = $client->request(strtoupper($request->method), $request->uri, $options);
                fwrite($logFile, $res->getBody() . "\n");
                $result = json_decode($res->getBody());

                if ($result->error > 0) {
                    $this->info(json_encode($result->data, JSON_PRETTY_PRINT));
                } else {
                    $this->info($result->msg);
                }
            }

        }

        fclose($logFile);


    }


    /**
     * @param $formParams
     * @param $i
     */
    private function parseFormParams ($formParams, $i) {
        $multipart = [];

        foreach ($formParams as $field => $value) {
            if (strpos($value, 'file:///') === 0) {
                $filename = str_replace('file:///', '',  $value);
                if (file_exists($filename)) {
                    $value = fopen($filename, 'r');
                }


            } else {
                $value = sprintf($value, $i);
            }

            $multipart[] = ['name' => $field, 'contents' => $value];
        }

        return $multipart;
    }


}