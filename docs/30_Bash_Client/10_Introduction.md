## Bash client

This is an example to access the appmonitor url with a bash script.
You need

* api.sh
* api_config.sh

### Requirements

* curl must be installed
* jq - optional to highlight the output 

### Configure client

Copy `api_config.sh.dist` to `api_config.sh` (without .dist) and edit connection details:

```shell
AM_APIURL="http://appmonitor.example.com"
AM_APIUSER="api-test"
AM_APISECRET="tryme"
```
