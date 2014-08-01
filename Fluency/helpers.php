<?php
/**
 * common.php
 * 
 * @author Joy <anzhengchao@gmail.com>
 * @date   [2014-07-17 15:51]
 */

/**
 * 获取app基础路径
 *
 * @param string $appends 子目录
 *
 * @return string
 */
function app_path($appends = '')
{
    return str_finish(stream_resolve_include_path(APP_PATH), '/') . $appends;
}

/**
 * 生成CSS路径
 *
 * @param string $subPath 没有host的css路径
 *
 * @return string
 */
function css($subPath) {
    if (empty($subPath)) {
        return '';
    }

    $cssHost = (false === stripos($subPath, 'http')) ? str_finish(Config::get('url.static.css'), '/') : '';

    return asset($cssHost . ltrim($subPath, '/'));
}

/**
 * 生成img路径
 *
 * @param string $subPath 没有host的img路径
 *
 * @return string
 */
function img($subPath) {
    if (empty($subPath)) {
        return '';
    }
    
    $imgHost = (false === stripos($subPath, 'http')) ? str_finish(Config::get('url.static.img'), '/') : '';

    return asset($imgHost . ltrim($subPath, '/'));
}

/**
 * 生成js路径
 *
 * @param string $subPath 没有host的js路径
 *
 * @return string
 */
function js($subPath) {
    if (empty($subPath)) {
        return '';
    }
    
    $jsHost = (false === stripos($subPath, 'http')) ? str_finish(Config::get('url.static.js'), '/') : '';

    return asset($jsHost . ltrim($subPath, '/'));
}

/**
 * 生成资源链接
 *
 * @param string $url 资源URL
 *
 * @return string
 */
function asset($url) {
    if (empty($url)) {
        return '';
    }
    
    return $url . '?v=' . Config::get('app.static_version', date('Ym'));
}
    
/**
 * 生成或者读取datakey
 *
 * @param array $data  数据
 *
 * @return string
 */
function dataKey($data)
{
    if (is_array($data)) {
        $ret = base64_encode(encrypt(json_encode($data)));
    } elseif (is_string($data)) {
        $ret = json_decode(decrypt(base64_decode($data)), true);
    }

    return $ret;
}

/**
 * 获取加密key
 *
 * @return string
 */
function getSecurekey() {
    return md5('hello');
}

/**
 * 加密字符串
 *
 * @param string $input 
 *
 * @return string
 */
function encrypt($input) {
    $securekey = getSecurekey();
    return mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $securekey, $input, MCRYPT_MODE_ECB);
}

/**
 * 解密字符串
 *
 * @param string $input 
 *
 * @return string
 */
function decrypt($input) {
    $securekey = getSecurekey();
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $securekey, $input, MCRYPT_MODE_ECB));
}

/**
 * 获取配置文件路径
 *
 * @return string
 */
function config_path($subPath = '')
{
    return str_finish(app_path('config'), '/') . $subPath;
}

/**
 * 字节格式化
 *
 * @param integer $size 
 *
 * @return string
 */
function biteConvert($size)
{
    $unit=array('B','KB','MB','GB','TB','PB');

    return @round($size / pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

if (!function_exists('niceTime')) {
    function niceTime($date)
    {
        if(empty($date)) {
            return "No date provided";
        }

        
        $periods     = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $periodMulti = array("seconds", "minutes", "hours", "days", "weeks", "months", "years", "decades");
        $lengths     = array("60","60","24","7","4.35","12","10");
        
        $now         = time();
        $unix_date   = is_numeric($date) ? $date : strtotime($date);
        
           // check validity of date
        if(empty($unix_date)) {    
            return "Bad date";
        }

        // is it future date or past date
        if($now > $unix_date + 60) {    
            $difference = $now - $unix_date;
            $tense      = "time.ago";
            
        } else {
            $difference = abs($unix_date - $now);
            
            return trans("time.from_now");
        }
        
        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }
        
        $difference = round($difference);
        
        $lang = strtolower(Phalcon\DI::getDefault()->get('request')->getBestLanguage());

        if(($lang == 'en_us' || $lang == 'us') && $difference != 1) {
            $periods[$j].= "s";
        }

        // 翻译
        $tense = trans($tense);
        $periods = trans('time.'. rtrim($periods[$j], 's'));
        
        return "{$difference}{$periods}{$tense}";
    }
}

if ( ! function_exists('append_config'))
{
    /**
     * Assign high numeric IDs to a config item to force appending.
     *
     * @param  array  $array
     * @return array
     */
    function append_config(array $array)
    {
        $start = 9999;

        foreach ($array as $key => $value)
        {
            if (is_numeric($key))
            {
                $start++;

                $array[$start] = array_pull($array, $key);
            }
        }

        return $array;
    }
}

if(!function_exists('array_column')){ 
    function array_column($input, $columnKey, $indexKey=null){
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
        $indexKeyIsNull     = (is_null($indexKey))?true :false; 
        $indexKeyIsNumber   = (is_numeric($indexKey))?true:false; 
        $result                         = array(); 
        foreach((array)$input as $key=>$row){ 
            if($columnKeyIsNumber){ 
                $tmp= array_slice($row, $columnKey, 1); 
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
            }else{ 
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
            } 
            if(!$indexKeyIsNull){ 
                if($indexKeyIsNumber){ 
                  $key = array_slice($row, $indexKey, 1); 
                  $key = (is_array($key) && !empty($key))?current($key):null; 
                  $key = is_null($key)?0:$key; 
                }else{ 
                  $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                } 
            } 
            $result[$key] = $tmp; 
        } 

        return $result; 
    }
}

if ( ! function_exists('array_add'))
{
    /**
     * Add an element to an array if it doesn't exist.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    function array_add($array, $key, $value)
    {
        if ( ! isset($array[$key])) $array[$key] = $value;

        return $array;
    }
}

if ( ! function_exists('array_build'))
{
    /**
     * Build a new array using a callback.
     *
     * @param  array  $array
     * @param  \Closure  $callback
     * @return array
     */
    function array_build($array, Closure $callback)
    {
        $results = array();

        foreach ($array as $key => $value)
        {
            list($innerKey, $innerValue) = call_user_func($callback, $key, $value);

            $results[$innerKey] = $innerValue;
        }

        return $results;
    }
}

if ( ! function_exists('array_divide'))
{
    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array  $array
     * @return array
     */
    function array_divide($array)
    {
        return array(array_keys($array), array_values($array));
    }
}

if ( ! function_exists('array_dot'))
{
    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array   $array
     * @param  string  $prepend
     * @return array
     */
    function array_dot($array, $prepend = '')
    {
        $results = array();

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $results = array_merge($results, array_dot($value, $prepend.$key.'.'));
            }
            else
            {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }
}

if ( ! function_exists('array_except'))
{
    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array  $array
     * @param  array  $keys
     * @return array
     */
    function array_except($array, $keys)
    {
        return array_diff_key($array, array_flip((array) $keys));
    }
}

if ( ! function_exists('array_fetch'))
{
    /**
     * Fetch a flattened array of a nested array element.
     *
     * @param  array   $array
     * @param  string  $key
     * @return array
     */
    function array_fetch($array, $key)
    {
        foreach (explode('.', $key) as $segment)
        {
            $results = array();

            foreach ($array as $value)
            {
                $value = (array) $value;

                $results[] = $value[$segment];
            }

            $array = array_values($results);
        }

        return array_values($results);
    }
}

if ( ! function_exists('array_first'))
{
    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array    $array
     * @param  Closure  $callback
     * @param  mixed    $default
     * @return mixed
     */
    function array_first($array, $callback, $default = null)
    {
        foreach ($array as $key => $value)
        {
            if (call_user_func($callback, $key, $value)) return $value;
        }

        return value($default);
    }
}

if ( ! function_exists('array_last'))
{
    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array    $array
     * @param  Closure  $callback
     * @param  mixed    $default
     * @return mixed
     */
    function array_last($array, $callback, $default = null)
    {
        return array_first(array_reverse($array), $callback, $default);
    }
}

if ( ! function_exists('array_flatten'))
{
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array  $array
     * @return array
     */
    function array_flatten($array)
    {
        $return = array();

        array_walk_recursive($array, function($x) use (&$return) { $return[] = $x; });

        return $return;
    }
}

if ( ! function_exists('array_forget'))
{
    /**
     * Remove an array item from a given array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @return void
     */
    function array_forget(&$array, $key)
    {
        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            if ( ! isset($array[$key]) || ! is_array($array[$key]))
            {
                return;
            }

            $array =& $array[$key];
        }

        unset($array[array_shift($keys)]);
    }
}

if ( ! function_exists('array_get'))
{
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment)
        {
            if ( ! is_array($array) || ! array_key_exists($segment, $array))
            {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if ( ! function_exists('array_only'))
{
    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array  $keys
     * @return array
     */
    function array_only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}

if ( ! function_exists('array_pluck'))
{
    /**
     * Pluck an array of values from an array.
     *
     * @param  array   $array
     * @param  string  $value
     * @param  string  $key
     * @return array
     */
    function array_pluck($array, $value, $key = null)
    {
        $results = array();

        foreach ($array as $item)
        {
            $itemValue = is_object($item) ? $item->{$value} : $item[$value];

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key))
            {
                $results[] = $itemValue;
            }
            else
            {
                $itemKey = is_object($item) ? $item->{$key} : $item[$key];

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }
}

if ( ! function_exists('array_pull'))
{
    /**
     * Get a value from the array, and remove it.
     *
     * @param  array   $array
     * @param  string  $key
     * @return mixed
     */
    function array_pull(&$array, $key)
    {
        $value = array_get($array, $key);

        array_forget($array, $key);

        return $value;
    }
}

if ( ! function_exists('array_set'))
{
    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;

        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if ( ! isset($array[$key]) || ! is_array($array[$key]))
            {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if ( ! function_exists('array_where'))
{
    /**
     * Filter the array using the given Closure.
     *
     * @param  array  $array
     * @param  \Closure  $callback
     * @return array
     */
    function array_where($array, Closure $callback)
    {
        $filtered = array();

        foreach ($array as $key => $value)
        {
            if (call_user_func($callback, $key, $value)) $filtered[$key] = $value;
        }

        return $filtered;
    }
}

if ( ! function_exists('camel_case'))
{
    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    function camel_case($value)
    {
        return lcfirst(studly_case($value));
    }
}

if ( ! function_exists('studly_case'))
{
    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    function studly_case($value)
    {
        $value = ucwords(str_replace(array('-', '_'), ' ', $value));

        return str_replace(' ', '', $value);
    }
}


if ( ! function_exists('class_basename'))
{
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}


if ( ! function_exists('dd'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  dynamic  mixed
     * @return void
     */
    function dd()
    {
        array_map(function($x) { var_dump($x); }, func_get_args()); die;
    }
}

if ( ! function_exists('e'))
{
    /**
     * Escape HTML entities in a string.
     *
     * @param  string  $value
     * @return string
     */
    function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if ( ! function_exists('ends_with'))
{
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $haystack
     * @param string|array $needle
     * @return bool
     */
    function ends_with($haystack, $needle)
    {
        foreach ((array) $needles as $needle)
        {
            if ($needle == substr($haystack, -strlen($needle))) return true;
        }

        return false;
    }
}

if ( ! function_exists('head'))
{
    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param  array  $array
     * @return mixed
     */
    function head($array)
    {
        return reset($array);
    }
}

if ( ! function_exists('last'))
{
    /**
     * Get the last element from an array.
     *
     * @param  array  $array
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if ( ! function_exists('object_get'))
{
    /**
     * Get an item from an object using "dot" notation.
     *
     * @param  object  $object
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function object_get($object, $key, $default = null)
    {
        if (is_null($key) or trim($key) == '') return $object;

        foreach (explode('.', $key) as $segment)
        {
            if ( ! is_object($object) || ! isset($object->{$segment}))
            {
                return value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }
}

if ( ! function_exists('preg_replace_sub'))
{
    /**
     * Replace a given pattern with each value in the array in sequentially.
     *
     * @param  string  $pattern
     * @param  array   $replacements
     * @param  string  $subject
     * @return string
     */
    function preg_replace_sub($pattern, &$replacements, $subject)
    {
        return preg_replace_callback($pattern, function($match) use (&$replacements)
        {
            return array_shift($replacements);

        }, $subject);
    }
}

if ( ! function_exists('secure_asset'))
{
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @return string
     */
    function secure_asset($path)
    {
        return asset($path, true);
    }
}

if ( ! function_exists('url'))
{
    /**
     * Generate a HTTPS url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @return string
     */
    function url()
    {
        return forward_static_call_array(array('Url', 'get'), func_get_args());
    }
}

if ( ! function_exists('snake_case'))
{
    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    function snake_case($value, $delimiter = '_')
    {
        $replace = '$1'.$delimiter.'$2';

        return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
    }
}

if ( ! function_exists('starts_with'))
{
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needle
     * @return bool
     */
    function starts_with($haystack, $needle)
    {
        foreach ((array) $needles as $needle)
        {
            if ($needle != '' && strpos($haystack, $needle) === 0) return true;
        }

        return false;
    }
}

if ( ! function_exists('str_contains'))
{
    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string        $haystack
     * @param  string|array  $needle
     * @return bool
     */
    function str_contains($haystack, $needle)
    {
        foreach ((array) $needles as $needle)
        {
            if ($needle != '' && strpos($haystack, $needle) !== false) return true;
        }

        return false;
    }
}

if ( ! function_exists('str_finish'))
{
    /**
     * Cap a string with a single instance of a given value.
     *
     * @param  string  $value
     * @param  string  $cap
     * @return string
     */
    function str_finish($value, $cap)
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:'.$quoted.')+$/', '', $value).$cap;
    }
}

if ( ! function_exists('str_replace_array'))
{
    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string  $search
     * @param  array  $replace
     * @param  string  $subject
     * @return string
     */
    function str_replace_array($search, array $replace, $subject)
    {
        foreach ($replace as $value)
        {
            $subject = preg_replace('/'.$search.'/', $value, $subject, 1);
        }

        return $subject;
    }
}

if ( ! function_exists('trans'))
{
    /**
     * Translate the given message.
     *
     * @param  string  $id
     * @param  array   $parameters
     * @return string
     */
    function trans($id, $parameters = array())
    {
        return Lang::getLine($id, $parameters);
    }
}

if ( ! function_exists('t'))
{
    /**
     * Translate the given message.
     *
     * @param  string  $id
     * @param  array   $parameters
     * @return string
     */
    function t($id, $parameters = array())
    {
        return Lang::getLine($id, $parameters);
    }
}

if ( ! function_exists('path_join'))
{
    function path_join()
    {
        $arr = func_get_args();

        return str_replace(['//', '\\'], '/', join('/', $arr));
    }
}


if ( ! function_exists('value'))
{
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if ( ! function_exists('with'))
{
    /**
     * Return the given object. Useful for chaining.
     *
     * @param  mixed  $object
     * @return mixed
     */
    function with($object)
    {
        return $object;
    }
}


if(!function_exists('get_client_ip')){
    
    function get_client_ip() 
    { 
        $unknown = 'unknown'; 
        
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) 

            && $_SERVER['HTTP_X_FORWARDED_FOR'] 

            && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) { 

                $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 

        } elseif(isset($_SERVER['REMOTE_ADDR']) 

                && $_SERVER['REMOTE_ADDR'] 

                && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) { 

                $ip = $_SERVER['REMOTE_ADDR']; 
            } 

        if (false !== strpos($ip, ','))
        { 
            $ip = reset(explode(',', $ip));
        } 
        return ip2long($ip); 
    }
}