import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';
import { zeeltephp } from '../../../zeeltephp/vite-plugin';

export default defineConfig(({ mode }) => {
      return {
            plugins: [
                  zeeltephp(mode),
                  sveltekit(),
            ]
      };
});