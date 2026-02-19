# PHP: Appmonitor api class

You can use this class in your custom output page for monitoring.

Documentation for the class: [appmonitorapi.class.php.md](appmonitorapi.class.php.md)

## Usage

Here you find short snippets you can start with.

In the `php-example` directory is a simplified health monitor for customers with a reduced the detail level - without technical details from your environment.

### Fetch data

```php

require '../php-class/appmonitorapi.class.php';

/**
 * Return codes of the IML appmonitor and their meaning
 * @var array
 */
$aReturncodes=[
    0 => 'OK',
    1 => 'Unknown',
    2 => 'Warning',
    3 => 'Error',
];

# ---------- MAIN

// Init
$api = new appmonitorapi([
    'apiurl'=>'http://appmonitpr.example.com/api/v1',
    'user'=>'api-test',
    'secret'=>'tryme',
    // 'ttl' => 20,
    // 'cachedir' => '/some/where',
]);

// Get status for all apps matching the AND combination of given tags
$aData=$api->fetchByTags('myapp,live', false);

// check errors
if ( count($api->getErrors()) > 0 ) {
    echo "Found errors:". PHP_EOL;
    print_r($api->getErrors());
}

```

### Total status of all apps

```php

echo "Status total: " . $aReturncodes[$api->getGroupResult()]. PHP_EOL;
```

### Loop over each app

To show a very simple overview of each app of this group you need the application ids can use the methods

| Method                        | Type     | Description
|--                             |--        |--
| `getAppLabel(<appid>)`        | {string} | Get name of the application
| `getAppResultHard(<appid>)`   | {int}    | Get hard status (0 = OK ... 3 = Critical)

Each app is wrapped in a div with the class `"result-$iResult"` - by defining css classes .result-0 ... .result-3 you can colorize the output by status.

```php
$sOutGroup = '';
foreach ($api->getApps() as $sAppId) {

    $iResult = $api->getAppResultHard((string) $sAppId);
    $sAppname = $api->getAppLabel((string) $sAppId);

    $sOutGroup.= "<div class=\"app result-$iResult\">
            <span class=\"resultlabel\">$aReturncodes[$iResult]</span>
            <span class=\"appname\">$sAppname</span>
        </div>\n\n";
}
echo $sOutGroup ?: "No app was found.";
```

Other methods to generate a more advanced view

| Method                        | Type     | Description
|--                             |--        |--
| `getAppResultSince(<appid>)`  | {int}    | Unix timestamp since when the application is in this state
| `getAppHost(<appid>)`         | {string} | Get hostname of the app
| `getAppResultSoft(<appid>)`   | {int}    | Get soft status (of last request) 0..3
| `getAppLastResponses(<appid>)`| {array}  | Get an array of last responses with<br>- {int} timestamp<br>- {int} result code for state of the app at that moment<br>- {int} response time in [ms]
