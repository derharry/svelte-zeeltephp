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
		paths: {
			base: process.env.BASE
		}
	}
};
export default config;
