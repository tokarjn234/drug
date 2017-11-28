<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use App\Models\Company;
use DB;

class OpenSSLCertMediaid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OpenSSLCertMediaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create first Cert for Mediaid';

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
        $this->createCert();
    }

    /**
     * Creates new client certifiate
     * @author quantm
     * @param $commonName
     * @return object
     */
    private function addCer($commonName)
    {

        //  $baseDir  = base_path('certs/clients');

        $dn = array(
            "countryName" => env('CERT_COUNTRY_NAME'),
            "stateOrProvinceName" => env('CERT_PROVINCE'),
            "localityName" => env('CERT_CITY'),
            "organizationName" => env('CERT_ORGANIZATION_NAME'),
            "organizationalUnitName" => env('CERT_ORGANIZATION_UNIT_NAME'),
            "commonName" => $commonName,
            "emailAddress" => env('CERT_COUNTRY_NAME')
        );

        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $privateKey = openssl_pkey_new($config);

        // Extract the private key from $res to $privKey
        ///openssl_pkey_export($res, $privKey);
        $csr = openssl_csr_new($dn, $privateKey);

        //$sscert = openssl_csr_sign($csr, null, $privateKey, 365);

        openssl_csr_export($csr, $clientCsr);

        // openssl_x509_export_to_file($sscert, $baseDir . '/' . $dn['commonName'] . '.crt');

        openssl_pkey_export($privateKey, $clientPrivateKey);


        return (object)['csr' => $clientCsr, 'privateKey' => $clientPrivateKey];

    }

    /**
     * Creates new certifiate
     * @author DucDQ
     * @return mixed
     */
    private function createCert()
    {
        $lastCertificate = Certificate::orderBy('id', 'desc')->first();
        $commonName = '1001';

        if ($lastCertificate !== null) {
            $commonName = $lastCertificate->ssl_client_s_dn_cn + 1;
        }

        DB::beginTransaction();
        $exportPassword = str_random(8);

        $newCer = new Certificate();
        $newCer->status = Certificate::STATUS_NOT_DIVIDE;
        $newCer->export_password = $exportPassword;

//        if ($isMediaid) {
        $newCer->status = Certificate::STATUS_DIVIDED_TO_DEVICE;
        $newCer->issued_to_device_at = current_timestamp();
        $newCer->is_mediaid = 1;
//        }

        $newCer->save();


        $newCer->ssl_client_s_dn_cn = $commonName;

        $this->info("Creating new certificate...");

        $client = $this->addCer($commonName);

        $clientDir = base_path('certs/clients');
        $rootDir = base_path('certs/root');
        $certMediaidDir = base_path('certs/mediaid_certs');

        if (!file_exists($clientDir)) {
            mkdir($clientDir, 0777, true);
        }

        if (!file_exists($rootDir)) {
            mkdir($rootDir, 0777, true);
        }
        if (!file_exists($certMediaidDir)) {
            mkdir($certMediaidDir, 0777, true);
        }

        $caPassword = file_get_contents($rootDir . '/ca.key.passwd');
        // $clientCsr = file_get_contents("$clientDir/$commonName.csr");

        $caCert = file_get_contents("$rootDir/ca.crt");
        $privkey = [file_get_contents("$rootDir/ca.key"), $caPassword];

        $sscert = openssl_csr_sign($client->csr, $caCert, $privkey, 365);
        openssl_x509_export($sscert, $x509);

        /// openssl_x509_export_to_file($sscert, $clientDir . "/$commonName.crt");

        $clientPrivkey = [$client->privateKey, ''];

        //$x509 = file_get_contents($clientDir . "/$commonName.crt");

        openssl_pkcs12_export($x509, $clientPkcs12Certificate, $clientPrivkey, $exportPassword);

        $mediaidCertPath = "$certMediaidDir/cert_mediaid_$commonName.p12";
        file_put_contents($mediaidCertPath, $clientPkcs12Certificate);

        //$baseDir  = base_path('certs/mediaid_cert');

        $newCer->client_pkcs12_certificate = base64_encode($clientPkcs12Certificate);
        $newCer->client_private_key = $client->privateKey;

        $newCer->save();
        DB::commit();
        $this->info('--------------------------------------------');
        $this->info('Create new client certificate success.');
        $this->info('--------------------------------------------');
        $this->info('    New ssl_client_s_dn_cn=' . $commonName);
        $this->info('    Password=' . $exportPassword);
    }
}
