## Description 

### ZeeltePHP Vite Plugin `zeeltephp(mode)`
- Loads the `.env` file and generates missing variables  [.env Configuration](#env-configuration)
- Creates the required Key-Paths in your ./project  [Key Paths](#key-paths)
- Finalizes the build/api/ with your PHP files.
- 1.0.4 - tdb - Works as MiddleWare/Proxy for API requests


### Key Paths
- `/src/lib/lib_php/` → `/BUILD/api/zeeltephp/lib_php`
  <br> Shared PHP library, which files are pre-loaded before `+.php` files are called.
- `/src/routes/**` → `/BUILD/api/zeeltephp/zproutes`
  <br> Place your `+.php` files. 
- `/static/api/index.php` → `/BUILD/api/zeeltephp/index.php`
  <br> Entry point for ZeeltePHP API by zp_fetch_api().
- `/php_lib`, `/BUILD/api/zeeltephp/php_lib/`
  <br> PHP errors are logged here.


### Key Methods, Classes & Components

#### `zp_fetch_api(fetch, router, [, data, method, headers])` (Svelte)
  Handles most use cases for fetching data from the backend.
  <br> Uses Svelte's fetch which needs to be passed as parameter and ZP_ApiRouter for the routing details.
  <br> This method has overloads. See `zp.fetch.api.js / zp_fetch_api()` for more details.

  ```js
  zp_fetch_api(fetch, string, [, ..])          // use directly +server.php in given route
  zp_fetch_api(fetch, Event [, ..])            // AnyEventType; will be parsed by ZP_EventDetails 
  zp_fetch_api(fetch, URL|URLParams, [, ..])   // Will be parsed by ZP_EventDetails
  zp_fetch_api(fetch, ZP_ApiRouter [, ..])     // If you created (or modified) ZP_ApiRouter earlier.
  zp_fetch_api(fetch, ZP_EventDetails [, ..])  // If you created (or modified) ZP_EventDetails earlier.
  ```
  
  - **data {*}**: overrules auto-detected data to send. 
  - **method {string}**: overrules auto-detected request-method. e.g. GET or POST.
  - **headers {object}**: if you want to use custom headers.  

#### `ZP_ApiRouter` (Svelte and PHP)
  <br>Prepares the request (at Svelte) and destructs the route (at PHP) for `+.php`. 

#### `ZP_EventDetails`:
  <br> Collects information from Events like actions and data to transfer. 

####  `ZPDev.svelte`:    
  <br>Debugging component for your `+page.server.php` files.


### .gitignore Example
```sh
# ignore PHP error and logs
/php_lib
```