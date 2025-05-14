# ZeeltePHP (v1.0.2 rc1)
A SvelteKit-adapter-static 'PHP' plugin for projects using PHP-backend and use PHP like SvelteKit-files, `+page.server.php`.
* Svelte + PHP - combine best of both worlds in <your-project>.
* use ` + .php ` files inside your project as sveltekit's `+ server js|.ts`.
* develop almost as just SvelteKit native.
* port existing PHP-project into SvelteKit project structure, ready-to-port to SvelteKit-native. 
* port vice-verca - SvelteKit-native <-> SvelteKit-apapter-static + PHP. 
* ZeeltePHP acts as api/router/gateway between Svelte and PHP. (or imitates SvelteKit's backend)

## in short
* `npm run dev` and consume ` + .php´` files inside your `/src/routes` the same way as SvelteKit.
* `npm run build`, test the build, export/ftp to your production environment and call http://www.domain.com/<build>. 

## Instalation
1) create your SvelteKit-project via `npx sv create` including `adapter-static` into /htdocs or like http://locahost/<your-project>
2) install ZeeltePHP `npm add github:derharry/svelte-zeeltephp`, from `tgz` or copy `dist` to `/your-project/node-modules/zeeltephp`.
3) add `zeeltephp-vite-plugin`/`zeeltephp(mode)` to `vite.config.js`. 
``` // vite.config.js
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

4) run `npm run dev`  and dev should be running.  
* If not, check the cli output 🐘 ZeeltePHP and the path of `PUBLIC_ZEELTEPHP_BASE` meets meet your current dev-environment. It not, step 5 is required with .env.dev`PUBLIC_ZEELTEPHP_BASE` to your current dev-environment.
Hint - not existing paths are, if not exists, created:
```
/src/lib/zplib            # shared lib for your +.php files
/src/routes/+layout.js    # required by adapter-static
/static/api/index.php     # entry point of ZeeltePHP/Api
/.zp-log                  # PHP errors or your logs are output here.
```

5) for `npm bun build`, configure `svelte.config.js` to consume .env variables.
``` // svelte.config.js
import adapter from '@sveltejs/adapter-static';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	preprocess: vitePreprocess(),
	kit: {
		adapter: adapter({
			pages: process.env.BUILD_DIR,
			assets: process.env.BUILD_DIR,
		}),
		prerender: {
			entries: ['*']
		},
		paths: {
			base: process.env.BASE
		}
	},
	trailingSlash: 'always'
};
export default config;
```

and in your projects root place or configure your .env file
``` # .env.buid  (dev-env: .env.dev or .env.development)
BUILD_DIR               = build-env             #  path/to/build-output 
BASE                    = /path/to/build        # [DOCUMENT_ROOT]
PUBLIC_ZEELTEPHP_BASE   = (dev)http://localhost/path/to/project/static/api 
PUBLIC_ZEELTEPHP_BASE   = (dev)http://localhost/path/to/project/static/api 

PUBLIC_ZEELTEPHP_BASE   = /path/to/build/ (build)/[DOCUMENT_ROOT]/path/to/build/api
```


6) *optional* demo/debugging, copy&paste `/zpdemo/` into your `/src/routes/**` and see if your project is running in `dev`.
> green lights ? happy `SveltePHP-ing`
> other lights ? well ... include 6 and recheck 1-6.

7) if its still not working, check steps 1-6, feel free to contact me.

## Uninstall or Reinstall
1) *un-install* `npm remove zeeltephp`.
2) *un-install* manually remove paths created by post-install if you do not need them anymore.
3) *re-install* if required - move existing paths to let postinstaller re-create the paths.
4) *re-install* follow the install steps.


## Requirements
* Svelte 5 (^4.0 should work)
* SvelteKit ^2.0
* SvelteKit-Adapter-Static ^3.0
* HTTPD environment (apache, nginx, ...)
* PHP 8.3 (should work on ^8.0 (I will test it))

### Environments (examples as currently used)
Production
* https://www.example.com/<your-project> 
* HTTPd, PHP8, MySQL, ..

Development and testing
* http://localhost/**/<your-project>
* Windows       : bun, httpd 
* Windows + WSL : httpd / bun 
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
start using `zp_fetch_api()` and copy&paste `/zpdemo` within your /routes.

#### zeeltephp vite-plugin `zeeltephp(mode)`
* loads your .env-file and generates missing variables.  
* post-install - at `npm run dev` minimum required paths are, if not exist, created.
* post-build - after svelte-package, post-build finalizes the build with your PHP.

#### paths - development and build 
The following paths are auto-created by post-install. 

> /src/lib/zplib/  ->  /BUILD/api/zplib
Your shared PHP-lib - all files here are pre-loaded before +.php files are processed.

> /src/routes/**  ->  /BUILD/api/zproutes
Place any +.php files in the routes, same as SvelteKit-SSR +.js|.ts files.
You can place any other PHP files in routes, but you have to manually include them.

> /static/api/index.php  ->  /BUILD/api/index.php
In your project this file points to ZeeltePHP which runs from `@zeeltephp/dist/api`.
<br>In post-build the `@zeeltephp/dist/api/` folder is copied to /BUILD/api 

> /.zp-log  ->  /BUILD/api/log/
Any PHP errors will logged here.

#### .gitignore
you might want to exclude some folders.
```
# ignore PHP error folder
/.zp-log 
# or ignore *.log files
*.log
```

#### Methods, Components, Classes
> zp_fetch_api(fetch, router, dataOrEvent = undefined, method = undefined, headers = undefined)
You probably will use this method most of the time to request your +.php.
```
      handle_btnClick(event) {
            const promise = zp_fetch_api(fetch, event);
      }
      handle_formSubmit(event) {
            const promise = zp_fetch_api(fetch, event);
      }
      sendAnyData(event) {
            const promise = zp_fetch_api(fetch, event, myData);
      }
      load({url}) {
            // in +.page.js
            const promise = zp_fetch_api(fetch, url);
      }
```


> ZP_ApiRouter
Acts as router between Svelte and your PHP code.


> Class ZP_EventDetails
Collects data from EventTypes for action, value, data to send. 

> ZPDev.svelte
This component helps for general debugging of +page.server.php in any /routes/**/+page.svelte.
```
import { ZPDev } from "zeeltephp";
<ZPDev />
```

> VarDump.svelte
Dumps the content of given variable


#### .env config and variables
At runtime are auto-generated when no `.env file` is found or is missing. 
<br>At build they are saved to `/api/zeeltephp/.env` containing public from Vite, Svelte and some of ZP-isself. 
<br>For final production-builds set relative paths from its 'document_root' http://domain/<BUILD_DIR>

``` example.production
BUILD_DIR   = build-prod          # dev    (empty - not used)
                                  # build  build-env        /path/to/htdocs/build-prod
BASE        = /build-output       # dev    (empty)
                                  # build  /build-prod      /path/build-prod to running-directory
PUBLIC_ZEELTEPHP_BASE = http:///path/to/build/api
                                  # dev    http://localhost/your-project/static/api
                                  # build  http://localhost/build-env/api     http://domain.com/build-env
```

``` DB-Providers
ZEELTEPHP_DATABASE_URL=mysql2://username:password@database
ZEELTEPHP_DATABASE_URL=wordpress://path/to/wp-load.php
```

#### PHP
> Errors / exceptions
PHP errors are logged to `/.zp-log/`.

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