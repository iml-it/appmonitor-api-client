## Example health monitor page

This example shows a customer health page for a group of applications. It is a low level view without details.

To execute this demo you need

* a running appmonitor instance
* with min. 1 monitored application using minimum one tag

### Create an api user

On appmonitor server configure an user eg `api-test` with a secret of your choice and the role "api".

### Update config

Copy `config.php.dist` to `config.php` (without .dist) and edit connection details:

* 'apiurl'=>'http://localhost/api/v1'
* 'user'=>'api-test'
* 'secret'=>'tryme'

You can create as many groups as you want. Per group you can define a set of tags to group your applications.

### Start up

To start the php builtin webserver:

```shell
cd healthmonitor/example
php -S localhost:9000
```

Open `http://localhost:9000` to see the example page in the webbrowser.
