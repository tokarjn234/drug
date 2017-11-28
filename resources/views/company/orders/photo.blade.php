@extends('layouts.sb2adminnosidebar')

@section('title', $order->order_code)

@section('content')
    <style>
        @media print
        {
            .no-print, .no-print *
            {
                display: none !important;
            }
        }
    </style>
    <div class="check-presc">
        <div class="control-top clearfix">
            <div class="info-left">
                <table>
                    <colgroup>
                        <col width="30%">
                        <col width="70%">
                    </colgroup>
                    <tr>
                        <td>受付番号: {{$order->order_code}}</td>
                        <td>送信者名（カナ): {{ decrypt_data($order->first_name_kana) .' '. decrypt_data($order->last_name_kana) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">電話番号: {{decrypt_data($order->phone_number)}}</td>
                    </tr>
                    <tr>
                        <td colspan="2">メールアドレス: {{decrypt_data($order->email)}}</td>
                    </tr>
                    <tr>
                        <td colspan="2">受取希望日時: {{$order->visit_at_string}}</td>
                    </tr>
                    <tr>
                        <td>ジェネリック医薬品: {{$order->drugbrand_change}}</td>
                        <td>手帳シール:{{$order->drugbook_use}}</td>
                    </tr> 
					<tr>
                        <td style="word-wrap: break-word; white-space: pre-wrap;" colspan="2">コメント: {{$order->comment}}</td>
                    </tr>
                </table>
                <!--/info-left-->
            </div>
            <div class="control-right no-print">
                <button class="btn btn-primary btn-view-big" onclick="Photo.zoomOut()" type="button">＋拡大</button>
                <button class="btn btn-primary btn-view-small" onclick="Photo.zoomIn()" type="button">－縮小</button>
                <button class="btn btn-primary btn-print" onclick="Photo.print()" type="button">印刷</button>
                <!--/control-right--></div>
            <!--/control-top--></div>
        <div class="no-print" style="width: 50%;margin: 0 auto;text-align: center">
            <nav id="pagination">
                <ul class="pagination">
                    <li class="first"  id="prevBtn">
                        <a  onclick="Photo.prev();return false;" href="">«</a>
                    </li>
                    <li class="">
                        <a class="pageDisplay" onclick="return false;" href=""></a>
                    </li>

                    <li id="nextBtn">
                        <a  onclick="Photo.next();return false" href="">»</a>
                    </li>
                </ul>

            </nav>
        </div>
        <div class="view-presc" id="photo">

        </div>    <!--/view-presc-->
        <div class="no-print" style="width: 50%;margin: 0 auto;text-align: center">
            <nav id="pagination">
                <ul class="pagination">
                    <li class="first"  id="prevBtn">
                        <a  onclick="Photo.prev();return false;" href="">«</a>
                    </li>
                    <li class="">
                        <a class="pageDisplay" onclick="return false;" href=""></a>
                    </li>

                    <li id="nextBtn">
                        <a  onclick="Photo.next();return false" href="">»</a>
                    </li>
                </ul>

            </nav>
        </div>
        </div><!--check-presc-->
    <script>

        (function() {
            var $photo = document.getElementById('photo');
            var $preBtn = document.getElementById('prevBtn');
            var $nextBtn = document.getElementById('nextBtn');
            var $pageDiplay = document.getElementsByClassName('pageDisplay');

            var photos = JSON.parse('{!!addslashes(json_encode($photos))  !!}');

            var zoomDelta = 10;
            var currentZoom = 140;
            var currentPhoto  = 1;
            var maxPhoto = '{{count($photos)}}';

            window.Photo = {
                zoomOut : function() {
                    currentZoom +=zoomDelta;
                    $photo.style.zoom = currentZoom + '%';
                },
                zoomIn: function() {
                    currentZoom -=zoomDelta;
                    $photo.style.zoom = currentZoom + '%';
                },
                print: function() {
                    window.print();
                },
                next: function() {
                    if (currentPhoto == maxPhoto) {
                        return;
                    }

                    currentPhoto++;
                    this.render();


                },
                prev: function() {
                    if (currentPhoto == 1) {
                        return;
                    }

                    currentPhoto--;
                    this.render();
                },
                render: function() {
                    if (currentPhoto == 1) {
                        $preBtn.className ='disabled';
                        $nextBtn.className = '';
                    } else if (currentPhoto == maxPhoto) {
                        $nextBtn.className = 'disabled';
                        $preBtn.className ='';
                    } else {
                        $preBtn.className = '';
                        $nextBtn.className = '';
                    }

                    $pageDiplay[0].innerText = $pageDiplay[1].innerText = currentPhoto + '/' + maxPhoto;
                    $photo.innerHTML = photos[currentPhoto - 1].photo_url;
                    $photo.setAttribute('data-photo-id', photos[currentPhoto - 1].id);
                }

            }

            Photo.render();

        })();

    </script>

@endsection