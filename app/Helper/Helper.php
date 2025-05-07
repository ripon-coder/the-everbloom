<?php
namespace App\Helper;
class Helper{
    public function DateFormat($date){
        return date("d-m-Y", strtotime($date));
    }
}