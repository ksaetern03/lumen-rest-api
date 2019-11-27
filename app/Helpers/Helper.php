<?php 
namespace App\Helpers;

use DB;
use Cache;
use Redis;

class Helper {

    /**
     * Flush all cached user keys
     */
    public static function flushUsers(){
    	Cache::tags('users')->flush();
    }

}