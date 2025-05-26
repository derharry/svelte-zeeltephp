import fs from 'fs';
import path from 'path';

/**
 * Writes a message to the current console line, overwriting previous content.
 * Useful for progress updates or status messages.
 * Falls back to console.log for non-TTY environments.
 * @param {string} msg - The message to display.
 */
export function console_log_sameline(...args) {
     const msg = args.map(String).join(' ');
     if (process.stdout.isTTY) { 
          // Only if terminal supports it
          // print msg to cli and sets the curser to start of last line to overwrite.
          process.stdout.write(msg);
          process.stdout.clearLine(0);
          process.stdout.cursorTo(0);
     } else {
          // Fallback for non-TTY (e.g., redirected output)
          console.log(msg); 
     }
}

/**
 * Executes a single file system task synchronously: create directory, copy, or write file.
 * @param {string} name - Task name for logging. 
 * @param {string} todo - Task type: 'copy', 'mkdir', or 'file'.
 * @param {string} src  - Source path or file content (for 'file').
 * @param {string} dst  - Destination path.
 * @param {function|string} filter - Optional filter for copying files. Function or extension string.
 * @param {boolean} [verbose=false] - If true, logs more details.
 * @returns {boolean} True if task succeeded, false otherwise.
 */
export function exec_task(name, todo, src, dst, filter, verbose = false) {
     const spacer = '  ';
     try {
          //console_log_sameline(spacer +` ⚙️ ${name} ${todo} ${dst}`);
          let pathRoot = process.cwd();
          let realSrc  = path.join(pathRoot, src);
          let realDst  = path.join(pathRoot, dst);

          let success = false;
          let message = '';

          switch (todo) {
               case 'mkdir':
                    console_log_sameline(spacer, ' creating ', dst);
                    fs.mkdirSync(realDst, { recursive: true });
                    success = fs.existsSync(realDst);
                    if (success)
                         message = 'created  ' + dst;
                    else message = 'could not create ' + dst;
               break;

               case 'copy':
                    console_log_sameline(spacer, '    copying  ', name, '  ← ', src); //' ' + src +
                    if (!fs.existsSync(realSrc)) {
                         message = 'could not find source ' + src;
                         break;
                    }
                    copyRecursiveSync(realSrc, realDst, filter);
                    success = fs.existsSync(realDst);
                    if (success)
                         message = 'copied   ' + (verbose ? name +'  ← '+src : name);
                    else message = 'could not copy ' + src;
               break;

               case 'file':
                    console_log_sameline(spacer, ' saving   ', name, ' ', dst);
                    // Implement synchronous file write
                    fs.writeFileSync(realDst, src);
                    success  = fs.existsSync(realDst);
                    message  = (success ? 'created  ' : 'creation failed ') + dst;
               break;

               case 'default':
                    message = `unsupported task type: ${todo}`;
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
 * Recursively copies a directory or file, applying a filter if provided.
 * @param {string} src - Source file or directory path.
 * @param {string} dest - Destination file or directory path.
 * @param {function} [filter] - Optional filter function (srcPath) => boolean.
 */
export function copyRecursiveSync(src, dest, filter = () => true) {
     const stats = fs.statSync(src);
     if (stats.isDirectory()) {
          // Ensure destination directory exists
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
          // Overwrite files if they exist
          fs.copyFileSync(src, dest, fs.constants.COPYFILE_FICLONE);
     }
}

     /*
     // icons
     // 🐘 ✅ ✔ ❌ 🚀 ⚠️ 🚨 📁 ⚙️ ✔️ ← →

     try {
     } catch (error) {
          console.error('🐘❌ ZeeltePHP Plugin Error: ', error);
          throw error; // Ensure build fails on critical errors
     }

     /**
      * Loads all key-value pairs from an object into process.env, with optional prefix.
      * @param {Object} obj - Object with env variables.
      * @param {string} [prefix=''] - Optional prefix for env variable names.
      * /
     export function load_into_process_env(obj, prefix = '') {
     Object.entries(obj).forEach(([key, value]) => {
          process.env[`${prefix}${key}`] = value;
     });
     }

     /**
      * Prints all key-value pairs from an object as they appear in process.env.
      * @param {Object} obj - Object with env variables.
      * @param {string} [prefix=''] - Optional prefix for env variable names.
      * /
     export function print_process_env(obj, prefix = '') {
     Object.entries(obj).forEach(([key, value]) => {
          key = prefix + key;
          console.log('@@@', key, process.env[key]);
     });
     }

*/
