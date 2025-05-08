<?php
namespace App\Helper;
class Helper{
    public static function DateFormat($date){
        return date("d-m-Y", strtotime($date));
    }
}