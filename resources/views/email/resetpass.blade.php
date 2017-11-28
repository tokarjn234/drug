<div>
    <div style="padding-top: 10px; padding-bottom: 10px; border-top: 1px dashed #000;border-bottom: 1px dashed #000;float:left;">
        パスワードの再設定を受付いたしました。
    </div>
    <div style="clear:both;"></div>
    <p>下記のURLにアクセスして、パスワード再設定の完了手続きをお願いいたします。</p>

    <p>
        パスワード再設定用URL<br/><a
                href="{!! replace_url(action('Home\MailsController@putResetPassword').'?rid='.$reset_pass_token.'&cp_n='.$company_name) !!}">{!! replace_url(action('Home\MailsController@putResetPassword').'?rid='.$reset_pass_token.'&cp_n='.$company_name) !!}</a>
    </p>

    <p>
        ※URLの有効期限は、{!! $reset_pass_token_expire !!}までです。<br/>
        有効期限を過ぎてしまった場合は、再度ログイン画面からやり直してください。
    </p>

    <p>-----------------------------------------------------------</p>

    <p>発行元 ： {!! $company_name !!}ドラッグ</p>

    <p>-----------------------------------------------------------</p>

    <p>©2016 {!! $company_name !!} Drug Co., Ltd. </p>
</div>