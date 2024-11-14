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
 * @version v0.1
 * @author Axel Hahn
 * @link https://github.com/iml-it/appmonitor-api-client
 * @license GPL
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * --------------------------------------------------------------------------------<br>
 * 2024-11-14  0.1  axel.hahn@unibe.ch  first lines
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

Get Date from API by given list of tags

**Return**: `bool`

**Parameters**: **2**

| Parameter | Type | Description
|--         |--    |--
| \<optional\> array $aTags = [] | `array` | 
| \<optional\> bool $bFull = false | `bool` | 


### 🔹 public fetchData()

Fetch all urls to get upto date monitoring data. It first checksif cache files for the urls are outdated (older than 10 seconds).If so, they are fetched using the _multipleHttpGet method. Ifnot, their cache files are read instead.
Then, all received data is looped over to extract metadata perapplication, which is stored in the internal _aData array.It returns true if all data for all apps were fetched.


**Return**: `bool`

**Parameters**: **1**

| Parameter | Type | Description
|--         |--    |--
| \<required\> array $aRelUrls | `array` | array of relative urls to fetch


### 🔹 public getAppData()

Get an array of app metadata of a single app.You need to get the list of all applications first.@see getApps()

**Return**: `array`

**Parameters**: **1**

| Parameter | Type | Description
|--         |--    |--
| \<required\> string $sApp | `string` | 


### 🔹 public getApps()

Get a list of all app keys in the result setUse this to loop over all fetched apps.@see getAppData(<ID>)

**Return**: `array`

**Parameters**: **0**


### 🔹 public getErrors()

Get array of all errors while fetching the data.Print its output only in development environment only.

**Return**: `array`

**Parameters**: **0**


### 🔹 public setConfig()

Set configuration

**Return**: `void`

**Parameters**: **1**

| Parameter | Type | Description
|--         |--    |--
| \<required\> array $aConfig | `array` | configuration array with subkeys                        - apiurl                        - user                        - secret   (for hmac hash)                        - password (for basic auth)




---
Generated with Axels PHP class doc parser.