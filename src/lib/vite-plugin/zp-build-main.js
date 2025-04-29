//zp-build-main.js
import fs from 'fs';
import path from 'path';


export async function zeeltephp_build(mode, srcRoot) {
    try {
        console.log(`🚀 ZP Build (${mode}) → ${srcRoot}}`);
        const BUILD_DIR  = process.env.BUILD_DIR || './build';
        const DIR_API    = BUILD_DIR +'/api/lib';
        const DIR_ROUTES = BUILD_DIR +'/api/lib';
        const ENV_PATH   = BUILD_DIR +'/api/zeeltephp/.env';

        console.log(`🚀 ZP Build  ISSTandALone(${process.env.VITE_LIB_BUILD}`);

        console.log(`   mode        ${mode}`)
        console.log(`   srcRoot     ${srcRoot}`)
        console.log(`   BUILD_DIR   ${BUILD_DIR}`)
        console.log(`   DIR_API     ${DIR_API}`)
        console.log(`   DIR_ROUTES  ${DIR_ROUTES}`)

        // Create base directories
        //await ensureDir(`${DIR_API}`);
        //await ensureDir(`${DIR_ROUTES}`);

        // PHP file operations
        const operations = [
            { src: process.env._ZP_PATH.API,    dest: path.join(BUILD_DIR, 'api') },
            { src: process.env._ZP_PATH.ZPLIB,  dest: path.join(BUILD_DIR, 'api/lib') },
            { src: process.env._ZP_PATH.ROUTES, dest: path.join(BUILD_DIR, 'api/routes') },
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
        const envVars = Object.entries(process.env)
            .filter(([key]) => key.startsWith('BASE') || 
                                key.startsWith('PUBLIC_') || 
                                key.startsWith('ZEELTEPHP_'))
            .map(([key, value]) => 
                key === 'BASE' ? `PUBLIC_BASE=${value}` : `${key}=${value}`
            )
            .join('\n');
		if (!fs.existsSync(path.dirname(ENV_PATH))) 
			fs.mkdirSync(path.dirname(ENV_PATH), { recursive: true });
		fs.writeFileSync(ENV_PATH, envVars);
		console.log(`   ✅ Generated: ${ENV_PATH}`);

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