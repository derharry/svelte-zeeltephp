import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';
import { zeeltephp } from '$lib/vite-plugin/zeeltephp-vite-plugin.js';

export default defineConfig(({ mode }) => {

	// set ZP_SELF because of ZeeltePHP-context and not in consumer/dist
	process.env.ZP_SELF = true;

	return {
		 plugins: [
			  sveltekit(), 
			  zeeltephp(mode)
		 ],
		 // The request url "C:\Users\harry\xCode\www\svelte-zeeltephp\dist\index.js" is outside of Vite serving allow list.
		 server: {
			  fs: {
			    // Allow serving files from one level up to the project root
			    allow: ['..', '../node_modules/zeeltephp']
			  }
		 }
	};

});