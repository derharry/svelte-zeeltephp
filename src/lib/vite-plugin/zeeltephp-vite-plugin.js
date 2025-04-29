//zeeltephp-vite-plugin.js

import { zeeltephp_build } from './zp-build-main.js';
//import { PATHS } from './zp-static-api.js';
import path from 'path';


export function zeeltephp() {
  let config;
  let buildMode = 'production'; // Default Vite mode
  let consumerRoot = process.cwd(); // Consumer project root
  let isBuild = false; // Track build/dev mode

  return {
    name: 'zeeltephp-vite-plugin',
    configResolved(resolvedConfig) {
      config = resolvedConfig;
      buildMode = config.mode;
      consumerRoot = config.root;
      isBuild = config.command === 'build';
      const isLibBuild = process.env.VITE_LIB_BUILD === 'true';
      if (isLibBuild) {
        process.env._ZP_PATH = {}
        process.env._ZP_PATH.API = 'static/api';
        process.env._ZP_PATH.ZPLIB = 'src/lib/zplib';
        process.env._ZP_PATH.ROUTES = 'src/routes';
      } else {
        process.env._ZP_PATH = {}
        process.env._ZP_PATH.API = path.join(config.root, 'node_modules/zeeltephp/dist/api');
        process.env._ZP_PATH.ZPLIB = path.join(config.root, 'src/lib/zplib');
        process.env._ZP_PATH.ROUTES = path.join(config.root, 'src/routes');
      }
    },
    closeBundle: {
      sequential: true,
      order: 'post',
      async handler() {
        if (!isBuild) return; // skip during dev mode
        await zeeltephp_build(buildMode, consumerRoot);
      }
    }
  };
}