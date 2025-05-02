import { loadEnv } from 'vite';
import path from 'path';


export function zeeltephp_loadEnv(mode) {
  try {
    // did we already loaded ZeeltePHP .env?
    // only once
    if (process.env?.ZP_MODE) return false;

    // load default vite/svelte --mode .ENV-file
    // required setup to use in all projects using ZeeltePHP like in vite.config.js
    process.env = loadEnv(mode, process.cwd(), '');

    // internal...
    // set internal vars for plugin
    process.env.ZP_CP   = process.env.npm_package_name == 'zeeltephp' ? 'zp' : 'cp' // self or consumer?
    process.env.ZP_MODE = mode == 'dev' || mode == 'development'; // --mode .env

    const ZP_MODE     = process.env.ZP_MODE;
    const DIR_BUILD   = process.env.BUILD_DIR || 'build-env';
    const DIR_PROJECT = path.basename(process.cwd());


    console.log(`🚀 ZeeltePHP - environment .env.${mode} dev:${ZP_MODE}`)
    //console.log('    :', process.env.ZP_CP, process.env.npm_package_name, `--mode env.${mode}`);
    //console.log('    :', process.env.ZP_MODE);

    // var, dev, build
    const ENV_MINIMUM_VARS = [
      ['BUILD_DIR', '', DIR_BUILD],
      ['BASE', '', `/${DIR_PROJECT}/${DIR_BUILD}`],
      ['PUBLIC_ZEELTEPHP_BASE',
        'http://localhost/' + DIR_PROJECT + '/static/api/',
        `/${DIR_PROJECT}/${DIR_BUILD}/api/`
      ],
      ['ZEELTEPHP_DATABASE_URL', 'wordpress://../wordpress-project/', 'mysql2://root@localhost/test']
    ];
    for (let [envvar, dev, build] of ENV_MINIMUM_VARS) {
      let value = process.env[envvar];
      if (value == undefined) {
        //console.log(' - ', envvar, 'd:', dev, 'b:', build);
        process.env[envvar] = ZP_MODE ? dev : build;
        value = process.env[envvar];
        console.log('  !', envvar, value);
      } else {
        console.log('   ', envvar, value);
      }
    }
    //set API required paths
    //set consumer by default
    process.env.ZP_PATH_API = './node_modules/zeeltephp/dist/api/';
    process.env.ZP_PATH_ZPLIB  = './src/lib/zplib/';
    process.env.ZP_PATH_ROUTES = './src/routes/';
    if (process.env.npm_package_name == 'zeeltephp') {
      // paths for self mode
      process.env.ZP_PATH_API    = './static/api/';
    }
  } catch (error) {
    console.error(' EEE ', error);
  }
}