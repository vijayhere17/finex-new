<?php
    use App\Models\CoinRateMaster;

    if(!function_exists('formatdate')){
        function formatdate($date, $format){
           return date($format, strtotime($date));
        }
    }
    
    if(!function_exists('formatdecimal')){
        function formatdecimal($amount, $decimal){
           return number_format((float)$amount, $decimal, '.', '');
        }
    }

    if(!function_exists('getcoinrate')){
        function getcoinrate(){
            $object = CoinRateMaster::orderBy('id','desc')->first();
            return number_format((float)$object->rate, 8, '.', '');
            
            return number_format((float)$rate, 8, '.', '');
        }
    }
    
    if(!function_exists('getwithdrawrate')){
        function getwithdrawrate(){
            $object = CoinRateMaster::orderBy('id','desc')->first();
            return number_format((float)$object->rate, 8, '.', '');

            return number_format((float)$rate, 8, '.', '');
        }
    }

    if(!function_exists('obscureAddress')){
        function obscureAddress($address) {
            return substr($address, 0, 6) . '...' . substr($address, -4);
        }
    }

 
    