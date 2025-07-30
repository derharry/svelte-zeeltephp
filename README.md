# ZeeltePHP (v1.0.4 rc1)

A SvelteKit adapter-static plugin that enables seamless PHP backend integration using SvelteKit-style file conventions (e.g., `+page.server.php`, `+layout.server.php`, `+server.php`).

**Combine the best of Svelte and PHP in your project.**
- Use `+.php` files in your project just like SvelteKit’s `+server.js|ts`.
- Develop as if you’re using SvelteKit natively, including hot-reloading.
- Easily port existing PHP projects into the SvelteKit project structure, or move SvelteKit-native projects to a PHP backend.

---

## In Short

- Run `npm run dev` and use `+.php` files inside your `/src/routes`.
- Run `npm run build` and deploy to your production environment. Access your app at `http://domain/<build>`.

[TOC]: # "## Table of Contents"
## Table of Contents
- [Installation](#installation)
  - [Quick start](#quick-start)
  - [Minimum Requirements](#minimum-requirements)
  - [Example Environments](#example-environments)
  - [Uninstall](#uninstall)
- [Usage Examples](#usage-examples)
  - [+page.server.php](#php-pageserverphp)
  - [+layout.server.php](#php-layoutserverphp)
  - [+page.svelte.js](#svelte-pagejs)
  - [+page.svelte](#svelte-pagesvelte)
  - [+server.php](#php-serverphp)
- [Description](#description)
  - [ZeeltePHP Vite Plugin](#zeeltephp-vite-plugin-zeeltephp-mode)
  - [Key Paths](#key-paths)
  - [.ignore example](#gitignore-example)
  - [ZP Demo](#zp-demo)
  - [ZP Dev](#zp-dev)
  - [.env Configuration](#env-configuration)
  - [Key Methods, Classes & Components](#key-methods-classes--components)
  - [PHP](#php)
    - [Globals](#globals)
    - [Lib](#lib)
    - [Database Provider](#database-provider)
- [Troubleshooting](#troubleshooting)
- [Release Notes & Roadmap](#release-notes--roadmap)
- [Reporting Bugs or Feature Requests](#reporting-bugs-or-feature-requests)
---

## Quick start
Follow the installation steps 1 - 5. 

## Installation

1. **Create your SvelteKit project**  
   ```
   npx sv create myZPproject
   ```
   Be sure to use `adapter-static` and place your project in your local `DOCUMENT_ROOT` directory. e.g. `/htdocs`. 

2. **Install ZeeltePHP**  
   ```
   npm add github:derharry/svelte-zeeltephp
   ```
   Or use a `.tgz` file.

3. **Add Vite Plugin**  
   Update your `vite.config.js`:
   ```js
   import { sveltekit } from '@sveltejs/kit/vite';
   import { defineConfig } from 'vite';
   import { zeeltephp } from 'zeeltephp/vite-plugin';   // add
   
   export default defineConfig(({ mode }) => {     // add mode
      return {
         plugins: [
            zeeltephp(mode),   // add, before sveltekit()
            sveltekit(),
         ]
      }
   });
   ```
   <ins>Note:</ins> the vite-plugin creates, if not exist, following paths in your project:
   ```sh
   /src/lib_php            # Shared PHP library for your +.php files
   /src/routes/+layout.js  # Required by adapter-static
   /static/api/index.php   # PHP api entry point
   /php_log                # PHP error and log output
   ```

4. **Start Development (Optional)**  
   ```
   npm run dev
   ```
   Open `http://localhost:5173/myZPproject` to check if your app is working.
   <br>If you encounter issues, check the CLI output for 🐘 ZeeltePHP and ensure the required paths have been created.

5. **Demo & Debug (Optional)**  
   Copy `/zpdemo/` into `/src/routes/` and verify it runs in development mode.
   <br>Green icons and receiving responses? Have fun `SveltePHP'ing`.

6. **Configure for Build**  
   Update `svelte.config.js` to use `.env` variables:
   ```js
   import adapter from '@sveltejs/adapter-static';

   const config = {
      kit: {
         adapter: adapter({
            pages:  process.env.BUILD_DIR,  // add
            assets: process.env.BUILD_DIR,  // add
         }),
         paths: { 
            base: process.env.BASE          // add
         }
      }
   };
   export default config;
   ```

   (Optional)
   ```
   npm run build
   ```
   Open `http://localhost/myZPproject/build/` and see if your build is working. 


7. **Configure `.env` for Build or Final Production Deployment**  
   Configure your `.env.production` for deployment (e.g. export/FTP).
   See `Description/.env Configuration` for more details.
   ```
   BUILD_DIR=../build-prod                 # build-output folder; e.g. http://localhost/build-prod
   BASE=/build-prod                        # starting from `DOCUMENT_ROOT`; e.g. http://www.domain.com/build-prod
   PUBLIC_ZEELTEPHP_BASE=/build-prod/api/  # Same as BASE but ends with `api/`
   ```
   <ins>Note:</ins> If no `.env.production` is specified, the variables are auto-generated and will run by default as: 
         `http://localhost/myZPproject/build` or `https://www.domain.com/myZPproject/build`.

---

## Uninstall

1. Uninstall ZeeltePHP:
   ```
   npm remove zeeltephp
   ```
2. Manually remove the auto-created paths if not needed.

------

## Minimum Requirements

- Svelte 5
- SvelteKit 2
- SvelteKit Adapter Static 3

- 1.0.4 tbd - 
  - A /path/to/php.exe (without static environment (for dev only)) (See .env.ZEELTEPHP_EXE)
  OR
  - a local HTTPd server (Apache, Nginx, etc.) with PHP 8.

---

### Example Environments

**Production**  `https://www.example.com/<my-build>` 
- Linux, Apache 2.4, PHP 8.3, MariaDB

**Development** `http://localhost/<my-project>/<my-build>` 
- bun, npm, or others at default port 5173
- 1.0.4 tbd - 
  - A /path/to/php.exe (without static environment (for dev only)) (See .env.ZEELTEPHP_EXE)
  OR
  - XAMPP-ApacheFriends with PHP 8.0 + 8.2, MariaDB at default port 80/443

---

## Usage Examples

### PHP: `+page.server.php`
```php
<?php 

      function load() {
            global $data;

            return 'Hello PHP';

            // or

            return [
                  'message' => 'Hello from PHP',
            ];
      }

      function actions($action, $value) {
            global $db, $data;

            // e.g. ?/myAction from SvelteKit.

            switch ($action) {

                  case 'myAction':
                              return "Hello myAction from PHP. received value: $value";
                        break;

                  case 'myDBrequestAction':

                              $result = $db->query("SELECT 'Hello from DB' AS message;");  
                              return $result;

                              // You can use $db for your own DB logic.
                              // When .env.ZEELTEPHP_DATABASEURL is used, ZeeltePHP uses the internal db.PROVIDER.php.

                        break;
            }
	}

      function action_myAction($value) {

            // This 'outside' action-method takes precedence over actions() 

            $result = myLibFunction($value);  // from Shared PHP lib which is pre-loaded. No include() required.

            return $result;
      }

?>
```

### PHP: `+layout.server.php`
```php
<?php

      function load() {
            global $data;

            return 'Hello Layout-PHP';

            // or

            return [
                  'message' => 'Hello from Layout-PHP',
            ];
      }
?>
```


### Svelte: `+page.js`
```js
import { zp_fetch_api } from "zeeltephp";

export async function load({ fetch, url }) {

      const result_php = await zp_fetch_api(fetch, url);

      or 

      const promise_php = zp_fetch_api(fetch, url);

      return [
            message: 'Hello from +page.js',
            result_php,
            promise_php
      ];
}
```

### Svelte: `+page.svelte`
```html
<script>
      import { zp_fetch_api } from "zeeltephp";

      export let data;  

      let promise = data?.promise_php || undefined;

      async function handle_click(event) {
            promise = zp_fetch_api(fetch, event)
                  .then(data => {})
                  .catch(error => {})
      }

      async function handle_submit(event) {
            const data = await zp_fetch_ap(fetch, event);
      }
</script>

<button
      type="button"
      formaction="?/myAction"
      value="1"
      on:click={handle_click}
/>

<form on:submit={handle_submit}>
      <button
            type="submit"
            formaction="?/myAction"
            value="42"
      />
      {#await promise}
            ..
      {:then data}
            ..
      {:catch error}
            .. 
      {/await}
</form>
```

### PHP: `+server.php`
```php
<?php

      // same for POST, PUT, PATCH, DELETE, HEAD
      function GET() {
            global $data;
            return [
                  'message' => 'Hello from +server.php-GET',
            ];
      }
      
      // fallback if none of the others exist 
      function fallback() {
            global $data;
            return [
                  'message' => 'Hello from +server.php-fallbacck',
            ];
      }
?>
```



---

## Description 

### TL;TR
- Copy `/zpdemo` into your `/routes` and see if it works.
- Start using `zp_fetch_api()`, `ZP_EventDetails(event)`, and `ZP_ApiRouter()`.


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
  Handles most use cases for fetching data from the PHP api.
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
  <br>Debugging component for your `+page.server.php` files. It's initial settings work with `ZPDemo`

####  `VarDump.svelte`:  
  <br>Shows (dumps) the content of a variable visually in UI. Like PHPs var_dump().


### .gitignore Example
```sh
# ignore PHP error and logs
/php_lib
```

### ZP Demo
The `/zpdemo/` folder serves as a complete demo and debugger. 
<br>Copy it into your `routes` and access it at `http://localhost/myZPproject/zpdemo`.
<br>If `zpdemo` does not work, there may be a misconfiguration from the installation steps. 

### ZP Dev
See troubleshooting for more details.
* Use <ZPDev /> to inspect and interact with your API routes directly from the browser. 
* The load() and actions() should return all globals for best debugging results.
* You can test both GET and POST actions, as well as custom action handlers.
* PHP Errors will be shown in <ZPDev /> directly from the browser, but 
  you can also access http://localhost/myZPproject/static/api 
  
### .env Configuration
ZeeltePHP is designed to run in different environments which can be configured 
in .env-files specific dev and production build environment/landscape.

- Variables in .env files are available in Svelte and your PHP code.
- The variables are optional and auto-generated if missing at both, development and build, 
  presumming running in DOCUMENT_ROOT/<your-project>  and shown in CLI output.
- For production builds, use relative paths from the DOCUMENT_ROOT where the app will run.
- At builds the variables with prefix PUBLIC_, VITE_ or ZEELTEPHP_ are exported to `/BUILD_DIR/api/zeeltephp/.env`.

The following variable options are meant as:
                   [filename]        [the .env file is loaded at]
  development:     .env.development  `npm run dev`
  build:           .env.production   `npm run build` to run and test your local-build
  production:      .env.<any-name>   `npm run build --mode <any-name>` the export to run on production webhosting environment

```sh
BUILD_DIR=myBuild
      # development:  empty; not used
      # build:        build is saved to /myZPproject/myBuild;
      #    or:        ../myStageTest
      # production:   mySITE;  Build-name to export/FTP and on to run on final-destination; 
      #                        For export the folder can be any naming e.g. myExportBuild, as long it is renamed
      #                        to as specified at BASE at its final destination to run your App e.g. mySITE

BASE=/myZPproject/myBuild
      # development:    empty
      # build:          /myZPproject/myBuild; http://localhost/myZPproject/myBuild
      #    or:          /myStageTest; http://localhost/myStageTest
      # production:     /mySITE     ; http://www.domain.com/mySITE
      #    in root:     /           ; http://www.domain.com/ 

PUBLIC_ZEELTEPHP_BASE=/myZPproject/myBuild/api
      # development:    http://localhost/myZPproject/static/api/ 
      #                 1.0.4 - tdb - default to just /api/
      # build:          /myZPproject/myBuild/api/
      #    or:          /myStageTest/api/
      # production:     /mySITE/api/
      #    in root:     /api/

ZEELTEPHP_EXE=/path/to/php.exe
     # development:    1.0.4 tbd - if set - ZeeltePHP uses CLI/php.exe instead of localhost.
     #                                      Project can be saved anywhere (not in DOCUMENT_ROOT)
     # build:          to let the build run with CLI/php.exe
```

---

### PHP

#### PHP error log
- Errors are logged to `/php_log`, `/BUILD/api/zeeltephp/php_log/` as long its allowed to be set via `ini_set('error_log')`.
- At error output (log, responses) 
  - the path `/api/zeeltephp` is shortened to `/api` for readability.
  - full system paths are changed to relative paths.
- For saving custom log-files you can use `PATH_ZPLOG`.
  - e.g. `file_put_contents(PATH_ZPLOG."mylog.txt")`.
  - **zp_log( $content )** writes the content to `log.log`.


#### Globals
These global variables are accessible from anywhere in your PHP code.
- **$zpAR**: class ZP_ApiRouter.
- **$env**:  `.env` configuration. 
- **$db**:   Shared DB-Connection. 

#### PHP.exe
ZeeltePHP runs on CLI/php.exe when the .env.ZEELTEPHP_EXE=/path/to/php.exe variable is set. 
A static-localhost-environment is then not required (Apache, Nginx, ..).
See [.env Configuration](#env-configuration) for details.


#### Database Provider 
***experimental!  in development!***
<br>There are currently 2 classes for MySQL and WordPress available at `/api/zeeltephp/lib/db`. 
<br>They can be used for any custom query and contain some helper methods for like basic CRUD operations.
<br>Any attribute or method that is available at `mysqli` or `wp-load` can be used - they are forwarded via PHP's magic getter and method.
<br>
<br>When `.env.ZEELTEPHP_DATABASE_URL` is set, ZeeltePHP uses the corresponding internal database provider class and is available from Global `$db`.

```sh
ZEELTEPHP_DATABASE_URL=mysql2://username:password@hostname:port/database
ZEELTEPHP_DATABASE_URL=wordpress://path/to/your/wp-load.php
```

```php

   function example_db_usage() {
      global $db;

      // optional connect
      //   the connect() is automatically opened as soon any $db->METHOD() is called;
      $db->connect();

      // is there a connection ?
      if ($db->connect()) {
            // returns true or false on succes 
            // returns true when already connected to avoid multiple connections.
      }
      if ($db->isConnected()) {
            // returns true or false if is connected.
      }

      // just a query
      $res = $db->query('SELECT * FROM ...'); 

      // prepare data for CRUD operations
      //  * $data must be an array key-value-list where key = column-name.
      //  * The data gets prepared, sanitized for matching key/columns. 
      //  * non matching key/columns will be ignored.
      $data = $_POST;

      // insert data
      //   returns the inserted ID on success;  false on failure
      $insertedID = $db->insert('tableName', $data); 

      // update data
      //  returns true of false; 
      //       true  on 1+ affected-rows
      //       false on 0  affected-rows
      //  * id, uuid are ignored if in $data
      //  @param id is an array key-value-list of matches combined with AND
      $success = $db->update('tableName', ['id' => $id], $data);
                        
      // delete data
      //  returns true of false; 
      //       true  on 1+ affected-rows
      //       false on 0  affected-rows
      //  @param id is an array key-value-list of matches combined with AND
      $success = $db->delete('tableName', ['id' => $id]);

      // the number of affected rows of last query
      $affectedRows = $db->affected_rows;

      // the error message of last query
      $error = $db->last_error();

      // optional disconnect
      //   the connection is automatically closed at __destruct();
      $db->close();

   }
```

#### Lib
At `/api/zeeltephp/lib/` you’ll find a collection of useful helper methods.
<br>To use them you have to include them in your PHP code.
<br>Currently:

- **merge_key_value_list(array $src, array $insert): array** 
  <br>`include_once('lib/inc/tools.php');`
  <br>returns a merged key-value-list by copying the values from $insert matching the keys from $src.
  <br>e.g.:  $list = merge_key_value_list( $toUpdate, $_POST );

- **zp_scandir($path, $regExp = null): array** 
  <br>`include_once('lib/io/io.dir.php');`
  <br>returns a list of files and folders within the path. Use a RegExp like '.php$' for name matching.

- **zp_scandirRecursiveDown($path, $regExp = null, $maxDepth = 1): array** 
  <br>`include_once('lib/io/io.dir.php');`
  <br>returns a list of files and folders, recursive downwards, with relative paths starting from given path. 
  <br>By default only 1 level down.
  <br>Use a RegExp like '.php$' for name matching.

- **zp_scandirRecursiveUp($path, $regExp = null, $maxDepth = 1): array**  
  <br>`include_once('lib/io/io.dir.php');`
  <br>returns a list of files and folders, recursive upwards, with relative paths starting from given path. 
  <br>By default only 1 level up, which is similar to zp_scandir('..'); but $maxDepth = 2 is '..' and '../..';
  <br>Use a RegExp like '.php$' for name matching.

---

## Troubleshooting
If you encounter issues with your route, load(), or actions(), you can debug your setup by including `<ZPDev />` in your `+page.svelte`.
<br>This enables you to use the `+page.server.php` `+server.php` `+layout.server.php` directly to execute load(), actions(), edit action names/values, and send test data as either FormData or JSON.
<br>Make sure the global variables $zpAR, $env, and $db are accessible and exposed.
<br>
<br>**<ins>Attention!</ins>** 
<br>Don't forget to remove `<ZPDev />` and exposed globals before deploying to production to avoid exposing debug tools and sensitive data.
<br>

**`+page.svelte` or in a Component.**
```svelte
<script>
     import { ZPDev } from "zeeltephp";
</script>
<ZPDev />
```

**+page.server.php**
```php
<?php

      function load() {
            // load(), actions()
            global $zpAR, $env, $db;
            return [
                  'zpAR'  => $zpAR, // expose ApiRouter of PHP
                  'zpEnv' => $env,  // expose your exported .env
                  'zpDB'  => $db    // export your DB or ZP-Database-provider connection
            ];
      }
?>
```


---

## Release Notes & Roadmap
- More documention will follow.
- See [README.release-notes.md](README.release-notes.md).
- See [README.roadmap.md](README.roadmap.md).


## Reporting Bugs or Feature Requests
- If you find a bug or have a feature request, please:
  - Open an issue with a clear description of the problem or suggestion.
  - Include steps to reproduce the issue, your environment (OS, PHP version, etc.), and any relevant logs or screenshots.
  - For security vulnerabilities, please contact the maintainer directly.

---

**For further questions, support, contribution or suggestions feel free to contact the maintainer.**


