<div>
    <p>*****************************************<br/>
        {!! $company_name !!}ドラッグ　処方せん受信致しました。
        <br/>*****************************************</p>

    <p>下記のURLをクリックするとアプリ画面へ遷移し、お知らせ内容を確認することができます。 </p>

    <p>
        ■{!! $company_name !!}ドラッグ　お知らせ画面URL <br/>
        <a href="{!! replace_url(action('Home\MailsController@getOpenApp').'?flag='.$message_flag.'&rid='.$order_alias.'&namest='.urlencode($store_name).'&patientReplySetting='.$patientReplySetting.'&cp_n='.urlencode($company_name)) !!}">
            http://drugOrder.com/member/associateRegspFin?rid={!! $order_alias !!}
        </a>
    </p>

    <p>©2016 {!! $company_name !!} Drug Co., Ltd. </p>
</div>
