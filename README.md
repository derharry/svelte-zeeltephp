# Svelte-ZeeltePHP (v1.0 rc)
Svelte + PHP 
<br>PHP Backend for SvelteKit


## Install 
6 steps to do

1) add zeeltephp to trustedDependencies in your package.json so it may exec postinstallion<br>
`"trustedDependencies": ["zeeltephp"],`

2) add zeeltephp to your package via<br>
`bun add github:derharry/svelte-zeeltephp`<br>
postinstall will create required paths and files if not exists.<br>
```
./src/lib/zplib           host your php-lib php files here  (mkdir only)
./src/routes/+layout.js   required by svelte:adapter-static (copyfile +layout.js)
./static/api/index.php    required by ZeeltePHP (mkdir and copyfile static.api.index.php)
```

3) configure `vite.config.js` to load zeeltephp(mode)<br>
add zeeltephp(mode) to `vite.config.js`<br>
see `dist/templates/example.svelte.config.js `for details

4) configure `svelte.config.js` to load --mode .env.FILE <br>
configure your dist/templates/example.vite.config.js

5) change `package.json/scripts` to run via --mode .env file (.env.dev or .env.build)
```
  "dev": "vite dev --mode dev",         //optional, from v1.0 zeeltephp_loadEnv() 
  "build": "vite build --mode build", 
  "build.xyz": "vite build --mode build.xyz",
```

6) create the .env files
* .env.dev         optional, zeeltephp_loadEnv() will be used 
* .env.build       required for local build-output and testing
* .env.build.xyz   required for hosting build-output and run 
BUILD_DIR=path for build-output
BASE=path where build will running. 
PUBLIC_ZEELTEPHP_BASE=dev:http://localhost/<path-to-project> public:/path-to-build-on-httpd-server
ZEELTEPHP_DATABASE_URL

## Stack usage (and developed on)
* HTTPD on http://localhost/<your-sveltekit-project>
*    DOCUMENT ROOT = /path/to/htdocs/<your-sveltekit-project>
* SvelteKit project
*    adapter-static
* Windows (only) + HTTPD (XAMPP from ApacheFriends) and bun run ANY
* Windows + WSL - where Windows-XAMPP and WSL bun run ANY
* HINT: 
*     your-project could can be placed and to be run in any depth of /htdocs/** 
*     

## +page.server.php logic - like /src/routes/**/+page.server.php
```PHP
<?php

      function load() {
            global $jsonResponse, $action, $server, $db, $env;
            // do anything
            $jsonResponse->data = [
                  'res_php'       => 'Hello from PHP/home',
                  'res_phpstatic' => lib_static()
            ];
            $jsonResponse->ok = true;
      }git


      function actions($action, $value) {
            global $jsonResponse, $action, $server, $db, $env;
		switch ($action) {
                  case 'FOO':
                              // do any..
                        break;
            }
	}

      // OR

      function action_FOO($value) {
            global $jsonResponse, $action, $server, $db, $env;
            // do any.. (this method is priorized over actions()!)
      }

?>
```

## how to use
1. see example logic above and place +page.server.php in any /src/routes/*
2. call zp_fetch_api() from +page.js or *.svelte 
3. use the response 
	* any like
	* {#await promise} .. {:then data} .. {:catch} .. {/await}
	* ..


## example load in /src/routes/*/+page.js
```JS
import { fetch_api } from "$lib/zeeltephp.api";

export async function load( { fetch, url } ) {
      const res_php = await fetch_api( fetch , url [, params ]); 
}
```

## example /src/routes/*/+page.server.php
> see example above


## Description

### tl;tr
> dive into /src which includes examples.

### Paths - development and build environment
/src/lib/zeeltephp/zp.fetch.api.js -> zp_fetch_api(fetch, url) 
> .env:PUBLIC_ZEELTEPHP_BASE is required -
> Acts as API between Svelte-project and PHP.
> Use it in +page.js|ts or *.svelte.
	
/src/lib/zplib/
> Place any custom library-PHP files here.
> All PHP files will be autoload.

/src/routes/*
> Place +page.server.php at any route.
> From +page.js or *.svelte call zp_fetch_api( fetch, url , params );

/static/api/ 
> This is the PHP (aka ZeeltePHP) folder which acts as server-side SvelteKit for backend.
> BUILD: this folder is copied to /BUILD/api/ and replaced by /dist/api

/static/api/index.php
> default php start file of httpd-server
> zp_fetch_api() will request to here which includes any +page.server.php and load lib, zplib


### .env variables
BUILD_DIR=path for build-output
BASE=path where build will running. 
PUBLIC_ZEELTEPHP_BASE=dev:http://localhost/<path-to-project> public:/path-to-build-on-httpd-server
ZEELTEPHP_DATABASE_URL=mysql2://username:password@database
ZEELTEPHP_DATABASE_URL=wordpress://path/to/wp-load.php

> ZeeltePHP supported DB-providers like Wordpress, MySQL, ...

### zp_fetch_api( fetch , url )
###   at +page/load ({ fetch, url })
> tbd

### What happens in the background
> tbd

## Roadmap ideas 
* support for +hooks.php, +server.php, ... including route-placements
* SvelteKit Actions deep dive
* (done 25-04-01) Support for DB-MySQL
* (done 25-04-15) added post-build for builds
* (done 25-05-01) Install via bun add github
* (done 25-05-01) Run as Vite/Svelte Plugin for development, static at /BUILD
* (done 25-05-01) Postinstall to create +layout.js, /static/api/index.php
* (done 25-05-01) Autoload and print .env variables
* Add installation via tgz or Github-Releases or NPM-package