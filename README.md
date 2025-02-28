# Svelte-ZeeltePHP

This repo holds some configurations as "base template" to use PHP as Svelte(Kit) backend.
Use load() or actions() within +page.server.php similar to SvelteKit +page.server.js or api/+server.js

Its intended for SSG (CRS/SPA) apps which will run on static-webhosting like apache-httpd when NodeJS is not possible, but we still want to develop the SvelteKit way. 


## Use of .env.build  and  process.env.VAR within svelte.config.js
Use .env.build or .env.build.any to use variables that affects the build-output for different projects.

### .env Examples
#### .env.development
bun run dev - and use PHP inside /src/routes/.../+page.server.php (similar as +page.server.js)
```
BUILD_DIR=build-dev
BASE=
PUBLIC_ZEELTEPHP_BASE=http://localhost/project-xyz/static/api/
ZEELTEPHP_DATABASE_URL=wordpress://../wordpress-project/
```

#### .env.build.xyz
bun run build --mode build.xyz
```
BUILD_DIR=../build-xyz
BASE=/build-xyz
PUBLIC_ZEELTEPHP_BASE=/build-xyz/api
ZEELTEPHP_DATABASE_URL=wordpress://../..
ZEELTEPHP_DATABASE_URL=mysql://<username>:<password>@<host>:<port>/<database_name>
```


### vite.config.js
this is the default vite.config.js - modified to use mode and load .env.build into process.env
```
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig, loadEnv } from 'vite';

export default defineConfig(({ mode }) => {
	console.log('Current mode:', mode); // Logs "build.zp" when --mode build.zp is used
	process.env = loadEnv(mode, process.cwd(),'');
	return {
		plugins: [sveltekit()]
	};
});
```

### svelte.config.js
now we can use the variables defined in .env as process.env.VAR
```
import adapter from "@sveltejs/adapter-static";
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

thats it to use "almost" out of the box bun run build --mode 'env'.

