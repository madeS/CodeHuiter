<?php

namespace CodeHuiter\Modifier;

class RequestUriParser
{
    public static function decode(string $requestUri): array
    {
        $result = ['path' => '','params' => []];
        $spliter = explode('?', $requestUri);
        $result['path'] = $spliter[0];
        if (count($spliter) > 1){
            $params = explode('&',$spliter[1]);
            foreach($params as $par){
                $parKV = explode('=', $par);
                if ($parKV[0] === 'bodyAjax') continue;
                $result['params'][$parKV[0]] = (isset($parKV[1])) ? urldecode($parKV[1]) : '';
            }
        }
        return $result;
    }

    public static function encode(array $requestUri): string
    {
        $uri = $requestUri['path'];
        if (count($requestUri['params'])>0){
            $uri_params = '';
            foreach($requestUri['params'] as $parK => $parV){
                if ($uri_params) $uri_params .= '&';
                else $uri_params .= '?';
                $uri_params .= $parK.'='.urlencode($parV);
            }
            $uri .= $uri_params;
        }
        return $uri;
    }

    public static function copy(array $requestUri): array
    {
        $result['path'] = $requestUri['path'];
        foreach($requestUri['params'] as $parK => $parV){
            $result['params'][$parK] = $parV;
        }
        return $result;
    }
}
