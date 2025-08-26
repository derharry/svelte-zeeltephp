//vite.config.js
import { sveltekit }    from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';
import { zeeltephp }    from './src/lib/vite-plugin/zeeltephp-vite-plugin.js';

export default defineConfig(({ mode }) => {

	// set ZP_SELF because of ZeeltePHP-context and not in consumer/dist
	process.env.ZP_IS_SELFENV = true;

	return {
		plugins: [
			zeeltephp(mode),
			sveltekit()
		],
		vite: {
			ssr: {
				noExternal: ['zeelte']
			}
		},
		 server: {
			// todo: still required?
			// # The request url "C:\Users\harry\xCode\www\svelte-zeeltephp\dist\index.js" is outside of Vite serving allow list.
			// # Allow serving files from one level up to the project root
			fs: {
				allow: ['..', '../node_modules/zeeltephp']
			}
		 }
	};
});