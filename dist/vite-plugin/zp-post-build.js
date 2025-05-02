//zp-build-main.js
import fs from 'fs';
import path from 'path';


export async function zeeltephp_postbuild() {
  try {
    console.log('🚀 ZeeltePHP - postbuild (closeBundle)');

    // Create base directories
    //await ensureDir(`${DIR_API}`);
    //await ensureDir(`${DIR_ROUTES}`);

    // PHP file operations
    const operations = [
      { src: process.env.ZP_PATH_API, dest: path.join(process.env.BUILD_DIR, 'api') },
      { src: process.env.ZP_PATH_ZPLIP, dest: path.join(process.env.BUILD_DIR, 'api/lib') },
      { src: process.env.ZP_PATH_ROUTES, dest: path.join(process.env.BUILD_DIR, 'api/routes') },
    ];
    operations.forEach(({ src, dest }) => {
      console.log(`   📁 Copying ${src} ${dest}`);
      if (!fs.existsSync(src)) {
        console.warn(`   ⚠️  Source missing: ${src}`);
        return;
      }
      if (!fs.existsSync(dest))
        fs.mkdirSync(dest, { recursive: true });
      copyRecursiveSync(src, dest, (file) => file.endsWith('.php'));
      console.log(`   ✅ Copied: ${src} → ${dest}`);
    });

    // Generate PHP .env
    const envFile = process.env.BUILD_DIR+'/api/zeeltephp/.env';
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