// zeeltephp-vite-plugin
//      Load ZeeltePHP in your-project
//          a) load .env variables and if not present auto generate
//          b) hooks into vite:/closebundle to execute zeeltephp_postbuild()
//      issued by vite.config
//      replaces previous versions: @/postinstall, @/trustedDependencies, @/zeeltephp-post-install.sh
import { zeeltephp_loadEnv } from './zp-loadEnv.js';
import { zeeltephp_postbuild } from './zp-post-build.js';

// postbuild - shared state to avoid double execution of of postinstall at svelte-package
let adapterStaticRuntimes = 0;

export function zeeltephp(mode) {
  try {
    // use console.log for debugging cases
    //    -> first cli output is from zeeltephp_loadEnv()
    // console.log('🐘 ZeeltePHP '); // - loading vite plugin 

    // load environment variables here! because configResolved not happens at mode builde
    zeeltephp_loadEnv(mode);

    return {
      name: 'zeeltephp-vite-plugin',
      configResolved(resolvedConfig) {
        //console.log('🐘 ZeeltePHP - config resolved ');
      },
      closeBundle: {
        sequential: true,
        order: 'post',
        async handler() {
          //console.log(' 🐘 ZeeltePHP - closeBundle');
          if (++adapterStaticRuntimes == 2) {
            // postinstall
            //    run after svelte-package is finishened.
            //    svelte-package build client and server which each calls closeBundle() (2 times), 
            //    so lets hook into 2nd call and do postinstall  (avoid running multiple times or too early)
            zeeltephp_postbuild();
          }
        }
      }
    }
  } catch (error) {
    console.error('🐘❌ ', error);
  }
}
