# Svelte-ZeeltePHP
# RC v0.1.0.12
Svelte + PHP <br>
PHP Backend for SvelteKit

## Install 
`bun add github:derharry/svelte-zeeltephp`

postinstall will create files if not exists - otherwhise copy them from dist/templates/ manually:
./src/lib/zplib         - for your lib php files (folder-only)
./src/routes/+layout.js - to run and build your application for adapter-static (file +layout.js)
./static/api/index.php  - to run your +page.server.php files (file static.api.index.php)

### .env.dev|build  - required for proper bun run dev and build
create the .env files and adjust package.json/scripts/ dev|build with --mode dev|build
and create the .env files based on config of zeelte --mode .env config (README.env.md)

### svelte.config.js
see dist/templates/example.svelte.config.js

### vite.config.js
see dist/templates/example.vite.config.js

## Stack usage
* SvelteKit 
* HTTPD on http://localhost 
* + PHP 
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

## Install (from rc1 - this steps are now done via postinstall)
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
> BUILD: this folder is copied to /BUILD/api/

/static/api/index.php
> default php start file of httpd-server
> zp_fetch_api() will request to here which includes any +page.server.php and load lib, zplib


### .env variables
BUILD_DIR
> path for build-output
BASE
> path where build will running. 
PUBLIC_ZEELTEPHP_BASE
> http://localhost/<path-to-project> or /path-to-build-on-httpd-server
ZEELTEPHP_DATABASE_URL
> ZeeltePHP supported DB-providers like Wordpress, MySQL, ...


### fetch_api( fetch , url )
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