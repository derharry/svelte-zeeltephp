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
	trailingSlash: 'always',
	// package: {} - config.package is no longer supported. See https://github.com/sveltejs/kit/discussions/8825 for more information.
};
export default config;
