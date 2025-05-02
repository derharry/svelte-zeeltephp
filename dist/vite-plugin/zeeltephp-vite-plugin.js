import { zeeltephp_postbuild } from './zp-post-build.js';
import { zeeltephp_loadEnv } from './zp-loadEnv.js';

// Shared build completion state
let buildCompleted = false;

export function zeeltephp(mode) {
  try {
    //console.log(' 🚀 ZeeltePHP - loading vite plugin ');
    zeeltephp_loadEnv(mode);
    return {
      name: 'zeeltephp-vite-plugin',
      configResolved(resolvedConfig) {
        //console.log(' 🚀 ZeeltePHP - config resolved ');
      },
      closeBundle: {
        sequential: true,
        order: 'post',
        async handler() {
          // Prevent double execution
          if (!buildCompleted) {
            buildCompleted = true;
            return;
          }
          // Mark build as completed
          process.env.ZP_isBuild = true;
          //console.log('🚀 ZeeltePHP - closeBundle');
          zeeltephp_postbuild();
        }
      }
    }
  } catch (err) {
    console.error(' EEE ', err);
  }
}