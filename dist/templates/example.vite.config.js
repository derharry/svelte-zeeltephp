import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';
import { zeeltephp } from '../vite-plugin/zeeltephp-vite-plugin.js';
//import { zeeltephp } from 'zeeltephp/vite-plugin';

export default defineConfig(({ mode }) => {
      return {
            plugins: [
                  sveltekit(), 
                  zeeltephp(mode)
            ]
      };
});