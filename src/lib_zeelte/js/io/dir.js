import path from 'path';
import fs   from 'fs';
import fsp  from 'fs/promises';

/**
 * dir does not exist or error
 * @param {string} path 
 * @returns boolean
 */
export function isDir(path, consoleError = false) {
     //console.log('#isDir() ', path)
     try {
          const stats = fs.statSync(path);
          return stats.isDirectory();
     } catch (error) {
          consoleError && console.error(error)
          return false;
     }
     //console.log('/isDir() ', path)
}
