## 📦 Class \appmonitorapi

```txt
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
 * @version v0.4
 * @author Axel Hahn
 * @link https://github.com/iml-it/appmonitor-api-client
 * @license GPL
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * --------------------------------------------------------------------------------<br>
 * 2024-11-14  0.1  axel.hahn@unibe.ch  first lines
 * 2024-11-15  0.2  axel.hahn@unibe.ch  update hmac authorization header; add verifications in setConfig(); configure ttl and cachedir
 * 2024-11-20  0.3  axel.hahn@unibe.ch  handle full data or metadate only; add 3 functions to get parts of the app result
 * 2024-11-20  0.4  axel.hahn@unibe.ch  add getAllApps, getAllTags, getGroupResult
 */
```

## 🔶 Properties

(none)

## 🔷 Methods

### 🔹 public __construct()

Constructor

**Return**: ``

**Parameters**: **1**

| Parameter | Type | Description
|--         |--    |--
| \<optional\> array $aConfig = [] | `array` | configuration array with subkeys                        - apiurl                        - user                        - secret


### 🔹 public fetchByTags()

Get application data of all matching apps by given list of tags@see getErrors()

**Return**: `bool`

**Parameters**: **2**

| Parameter | Type | Description
|--         |--    |--
| \<optional\> array $aTags = [] | `array` | 
| \<optional\> bool $bFull = false | `bool` | 


### 🔹 public fetchData()

Fetch all urls to get upto date monitoring data. It first checksif cache files for the urls are outdated (older than 10 seconds).If so, they are fetched using the _multipleHttpGet method. Ifnot, their cache files are read instead.
Then, all received data is looped over to extract metadata perapplication, which is stored in the internal _aData array.It returns true if all data for all apps were fetched.


**Return**: `array`

**Parameters**: **1**

| Parameter | Type | Description
|--         |--    |--
| \<required\> array $aRelUrls | `array` | array of relative urls to fetch


### 🔹 public getAllApps()

Get an array of all app ids as array. Each array value has - a key - the pplication id and - the subkeys "website" and "url".It returns false if the request faailed. You can use getErrors() to seefull data of the response@see getErrors()

**Return**: `array|bool`

**Parameters**: **0**


### 🔹 public getAllTags()

Get a flat list of tags as array.It returns false if the request faailed. You can use getErrors() to seefull data of the response@see getErrors()

**Return**: `array|bool`

**Parameters**: **0**


### 🔹 public getAppChecks()

Get an array of checks and their results by a given app id.Get an array of app meta data by a given app id.You need to get the list of all applications first to know the ID.@see getApps()

**Return**: `array`

**Parameters**: **1**

| Parameter | Type | Description
|--         |--    |--
| \<required\> string $sApp | `string` | App ID


### 🔹 public getAppData()

Get an array of all fetched app data by a given app id.You need to get the list of all applications first to know the ID.@see getApps()

**Return**: `array`

**Parameters**: **1**

| Parameter | Type | Description
|--         |--    |--
| \<required\> string $sApp | `string` | App ID


### 🔹 public getAppMeta()

Get an array of app meta data by a given app id.You need to get the list of all applications first to know the ID.@see getApps()

**Return**: `array`

**Parameters**: **1**

| Parameter | Type | Description
|--         |--    |--
| \<required\> string $sApp | `string` | App ID


### 🔹 public getAppResult()

Get an array of result meta infos by a given app id.This information is available with full fetches only.You need to get the list of all applications first to know the ID.@see getApps()

**Return**: `array`

**Parameters**: **1**

| Parameter | Type | Description
|--         |--    |--
| \<required\> string $sApp | `string` | App ID


### 🔹 public getApps()

Get a list of all app keys in the result setUse this to loop over all fetched apps.@see getAppData(<ID>)

**Return**: `array`

**Parameters**: **0**


### 🔹 public getErrors()

Get array of all errors of the last requestWarning: Print its output only in development environment only.

**Return**: `array`

**Parameters**: **0**


### 🔹 public getGroupResult()

Get the worst app result in the group

**Return**: `int`

**Parameters**: **0**


### 🔹 public setConfig()

Set configuration.Given values will be verified. It throws an exception if something is wrong.

**Return**: `void`

**Parameters**: **1**

| Parameter | Type | Description
|--         |--    |--
| \<required\> array $aConfig | `array` | configuration array with subkeys                        - apiurl    string  url of appmonitor api, eg http://localhost/api/v1                        - user      string  username for basic auth or hmac hash                        - secret    string  (for hmac hash)                        - password  string  (for basic auth)                        - ttl       int     time to live in seconds (0 = no caching; max. 60)                        - cachedir  string  path to cache dir




---
Generated with Axels PHP class doc parser.