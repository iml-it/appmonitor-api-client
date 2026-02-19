## Get help

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
