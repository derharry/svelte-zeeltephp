import { mkdir, copyFile } from 'node:fs/promises';
import fs from 'fs';
import path from 'path';
import { console_log_sameline, copyRecursiveSync } from './inc.tools.js';

// Configuration constants
const BUILD_CONFIG = {
     DIR_PERMISSIONS:  0o755,
     FILE_PERMISSIONS: 0o644,
     ENV_WHITELIST: ['BASE', 'PUBLIC_', 'ZEELTEPHP_']
};


// zp-post-build.js
/**
 * Post-Build Script for ZeeltePHP
 * -------------------------------
 * 1. Copies PHP files to build directory
 * 2. Generates PHP environment file
 * 3. Creates required directories
 */
// replaces @:/package.json/scripts-configs, @:/zp-post-build.sh
export async function zeeltephp_postbuild() {
try {
     console.log('🐘 ZeeltePHP - post-build ');

     // get BUILD_DIR from .env
     const BUILD_DIR = path.join(process.env.BUILD_DIR);

     // do not execute when BUILD_DIR is empty; prevent execution on specific restarts of Vite.
     if (BUILD_DIR == "") return;

     // copy file tasks for +.php and zplib
     const copy_php_tasks = [
          { src_path: process.env.ZP_PATH_API,    dest_path: BUILD_DIR +'/api' },
          { src_path: process.env.ZP_PATH_ZPLIB,  dest_path: BUILD_DIR +'/api/zeeltephp/zplib' },
          { src_path: process.env.ZP_PATH_ROUTES, dest_path: BUILD_DIR +'/api/zeeltephp/zproutes' },
     ];
     // process tasks
     copy_php_tasks.forEach(({ src_path, dest_path }) => {
          console_log_sameline(`   📁 Copying ${src_path} → ${dest_path}`);
          if (!fs.existsSync(src_path)) {
          console.warn(`     source missing: ${src_path}`);
          return;
          }
          if (!fs.existsSync(dest_path))
          fs.mkdirSync(dest_path, { recursive: true });
          copyRecursiveSync(src_path, dest_path, (file) => file.endsWith('.php'));
          console.log(`   ✔ copied  ${src_path} → ${dest_path}`);
     });

     // Generate PHP .env
     const envFile = BUILD_DIR+'/api/zeeltephp/.env';
     const envVars = Object.entries(process.env)
          .filter(([key]) => key.startsWith('BASE') ||
          key.startsWith('PUBLIC_') ||
          key.startsWith('ZEELTEPHP_'))
          .map(([key, value]) =>
          key === 'BASE' ? `PUBLIC_BASE=${value}` : `${key}=${value}`
          )
          .join('\n');
     fs.writeFileSync(envFile, envVars);
     console.log(`   ✔ created ${envFile}`);

     // 4. if not exist create /node_modules/zeeltephp/dist/api/log
     if (!fs.existsSync(BUILD_DIR +'/api/zeeltephp/log')) 
          await mkdir(BUILD_DIR +'/api/zeeltephp/log')

          console.log('🐘 ZeeltePHP postbuild complete');

     } catch (error) {
          console.error('🐘 ❌ ZeeltePHP postbuild ERROR: ', error.message);
          process.exit(1);
     }
}


