<?php

namespace App\Http\Controllers\Mediaid;
use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Store;
use App\Models\Company;


class CertificatesController extends MediaidAppController
{
    /**
     * Certificates list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getListCertificate() {

        $searchData = session('Mediaid_CertificateSearchData');

        // List certificate by company_id
        if(!empty($_GET['company_id'])){
            $companyId = Company::whereAlias($_GET['company_id'])->lists('id')->first();
            $searchData['company_id'] = $companyId;
            session(['Mediaid_CertificateSearchData' => $searchData]);
        }else if(!isset($_GET['company_id']) && !isset($_GET['page'])){

            if(isset($searchData['company_id'])){
                unset($searchData['company_id']);
                session(['Mediaid_CertificateSearchData' => $searchData]);
            }
        }

        if ($searchData) {
            return view('mediaid.certificates.list_certificate', $this->getSearchData($searchData));
        }

        $paginate = Certificate::select(
            'certificates.*', 'stores.name AS store_name', 'companies.name AS company_name', 'companies.id AS company_id')
            ->leftJoin('stores', 'stores.id', '=', 'certificates.store_id')
            ->leftJoin('companies', 'companies.id', '=', 'certificates.company_id')
            ->whereIsMediaid(Certificate::IS_NOT_MEDIAID)
            ->orderBy('id', 'desc')
            ->paginate(10);

        $certificates = Certificate::render($paginate);

        return view('mediaid.certificates.list_certificate', [
            'paginate' => $paginate,
            'jsonData' => [
                'countData' => $this->countCertificates(),
                'certificates' => $certificates,
                'paginate' => $paginate->currentPage(),
                'restoreCertificateUrl' => action('Mediaid\CertificatesController@postRestoreCertificate'),
                'disableCertificateUrl' => action('Mediaid\CertificatesController@postDisabledCertificate'),
            ]]);
    }


    /**
     * Counts certificates data
     * @return array
     */
    private function countCertificatesByCompanyId($companyId) {
        $totalCerCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($companyId)->count();
        //$dividedToStoreCount = Certificate::whereCompanyId($companyId)->whereStatus(Certificate::STATUS_DIVIDED_TO_STORE)->count();
        $dividedToDeviceCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($companyId)->whereStatus(Certificate::STATUS_DIVIDED_TO_DEVICE)->count();
        $availableCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($companyId)->whereStatus(Certificate::STATUS_NOT_DIVIDE)->orWhereNull('status')->count();
        $inactiveCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($companyId)->whereStatus(Certificate::STATUS_INACTIVE)->count();
        $dividedToStoreCount = $totalCerCount - $availableCount;

        return compact('totalCerCount', 'dividedToStoreCount', 'dividedToDeviceCount', 'availableCount', 'inactiveCount');
    }

    /**
     * Counts certificates data
     * @return array
     */
    private function countCertificates() {
        $totalCerCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->count();
        $dividedToStoreCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereStatus(Certificate::STATUS_DIVIDED_TO_STORE)->count();
        $dividedToDeviceCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereStatus(Certificate::STATUS_DIVIDED_TO_DEVICE)->count();
        $availableCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereStatus(Certificate::STATUS_NOT_DIVIDE)->orWhereNull('status')->count();
        $inactiveCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereStatus(Certificate::STATUS_INACTIVE)->count();

        return compact('totalCerCount', 'dividedToStoreCount', 'dividedToDeviceCount', 'availableCount', 'inactiveCount');
    }

    /**
     * Searches certificates
     */
    private function getSearchData($request) {

        $queryable = Certificate::select('certificates.*', 'stores.name AS store_name', 'companies.name AS company_name', 'companies.id AS company_id')
            ->leftJoin('stores', 'stores.id', '=', 'certificates.store_id')
            ->leftJoin('companies', 'companies.id', '=', 'certificates.company_id')
            ->whereIsMediaid(Certificate::IS_NOT_MEDIAID)
            ->orderBy('id', 'desc');

        if (!empty ($request['ssl_client_s_dn_cn'])) {
            $queryable->search('ssl_client_s_dn_cn', $request['ssl_client_s_dn_cn']);
        }

        if (!empty ($request['company_name'])) {
            $queryable->search('companies.name', $request['company_name']);
        }

        if (!empty ($request['company_id'])) {
            $queryable->search('certificates.company_id', $request['company_id']);
        }

        if (isset ($request['status']) && $request['status'] != -1) {
            $queryable->where('certificates.status', '=' , $request['status']);
        }

        $paginate = $queryable->paginate(10);

        $certificates = Certificate::render($paginate);

        return ['paginate' => $paginate,

            'search' => $request,
            'jsonData' => [
                'countData' => $this->countCertificates(),
                'certificates' => $certificates,
                'paginate' => $paginate->currentPage(),
                'restoreCertificateUrl' => action('Mediaid\CertificatesController@postRestoreCertificate'),
                'disableCertificateUrl' => action('Mediaid\CertificatesController@postDisabledCertificate'),
            ]];
    }

    /**
     * Searches certificates
     */
    private function getSearchStatusCertsData($request) {
        $queryable = Company::select()
            ->orderBy('id', 'desc');

        if (!empty ($request['company_name'])) {
            $queryable->search('companies.name', $request['company_name']);
        }

        $paginate = $queryable->paginate(10);
        $listCompanies = Company::render($paginate);
        $companyCertificates = array();

        foreach($listCompanies as $company){
            $company['count'] =  $this->countCertificatesByCompanyId($company['id']);
            $companyCertificates[] = $company;
        }

        return ['paginate' => $paginate,
                'search' => $request,
                'certificates' => $companyCertificates,
                ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postListCertificate(Request $request) {
        if ($request->input('_clear')) {
            session(['Mediaid_CertificateSearchData' => null]);
            return redirect()->to(action('Mediaid\CertificatesController@getListCertificate'));
        }

        session(['Mediaid_CertificateSearchData' => $request->all()]);

        return redirect()->to(action('Mediaid\CertificatesController@getListCertificate'));
    }

    /**
     * Issues Certificates to stores
     * @param Request $request
     * @throws
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postIssueCertificates(Request $request) {

        $certificates = Certificate::whereIn('alias', $request->input('item'))
            ->whereIn('status', [Certificate::STATUS_DIVIDED_TO_STORE, Certificate::STATUS_NOT_DIVIDE])
            ->get();


        if ($request->input('action') === 'issue') {
            $store = Store::select('id', 'name')->whereAlias($request->input('store_alias'))->first();

            if (empty ($store) ) {
                throw new \Exception('Invalid store');
            }

            foreach ($certificates as $cer) {
                $cer->store_id = $store->id;
                $cer->status = Certificate::STATUS_DIVIDED_TO_STORE;
                $cer->issued_to_store_at = current_timestamp();
                $cer->save();
            }

            return view('mediaid.certificates.export', ['certificates' => $certificates, 'store' => $store]);
        }

        $firstStoreAlias = Store::orderBy('id', 'asc')->first()->alias;

        $stores = Store::lists('name', 'alias')->toArray();


        return view('mediaid.certificates.issue_certificates', [
            'certificates' => $certificates,
            'stores' => $stores,
            'firstStoreAlias' => $firstStoreAlias,
            'jsonData' => [
                'certificateCount' => count($certificates),
                'stores' => $stores,
                'firstStoreAlias' => $firstStoreAlias]
            ]
        );
    }

    /**
     * Redirects to index
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getIssueCertificates() {
        return redirect()->action('Mediaid\CertificatesController@getIndex');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRestoreCertificate(Request $request) {
        $certificate = Certificate::whereAlias($request->input('alias'))->first();

        if (empty ($certificate)) {
            return r_err('Certificate not found');
        }

        $certificate->store_id = null;
        $certificate->status = Certificate::STATUS_NOT_DIVIDE;
        $certificate->issued_to_store_at = null;
        $certificate->name = null;

        $result = $certificate->save();
        $updatedAt = date('Y/m/d', strtotime($certificate->updated_at)) . '<br>' . date('H:i', strtotime($certificate->updated_at));

        return $result ? r_ok(['$status' => Certificate::$statuses[$certificate->status],'updated_at' => $updatedAt, 'count' => $this->countCertificates()], 'ok') : r_err('failed');
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

        return $result ? r_ok(['$status' => Certificate::$statuses[$certificate->status], 'updated_at' => $updatedAt, 'count' => $this->countCertificates()], 'ok') : r_err('failed');
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postIndex(Request $request)
    {
        if ($request->input('_clear')) {
            session(['Mediaid_Status_CertificateSearchData' => null]);
            return redirect()->to(action('Mediaid\CertificatesController@getIndex'));
        }

        session(['Mediaid_Status_CertificateSearchData' => $request->all()]);

        return redirect()->to(action('Mediaid\CertificatesController@getIndex'));
    }



    public function getIndex(){


        $searchData = session('Mediaid_Status_CertificateSearchData');

        if ($searchData) {
            return view('Mediaid.certificates.index', $this->getSearchStatusCertsData($searchData));
        }

        $paginate = Company::select()
            ->orderBy('id', 'desc')
            ->paginate(10);

        $listCompanies = Company::render($paginate);
        $companyCertificates = array();

        foreach($listCompanies as $company){
            $company['count'] =  $this->countCertificatesByCompanyId($company['id']);
            $companyCertificates[] = $company;
        }

        return view('mediaid.certificates.index', [
            'paginate' => $paginate,
            'certificates' => $companyCertificates,
            'jsonData' => [
                'certificates' => $companyCertificates,
                'paginate' => $paginate->currentPage(),
                'restoreCertificateUrl' => action('Mediaid\CertificatesController@postRestoreCertificate'),
                'disableCertificateUrl' => action('Mediaid\CertificatesController@postDisabledCertificate'),
            ]]);
    }

}