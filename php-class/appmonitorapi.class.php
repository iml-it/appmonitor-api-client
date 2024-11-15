<?php
/**
 * ____________________________________________________________________________
 * 
 *  _____ _____ __                   _____         _ _           
 * |     |     |  |      ___ ___ ___|     |___ ___|_| |_ ___ ___ 
 * |-   -| | | |  |__   | .'| . | . | | | | . |   | |  _| . |  _|
 * |_____|_|_|_|_____|  |__,|  _|  _|_|_|_|___|_|_|_|_| |___|_|  
 *                          |_| |_|                              
 *                    _                            _                       
 *            ___ ___|_|   ___ ___ ___ _ _ ___ ___| |_                     
 *           | .'| . | |  |  _| -_| . | | | -_|_ -|  _|                    
 *           |__,|  _|_|  |_| |___|_  |___|___|___|_|                      
 *               |_|                |_|                                                                     
 * ____________________________________________________________________________
 * 
 * APPMONITOR API CLIENT<br>
 * <br>
 * THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE <br>
 * LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR <br>
 * OTHER PARTIES PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, <br>
 * EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED <br>
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE <br>
 * ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. <br>
 * SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY <br>
 * SERVICING, REPAIR OR CORRECTION.<br>
 * <br>
 * --------------------------------------------------------------------------------<br>
 * @version v0.1
 * @author Axel Hahn
 * @link https://github.com/iml-it/appmonitor-api-client
 * @license GPL
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * --------------------------------------------------------------------------------<br>
 * 2024-11-14  0.1  axel.hahn@unibe.ch  first lines
 * 2024-11-15  0.2  axel.hahn@unibe.ch  update hmac authorization header; add verifications in setConfig(); configure ttl and cachedir
 */
class appmonitorapi
{

    /**
     * Array of curl default option for http requests
     * @var array
     */
    protected array $curl_opts = [
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FAILONERROR => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_USERAGENT => 'Appmonitor api client 0.2 (see https://github.com/iml-it/appmonitor-api-client/)',
        // CURLMOPT_MAXCONNECTS => 10
    ];

    /**
     * Configuration
     * @var array
     */
    protected array $aConfig = [];

    /**
     * Extracted application meta data
     * @var array
     */
    protected array $_aData = [];

    /**
     * Failed requests
     * @var array
     */
    protected array $_aErrors = [];

    /**
     * Caching time for http requests
     * @var int
     */
    protected int $iTTL = 15; // default TTL in seconds to prevent DOS on appmonitor

    /**
     * Cache dir to store api response
     * @var string
     */
    protected string $_sCachedir ='';

    // ----------------------------------------------------------------------
    // Init
    // ----------------------------------------------------------------------

    /**
     * Constructor
     * @param array $aConfig  configuration array with subkeys
     *                         - apiurl
     *                         - user
     *                         - secret
     */
    public function __construct(array $aConfig = [])
    {
        $this->_sCachedir = sys_get_temp_dir();
        if (count($aConfig)) {
            $this->setConfig($aConfig);
        }
    }

    /**
     * Set configuration.
     * Given values will be verified. It throws an exception if something is wrong.
     * 
     * @param array $aConfig  configuration array with subkeys
     *                         - apiurl    string  url of appmonitor api, eg http://localhost/api/v1
     *                         - user      string  username for basic auth or hmac hash
     *                         - secret    string  (for hmac hash)
     *                         - password  string  (for basic auth)
     *                         - ttl       int     time to live in seconds (0 = no caching; max. 60)
     *                         - cachedir  string  path to cache dir
     * @return void
     */
    public function setConfig(array $aConfig): void
    {
        $this->aConfig = [];

        // ----- apply known values

        foreach(['apiurl', 'user', 'secret', 'password'] as $sKey){
            if (isset($aConfig[$sKey])) {
                $this->aConfig[$sKey] = $aConfig[$sKey];
                unset($aConfig[$sKey]);
            }
        }

        if(isset($aConfig['ttl'])){
            $this->iTTL = (int)$aConfig['ttl'];
            unset($aConfig['ttl']);
        }
        if(isset($aConfig['cachedir'])){
            $this->_sCachedir = $aConfig['cachedir'];
            unset($aConfig['cachedir']);
        }

        // ----- verifications

        if(count($aConfig)) {
            echo "ERROR in ".__METHOD__."(array)<br>Unknown configuration keys: '" . implode("', '", array_keys($aConfig))."'<br>";
            throw new Exception('Unknown configuration key(s).');
        }
        if(!isset($this->aConfig['apiurl'])){
            echo "ERROR in ".__METHOD__."(array)<br>Missing configuration key: 'apiurl'<br>";
            throw new Exception("Missing configuration key: 'apiurl'");
        }

        if($this->iTTL < 0 || $this->iTTL > 60 ){
            echo "ERROR in ".__METHOD__."(array)<br>'ttl' must be a value between 0 and 60 (seconds).<br>";
            throw new Exception('Wrong configuration value.');
        }
        if(!is_dir($this->_sCachedir)){
            echo "ERROR in ".__METHOD__."(array)<br>given directory in 'cachedir' does not exist: '$this->_sCachedir'<br>";
            throw new Exception('Wrong configuration value.');
        }

    }

    // ----------------------------------------------------------------------
    // fetch data
    // ----------------------------------------------------------------------

    /**
     * Get a filename for a cache file for a given url
     * @param string $sUrl  url for api request
     * @return string
     */
    protected function _getCachefile(string $sUrl): string
    {
        return $this->_sCachedir . '/' . md5($sUrl) . '.data';
    }

    /**
     * Get age of cache file for a given url in seconds.
     * If file does not exist, return TTL*2 seconds
     * 
     * @param string  $sUrl  url to lookup its cache file
     * @return int
     */
    protected function _getCacheAge(string $sUrl): int
    {
        $sCachefile = $this->_getCachefile($sUrl);
        if (file_exists($sCachefile)) {
            return time() - filemtime($sCachefile);
        } else {
            return $this->iTTL * 2 + 1;
        }
    }

    /**
     * Helper function for multi_curl_exec
     * hint from kempo19b
     * http://php.net/manual/en/function.curl-multi-select.php
     * 
     * @param CurlMultiHandle  $mh             multicurl master handle
     * @param boolean          $still_running  
     * @return int
     */
    protected function full_curl_multi_exec(CurlMultiHandle $mh, bool|null &$still_running): int
    {
        do {
            $rv = curl_multi_exec($mh, $still_running);
        } while ($rv == CURLM_CALL_MULTI_PERFORM);
        return $rv;
    }

    /**
     * Ececute multiple http requests in parallel and return an array
     * with url as key and its result infos in subkeys
     *   - 'url'              {string} url
     *   - 'response_header'  {string} http response header
     *   - 'response_body'    {string} http response body
     *   - 'curlinfo'         {array}  curl request infos
     *   - 'curlerrorcode'    {int}    curl error code
     *   - 'curlerrormsg'     {string} curl error message
     *
     * @param array $aUrls  array of urls to fetch
     * @return array
     */
    protected function _multipleHttpGet(array $aUrls): array
    {
        $aResult = [];

        // prepare curl object
        $master = curl_multi_init();

        // use authentication
        if (
            isset($this->aConfig['user']) && $this->aConfig['user']
            && isset($this->aConfig['apipassword']) && $this->aConfig['apipassword']
        ) {
            $this->curl_opts[CURLOPT_USERPWD] = $this->aConfig['user'] . ':' . $this->aConfig['apipassword'];
        }

        // requires php>=5.5:
        if (function_exists('curl_multi_setopt')) {
            // force parallel requests
            curl_multi_setopt($master, CURLMOPT_PIPELINING, 0);
            // curl_multi_setopt($master, CURLMOPT_MAXCONNECTS, 50);
        }

        $curl_arr = [];
        foreach ($aUrls as $sKey => $sUrl) {

            $aBakHeader = $this->curl_opts[CURLOPT_HTTPHEADER] ?? [];

            // add HMAC auth if secret was given
            if (
                isset($this->aConfig['user']) && $this->aConfig['user']
                && isset($this->aConfig['secret']) && $this->aConfig['secret']
            ) {

                // Date: Thu, 14 Nov 2024 11:12:07 +0000
                $apiTS = date("r");
                $sMethod = "GET";
                $sQuery = parse_url($sUrl, PHP_URL_QUERY);
                $sRequest = parse_url($sUrl, PHP_URL_PATH) . ($sQuery ? "?$sQuery" : '');

                $sMyHash = base64_encode(hash_hmac(
                    "sha1",
                    "{$sMethod}\n{$sRequest}\n{$apiTS}",
                    $this->aConfig['secret']
                ));

                $this->curl_opts[CURLOPT_HTTPHEADER][] = "Date: $apiTS";
                $this->curl_opts[CURLOPT_HTTPHEADER][] = "Authorization: HMAC-SHA1 " . $this->aConfig['user'] . ":$sMyHash";
            }

            $curl_arr[$sKey] = curl_init($sUrl);
            curl_setopt_array($curl_arr[$sKey], $this->curl_opts);
            curl_multi_add_handle($master, $curl_arr[$sKey]);

            $this->curl_opts[CURLOPT_HTTPHEADER] = $aBakHeader;
        }

        // make all requests
        self::full_curl_multi_exec($master, $running);
        do {
            curl_multi_select($master);
            self::full_curl_multi_exec($master, $running);
            while ($info = curl_multi_info_read($master)) {
            }
        } while ($running);
        // get results
        foreach ($aUrls as $sKey => $sUrl) {
            $sHeader = '';
            $sBody = '';
            $aResponse = explode("\r\n\r\n", curl_multi_getcontent($curl_arr[$sKey]), 2);
            list($sHeader, $sBody) = count($aResponse) > 1
                ? $aResponse
                : [$aResponse[0], ''];

            $aResult[$sKey] = [
                'url' => $sUrl,
                'response_header' => $sHeader,
                'response_body' => $sBody,
                // 'curlinfo' => curl_getinfo($curl_arr[$sKey]),
                'curlerrorcode' => curl_errno($curl_arr[$sKey]),
                'curlerrormsg' => curl_error($curl_arr[$sKey]),
            ];
            curl_multi_remove_handle($master, $curl_arr[$sKey]);

            // write cache file
            file_put_contents($this->_getCachefile($sUrl), serialize($aResult[$sKey]));
        }
        curl_multi_close($master);
        return $aResult;
    }

    /**
     * Get Date from API by given list of tags
     * 
     * @param array $aTags
     * @param bool $bFull
     * @return bool
     */
    public function fetchByTags(array $aTags=[], bool $bFull=false): bool
    {
        // we need minumum one tag
        if(!count($aTags)) {
            return false;
        }

        $sUrl='/apps/tags/'
            .implode(',', $aTags)
            .($bFull ? '/all' : '/meta')
            ;

        return $this->fetchData([$sUrl]);
   }

    /**
     * Fetch all urls to get upto date monitoring data. It first checks
     * if cache files for the urls are outdated (older than 10 seconds).
     * If so, they are fetched using the _multipleHttpGet method. If
     * not, their cache files are read instead.
     *
     * Then, all received data is looped over to extract metadata per
     * application, which is stored in the internal _aData array.
     * 
     * It returns true if all data for all apps were fetched.
     *
     * @param  array  $aRelUrls  array of relative urls to fetch
     * @return bool Success
     */
    public function fetchData(array $aRelUrls): bool
    {
        $this->_aData = [];
        $this->_aErrors = [];

        $aUrls = [];
        $aCached = [];

        foreach ($aRelUrls as $sUrl) {
            $sFullUrl = $this->aConfig['apiurl'] . $sUrl;
            if ($this->_getCacheAge($sFullUrl) > $this->iTTL) {
                $aUrls[] = $sFullUrl;
            } else {
                $aCached[] = $sFullUrl;
            }
        }

        $aData = [];
        // add data from cache
        foreach ($aCached as $sUrl) {
            $sCachefile = $this->_getCachefile($sUrl);
            if (file_exists($sCachefile)) {
                $myData = unserialize(file_get_contents($sCachefile));
                $aData[] = $myData;
            } else {
                echo "DEBUG: Cache file $sCachefile not found for url $sUrl<br>\n";
            }
        }

        // fetch outdated http data
        $aData = array_merge($aData, $this->_multipleHttpGet($aUrls));

        // loop over all results to extract metadata per application
        foreach ($aData as $aResult) {

            $sMonitorData = $aResult['response_body'] ?? '';
            if ($sMonitorData) {
                $bOK = false;
                $aMonitorData = json_decode($sMonitorData, 1);
                foreach ($aMonitorData as $sKey => $aAppResult) {
                    if (isset($aAppResult['website'])) {
                        $this->_aData[$aAppResult['website'] . '__' . $sKey] = $aAppResult;
                        $bOK = true;
                    }
                }
                if (!$bOK) {
                    $this->_aErrors[] = $aResult;
                }
            } else {
                $this->_aErrors[] = $aResult;
            }
        }

        // order by app name
        ksort($this->_aData);

        return !!count($this->_aErrors);
    }

    // ----------------------------------------------------------------------
    // return results
    // ----------------------------------------------------------------------

    /**
     * Get a list of all app keys in the result set
     * Use this to loop over all fetched apps.
     * 
     * @see getAppData(<ID>)
     * 
     * @return array
     */
    public function getApps(): array
    {
        return array_keys($this->_aData) ?? [];
    }

    /**
     * Get an array of app metadata of a single app.
     * You need to get the list of all applications first.
     * 
     * @see getApps()
     * 
     * @return array
     */
    public function getAppData(string $sApp): array
    {
        return $this->_aData[$sApp] ?? [];
    }

    /**
     * Get array of all errors while fetching the data.
     * Print its output only in development environment only.
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_aErrors;
    }
}