import fs from 'fs';
import path from 'path';
import { mkdir, cp } from 'node:fs/promises'


/**
 * uprints msg to console.log and sets the curser to start of last line to overwrite.
 * use console.log to overwrite this and start a new line.
 * @param {*} msg 
 */
export function console_log_sameline(msg) {
     if (process.stdout.isTTY) { // Only if terminal supports it
          process.stdout.write(msg);
          process.stdout.clearLine(0);
          process.stdout.cursorTo(0);
     } else {
          console.log(msg); // Fallback for non-TTY (e.g., redirected output)
     }
}


export function exec_task(title, name, todo, src, dst, filter, verbose = false) {
     try {
          const spacer = '  ';
          //console_log_sameline(spacer +` ⚙️ ${name} ${todo} ${dst}`);

          let pathRoot = process.cwd();
          let realSrc  = path.join(pathRoot, src);
          let realDst  = path.join(pathRoot, dst);

          let success = false;
          let message = '';

          switch (todo) {
               case 'mkdir':
                    console_log_sameline(spacer +' creating ' + dst);
                    fs.mkdirSync(realDst, { recursive: true });
                    success = fs.existsSync(realDst);
                    if (success)
                         message = 'created  ' + dst;
                    else message = 'could not create ' + dst;
               break;

               case 'copy':
                    console_log_sameline(spacer +'    copying  ' + name + '  ← ' + src); //' ' + src +
                    if (!fs.existsSync(realSrc)) {
                         message = 'could not find source ' + src;
                         break;
                    }
                    copyRecursiveSync(realSrc, realDst); //, filter);
                    success = fs.existsSync(realDst);
                    if (success)
                         message = 'copied   ' + (verbose ? name +'  ← '+src : name);
                    else message = 'could not copy ' + src;
               break;

               case 'file':
                    console_log_sameline(spacer +' saving   ' + name + ' ' + dst);
                    // Implement synchronous file write
                    fs.writeFileSync(realDst, src);
                    success  = fs.existsSync(realDst);
                    message  = (success ? 'created  ' : 'creation failed ') + dst;
               break;               
          }

          if (success)
               console.log(spacer, '✅', message);
          else console.log(spacer, '❌', message);

          return success;

     } catch (error) {
          console.error(spacer, '🐘❌ ZeeltePHP Plugin Error: ', error);
          throw error; // Ensure build fails on critical errors
     }
}

/**
 * copy a folder
 * @param {string} src 
 * @param {string} dest 
 * @param {callback_function} filter 
 */
export function copyRecursiveSync(src, dest, filter = () => true) {
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

/*
     try {
     } catch (error) {
          console.error('🐘❌ ZeeltePHP Plugin Error: ', error);
          throw error; // Ensure build fails on critical errors
     }

     function load_into_process_env(obj, prefix = '') {
          Object.entries(obj).forEach(([key, value]) => {
               process.env[`${prefix}${key}`] = value;
          });
     }

     function print_process_env(obj, prefix = '') {
          Object.entries(obj).forEach(([key, value]) => {
               key = prefix + key;
               console.log('@@@', key, process.env[key] );
          });
     }
*/