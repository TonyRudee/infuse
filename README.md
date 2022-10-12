# INFUSEMedia test case

## Architecure

Since we are not building RESTfull API (just beacuse it will need proper server configuration),\
so we have a single waypoint **main.php**. To route the query we use **action** param.

Also we use **application/json** format for requests and responces both. For that purpose we use **php://input**

## Backend

We have a sigle class to handle input, responces and all the logic with separate methods for each api call and basic error handling. We also use a PDO as DB interface.

## Frontend

We have a _Vanilla.JS_ on fronted, but we use **modern sintax** and no transpilers and polyfills.\
So you have to use a **modern browser**.

JS app also has same pattern as a backend: single object in responce of everything\
We also have a **Change banner now!** button wich re-inits the app and makes it easier to test.

## Config

- Change DB connection params in _main.php_
- Avalable banners names are also declared in _main.php_
- Change _apiPath_ param in _index.js_, specifty actual full path
- Specify actual _imagesPath_ and _defaultImageName_ in _index.js_
