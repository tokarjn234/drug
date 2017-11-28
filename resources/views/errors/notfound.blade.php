<?php
/**
 * Created by PhpStorm.
 * User: datpa_55
 * Date: 28/3/2016
 * Time: 1:25 PM
 */
?>


@extends('layouts.sb2adminnosidebar')

@section('content')
<<<<<<< HEAD
    <style>
        .logo {
            font-size: 200px;
            color: #8F8E8C;
            text-align: center;
            margin-bottom: 1px;
            text-shadow: 1px 1px 6px #fff;
            font-family: 'Courgette', cursive;
        }
        p {
            color: rgb(228, 146, 162);
            font-size: 20px;
            margin-top: 1px;
            text-align: center;
        }
    </style>
    <div class="text-center">
        <h1 class="logo">404</h1>
        <p>Error occurred! - Not Found</p>
        <div class="sub">
            <p><a href="{!! action('Home\OrdersController@getIndex') !!}" class="btn btn-outline btn-default">Back</a></p>
        </div>
    </div>
=======
    <h1 class="text-center">
        <p><span>404</span> not found</p>
    </h1>
>>>>>>> drugorder_release20160328
@stop