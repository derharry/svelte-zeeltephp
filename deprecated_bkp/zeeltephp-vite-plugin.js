import { zeeltephp_postbuild } from './zp-post-build.js';
import { zeeltephp_loadEnv } from './zp-loadEnv.js';

/*
let config;
let buildDir = 'dist'; // Default fallback
let buildType = 'consumer'; // 'lib' | 'consumer' | 'dev'
let buildMode = 'production';
let consumerRoot = process.cwd();
let isBuildMode = false;
  console.log(' 🚀 ZeeltePHP - postbuild ');
  return {
    name: 'zeeltephp-vite-plugin',
    configResolved(resolvedConfig) {
      /*
      config = resolvedConfig;
      buildMode = config.mode;
      consumerRoot = config.root;
      isBuildMode = config.command === 'build';
      console.log(' config resolved ');
      // Detect build type
      if (dev) buildType = 'dev';
      else if (buildDir == 'dist') {

      }
      buildType = process.env.VITE_LIB_BUILD === 'true'
        ? 'lib'
        : (isBuildMode ? 'consumer' : 'dev');
      console.log(' buildType ', buildType);

      // Get build dir from environment
      buildDir = process.env.BUILD_DIR || config.build?.outDir || 'dist';
      console.log(' BUILD_DIR ', buildDir);
      * /
    },
  }
  */




/*
let buildMode = 'production';
let consumerRoot = process.cwd();
let isBuildMode = false;
let buildType = 'consumer'; // 'lib' | 'consumer' | 'dev'

return {
  name: 'zeeltephp-vite-plugin',
  configResolved(resolvedConfig) {
    config = resolvedConfig;
    buildMode = config.mode;
    consumerRoot = config.root;
    isBuildMode = config.command === 'build';

    // Detect build type
    buildType = process.env.VITE_LIB_BUILD === 'true' 
      ? 'lib' 
      : (isBuildMode ? 'consumer' : 'dev');

    // Set paths based on build type
    if (buildType === 'lib') {
      console.log('🏗️  Building LIBRARY package');
      process.env._ZP_PATH = {
        API: 'static/api',
        ZPLIB: 'src/lib/zplib',
        ROUTES: 'src/routes'
      };
    } else if (buildType === 'consumer') {
      console.log('🚀 Building CONSUMER project');
      process.env._ZP_PATH = {
        API: path.join(config.root, 'node_modules/zeeltephp/dist/api'),
        ZPLIB: path.join(config.root, 'src/lib/zplib'),
        ROUTES: path.join(config.root, 'src/routes')
      };
    } else {
      console.log('🔧 Running in DEV mode');
      process.env._ZP_PATH = {
        API: path.join(config.root, 'node_modules/zeeltephp/dist/api'),
        ZPLIB: path.join(config.root, 'src/lib/zplib'),
        ROUTES: path.join(config.root, 'src/routes')
      };
    }
  },
  closeBundle: {
    sequential: true,
    order: 'post',
    async handler() {
      if (!isBuildMode) return;
      
      console.log(`📦 Build type: ${buildType.toUpperCase()}`);
      console.log(`🔨 Mode: ${buildMode}`);
      
      if (buildType === 'lib') {
        console.log('📁 Processing library-specific build tasks');
        await zeeltephp_build('lib', consumerRoot);
      } else {
        console.log('📂 Processing consumer build tasks');
        await zeeltephp_build('consumer', consumerRoot);
      }
    }
  }
    */


/**
 * 
 //zeeltephp-vite-plugin.js

import { zeeltephp_build } from './zp-build-main.js';
//import { PATHS } from './zp-static-api.js';
import path from 'path';


export function zeeltephp() {
console.log(' ZeeltePHP ');
let config;
let buildMode = 'production'; // Default Vite mode
let consumerRoot = process.cwd(); // Consumer project root
let isBuildMode = false; // Track build/dev mode

return {
name: 'zeeltephp-vite-plugin',
configResolved(resolvedConfig) {
  console.log('    - config resolved ');
  config = resolvedConfig;
  buildMode = config.mode;
  consumerRoot = config.root;
  isBuildMode = config.command === 'build';
  const isLibBuild = process.env.VITE_LIB_BUILD === 'true';
  console.log('    - isBuildMode ',isBuildMode);
  console.log('    - consumerRoot ',consumerRoot);
  console.log('    - isLibBuild ',isLibBuild);
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
    if (!isBuildMode) return; // skip during dev mode
    //await zeeltephp_build(buildMode, consumerRoot);
  }
}
};
}
 * 
 */
