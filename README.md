# ZeeltePHP (v1.0.2 rc1)
A SvelteKit-adapter-static 'PHP' plugin for projects using PHP-backend and use PHP like SvelteKit-files, aka `+page.server.php`.
* Svelte + PHP.
* combine best of both worlds in <your-project>.
* use `+server.php` files inside SvelteKit-projects like its counter parts.
* port existing PHP-project into SvelteKit project structure, to be ready-to-port to SvelteKit-native. 
* port vice-verca - fully SvelteKit-native <<-->> SvelteKit-apapter-static + PHP. 


## Instalation
1) create your SvelteKit-project via `bunx sv create` including `adapter-static` into /path/to/your/htdocs aka http://locahost/<your-project>
2) install ZeeltePHP `bun add github:derharry/svelte-zeeltephp`, from `tgz` or copy `dist`.
3) add `zeeltephp-vite-plugin`/`zeeltephp(mode)` to `vite.config.js`. 
``` vite.config.js
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';
import { zeeltephp } from 'zeeltephp/vite-plugin';

export default defineConfig(({ mode }) => {
      return {
            plugins: [
                  sveltekit(), 
                  zeeltephp(mode)
            ]
      };
});
```
4) run `npm run dev`, and all should be running. 
5) *optional* demo/debugging, copy&paste `/zpdemo/` into your `/src/routes/**` and see if your project is running in `dev`.
> green lights ? happy `SveltePHP-ing`
> other lights ? well ... include 6 and recheck 1-6.
6) *required for builds*, configure `svelte.config.js` to use .env variables. see `/templates/example.svelte.config.js` for details.


## Uninstall or Reinstall
1) *un-install* `npm remove zeeltephp`.
2) *un-install* manually remove paths created by post-install if you do not need them anymore.
3) *re-install* if required - move existing paths to let postinstaller re-create the paths.
4) *re-install* follow the install steps.

## Requirements
* Svelte 5 (4 should work)
* SvelteKit 2
* SvelteKit-Adapter-Static
* HTTPD environment (apache, nginx, any static)
* PHP 8

### Environments (examples as currently used)
Production
* https://www.example.com/<your-project> 
* HTTPd, PHP8, MySQL, wordpress, ..

Development and testing
* http://localhost/**/<your-sveltekit-project>
* Windows only  : bun, httpd 
* Windows + WSL : htpd / bun 
* httpd (dev local) = XAMPP from Apachefriends, PHP8


## Examples - How to use
1. use load(), actions(), or action_FOO() in +page.server.php
2. use zp_fetch_api() from .js and .svelte 
	* const result  = await zp_fetch_api()
	* const promise = zp_fetch_api()
            .then(data => {})
            .catch(error => {})
	* {#await promise}
        {:then data}
        {:catch} 
        {/await}

``` +page.server.php
<?php

      // do any of your PHP code here or within load, actions context

      function load() {
            global $db, $env, $zpAR;
            
            return [
                  'res_php'   => 'Hello from PHP/home',
                  'res_zplib' => function_from_zplibzplib()
            ]
      }

      function actions($action, $value) {
            global $db, $env, $zpAR;

		switch ($action) {
                  case 'FOO':
                        break;
            }

            return [
                  'res_php'   => 'Hello from PHP/home',
                  'res_zplib' => function_from_zplibzplib()
            ]
	}

      function action_FOO($value) {
            global $db, $env, $zpAR;

            // this method is priorized over actions()!

            return [
                  'res_php'   => 'Hello from PHP/home',
                  'res_zplib' => function_from_zplibzplib()
            ]
      }

?>
```

``` +page.js
import { zp_fetch_api } from "zeeltephp";

export async function load( { fetch, url } ) {
      const result_php = await zp_fetch_api( fetch , url); 

      return [
            result_php
      ];
}
```

``` +page.svelte
import { zp_fetch_api } from "zeeltephp";

export let data;

let promise; 

async function handle_click(event) {
      promise = zp_fetch_ap(fetch, event)
            .then(data => {})
            .catch(error => {})
}

async function handle_submit(event) {
      const data = await zp_fetch_ap(fetch, event)
}

<form on:submit={handle_submit}>
      <button
            type="submit"
            formaction="?/FOO"
            value="zoo"
      />
      {#await promise}
            ..
      {:then data}
            ..
      {:catch error}
            .. 
      {/await}
</form>

<button
      type="button"
      formaction="?/FOO"
      name="btn"
      value="zoo"
      on:click={handle_click}
/>
```

## Description

### tl;tr
start using `zp_fetch_api()` or  `/zpdemo`.

#### zeeltephp vite-plugin
* loads .env 
* post-install In dev-mode it will create, if not exist, minimum required paths.
* post-build   runs at vite-closeBunlde and after svelte-package to do post-install-steps on final build, which is ready for production (ftp?).

#### paths - development and build environment
> dev/src/lib/zplib/    /BUILD/api/zplib
Shared-library for your PHP - all files are autoloaded.

> /src/routes/**    /BUILD/api/zproutes
Place any +page.server.php files in the routes. 
Use zp_fetch_api() to handle the request (the rest..)

> /static/api/index.php     /BUILD/api/
* if path not exists - zeeltephp_loadEnv() autoinstalls this path.
* if you need your own api-path - you need to integrate zeelte-init.php.
<br>in mode dev     will use zeeltephp from @zeeltephp/dist/templates/static/api
<br>in mode build   will copy @zeeltephp/../api to /BUILD/api

> /dist/api         /BUILD/api 
> /static/api       /BUILD/api
This is where ZeeltePHP lives in your project and "acts" as "SvelteKit" imitating backend.
<br>BUILD: this folder is copied to /BUILD/api/ 

#### Methods, Components, Classes
> zp_fetch_api(fetch, url) 
Its a wrapper over Svelte's fetch which does 1 in all.
Acts as API between Svelte-project and .
<br>is using PUBLIC_ZEELTEPHP_BASE which points to respective /api/index.php

> ZP_ApiRouter (svelte, php)
On both sides, Svelte and PHP, this is the main API/router between your Svelte and PHP code.
If something does not work, use ZPDev to debug.

> Class ZP_EventDetails
tbd

> ZPDev.svelte
This component helps for general debugging of +page.server.php in any /routes/**/+page.svelte file to see if all is corretly setup to use ZeeltePHP.

> VarDump.svelte
Dumps the content of given variable


#### .env variables
The variables are auto-generated at runtime by zeeltephp_loadEnv() when no .env file is found.
<br>auto-generated variables are for when project is saved inside your [DOCUMENT_ROOT]/<your-project>
<br>when 
<br>For final production-builds set relative paths from its 'document_root' http://domain/<BUILD_DIR>
``` 
BUILD_DIR   path/to/build-output 
BASE        [DOCUMENT_ROOT]/path/to/build
PUBLIC_ZEELTEPHP_BASE=  (dev)http://localhost/path/to/project/static/api (production)[DOCUMENT_ROOT]/path/to/build/api
```

``` example.production
BUILD_DIR   = build-prod          # auto dev    (empty - not used)
                                  #      build  build-env
                                  # /path/to/htdocs/build-prod
BASE        = /build-output       # auto dev    (empty)
                                  #      build  /build-prod
                                  # 
PUBLIC_ZEELTEPHP_BASE = http:///path/to/build/api
                                  # auto dev    http://localhost/your-package/static/api
                                  #      build  http://localhost/build-env/api
                                  # http://domain.com/build-prod
```

``` DB-Providers
ZEELTEPHP_DATABASE_URL=mysql2://username:password@database
ZEELTEPHP_DATABASE_URL=wordpress://path/to/wp-load.php
ZEELTEPHP_DATABASE_URL=sqlite3://path/to/sqlitedb
```

#### PHP
> Errors / exceptions
PHP errors are logged to your-project `/static/api/zeeltephp/log`.

> global $zpAR
Holds the ZP_ApiRouter of PHP side. 
This helps to see if zpAR is correct between Svelte and PHP.

> global $env
Holds the PUBLIC .env variables. BUILD:/api/zeeltephp.env can hold private .env.variables as well.

> globals $db
With .env.ZEELTEPHP_DATABASEURL zeeltephp will load its default database connector/plugin.

### What happens in the background
### - or in case you don't want to use zp_fetch_api()
Dive into documention of src-code@/zp.fetch.api.js and ZP_Router.

## ReleaseNotes / Roadmap / ideas
See README.release-notes.md