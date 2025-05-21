import { zeeltephp_loadEnv } from './zp-loadEnv.js';
import { zeeltephp_postbuild } from './zp-post-build.js';

// postbuild - shared state to avoid double execution of of postinstall at svelte-package
let adapterStaticRuntimes = 0;

/**
 * ZeeltePHP Vite Plugin
 * ---------------------
 * Integrates ZeeltePHP into Vite/SvelteKit projects by:
 * 1. Loading/generating environment variables
 * 2. Handling post-build operations for PHP integration
 * 
 * Usage in vite.config.js:
 * plugins: [zeeltephp(mode)]
 *
 * @param {string} mode - Vite mode (development/production)
 * @returns {import('vite').Plugin} Vite plugin object
 */
//replaces previous versions: @/postinstall, @/trustedDependencies, @/zeeltephp-post-install.sh
export function zeeltephp(mode) {
     try {
          // Phase: Load environment variables early in the build process
          // load environment variables here! because configResolved not happens at build time
          zeeltephp_loadEnv(mode);

          return {
               name: 'zeeltephp-vite-plugin',
               
               /**
                * Vite config resolved hook (optional future use)
                */
               configResolved(resolvedConfig) {
                    // Reserved for future configuration adjustments
               },

               /**
                * Post-build handler for PHP integration
                */
               closeBundle: {
                    sequential: true,
                    order: 'post',
                    async handler() {
                         // Phase : Run post-build operations after final bundle
                         if (++adapterStaticRuntimes === 2) {
                              // Prevent duplicate execution in multi-build scenarios
                              // (SvelteKit adapter-static typically calls closeBundle twice)
                              zeeltephp_postbuild();
                         }
                    }
               }
          }
     } catch (error) {
          console.error('🐘❌ ZeeltePHP Plugin Error: ', error);
          throw error; // Ensure build fails on critical errors
     }
}
