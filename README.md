# ZeeltePHP (v1.0.3 rc1)

A SvelteKit adapter-static plugin that enables seamless PHP backend integration using SvelteKit-style file conventions (e.g., `+page.server.php`).

**Combine the best of Svelte and PHP in your project.**
- Use `+.php` files in your project just like SvelteKit’s `+server.js|ts`, including hotreload.
- Develop almost as if you’re using SvelteKit natively.
- Easily port existing PHP projects into the SvelteKit project structure, or move SvelteKit-native projects to a PHP backend.

---



## In Short

- Run `npm run dev` and use `+.php` files inside your `/src/routes` as you would in SvelteKit.
- Run `npm run build` and deploy to your production environment. Access your app at `http://domain/<build>`.

---

## Instalation

1. **Create your SvelteKit project**  
   ```
   npx create svelte <your-project>
   ```
   Be sure to use `adapter-static` and place your project in your local `/htdocs` or as your `DOCUMENT_ROOT` directory. 

2. **Install ZeeltePHP**  
   ```
   npm add github:derharry/svelte-zeeltephp
   ```
   Or use a `.tgz` file.

3. **Add ZeeltePHP Vite Plugin**  
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

4. **Start Development**  
   ```
   npm run dev
   ```
   And open in your browser e.g. http://localhost/<your-project>;
   If you encounter issues, check the CLI output for 🐘 ZeeltePHP.  
   You might need a `.env.development` with the correct `PUBLIC_ZEELTEPHP_BASE`.

   **Auto-created paths:**
   ```
   /src/lib/zplib            # Shared PHP library for your +.php files
   /src/routes/+layout.js    # Required by adapter-static
   /static/api/index.php     # ZeeltePHP API entry point
   /.zp-log                  # PHP error and log output
   ```

5. **Configure for Build**  
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

   Configure your `.env.production` for deployment:
   ```
   # .env.build
   BUILD_DIR=../build-prod                            # build-output folder. e.g. run from http://localhost/<BUILD_DIR> (when ../)
   BASE=/path/to/build-prod                           # /path/to/build starting from `DOCUMENT_ROOT`; e.g. http://domain/path/to/build
   PUBLIC_ZEELTEPHP_BASE=/path/build-prod/api         # Same as BASE but ends with /api
   ```
   * If no `.env.production` is specified, the variables are auto-generated to run the build by default 
     from `http://localhost/<your-project>/<BUILD_DIR>`  or  `https://www.domain.com/<BUILD_DIR>`.

6. **(Optional) Demo & Debug**  
   Copy `/zpdemo/` into `/src/routes/` and verify your project runs in dev mode.
   Green lights and responses? Happy `SveltePHP'ing`

---

## Uninstall

1. Uninstall ZeeltePHP:
   ```
   npm remove zeeltephp
   ```
2. Manually remove any paths created by post-install if not needed.

------

## Requirements

- Svelte 5
- SvelteKit 2
- SvelteKit Adapter Static 3
- PHP 8
- HTTP server (Apache, Nginx, etc.) 

---

### Example Environments

**Production**  
`https://www.example.com/<my-build>` 
- Linux, Apache 2.4, PHP 8.3, MariaDB

**Development**  
`http://localhost/<my-project>/<my-build>` 
- bun, npm on Windows or WSL
- XAMPP-ApacheFriends with PHP 8.0 + 8.2, MariaDB on Windows.

---

## Usage Examples

### PHP: `+page.server.php`
``` 
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
```
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
``` 
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


## What Happens Behind the Scenes
- ZeeltePHP executes your PHP files, handles routing, and sends the response as JSON.
- For advanced usage, see the documentation in `src/zp.fetch.api.js` and `ZP_Router`.
- Mode documention will follow.

### ZeeltePHP Vite Plugin `zeeltephp(mode)`
- Loads the `.env` file and generates missing variables.
- On `npm run dev`, missing required paths are created.
- Finalizes the build/api/ with your PHP files after SvelteKit has finished its build.

### Key Paths
- `/src/lib/zplib/` → `/BUILD/api/zplib`: Shared PHP library, which files are pre-loaded before `+.php` files are called.
- `/src/routes/**` → `/BUILD/api/zproutes`: Place your `+.php` files. 
- `/static/api/index.php` → `/BUILD/api/index.php`: Entry point for ZeeltePHP API by zp_fetch_api().
- `/.zp-log`, `/BUILD/api/log/`: PHP errors are logged here.

### .gitignore Example
```
# ignore PHP error and logs
/.zp-log
```

### Key Methods & Components
- **`zp_fetch_api()`**:  Main method to request your `+.php` files and solves the response.
- **`ZP_ApiRouter`**:    Sets up (at Svelte) or destructs (at PHP) the request-layer to or from PHP. 
- **`ZP_EventDetails`**: Collects information from Events like actions and data transfer. 
- **`ZPDev.svelte`**:    Debugging component for your project with ZeeltePHP and `+page.server.php`.
- **`VarDump.svelte`**:  Visually shows the content of a variable. Like PHPs var_dump().

### .env Configuration
- Auto-generated at both, development and build, if missing.
- For production builds set relative paths from your document root.
- At builds, variables with prefix PUBLIC_ or ZEELTEPHP_ will be exported to `/BUILD/api/zeeltephp/.env`.
```
BUILD_DIR=myBuild
      # development:    empty; not used
      # build:          build is saved to /<your-project>/myBuild;
      #    or:          ../myStageTest
      # production:     mySITE;  Build-name to export/FTP and on to run on final-destination; 
      #                          For export the folder can be any naming e.g. myExportBuild, as long it is renamed
      #                          to as specified at BASE at its final destination to run your App e.g. mySITE

BASE=/<your-project>/myBuild
      # development:    empty
      # build:          /<your-project>/myBuild ; http://localhost/<your-project>/myBuild
      #    or:          /myStageTest            ; http://localhost/myStageTest
      # production:     /mySITE                 ; http://www.domain.com/mySITE

PUBLIC_ZEELTEPHP_BASE=/myBuild/api
      # development:    http://localhost/<your-project>/static/api/
      # build:          /<your-project>/myBuild/api/
      #    or:          /myStageTest/api/
      # production:     /mySITE/api/
```

### Database Providers 
- Optional; If set in `.env`, the included /api/zeeltephp/lib/db/db.provider.php is available in `$db`.
``` 
ZEELTEPHP_DATABASE_URL=mysql2://username:password@hostname:port/database
ZEELTEPHP_DATABASE_URL=wordpress://path/to/your/wp-load.php
```

### PHP and Globals
- You can use and of the globals at any of your methods.
- **`PHP errors`** are logged to `/.zp-log`, `/BUILD/api/log/` if its allowed to be change: ini_set('error_log').
- **`$zpAR`**: is class ZP_ApiRouter; 
- **`$env`**: your `.env` configuration. 
- **`$db`**: Database connector/plugin (if configured).


---

## Release Notes & Roadmap

See [README.release-notes.md](README.release-notes.md).
See [README.roadmap.md](README.roadmap.md).

---

**For further questions, support, or suggestions feel free to contact the maintainer.**
