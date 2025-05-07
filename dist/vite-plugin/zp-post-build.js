//zp-build-main.js
import { mkdir, copyFile } from 'node:fs/promises'
import fs from 'fs';
import path from 'path';


export async function zeeltephp_postbuild() {
  try {
    //console.log('🚀 ZeeltePHP - postbuild (closeBundle)');

    // Create base directories
    //await ensureDir(`${DIR_API}`);
    //await ensureDir(`${DIR_ROUTES}`);

    const BUILD_DIR = path.join(process.env.BUILD_DIR);

    // PHP file operations
    const operations = [
      { src: process.env.ZP_PATH_API,    dest: BUILD_DIR +'/api' },
      { src: process.env.ZP_PATH_ZPLIB,  dest: BUILD_DIR +'/api/zeeltephp/zplib' },
      { src: process.env.ZP_PATH_ROUTES, dest: BUILD_DIR +'/api/zeeltephp/zproutes' },
    ];
    operations.forEach(({ src, dest }) => {
      logSameLine(`   📁 Copying ${src} → ${dest}`);
      if (!fs.existsSync(src)) {
        console.warn(`     Source missing: ${src}`);
        return;
      }
      if (!fs.existsSync(dest))
        fs.mkdirSync(dest, { recursive: true });
      copyRecursiveSync(src, dest, (file) => file.endsWith('.php'));
      console.log(`   ✅ Copied: ${src} → ${dest}`);
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
    console.log(`   ✅ Generated: ${envFile}`);


    
    // 4. if not exist create /node_modules/zeeltephp/dist/api/log
    if (!fs.existsSync(BUILD_DIR +'/api/zeeltephp/log')) 
      await mkdir(BUILD_DIR +'/api/zeeltephp/log')

    console.log('✅ ZeeltePHP build completed');
  } catch (error) {
    console.error('❌ ZeeltePHP build failed:', error.message);
    process.exit(1);
  }
}


function copyRecursiveSync(src, dest, filter = () => true) {
  const stats = fs.statSync(src);

  if (stats.isDirectory()) {
    // 🚨 Force create directory (even if exists)
    if (!fs.existsSync(dest))
      fs.mkdirSync(dest, { recursive: true });

    fs.readdirSync(src).forEach(child => {
      copyRecursiveSync(
        path.join(src, child),
        path.join(dest, child),
        filter
      );
    });
  } else if (filter(src)) {
    // 🚨 Force overwrite files
    fs.copyFileSync(src, dest, fs.constants.COPYFILE_FICLONE);
  }
}

function logSameLine(msg) {
    process.stdout.clearLine(0);
    process.stdout.cursorTo(0);
    return;
  if (process.stdout.isTTY) { // Only if terminal supports it
    process.stdout.clearLine(0);
    process.stdout.cursorTo(0);
    process.stdout.write(msg);
  } else {
    console.log(msg); // Fallback for non-TTY (e.g., redirected output)
  }
}