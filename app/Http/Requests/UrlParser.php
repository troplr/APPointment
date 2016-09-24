<?php

namespace App\Http\Requests;

class UrlParser
{
    /**
     * Get the host of the given url
     *
     * @param String $url
     * @param Boolean $getPort
     * @param Boolean $getUrlScheme
     * @param Boolean $getSubdomain
     * @return String
     */
    public static function getHost(String $url = null, Bool $getPort = false, Bool $getUrlScheme = false, Bool $getSubdomain = true)
    {
        $url = $url ?? url('');

        // Retrieve the host of the url
        // Remove 'www.' if it is set
        $host = str_replace('www.', '', parse_url($url, PHP_URL_HOST));

        if (! $getSubdomain && self::getSubdomain($host)) {
            $arr = explode('.', $host);

            // Set the new $host
            // Get only the last 2 elements from the array since that is the host
            $host = implode('.', array_slice($arr, -2, 2, true));
        }

        // Check if we need to get the port aswell
        if ($getPort) {
            // Append the port to $host
            $host .= ':' . parse_url($url, PHP_URL_PORT);
        }

        if ($getUrlScheme) {
            return parse_url($url, PHP_URL_SCHEME) . '://' . $host;
        }

        return $host;
    }

    /**
     * Check if we can find a subdomain in the given url
     * If we can, return it otherwise return false
     *
     * @param  String $url
     * @param  Bool $getPort
     * @return String|Boolean
     */
    public static function getSubdomain(String $url = null, Bool $getPort = false)
    {
        // Check if the url starts with http
        if ($url && substr($url, 0, 4) == 'http') {
            // Remove it
            $url = preg_replace('(^https?://)', '', $url);
        }

        $arr = explode('.', $url ?? self::getHost($url, $getPort));

        // Check if we we can find a subdomain
        if (count($arr) > 2) {
            // We can, let them pass
            return $arr[0];
        }

        return false;
    }

    /**
     * Set the domain, redirect to that url after we are done
     * At this moment we only support url's like this: subdomain.domain.com
     *
     * @param String $url
     * @return mixed
     */
    public static function setSubdomain(String $url = null)
    {
        $url = $url ?? url('');
        $host = self::getHost($url, env('APP_DEBUG', false));
        $scheme = parse_url($url, PHP_URL_SCHEME) . '://';

        // We don't have a subdomain, set it
        return redirect($scheme . get_company()->subdomain . '.' . $host);
    }
}
