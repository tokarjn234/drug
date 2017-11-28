@extends('layouts.sb2adminnosidebar')

@section('title', 'Image Prescription')

@section('content')
    <div class="check-presc">
        <div class="control-top clearfix">
            <div class="info-left">
                <p>受付番号：10001 送信者名：ヤマダタロウ</p>

                <p>電話番号：09012345678</p>

                <p>メールアドレス：yamada@abc.com</p>

                <p>受付希望日時：16:00以降</p>

                <p>ジェネリック医薬品：希望 手帳シール：必要</p>
                <!--/info-left--></div>
            <div class="control-right">
                <button class="btn btn-primary btn-view-big" type="button">＋拡大</button>
                <button class="btn btn-primary btn-view-small" type="button">－縮小</button>
                <button class="btn btn-primary btn-print" type="button">印刷</button>
                <!--/control-right--></div>
            <!--/control-top--></div>
        <div class="view-presc">
            {!! HTML::image("images/print.jpg") !!}
            <!--/view-presc--></div>
        <!--check-presc--></div>
@endsection