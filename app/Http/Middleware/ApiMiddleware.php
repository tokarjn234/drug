<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use App\Models\DebugLog;
use Mockery\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;


class ApiMiddleware
{

    public function handle($request, Closure $next)
    {
        if (empty ($request->headers->get('company'))) {
            return r_err([], ['Missing company params']);
        }
        $companyStatus = Company::findByAlias($request->headers->get('company'), 'status');
        if ($companyStatus == Company::STATUS_CANCELLATION_COMPLETED) {
            return r_err(['Company is not active'], __('Company is not active'), 4);
        }

        if (config('api.API_ACCESS_TOKEN_REQUIRED') && \Request::route()->getName() !== 'OAuthAccessTokenUri') {

            $oauth = \App\Lib\OAuth2Helper::initOauthServer();

            $globalRequest = \OAuth2\Request::createFromGlobals();

            if (!$oauth->verifyResourceRequest($globalRequest)) {
                $response = $oauth->getResponse();
                $resParams = $response->getParameters();

                if (!empty ($resParams)) {
                    return r_err([$resParams]);
                }

                return r_err(['Invalid access token.']);
            } else {
                $token = $oauth->getAccessTokenData($globalRequest);
                $request->token = $token;

                if ($request->header('AppKey') !== sha1($token['app_keycode'])) {
                    return r_err(['Invalid AppKey']);
                }
            }
        }

        $response = $next($request);

        try {
//            $entry = DebugLog::init();
//            $entry->message = 'ApiDebug';
//            $entry->event_type = 'info';
//
//            if (!empty ($request->token)) {
//                $entry->user_id = $request->token['user_id'];
//            }
//
//            $entry->response = json_encode($response->original);
//            $entry->save();
        } catch (\Exception $e) {

        }

        return $response;
    }
}