# PHP: Appmonitor api class

You can use this class in your custom output page for monitoring.

Documentation for the class: [appmonitorapi.class.php.md](appmonitorapi.class.php.md)

## Usage

This is a short snippet you can start with.

See the `php-example` directory for a simplified health monitor for customers where you can reduce the detail level.

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

// Output
if ( count($api->getErrors()) > 0 ) {
    // show all errors
    echo "Found errors:". PHP_EOL;
    print_r($api->getErrors());
}

echo "Status total: " . $aReturncodes[$api->getGroupResult()]. PHP_EOL;

// see https://os-docs.iml.unibe.ch/appmonitor/Server/API.html
print_r($aData);
```
