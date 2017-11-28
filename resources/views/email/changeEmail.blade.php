<div>
    <div style="padding-top: 10px; padding-bottom: 10px; border-top: 1px dashed #000;border-bottom: 1px dashed #000;float:left;">
        会員登録の受付をいたしました。
    </div>
    <div style="clear:both;"></div>
    <p>下記のURLにアクセスして、会員登録の完了の手続きをお願いいたします。 </p>

    <p>
        会員登録完了用URL <br/><a
                href="{!! replace_url(action('Home\MailsController@putConfirmEmailChangeProfile').'?rid='.$change_email_token.'&cp_n='.$company_name.'&rem='.$new_email) !!}">{!! replace_url(action('Home\MailsController@putConfirmEmailChangeProfile').'?rid='.$change_email_token.'&cp_n='.$company_name) !!}</a>
    </p>

    <p>
        ※URLの有効期限は、{!! $change_email_token_expire !!}までです。 <br/>
        有効期限を過ぎてしまった場合は、再度会員登録をやり直してください。
    </p>

    <p>-----------------------------------------------------------</p>

    <p>発行元 ： {!! $company_name !!}ドラッグ </p>

    <p>-----------------------------------------------------------</p>

    <p>©2016 {!! $company_name !!} Drug Co., Ltd. </p>
</div>