# Svelte-ZeeltePHP
Svelte + PHP <br>
PHP Backend for SvelteKit

This repo holds some configuration as "base template" to use PHP as Svelte(Kit) backend.
Use load() or actions() within +page.server.php similar to SvelteKit +page.server.js or api/+server.js

Its intended for SSG (CRS/SPA) apps which will run on static-webhosting like apache-httpd when NodeJS is not possible, but we still want to develop the SvelteKit way. 

### Stack:
* SvelteKit (or Zeelte-LIB)
* HTTPD on http://localhost 
* 	+ PHP 8 
* or XAMPP (like ApacheFriends)


## have the PHP logic like /src/routes/*/+page.server.php
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


      function actions() {
		// tbd
	}

?>
```
or 
```PHP
<?php

	// do any PHP

?>
```
or both.


## how to use
1. see example logic above and place +page.server.php in any /src/routes/*
2. call fetch_api() from +page.js or *.svelte 
3. use the response 
	* any like
	* {#await promise} .. {:then data} .. {:catch} .. {/await}
	* ..


## Tested and developed on
Windows + XAMPP as http://localhost/any-project
WSL-Ubuntu + BUN

any-project can be any sub-folder to the SvelteKit-project. 
The builds run completly on any httpd/root or /any-folder 


## Install
1. copy /static/api to you project
2. copy /static/api/zeeltephp/core/project_root-zeeltephp-post-build.sh to /project-root/zeeltephp-post-build.sh
3. follow steps of README.env.md
	set the variables of BUILD_DIR, BASE, PUBLIC_ZEELTEPHP_BASE, ZEELTEPHP_DATABASE_URL
4. modify package.json/scripts/`vite dev|build` or manually `bun run dev|build --mode dev|build && ./zeeltephp-post-build.sh build`
	"dev": "vite dev --mode dev",
	"build": "vite build --mode build && ./zeeltephp-post-build.sh build",
5. ..tbd


#### example load in /src/routes/*/+page.js
```JS
import { fetch_api } from "$lib/zeeltephp.api";

export async function load( { fetch, url } ) {
      const res_php = await fetch_api( fetch , url [, params ]); 
}
```

#### example /src/routes/*/+page.server.php
> see example at intro


## Description

### tl;tr
> dive into /src which includes examples.

### Paths - development and build environment
/src/lib/zeeltephp.api.js -> fetch_api(fetch, url) 
> ` standalone - Can be saved to any path. (origin Zeelte-LIB) `
> .env:PUBLIC_ZEELTEPHP_BASE is requied -
> Acts as API between Svelte-project and PHP.
> Use it in +page.js|ts or *.svelte.
	

/src/lib/zplib/
> ` optional, but recommended to use (in cases of like backup the /src-folder and not also /static) `
> You can place any library-PHP files here.
> If this folder exists ZeeltePHP will auto-load all PHP files to be available in any +page.server.php route.

/src/routes/*
> Place +page.server.php at any route.
> From +page.js or *.svelte call fetch_api( fetch, url , params );

/static/api/ 
> This is the PHP (aka ZeeltePHP) folder which acts as server-side SvelteKit for backend.
> BUILD: this folder is copied to /BUILD/api/

/static/api/index.php
> ` default php start file of httpd-server `
fetch_api() will request to here which includes any +page.server.php and load lib, zplib

/static/api/lib/
> Same as zplib, but zplib is preferred to use.
> Use your lib-file here or in zplib, or use both. 
> But in case of both, path-to/lib-file.php must be unique!
> BUILD: /lib/zplib/ will be copied into this folder - and priorized if already exists here (replaced).

/static/api/routes/*/
> You can place +page.server.php files here, at /src/routes, or both.
> But in case of both, /path-route/+page.server.php must be unique!
> BUILD: /src/routes/* will be copied into this folder - and priorized if already exists here (replaced).


### .env variables
BUILD_DIR
> path for build-output
BASE
> path where build will running. 
PUBLIC_ZEELTEPHP_BASE
> http://localhost/<path-to-project> or /path-to-build-on-httpd-server
ZEELTEPHP_DATABASE_URL
> ZeeltePHP supported DB-providers like Wordpress, MySQL, ...
> Currently only wordpress://path-to-wordpress-project-root


### fetch_api( fetch , url )
###   at +page/load ({ fetch, url })
> tbd


### What happens in the background
> tbd


## Roadmap ideas 
* support for +hooks.php, +server.php, ... including route-placements
* SvelteKit Actions deep dive
* Run as Vite/Svelte Plugin for development, static at /BUILD
* Support for DB-MySQL
