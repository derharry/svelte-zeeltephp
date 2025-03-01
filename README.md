# Svelte-ZeeltePHP
#	Svelte + PHP

This repo holds some configuration as "base template" to use PHP as Svelte(Kit) backend.
Use load() or actions() within +page.server.php similar to SvelteKit +page.server.js or api/+server.js

Its intended for SSG (CRS/SPA) apps which will run on static-webhosting like apache-httpd when NodeJS is not possible, but we still want to develop the SvelteKit way. 

Stack:
* SvelteKit (or Zeelte-LIB)
* PHP 8
* README.env.md for .env within svelte.config.js


have the PHP logic like
/src/routes/*/+page.server.php
```
<?php

      function load() {
            global $jsonResponse, $action, $server, $db, $env;
            // do anything
            $jsonResponse->data = [
                  'res_php'       => 'Hello from PHP/home',
                  'res_phpstatic' => lib_static()
            ];
            $jsonResponse->ok = true;
      }


      function actions() {
		// tbd
	}

?>
```

## Install
1. copy /static/api to you project
2. copy /static/api/zeeltephp/core/project_root-zeeltephp-post-build.sh to /project-root/zeeltephp-post-build.sh
3. follow steps of README.env.md
	set the variables of BUILD_DIR, BASE, PUBLIC_ZEELTEPHP_BASE, ZEELTEPHP_DATABASE_URL
4. modify package.json/scripts to `bun run dev|build` or manually `bun run dev|build --mode dev|build && ./zeeltephp-post-build.sh build`
	"dev": "vite dev --mode dev",
	"build": "vite build --mode build && ./zeeltephp-post-build.sh build",
5. 


#### example /src/routes/*/+page.js
```
//import zeeltephp.api - or use Zeelte-LIB
import { fetch_api } from "$lib/zeeltephp.api";

export async function load( { fetch, url } ) {
      const res_php = await fetch_api( fetch , url ); 
}
```

#### example /src/routes/*/+page.server.php
see into


## Descriptions

### Paths


### .env variables


### +page/load


### fetch_api( fetch , url )