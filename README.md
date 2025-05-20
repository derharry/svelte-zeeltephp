# ZeeltePHP (v1.0.3 rc1)

A SvelteKit adapter-static plugin that enables seamless PHP backend integration using SvelteKit-style file conventions (e.g., `+page.server.php`).

**Combine the best of Svelte and PHP in your project.**
- Use `+.php` files in your project just like SvelteKit’s `+server.js|ts`.
- Develop almost as if you’re using SvelteKit natively, including hot-reloading.
- Easily port existing PHP projects into the SvelteKit project structure, or move SvelteKit-native projects to a PHP backend.

---

## In Short

- Run `npm run dev` and use `+.php` files inside your `/src/routes`.
- Run `npm run build` and deploy to your production environment. Access your app at `http://domain/<build>`.

[TOC]: # "## Table of Contents"
## Table of Contents
- [Installation](#instalation)
  - [Miminum requirements](#minimum-requirements)
  - [Example Environments](#example-environments)
  - [Uninstall](#uninstall)
- [Usage Examples](#usage-examples)
  - [+page.server.php](#php-pageserverphp)
  - [+page.server.php](#svelte-pagejs)
  - [+page.svelte](#svelte-pagejs)
- [Description](#description)
  - [ZeeltePHP Vite Plugin](#database-providers)
  - [Key Paths](#database-providers)
  - [.ignore example](#gitignore-example)
  - [ZPDemo](#zpdemo)
  - [.env Configuration](#env-configuration)
  - [Key Methods, Classes & Components](#key-methods-classes--components)
  - [Database Providers](#database-providers)
  - [PHP and globals](#php-and-globals)
- [Release Notes & Roadmap](#release-notes--roadmap)

---

## Instalation

1. **Create your SvelteKit project**  
   ```
   npx create svelte myZPproject
   ```
   Be sure to use `adapter-static` and place your project in your local `DOCUMENT_ROOT` directory. e.g. `/htdocs`. 

2. **Install ZeeltePHP**  
   ```
   npm add github:derharry/svelte-zeeltephp
   ```
   Or use a `.tgz` file.

3. **Add Vite Plugin**  
   Update your `vite.config.js`:
   ```
   import { sveltekit } from '@sveltejs/kit/vite';
   import { defineConfig } from 'vite';
   import { zeeltephp } from 'zeeltephp/vite-plugin';  // add

   export default defineConfig(({ mode }) => ({  // add or change
     plugins: [
       sveltekit(),
       zeeltephp(mode)  // add
     ]
   }));
   ```
   Note: the vite-plugin creates following required paths in your project:
   ```
   /src/lib/zplib            # Shared PHP library for your +.php files
   /src/routes/+layout.js    # Required by adapter-static
   /static/api/index.php     # PHP api entry point
   /.zp-log                  # PHP error and log output
   ```

4. **(Optional) Start Development**  
   ```
   npm run dev
   ```
   Open `http://localhost:5173/myZPproject` and see if your App is working at this stage. 
   <br>If you encounter issues, check the CLI output for 🐘 ZeeltePHP. 
   <br>A `.env.development` with the correct `PUBLIC_ZEELTEPHP_BASE` might be required.

5. **(Optional) Demo & Debug**  
   Copy `/zpdemo/` into `/src/routes/` and verify if it runs in `dev mode`.
   Green lights and responses? Have fun with `SveltePHP'ing`.

6. **Configure for Build**  
   Update `svelte.config.js` to use `.env` variables:
   ```
   import adapter from '@sveltejs/adapter-static';
   import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

   const config = {
      preprocess: vitePreprocess(),
      kit: {
         adapter: adapter({
            pages:  process.env.BUILD_DIR,  // add
            assets: process.env.BUILD_DIR,  // add
         }),
         prerender: { entries: ['*'] },
         paths: { 
            base: process.env.BASE       // add
        }
      },
      trailingSlash: 'always'
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
   BASE=/build-prod                        # starting from `DOCUMENT_ROOT`; e.g. http://localhost/build-prod
   PUBLIC_ZEELTEPHP_BASE=/build-prod/api/  # Same as BASE but ends with `api/`
   ```
   Note: If no `.env.production` is specified, the variables are auto-generated and will run by default as: 
         `http://localhost/myZPproject/build` or `https://www.domain.com/build`.

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
- PHP 8
- HTTPd server (Apache, Nginx, etc.) with PHP 8.

---

### Example Environments

**Production**  `https://www.example.com/<my-build>` 
- Linux, Apache 2.4, PHP 8.3, MariaDB

**Development** `http://localhost/<my-project>/<my-build>` 
- bun, npm, or others.
- XAMPP-ApacheFriends with PHP 8.0 + 8.2, MariaDB

---

## Usage Examples

### PHP: `+page.server.php`
```php
<?php

      function load() {

            return 'Hello PHP';

            // or

            return [
                  'message' => 'Hello from PHP',
            ];
      }

      function actions($action, $value) {
            global $db;

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
      ] 
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
      const data = await zp_fetch_ap(fetch, event)
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

---

## Description 

### TL;DR
- Copy `/zpdemo` into your `/routes` and see if it works.
- Start using `zp_fetch_api()`, `ZP_EventDetails(event)`, and `ZP_ApiRouter()`.

### ZeeltePHP Vite Plugin `zeeltephp(mode)`
- Loads the `.env` file and generates missing variables.
- Creates the required key-paths.
- Finalizes the build/api/ with your PHP files after SvelteKit has finished the build.

### Key Paths
- `/src/lib/zplib/` → `/BUILD/api/zplib`
  <br> Shared PHP library, which files are pre-loaded before `+.php` files are called.
- `/src/routes/**` → `/BUILD/api/zproutes`
  <br> Place your `+.php` files. 
- `/static/api/index.php` → `/BUILD/api/index.php`
  <br> Entry point for ZeeltePHP API by zp_fetch_api().
- `/.zp-log`, `/BUILD/api/log/`
  <br> PHP errors are logged here.

### .gitignore Example
```
# ignore PHP error and logs
/.zp-log
```

### ZPDemo
The folder `/zpdemo/` is intended as full demo and debugger. 
<br>Copy it into your `routes` and call it `http://localhost/myZPproject/zpdemo`.
<br>If `zpdemo` does not work, there might be a mis-configuration from the install-steps. 

### Key Methods, Classes & Components

#### `zp_fetch_api(fetch, router, [, data, method, headers])` (Svelte)
  Handels and resolves most use-cases to fetch from PHP-api.
  <br> Uses Svelte's fetch which needs to be passed as parameter and ZP_ApiRouter for the routing details.
  <br> This method has Overloads. See `zp.fetch.api.js / zp_fetch_api()` for more details.ssss 
  ```
  zp_fetch_api(fetch, Event [, ..])            AnyEventType; will be parsed by ZP_EventDetails 
  zp_fetch_api(fetch, URL|URLParams, [, ..])   will be parsed by ZP_EventDetails
  zp_fetch_api(fetch, ZP_ApiRouter [, ..])     If you created (or modified) ZP_ApiRouter earlier.
  zp_fetch_api(fetch, ZP_EventDetails [, ..])  If you created (or modified) ZP_EventDetails earlier.
  ```
  If set:
  * data {*}: overrules auto-detected data to send. 
  * method {string}: overrules auto-detected request-method. e.g. GET or POST.
  * headers {object}: if you want to use custom headers.  

#### `ZP_ApiRouter` (Svelte and PHP)
  <br>Prepares the request (at Svelte) and destructs the route (at PHP) for `+.php`. 

#### `ZP_EventDetails`:
  <br> Collects information from Events like actions and data to transfer. 

####  `ZPDev.svelte`:    
  <br>Debugging component for your `+page.server.php` files. It's initial settings work with `ZPDemo`

####  `VarDump.svelte`:  
  <br>Shows (dumps) the content of a variable visually in UI. Like PHPs var_dump().
  

### .env Configuration
- Auto-generated at both, development and build, if missing.
- For production settings, use relative paths from the document root where the app will run.
- Variables with prefix PUBLIC_ or ZEELTEPHP_ are exported to `/BUILD_DIR/api/zeeltephp/.env` to use in BUILD.
```sh
BUILD_DIR=myBuild
      # development:    empty; not used
      # build:          build is saved to /myZPproject/myBuild;
      #    or:          ../myStageTest
      # production:     mySITE;  Build-name to export/FTP and on to run on final-destination; 
      #                          For export the folder can be any naming e.g. myExportBuild, as long it is renamed
      #                          to as specified at BASE at its final destination to run your App e.g. mySITE

BASE=/myZPproject/myBuild
      # development:    empty
      # build:          /myZPproject/myBuild ; http://localhost/myZPproject/myBuild
      #    or:          /myStageTest            ; http://localhost/myStageTest
      # production:     /mySITE                 ; http://www.domain.com/mySITE

PUBLIC_ZEELTEPHP_BASE=/myBuild/api
      # development:    http://localhost/myZPproject/static/api/
      # build:          /myZPproject/myBuild/api/
      #    or:          /myStageTest/api/
      # production:     /mySITE/api/
```

### Database Providers 
- Optional; If specified in `.env`, the included /api/zeeltephp/lib/db/db.provider.php is available to use with `$db`.
```sh
ZEELTEPHP_DATABASE_URL=mysql2://username:password@hostname:port/database
ZEELTEPHP_DATABASE_URL=wordpress://path/to/your/wp-load.php
```

### PHP and Globals
- You can use and of the globals at any of your methods.
- **`PHP errors`** are logged to `/.zp-log`, `/BUILD/api/log/` if its allowed to be change: ini_set('error_log').
- **`$zpAR`**: is class ZP_ApiRouter; 
- **`$env`**: your `.env` configuration. 
- **`$db`**: Database connector/plugin (if configured).

## What Happens Behind the Scenes
- ZeeltePHP executes your PHP files, handles routing, and sends the response as JSON.
- For advanced usage, see the documentation in `src/zp.fetch.api.js` and `ZP_Router`.

---

## Release Notes & Roadmap
- More documention will follow.
- See [README.release-notes.md](README.release-notes.md).
- See [README.roadmap.md](README.roadmap.md).

---

**For further questions, support, or suggestions feel free to contact the maintainer.**
