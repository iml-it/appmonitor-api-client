## Requirements

### Appmonitor server

To execute the demo or test the class you need

* A running instance of IML appmonitor - see <https://github.com/iml-it/appmonitor>
* Your monitored webapplications must use tags (for phase, product and kind of task)

### Access API

On IML Appmonitor server you need to configure an user eg `api-test` with a secret of your choice and the role "api".

On your instance of IML Appmonitor edit the file `public_html/server/config/appmonitor-server-config.json`. In the section "users" 

* add the user "api-test" 
* with a secret (it must be configured in the api clients too)
* In the list of roles must be "api" to allow api access

```json
{
    ...
    "users": {
        ...
        "api-test": {
            "secret": "tryme",
            "comment": "API Test user",
            "roles": [
                "api"
            ]
        },
        ...
    }
}
```
