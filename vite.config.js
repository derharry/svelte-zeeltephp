import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';
import { zeeltephp } from '$lib/vite-plugin/zeeltephp-vite-plugin.js';

export default defineConfig(({ mode }) => {
      return {
            plugins: [
                  sveltekit(), 
                  zeeltephp(mode)
            ]
      };
});