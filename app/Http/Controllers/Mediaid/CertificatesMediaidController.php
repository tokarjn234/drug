<?php

namespace App\Http\Controllers\Mediaid;
use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Company;
use DB;


class CertificatesMediaidController extends MediaidAppController
{

    public function getIndex(){

        $certificates = Certificate::whereIsMediaid(Certificate::IS_MEDIAID)
            ->orderBy('id','desc')
            ->paginate(10);
        return view('mediaid.certificates.mediaid_certificates',compact('certificates'));
    }

    public function postIndex(Request $request){
        $certAlias = $request->input('cert_alias');
        $disableCertAlias = $request->input('disable_cert');
        $addCerts = $request->input('add_certs');

        if($addCerts){
            $certNumber = $request->input('cert_number');
            DB::beginTransaction();

            if(!empty($certNumber)){
                $certificatesLeft = Certificate::whereNull('company_id')
                    ->whereIsMediaid(Certificate::IS_NOT_MEDIAID)
                    ->orderBy('id','desc')
                    ->limit($certNumber)
                    ->get();

                if(count($certificatesLeft) == intval($certNumber)){

                    foreach($certificatesLeft as $cert){
                        if(!$this->addCertToMediaid($cert['id'])){
                            DB::rollBack();
                        }
                    }

                    DB::commit();
                }else{
                    return redirect()->action('Mediaid\CertificatesMediaidController@getIndex')->withErrors(['AddCertFailFailed' => __('The amount of certificates is not enough')])->withInput();
                }
            }else{
                return redirect()->action('Mediaid\CertificatesMediaidController@getIndex')->withErrors(['AddCertFailFailed' => __('Please enter amount of certificates')])->withInput();
            }
        }else if($certAlias){
            // last step: user downloads certificate and install on current pc
            $certAlias = $request->input('cert_alias');
            $cert = Certificate::whereAlias($certAlias)
                ->whereIsMediaid(Certificate::IS_MEDIAID)
                ->first();

            if (empty ($cert)) {
                return redirect()->action('Mediaid\CertificatesMediaidController@getIndex');
            }

            $cert->status = Certificate::STATUS_DIVIDED_TO_DEVICE;
            $cert->issued_to_device_at = current_timestamp();
            $cert->save();

            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Disposition: attachment; filename="cert_mediaid_' . $cert->ssl_client_s_dn_cn . '.p12";');
            header('Content-Transfer-Encoding: binary');

            echo base64_decode($cert->client_pkcs12_certificate);
            exit;
        }else if($disableCertAlias){
            $cert = Certificate::whereAlias($disableCertAlias)
                ->whereIsMediaid(Certificate::IS_MEDIAID)
                ->first();

            if (empty ($cert)) {
                return redirect()->action('Mediaid\CertificatesMediaidController@getIndex');
            }

            $cert->status = Certificate::STATUS_INACTIVE;
            $cert->issued_to_device_at = current_timestamp();
            $cert->save();
        }

       return redirect()->back();
    }

    private function addCertToMediaid($certId){
        $updateCert = Certificate::whereId($certId)
            ->whereIsMediaid(Certificate::IS_NOT_MEDIAID)
            ->whereNull('company_id')
            ->first();

        if($updateCert){
            $updateCert->is_mediaid = Certificate::IS_MEDIAID;

            if($updateCert->save()){
                return true;
            }
        }

        return false;

    }

        /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDisabledCertificate(Request $request) {
        $certificate = Certificate::whereAlias($request->input('alias'))->first();

        if (empty ($certificate)) {
            return r_err('Certificate not found');
        }

        $certificate->status = Certificate::STATUS_INACTIVE;

        $result = $certificate->save();
        $updatedAt = date('Y/m/d', strtotime($certificate->updated_at)) . '<br>' . date('H:i', strtotime($certificate->updated_at));

        return $result ? r_ok(['$status' => Certificate::$statuses[$certificate->status], 'updated_at' => $updatedAt], 'ok') : r_err('failed');
    }
}