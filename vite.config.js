import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig, loadEnv } from 'vite';
import {zeeltephp} from '$lib/vite-plugin/zeeltephp-vite-plugin.js';

export default defineConfig(({ mode }) => {
      console.log('Current mode:', mode); // Logs "build.zp" when --mode build.zp is used
      process.env = loadEnv(mode, process.cwd(),'');
      return {
            plugins: [sveltekit(), zeeltephp()]
      };
});