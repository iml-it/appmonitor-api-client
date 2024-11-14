## Example bash script

This example to access the appmonitor url with a bash script.

To execute it you need

* a running appmonitor instance
* with min. 1 monitored application using minimum one tag

### Create an api user

On appmonitor server configure an user eg `api-test` with a secret of your choice and the role "api".

### Update config

Copy `api_config.sh.dist` to `api_config.sh` (without .dist) and edit connection details:

```txt
AM_APIURL="http://localhost:8001"
AM_APIUSER="api-test"
AM_APISECRET="tryme"
```

### CLI usage

#### Get help

Use the parameter `-h`to get a short help.

`./api.sh -h`

```txt
APPMONITOR API CLIENT :: Bash

SYNTAX
    api.sh [OPTIONS] [URL]

OPTIONS
    -h|--help      show this help and exit
    -d|--debug     show more output

EXAMPLES
    api.sh /api/v1/apps/tags 
                   list all tags

    api.sh /api/v1/apps/tags/monitoring/meta
                   Get metadata of all apps with tag 'monitoring'

    api.sh /api/v1/apps/tags/monitoring,live/meta
                   Get metadata of all apps with tags 'monitoring' and 'live'
```

#### Start

This is a good starting point for your exploration: `/api/v1/apps`.
If there are no application data you get a hint for the next possible subdir to add:

`./api.sh  /api/v1/apps`

```txt
{
    "allowed_subkeys": [
        "id",
        "tags"
    ]
}
```

#### Data for a single application

Let's list all known IDs first:

`./api.sh /api/v1/apps/id`

```txt
{
    "60b1104800798cd79b694ca6f6764c15": {
        "website": "Appmonitor server",
        "url": "http:\/\/localhost\/client\/check-appmonitor-server.php"
    }
}
```

The first level subkey is our application id.

`./api.sh  /api/v1/apps/id/60b1104800798cd79b694ca6f6764c15`

```txt
{
    "allowed_subkeys": [
        "all",
        "checks",
        "meta"
    ]
}
```

OK I need to add a keyword to define the amount of data.

`./api.sh  /api/v1/apps/id/60b1104800798cd79b694ca6f6764c15/meta`

```txt
{
    "60b1104800798cd79b694ca6f6764c15": {
        "host": "60875f13b371",
        "website": "Appmonitor server",
        "ttl": 300,
        "result": 0,
        "time": "3.321ms",
        "version": "php-client-v0.137",
        "tags": [
            "monitoring"
        ]
    }
}
```

#### Get all apps with a tag

First of all let's have look which tags currently exist:

`./api.sh /api/v1/apps/tags`

```txt
{
    "tags": [
        "live",
        "monitoring"
    ]
}
```

I know there is a tag `monitoring` .. lets fetch all applications using this tag:

`./api.sh /api/v1/apps/tags/monitoring/meta`

```txt
{
    "60b1104800798cd79b694ca6f6764c15": {
        "host": "www.example.com",
        "website": "Appmonitor server",
        "ttl": 300,
        "result": 0,
        "time": "3.000ms",
        "version": "php-client-v0.137",
        "tags": [
            "monitoring"
        ]
    }
}
```

#### Debug mode

The parameter `-d` shows more connection details.

`./api.sh -d  /api/v1/apps/tags`

```txt
-----------------------------------------------------------------------------
GET /api/v1/apps/tags
-----------------------------------------------------------------------------
RAW data for hashed secret:
GET
/api/v1/apps/tags
Thu, 14 Nov 2024 16:15:14.785036764 CET

-----------------------------------------------------------------------------
Curl params:
-H Accept: application/json -H Content-Type: application/json -X GET -s -i -H Date: Thu, 14 Nov 2024 16:15:14.785036764 CET -H Authorization: api-test:MjYyODVjZWFmNGVlZWNhYmU3NmRlNjhhZmFkYWQzZTUwYzFlZTkzNQ==
http://localhost:8001/api/v1/apps/tags

-----------------------------------------------------------------------------
HTTP/1.1 200 OK
Date: Thu, 14 Nov 2024 15:15:14 GMT
Server: Apache/2.4.57 (Debian)
Access-Control-Allow-Methods: GET, OPTIONS
Access-Control-Allow-Origin: *
Access-Control-Allow-Headers: *
Access-Control-Allow-Credentials: true
Strict-Transport-Security: max-age=63072000; includeSubDomains; preload
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Feature-Policy: sync-xhr 'self'
Referrer-Policy: strict-origin-when-cross-origin
Content-Length: 60
Content-Type: application/json

{
    "tags": [
        "live",
        "monitoring"
    ]
}
rc=0
```
