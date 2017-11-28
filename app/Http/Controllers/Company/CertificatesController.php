<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Store;

class CertificatesController extends CompanyAppController
{
    /**
     * Certificates list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex()
    {

        $searchData = session('Company_CertificateSearchData');

        if ($searchData) {
            return view('company.certificates.index', $this->getSearchData($searchData));
        }

        $companyId = $this->getCurrentCompany('id');

        $paginate = Certificate::select(
            'certificates.id', 'certificates.alias', 'ssl_client_s_dn_cn', 'certificates.created_at', 'store_id',
            'issued_to_store_at', 'certificates.name', 'certificates.updated_at', 'certificates.status', 'stores.name AS store_name')
            ->where('certificates.company_id', '=', $companyId)
            ->leftJoin('stores', 'stores.id', '=', 'certificates.store_id')
            ->whereIsMediaid(Certificate::IS_NOT_MEDIAID)
            ->orderBy('id', 'desc')
            ->paginate(10);

        $certificates = Certificate::render($paginate);


        return view('company.certificates.index', [
            'paginate' => $paginate,
            'jsonData' => [
                'countData' => $this->countCertificates(),
                'certificates' => $certificates,
                'paginate' => $paginate->currentPage(),
                'restoreCertificateUrl' => action('Company\CertificatesController@postRestoreCertificate'),
                'disableCertificateUrl' => action('Company\CertificatesController@postDisabledCertificate'),
            ]]);
    }


    /**
     * Counts certificates data
     * @return array
     */
    private function countCertificates()
    {
        $companyId = $this->getCurrentCompany('id');
        $totalCerCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($companyId)->count();
        //$dividedToStoreCount = Certificate::whereCompanyId($companyId)->whereStatus(Certificate::STATUS_DIVIDED_TO_STORE)->count();
        $dividedToDeviceCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($companyId)->whereStatus(Certificate::STATUS_DIVIDED_TO_DEVICE)->count();
        $availableCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($companyId)->whereStatus(Certificate::STATUS_NOT_DIVIDE)->orWhereNull('status')->count();
        $inactiveCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($companyId)->whereStatus(Certificate::STATUS_INACTIVE)->count();
        $dividedToStoreCount = $totalCerCount - $availableCount;

        return compact('totalCerCount', 'dividedToStoreCount', 'dividedToDeviceCount', 'availableCount', 'inactiveCount');
    }

    /**
     * Searches certificates
     */
    private function getSearchData($request)
    {

        $companyId = $this->getCurrentCompany('id');

        $queryable = Certificate::select('certificates.*', 'stores.name AS store_name')
            ->where('certificates.company_id', '=', $companyId)
            ->leftJoin('stores', 'stores.id', '=', 'certificates.store_id')
            ->whereIsMediaid(Certificate::IS_NOT_MEDIAID)
            ->orderBy('id', 'desc');

        if (!empty ($request['ssl_client_s_dn_cn'])) {
            $queryable->search('ssl_client_s_dn_cn', $request['ssl_client_s_dn_cn']);
        }

        if (!empty ($request['store_name'])) {
            $queryable->search('stores.name', $request['store_name']);
        }

        if (isset ($request['status']) && $request['status'] != -1) {
            $queryable->where('certificates.status', '=', $request['status']);
        }

        $paginate = $queryable->paginate(10);

        $certificates = Certificate::render($paginate);

        return ['paginate' => $paginate,

            'search' => $request,
            'jsonData' => [
                'countData' => $this->countCertificates(),
                'certificates' => $certificates,
                'paginate' => $paginate->currentPage(),
                'restoreCertificateUrl' => action('Company\CertificatesController@postRestoreCertificate'),
                'disableCertificateUrl' => action('Company\CertificatesController@postDisabledCertificate'),
            ]];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postIndex(Request $request)
    {
        if ($request->input('_clear')) {
            session(['Company_CertificateSearchData' => null]);
            return redirect()->to(action('Company\CertificatesController@getIndex'));
        }

        session(['Company_CertificateSearchData' => $request->all()]);

        return redirect()->to(action('Company\CertificatesController@getIndex'));
    }

    /**
     * Issues Certificates to stores
     * @param Request $request
     * @throws
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postIssueCertificates(Request $request)
    {
        $company = $this->getCurrentCompany();
        $certificates = Certificate::whereIn('alias', $request->input('item'))
            ->whereCompanyId($company->id)
            ->whereIsMediaid(Certificate::IS_NOT_MEDIAID)
            ->whereIn('status', [Certificate::STATUS_DIVIDED_TO_STORE, Certificate::STATUS_NOT_DIVIDE])
            ->get();


        if ($request->input('action') === 'issue') {

            // check alias of store or company
            $store = Store::select('id', 'name')->whereCompanyId($company->id)
                ->whereNull('is_deleted')
                ->where('is_published', 1)
                ->whereAlias($request->input('store_alias'))->first();

            if (empty ($store)) {

                if($request->input('store_alias') == $company->alias){
                    foreach ($certificates as $cer) {
                        $cer->status = Certificate::STATUS_DIVIDED_TO_STORE;
                        $cer->issued_to_store_at = current_timestamp();
                        $cer->save();
                    }
                    return view('company.certificates.export', ['certificates' => $certificates, 'company' => $company]);
                }

                throw new \Exception('Invalid store');
            }else{
                foreach ($certificates as $cer) {
                    $cer->store_id = $store->id;
                    $cer->status = Certificate::STATUS_DIVIDED_TO_STORE;
                    $cer->issued_to_store_at = current_timestamp();
                    $cer->save();
                }
            }

            return view('company.certificates.export', ['certificates' => $certificates, 'store' => $store]);
        }

        $firstStoreAlias = Store::whereCompanyId($company->id)
            ->whereNull('is_deleted')
            ->where('is_published', 1)
            ->orderBy('id', 'asc')->first()->alias;

        $stores = Store::whereCompanyId($company->id)
            ->whereNull('is_deleted')
            ->where('is_published', 1)
            ->lists('name', 'alias')->toArray();


        $currentCompany[$company['alias']] = $company['name'];
        $stores = array_merge($stores,$currentCompany);

        return view('company.certificates.issue_certificates', [
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
    public function getIssueCertificates()
    {
        return redirect()->action('Company\CertificatesController@getIndex');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRestoreCertificate(Request $request)
    {
        $certificate = Certificate::whereCompanyId($this->getCurrentCompany('id'))->whereAlias($request->input('alias'))->first();

        if (empty ($certificate)) {
            return r_err('Certificate not found');
        }

        $certificate->store_id = null;
        $certificate->status = Certificate::STATUS_NOT_DIVIDE;
        $certificate->issued_to_store_at = null;
        $certificate->name = null;

        $result = $certificate->save();
        $updatedAt = date('Y/m/d', strtotime($certificate->updated_at)) . '<br>' . date('H:i', strtotime($certificate->updated_at));

        return $result ? r_ok(['$status' => Certificate::$statuses[$certificate->status], 'updated_at' => $updatedAt, 'count' => $this->countCertificates()], 'ok') : r_err('failed');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDisabledCertificate(Request $request)
    {
        $certificate = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($this->getCurrentCompany('id'))->whereAlias($request->input('alias'))->first();

        if (empty ($certificate)) {
            return r_err('Certificate not found');
        }

        $certificate->status = Certificate::STATUS_INACTIVE;

        $result = $certificate->save();
        $updatedAt = date('Y/m/d', strtotime($certificate->updated_at)) . '<br>' . date('H:i', strtotime($certificate->updated_at));

        return $result ? r_ok(['$status' => Certificate::$statuses[$certificate->status], 'updated_at' => $updatedAt, 'count' => $this->countCertificates()], 'ok') : r_err('failed');
    }

}