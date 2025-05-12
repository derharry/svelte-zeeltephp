import fs from 'fs';
import path from 'path';

// icons
// 🐘 ✅ ✔ ❌ 🚀 🚨 📁

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

