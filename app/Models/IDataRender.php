<?php


namespace App\Models;

interface IDataRender
{
    public static function getRenderSettings();
    public static function render($data);
}