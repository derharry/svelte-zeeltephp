# .env*, process.env.VAR within svelte.config.js
Use variables from .env.dev, .env.build or .env.build.any for different project builds.

Stack:
      * SvelteKit 
      * SvelteKit Adapter-Static
      * .env*
      * svelte.config.js

To run:
      `bun run dev --mode dev`
      `bun run build --mode build`
      `bun run build.any --mode build.any`


#### +layout.js
activate SSG by adding /src/routes/+layout.js
```
export const prerender = true;
export const ssr = false;
export const csr = true;
export const trailingSlash = 'always';
```


#### .env.dev
we need /.env.dev for bun `run --mode dev`
and use PHP inside /src/routes/*/+page.server.php
```
BUILD_DIR=build-dev
BASE=
PUBLIC_ZEELTEPHP_BASE=http://localhost/project-xyz/static/api/
ZEELTEPHP_DATABASE_URL=wordpress://../wordpress-project/
```

#### .env.build.xyz
we need /.env.build or /.env.build.any for `bun run build --mode build.xyz`
```
BUILD_DIR=../build-xyz
BASE=/build-xyz
PUBLIC_ZEELTEPHP_BASE=/build-xyz/api
ZEELTEPHP_DATABASE_URL=wordpress://../../wordpress-project/
ZEELTEPHP_DATABASE_URL=mysql://<username>:<password>@<host>:<port>/<database_name>
```

#### vite.config.js
added loadEnv and modified default defineConfig to use mode and load the .env.build file into process.env
```
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig, loadEnv } from 'vite';

export default defineConfig(({ mode }) => {
      //console.log('Current mode:', mode); // Logs "build.zp" when --mode build.zp is used
      process.env = loadEnv(mode, process.cwd(),'');
      return {
            plugins: [sveltekit()]
      };
});
```

#### svelte.config.js
now we can use the variables defined in .env with process.env
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


Thats it to use "almost" out of the box bun run build --mode 'env'
