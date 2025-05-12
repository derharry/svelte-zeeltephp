import adapter from '@sveltejs/adapter-static';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';
import path from 'path';

// https://github.com/l-portet/svelte-switch-case
//import switchCase from 'svelte-switch-case';

const config = { 
	preprocess: [
		vitePreprocess(),
		//switchCase()
	],
     kit: { 
		adapter: adapter({
			pages: process.env.BUILD_DIR,
			assets: process.env.BUILD_DIR,
		}),
		prerender: {
			entries: ['*']
		},
		// set alias zeeltephp - so /routes/** can be used as zpdemo
		alias: {
			'zeeltephp': process.env.ZP_IS_SELFENV ? path.resolve('./src/lib/index.js') : 'zeeltephp'
		},
     }, 
	trailingSlash: 'always',
	// package: {} - config.package is no longer supported. See https://github.com/sveltejs/kit/discussions/8825 for more information.
};


export default config;
