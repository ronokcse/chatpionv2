<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(! function_exists('http_api_run_curl')){
    function http_api_run_curl($endpoint_url=null,$method = null,$body_data = null, $body_data_type = null, $header_data = [], $option_data=[], $cookie_data = [],$json=false) {
        $ci = &get_instance();
        if(empty($endpoint_url) || empty($method) || (empty($body_data_type) && strtoupper($method) !== 'GET')){
            $error_response = ['data_error'=>$ci->lang->line("Missing required params to call the API.")];
            return $json ? json_encode($error_response) : $error_response;
        }

        if(!isset($option_data["CURLOPT_RETURNTRANSFER"])) $option_data["CURLOPT_RETURNTRANSFER"] = true;
        if(!isset($option_data["CURLOPT_FOLLOWLOCATION"])) $option_data["CURLOPT_FOLLOWLOCATION"] = true;
        if(!isset($option_data["CURLOPT_SSL_VERIFYPEER"])) $option_data["CURLOPT_SSL_VERIFYPEER"] = false;
        if(!isset($option_data["CURLOPT_SSL_VERIFYHOST"])) $option_data["CURLOPT_SSL_VERIFYHOST"] = false;

        $response = [];
        try{
            if(strtoupper($method) === 'GET' && !empty($body_data) && is_array($body_data)) {
                $endpoint_url .= '?' . http_build_query($body_data);
            }

            $ch = curl_init();
            $option_data["CURLOPT_URL"] = $endpoint_url;
            $option_data["CURLOPT_CUSTOMREQUEST"] = strtoupper($method);

            if (!empty($body_data) && strtoupper($method) !== 'GET') {
                $is_array_body = is_array($body_data);
                if($body_data_type == 'form-data' && $is_array_body){
                    $option_data["CURLOPT_POSTFIELDS"] = $body_data;
                    $header_data['Content-Type'] = 'multipart/form-data';
                }
                else if($body_data_type == 'url-encode' && $is_array_body){
                    $body_string = http_build_query($body_data);
                    $option_data["CURLOPT_POSTFIELDS"] = $body_string;
                    $header_data['Content-Type'] = 'application/x-www-form-urlencoded';
                }
                else if($body_data_type == 'binary' && $is_array_body){
                    $binary_data = array_pop($body_data);
                    $binary_data = @file_get_contents($binary_data);
                    $option_data["CURLOPT_POSTFIELDS"] = $binary_data;
                    $header_data['Content-Type'] = 'application/octet-stream';
                }
                else { // this is default and json type
                    $body_string = $is_array_body ? json_encode($body_data) : $body_data;
                    $option_data["CURLOPT_POSTFIELDS"] = $body_string;
                    $header_data['Content-Type'] = 'application/json';
                }
            }

            if(!empty($header_data)){
                $headers = [];
                foreach ($header_data as $key => $value) {
                    $headers[] = "$key:$value";
                }
                $option_data["CURLOPT_HTTPHEADER"] = $headers;
            }

            if (!empty($cookie_data)) {
                $cookie_string = '';
                foreach ($cookie_data as $key => $value) {
                    $cookie_string .= "$key=$value; ";
                }
                $option_data["CURLOPT_COOKIE"] = rtrim($cookie_string, '; ');
            }


            if(!empty($option_data)){
                foreach($option_data as $key=>$value){
                    if(defined($key)) {
                        curl_setopt($ch, constant($key), $value);
                    }
                }
            }

            $response = curl_exec($ch);
            if(curl_errno($ch)) {
                $curl_error =  curl_error($ch);
                $response = curl_getinfo($ch);
                $response['data_error'] = $curl_error;
                $response = json_encode($response);
            }
            else{
                json_decode($response);
                $is_invalid_json = json_last_error() == JSON_ERROR_NONE ? false : true;
                if($is_invalid_json) {
                    $is_valid_xml = @simplexml_load_string($response,"SimpleXMLElement", LIBXML_NOCDATA);
                    try{
                        if($is_valid_xml) $response = convert_xml_to_json($response);
                        else $response = convert_html_to_json($response);
                    }
                    catch (\Throwable $e){
                        $response = json_encode(['data_error'=>$e->getMessage()]);
                    }
                }
            }
        }
        catch (\Throwable $e){
            $response = json_encode(['data_error'=>$e->getMessage()]);
        }
        finally {
            if (isset($ch)) {
                curl_close($ch);
            }
        }
        return $json ? $response : json_decode($response,TRUE);
    }
}



if ( ! function_exists('convert_xml_to_json')){
    function convert_xml_to_json($xml){
        $obj = new SimpleXMLElement($xml);
        return json_encode($obj);
    }
}

if ( ! function_exists('convert_html_to_json')){
    function convert_html_to_json($html) {
        $html = trim($html);
        if (empty($html)) {
            return json_encode([]);
        }
        if(!preg_match('/<[^<]+?>/', $html)){
            return json_encode([$html]);
        }

        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $data = [];
        function traverseNode($node) {
            $result = [];

            if ($node->nodeType === XML_ELEMENT_NODE) {
                $result['tag'] = $node->nodeName;
                $result['attributes'] = [];

                foreach ($node->attributes as $attr) {
                    $result['attributes'][$attr->nodeName] = $attr->nodeValue;
                }
            }

            if ($node->hasChildNodes()) {
                foreach ($node->childNodes as $child) {
                    $result['children'][] = traverseNode($child);
                }
            } else {
                if ($node->nodeType === XML_TEXT_NODE) {
                    $result['text'] = trim($node->nodeValue);
                }
            }
            return $result;
        }
        foreach ($dom->childNodes as $child) {
            $data[] = traverseNode($child);
        }
        return json_encode($data);
    }

}

if ( ! function_exists('channel_shortname_to_longname'))
{
    function channel_shortname_to_longname($shortname='',$meta_name=false){
        $shortname = strtolower($shortname);
        if($shortname=='fb') return $meta_name ? "Meta" : "Facebook";
        elseif($shortname=='ig') return $meta_name ? "Meta" : "Instagram";
        else return $shortname;
    }

}
if ( ! function_exists('channel_longname_to_shortname'))
{
    function channel_longname_to_shortname($longname=''){
        $longname = strtolower($longname);
        if($longname=='facebook') return "fb";
        elseif($longname=='instagram') return "ig";
        else return $longname;
    }
}

if ( ! function_exists('get_curl_constants')){
    function get_curl_constants(){
        $curl_constants = [
            // "CURLOPT_AUTOREFERER" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_BUFFERSIZE" => ["default" => 16384, "type" => "integer"],
            // "CURLOPT_CAINFO" => ["default" => "", "type" => "string"],
            // "CURLOPT_CAPATH" => ["default" => "", "type" => "string"],
            "CURLOPT_CONNECTTIMEOUT" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_CONNECTTIMEOUT_MS" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_COOKIE" => ["default" => "", "type" => "string"],
            // "CURLOPT_COOKIEFILE" => ["default" => "", "type" => "string"],
            // "CURLOPT_COOKIEJAR" => ["default" => "", "type" => "string"],
            // "CURLOPT_COOKIESESSION" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_CRLF" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_CUSTOMREQUEST" => ["default" => "", "type" => "string"],
            // "CURLOPT_DNS_CACHE_TIMEOUT" => ["default" => 120, "type" => "integer"],
            // "CURLOPT_DNS_USE_GLOBAL_CACHE" => ["default" => 1, "type" => "boolean"],
            // "CURLOPT_EGDSOCKET" => ["default" => "", "type" => "string"],
            "CURLOPT_ENCODING" => ["default" => "", "type" => "string"],
            // "CURLOPT_EXPECT_100_TIMEOUT_MS" => ["default" => 1000, "type" => "integer"],
            "CURLOPT_FAILONERROR" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_FILE" => ["default" => "", "type" => "resource"],
            // "CURLOPT_FILETIME" => ["default" => 0, "type" => "boolean"],
            "CURLOPT_FOLLOWLOCATION" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_FORBID_REUSE" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_FRESH_CONNECT" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_FTP_CREATE_MISSING_DIRS" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_FTP_USE_EPRT" => ["default" => 1, "type" => "boolean"],
            // "CURLOPT_FTP_USE_EPSV" => ["default" => 1, "type" => "boolean"],
            // "CURLOPT_HEADER" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_HEADERFUNCTION" => ["default" => "", "type" => "callable"],
            // "CURLOPT_HTTP200ALIASES" => ["default" => [], "type" => "array"],
            "CURLOPT_HTTPAUTH" => ["default" => CURLAUTH_BASIC, "type" => "integer"],
            // "CURLOPT_HTTPGET" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_HTTPHEADER" => ["default" => [], "type" => "array"],
            "CURLOPT_HTTPPROXYTUNNEL" => ["default" => 0, "type" => "boolean"],
            "CURLOPT_HTTP_VERSION" => ["default" => CURL_HTTP_VERSION_NONE, "type" => "integer"],
            // "CURLOPT_INFILE" => ["default" => "", "type" => "resource"],
            // "CURLOPT_INFILESIZE" => ["default" => -1, "type" => "integer"],
            // "CURLOPT_INTERFACE" => ["default" => "", "type" => "string"],
            // "CURLOPT_IPRESOLVE" => ["default" => CURL_IPRESOLVE_WHATEVER, "type" => "integer"],
            // "CURLOPT_KEYPASSWD" => ["default" => "", "type" => "string"],
            // "CURLOPT_KRB4LEVEL" => ["default" => "", "type" => "string"], // Deprecated as of PHP 5.3.0
            "CURLOPT_LOGIN_OPTIONS" => ["default" => "", "type" => "string"],
            // "CURLOPT_LOW_SPEED_LIMIT" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_LOW_SPEED_TIME" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_MAXCONNECTS" => ["default" => 5, "type" => "integer"],
            // "CURLOPT_MAXFILESIZE" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_MAXREDIRS" => ["default" => 20, "type" => "integer"],
            // "CURLOPT_MAX_RECV_SPEED_LARGE" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_MAX_SEND_SPEED_LARGE" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_NETRC" => ["default" => CURL_NETRC_IGNORED, "type" => "integer"],
            // "CURLOPT_NOBODY" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_NOPROGRESS" => ["default" => 1, "type" => "boolean"],
            // "CURLOPT_NOSIGNAL" => ["default" => 0, "type" => "boolean"],
            "CURLOPT_PASSWORD" => ["default" => "", "type" => "string"],
            // "CURLOPT_PATH_AS_IS" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_PIPEWAIT" => ["default" => 0, "type" => "boolean"],
            "CURLOPT_PORT" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_POST" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_POSTFIELDS" => ["default" => "", "type" => "string|array"],
            // "CURLOPT_POSTREDIR" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_PRE_PROXY" => ["default" => "", "type" => "string"],
            // "CURLOPT_PRIVATE" => ["default" => "", "type" => "string"],
            // "CURLOPT_PROGRESSFUNCTION" => ["default" => "", "type" => "callable"],
            // "CURLOPT_PROTOCOLS" => ["default" => CURLPROTO_ALL, "type" => "integer"],
            "CURLOPT_PROXY" => ["default" => "", "type" => "string"],
            // "CURLOPT_PROXYAUTH" => ["default" => CURLAUTH_BASIC, "type" => "integer"],
            // "CURLOPT_PROXYHEADER" => ["default" => [], "type" => "array"],
            "CURLOPT_PROXYPORT" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_PROXYTYPE" => ["default" => CURLPROXY_HTTP, "type" => "integer"],
            "CURLOPT_PROXYUSERPWD" => ["default" => "", "type" => "string"],
            // "CURLOPT_PROXY_CAINFO" => ["default" => "", "type" => "string"],
            // "CURLOPT_PROXY_CAPATH" => ["default" => "", "type" => "string"],
            // "CURLOPT_PROXY_CRLFILE" => ["default" => "", "type" => "string"],
            // "CURLOPT_PROXY_TLSAUTH_PASSWORD" => ["default" => "", "type" => "string"],
            // "CURLOPT_PROXY_TLSAUTH_TYPE" => ["default" => "SRP", "type" => "string"],
            // "CURLOPT_QUOTE" => ["default" => [], "type" => "array"],
            // "CURLOPT_RANGE" => ["default" => "", "type" => "string"],
            // "CURLOPT_READFUNCTION" => ["default" => "", "type" => "callable"],
            "CURLOPT_REFERER" => ["default" => "", "type" => "string"],
            // "CURLOPT_REDIR_PROTOCOLS" => ["default" => CURLPROTO_ALL, "type" => "integer"],
            // "CURLOPT_RESUME_FROM" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_RETURNTRANSFER" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_SASL_IR" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_SSL_CIPHER_LIST" => ["default" => "", "type" => "string"],
            // "CURLOPT_SSL_CTX_FUNCTION" => ["default" => "", "type" => "callable"],
            // "CURLOPT_SSLCERT" => ["default" => "", "type" => "string"],
            // "CURLOPT_SSLCERTPASSWD" => ["default" => "", "type" => "string"],
            // "CURLOPT_SSLCERTTYPE" => ["default" => "", "type" => "string"],
            // "CURLOPT_SSLENGINE" => ["default" => "", "type" => "string"],
            // "CURLOPT_SSLENGINE_DEFAULT" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_SSLKEY" => ["default" => "", "type" => "string"],
            // "CURLOPT_SSLKEYPASSWD" => ["default" => "", "type" => "string"],
            // "CURLOPT_SSLKEYTYPE" => ["default" => "", "type" => "string"],
            // "CURLOPT_SSLVERSION" => ["default" => 0, "type" => "integer"],
            "CURLOPT_SSL_VERIFYHOST" => ["default" => 0, "type" => "integer"],
            "CURLOPT_SSL_VERIFYPEER" => ["default" => 0, "type" => "boolean"],
            "CURLOPT_SSL_VERIFYSTATUS" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_STDERR" => ["default" => "", "type" => "resource"],
            // "CURLOPT_TCP_FASTOPEN" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_TCP_NODELAY" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_TELNETOPTIONS" => ["default" => [], "type" => "array"],
            // "CURLOPT_TIMECONDITION" => ["default" => CURL_TIMECOND_NONE, "type" => "integer"],
            "CURLOPT_TIMEOUT" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_TIMEOUT_MS" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_TIMEVALUE" => ["default" => 0, "type" => "integer"],
            // "CURLOPT_TRANSFERTEXT" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_UNRESTRICTED_AUTH" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_UPLOAD" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_URL" => ["default" => "", "type" => "string"],
            "CURLOPT_USERAGENT" => ["default" => "", "type" => "string"],
            "CURLOPT_USERNAME" => ["default" => "", "type" => "string"],
            "CURLOPT_PASSWORD" => ["default" => "", "type" => "string"],
            "http_api" => ["default" => "", "type" => "string"],
            // "CURLOPT_VERBOSE" => ["default" => 0, "type" => "boolean"],
            // "CURLOPT_WRITEDATA" => ["default" => "", "type" => "resource"],
            // "CURLOPT_WRITEFUNCTION" => ["default" => "", "type" => "callable"],
            "CURLOPT_XOAUTH2_BEARER" => ["default" => "", "type" => "string"]
        ];
        return $curl_constants;
    }
}

//https://fellowtuts.com/php/multidimensional-array-object-conversion/#:~:text=A%20combination%20of%20PHP%27s%20JSON,can%20magically%20do%20the%20conversion.&text=%24obj%20%3D%20json_decode(json_encode(,string%20to%20a%20stdClass%20object.
if( ! function_exists('array_to_obj')){
    function array_to_obj($arr) {
        if(is_json($arr)) $arr = json_decode($arr,true);
        if (is_array($arr)){
            $new_arr = array();
            foreach($arr as $k => $v) {
                //if (is_integer($k)) {
                    // Needs this if you have indexed keys at the top level in the array
                    // and want to utilize the indexes: eg. $o->index{1}
                    //$new_arr[$k] = array_to_obj($v);
                //}
                //else
                $new_arr[$k] = array_to_obj($v);

            }
            return (object) $new_arr;
        }
        // else maintain the type of $arr
        return $arr;
    }
}

if ( ! function_exists('array_depth')){
    function array_depth(array $array) {
        $max_depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = array_depth($value) + 1;

                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }
        return $max_depth;
    }
}

if ( ! function_exists('array_flatten')){
    function array_flatten($array=[],$keep_array_index=false) {
        if(is_json($array)) $array = json_decode($array,true);
        if(!is_array($array)) return [];
        if(empty($array)) return [];


        $result = array();
        foreach ($array as $k1 => $v1) {
            if (is_array($v1)) {
                foreach ($v1 as $k2=>$v2){
                    if (is_array($v2)) {
                        foreach ($v2 as $k3=>$v3){
                            if (is_array($v3)) {
                                foreach ($v3 as $k4=>$v4){
                                    if (is_array($v4)) {
                                        foreach ($v4 as $k5=>$v5){
                                            if (is_array($v5)) {
                                                foreach ($v5 as $k6=>$v6){
                                                    if (is_array($v6)) {
                                                        foreach ($v6 as $k7=>$v7){
                                                            if (is_array($v7)) {
                                                                foreach ($v7 as $k8=>$v8){
                                                                    $result[$k1.'>'.$k2.'>'.$k3.'>'.$k4.'>'.$k5.'>'.$k6.'>'.$k7.'>'.$k8] = $v8;
                                                                }
                                                            }
                                                            if($keep_array_index) $result[$k1.'>'.$k2.'>'.$k3.'>'.$k4.'>'.$k5.'>'.$k6.'>'.$k7] = $v7;
                                                        }
                                                    }
                                                    if($keep_array_index) $result[$k1.'>'.$k2.'>'.$k3.'>'.$k4.'>'.$k5.'>'.$k6] = $v6;
                                                }
                                            }
                                            if($keep_array_index) $result[$k1.'>'.$k2.'>'.$k3.'>'.$k4.'>'.$k5] = $v5;
                                        }
                                    }
                                    if($keep_array_index) $result[$k1.'>'.$k2.'>'.$k3.'>'.$k4] = $v4;
                                }
                            }
                            if($keep_array_index) $result[$k1.'>'.$k2.'>'.$k3] = $v3;
                        }
                    }
                    if($keep_array_index) $result[$k1.'>'.$k2] = $v2;
                }
            }
            $result[$k1] = $v1;
        }
        ksort($result);
        return $result;
    }
}

if(! function_exists('is_json')){
    function is_json($string=null){
        return !empty($string) && is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}

if ( ! function_exists('workflow_formatter')) {
    function workflow_formatter($input='',$formatter=[],$variables=[])
    {
        if(is_null($input)) $input = '';
        if(empty($formatter)) return $input;
        $input_formatted = $input;
        foreach ($formatter as $key=>$value){
            $type = $value['formatter_type'] ?? null;
            $params = isset($value['params']) ? json_decode($value['params'],true) : [];
            if($type=="concat-list-items" && is_object($input_formatted)) {
                $glue = $params[1] ?? '';
                if($glue=='[space]') $glue = ' ';
                $position = $params[2] ?? 0;
                $list_values = [];
                foreach ($input_formatted as $k=>$v){
                    $list_values[] = $v->$position ?? '';
                }
                $input_formatted = implode($glue,$list_values);
            }
            else if($type=="default-value" && !is_object($input_formatted)) {
                $default = $params[1] ?? '';
                if($default=='[space]') $default = ' ';
                $input_formatted = !empty($input_formatted) ? $input_formatted : $default;
            }
            else if($type=="static-value" && !is_object($input_formatted)) {
                $static = $params[1] ?? '';
                if($static=='[space]') $static = ' ';
                $input_formatted = $static;
            }
            else if(($type=="concat-left" || $type=="concat-right") && !is_object($input_formatted)) {
                $glue = $params[1] ?? '';
                if($glue=='[space]') $glue = ' ';
                $concat = $params[2] ?? '';
                if($concat=='[space]') $concat = ' ';
                if(str_starts_with($concat,'[') && str_ends_with($concat,']')) $concat = $variables[$concat] ?? $concat;
                $input_formatted = $type=='concat-left' ? $concat.$glue.$input_formatted : $input_formatted.$glue.$concat;
            }
            else if(($type=="trim-left" || $type=="trim-right") && !is_object($input_formatted)) {
                $trim = $params[1] ?? '';
                if($trim=='[space]') $trim = ' ';
                $input_formatted = $type=='trim-left' ? ltrim($input_formatted,$trim) : rtrim($input_formatted,$trim);
            }
            else if($type=="split" && !is_object($input_formatted)) {
                $separator = $params[1] ?? '';
                if($separator=='[space]') $separator = ' ';
                $position = (int) $params[2] ?? 0;
                $exp  = !empty($separator) && !empty($input_formatted) ? explode($separator,$input_formatted) : [];
                $input_formatted = $exp[$position] ?? $input_formatted;
            }
            else if($type=="replace" && !is_object($input_formatted)) {
                $search = $params[1] ?? '';
                if($search=='[space]') $search = ' ';
                $replace = $params[2] ?? '';
                $input_formatted = str_replace($search,$replace,$input_formatted);
            }
            else if($type=="shorten" && !is_object($input_formatted)) {
                $limit = $params[1] ?? 0;
                $input_formatted = strlen($input_formatted)>$limit ? substr($input_formatted, 0, $limit).'...' : $input_formatted;
            }
            else if($type=="format-number" && !is_object($input_formatted)) {
                $decimal = $params[1] ?? 2;
                $decimal_separator = $params[1] ?? '';
                if($decimal_separator=='') $decimal_separator = '.';
                if($decimal_separator=='[space]') $decimal_separator = ' ';
                $thousand_separator = $params[3] ?? '';
                if($thousand_separator=='[space]') $thousand_separator = ' ';
                $input_formatted = number_format($input_formatted,$decimal,$decimal_separator,$thousand_separator);
            }
            else if($type=="day-add" && !is_object($input_formatted)) {
                $day = $params[1] ?? 0;
                $input_formatted = date('jS M y H:i', strtotime($input_formatted. ' + '.$day.' days'));
            }
            else if($type=="day-subtract" && !is_object($input_formatted)) {
                $day = $params[1] ?? 0;
                $input_formatted = date('jS M y H:i', strtotime($input_formatted. ' - '.$day.' days'));
            }
            else if($type=="number-add" && !is_object($input_formatted)) {
                $number = $params[1] ?? 0;
                $input_formatted = is_numeric($input_formatted) ? $input_formatted+$number : $input_formatted;
            }
            else if($type=="number-subtract" && !is_object($input_formatted)) {
                $number = $params[1] ?? 0;
                $input_formatted = is_numeric($input_formatted) ? $input_formatted-$number : $input_formatted;
            }
            else if($type=="number-multiply" && !is_object($input_formatted)) {
                $number = $params[1] ?? 0;
                $input_formatted = is_numeric($input_formatted) ? $input_formatted*$number : $input_formatted;
            }
        }
        return $input_formatted;
    }
}
