<?php

namespace Backup\Util;

class Readline
{
    //http://stackoverflow.com/a/25340777/3000068
    public static function readline(String $prompt=null){
        if(!function_exists('readline')){
            if($prompt){
                echo $prompt;
            }
            $fp = fopen("php://stdin","r");
            $line = rtrim(fgets($fp, 1024));
            return $line;
        } else {
            return readline($prompt);
        }
    }
}