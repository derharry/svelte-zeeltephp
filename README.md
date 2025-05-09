# ZeeltePHP (v1.0.1 rc)
A library-plugin for SvelteKit adapter-static projects and PHP backend by using /src/routes/+page.server.php
<br>Svelte + PHP
<br>Svelte - frontend
<br>PHP - backend (imitating SvelteKit)


## Instalation
1) create your like `bunx sv create your-project` SvelteKit minimal, no js/ts-doc, sveltekit-adapter-static,  

2) add zeeltephp to your sveltekit-project including adapter-static
<br>`bun add github:derharry/svelte-zeeltephp`

3) configure `vite.config.js` so ZeeltePHP load .env-file variables
<br>add `zeeltephp(mode)` to vite.config.js
<br>example `dist/templates/example.svelte.config.js `

4) ready to go `npm run dev`

5) configure `svelte.config.js` to use .env-FILE variables
<br>example `dist/templates/example.svelte.config.js`

<p>** following is required for final production build build.xyz **</p>

6) change `package.json/scripts` to run via --mode .env file (.env.dev or .env.build)
<br>* optional!   (from v1.0 zeeltephp_loadEnv())
```
  "dev": "vite dev --mode dev",         
  "build": "vite build --mode build",  
  "build.xyz": "vite build --mode build.xyz",
```

6) create the .env files 
<br>! See `.env variables` 
<br>for final production-build (e.g. ftp to hosting)
* .env.dev         optional, zeeltephp_loadEnv() sets the required VARS automatically when .env.dev not exists 
* .env.build       optional, zeeltephp_loadEnv() sets the required VARS automatically when .env.build not exists
* .env.build.xyz   required for production build-output


## Requirements
* HTTPD environment (Apache, ...)
* PHP 8
* Svelte 5 
* SvelteKit 2
* Adapter-Static


## Environments (examples as currently used)
Production
* https://www.example.com/<your-project> 
* Linux, Apache2, MySQL, PHP8, ..

Development and Testing
* http://localhost/**/<your-sveltekit-project>
* Windows only  : bun, httpd 
* Windows + WSL : htpd / bun 
* httpd (dev local) = XAMPP from Apachefriends, PHP 5-8


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

``` /+page.server.php
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


## Description in a nutshell
`zp_fetch_api()` acts as API between your Svelte(Kit)-project and ZeeltePHP. 
<br>It handles SvelteKits load(), actions of Forms or Buttons, etc.. 
<br>Instead of repeating code like:
```
tbd paste example
``` 
you can use
```
tbd paste example
```
It sends the request as GET, POST as JSON or FormData as required which is all set by `ZP_ApiRouter`
<br>ZeeltePHP-api will handle the ZP_ApiRouter-request and load the files PHP-files from DEV:`/src/lib/zplib,/src/routes`,/src or BUILD:`/api/zplib/,/api/zproutes`. 


### idea of project (origins)
In SvelteKit-only projects i use `fetch_api(), ZP_EventDetails` to handle all kind of use-cases by SvelteKit Form-Actions, API/routes, etc to handle SvelteKit fetch request and respones streamlined in my project. 
<br>Impressed by SvelteKit, adapter-static and NodeJS/Bun, there are still web-projects that are hosted httpd-only.
<br>I want to be able that I can port former projects to be in an almost ready-to-switch-state between SvelteKit-only and PHP backend 
    by using SvelteKit's project file-structure and use +server.php files respective to +server.js|.ts and with almost no-effort. 
<br>By like re-route the $lib/zp_fetch_api to fetch_api() in 1 file and remove the +layout.js file.
<br>Done switching between SvelteKit-only or ZeeltePHP-project. 


### tl;tr
dive into /src/routes for documentation and examples

#### Paths - development and build environment
> /src/lib/zplib/   /BUILD/api/zplib
Place your library-PHP files here.
All PHP files will be autoload, so all methods will be available anytime.

> /src/routes/**    /BUILD/api/zproutes
Place any +page.server.php files in the routes. 
Use zp_fetch_api() to handle the request.

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
Acts as API between Svelte-project and PHP.
<br>is using PUBLIC_ZEELTEPHP_BASE which points to respective /api/index.php

> Class ZP_ApiRouter
tbd
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
PHP errors are logged to `@zeeltephp/dist/api/zeeltephp/logs`

> Database connectors
ZeeltePHP has DB-providers for Wordpress, MySQL and loaded when .env.ZEELTEPHP_DATASEURL is set.

> globals $zpAR
Holds the ZP_ApiRouter on PHP side. This helps to see if zpAR is correct between Svelte and PHP.

> globals $env
Holds the PUBLIC .env variables or BUILD:/api/zeeltephp.env 

> globals $db
.env.ZEELTEPHP_DATABASE_URL must be set.
<br>hen  is set in .env then this holds the connection and methods to use on DB.

### What happens in the background
> tbd

## Roadmap 
* more documentation and examples
* support /api/routes**/+server.php (or +api.php) (in development)
* support for +hooks.php, +server.php, ... 
* support more event types at ZP_EventDetails (SvelteKit Actions deep dive)
* support installation via tgz or Github-Releases or NPM-package
* load response data from zp_fetch_api() directly into $page.data or $page.form to use export let data and form;
* move PHP error logging to /static/api/log <- move @zeeltephp/dist/api/zeeltephp/log/
* (done 25-04-01) Support for DB-MySQL
* (done 25-04-15) added post-build for builds
* (done 25-05-01) Install via bun add github
*                 Run as Vite/Svelte Plugin for development, static at /BUILD
*                 Postinstall to create +layout.js, /static/api/index.php
*                 Autoload and print .env variables
* (done 25-05-02) Install with demo/example 
*                 Zero config install with trustedDependencies, vite.config.js and svelte.config.js.
* (done 25-05-06) ZP_DEMO should be /src/routes/** as complete example, documentation and Live-app (Demo.svelte) within consumer project
*                 Live Demo and Debugger - use Demo.svelte to load Documentation and Debugger in consumer project.
*                 Integrate PostInstallation to zeeltephp_loadEnd() and so Postinstall and package.json/trustedDependencies is not required anymore.
* (done 25-05-07) add a debug-Component to use in lib and consumer project to see if all is up and running. -> ZPDev.svelte