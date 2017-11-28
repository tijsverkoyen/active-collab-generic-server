# ActiveCollab Generic Server for PhpStorm

As there is no support for [ActiveCollab](https://activecollab.com/) in [PhpStorm](https://www.jetbrains.com/phpstorm/)
I wrote this hackish implementation.

With this hack we can have Tasks inside PhpStorm

![tasklist](https://raw.githubusercontent.com/tijsverkoyen/active-collab-generic-server/master/assets/1_tasklist.png)

## Installation

Download this package on a PHP server and note the url (eg: https://foo.bar.tld).

## Usage

### Obtain the needed Template Variables

First we need to obtain some data we will need later on. This data is returned by our package, but we need
to construct an url.

We will need to append some GET parameters to our `/login` url.

* username: your ActiveCollab Username
* password: your ActiveCollab Password
* account: your ActiveCollab instance number, if your Active Collab url is: https://app.activecollab.com/123456, this is `123456`.

The final url will look something like: [https://foo.bar.tld/login?username=john@doe.com&password=super-secret-password&account=123456](#)

If you open this url in a browser it will result in a JSON-object like below


    {
      token: "12-3858f62230ac3c915f300c664312c63f",
      acArl: "https://app.activecollab.com/123456",
      userId: 1337
    }

### Configure the Generic Task Server

1. Go to `Tools → Tasks & Contexts → Configure Servers`.
2. Add a new `Generic` server type.
3. Enter the url (eg: https://foo.bar.tld).
4. Check `Login anonymously`.
5. Open the tab `Server Configuration`.
6. Click `Manage Template Variables`.
7. Add the needed variables based on the data we earlier retrieved.

You should end up with something like:

![tasklist](https://raw.githubusercontent.com/tijsverkoyen/active-collab-generic-server/master/assets/2_template_variables.png)

8. Configure the `Task List URL`: {serverUrl}/task-list?token={token}&acUrl={acUrl}&userId={userId}

![tasklist](https://raw.githubusercontent.com/tijsverkoyen/active-collab-generic-server/master/assets/3_server_configuration.png)

9. Choose `JSON` as the Response Type and add the correct paths for each property as indicated in the screenshot.


## Known issues

* There is no support for self-hosted ActiveCollab instances.
* There is no decent error handling. 



