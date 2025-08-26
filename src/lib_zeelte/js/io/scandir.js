import path from 'path';
import fs   from 'fs';
import fsp  from 'fs/promises';
import { browser } from '$app/environment';

import { isDir }  from './dir.js';
import { isFile } from './file.js';

/***
 * - SSR only
 * @returns { name, type, isDir, isFile, matches }
 */
export function scandir(dir, options = {} ) {
     if (browser) return [] // prevent CSR
     options = {
          recursive:     false,  // bool, false none, true(down) or 'up' 
          maxDepth:     1,      // max levels down or up
          collectDirs:  true,   // bool or RegExp
          collectFiles: true,   // bool or RegExp
          debug:        false,  // bool
          xr_depth: 0,
          xr_parts: [],
          xr_path:  [],
          ...options
     }
     if (!isDir(dir)) {
          console.debug('#/scandir() path is no dir:', dir)
          return false;
     }
     let content = []
     const debug = options.debug
     try {
          debug && console.log('#scandir()', dir, options)
          const ioItems = fs.readdirSync(dir, { withFileTypes: true });
          for (const ioItem of ioItems) {

               let parts    = [...options.xr_parts, ioItem.name]; // Create new array with appended part
               let pathJoin = [...options.xr_path , ioItem.name]; // For full system path

               const item = { 
                    name    : ioItem.name.toString(),
                    type    : ioItem.isDirectory() ? 'dir' : (ioItem.isFile() ? 'file' : null),
                    isDir   : ioItem.isDirectory(),
                    isFile  : ioItem.isFile(),
                    path    : path.join(dir, ioItem.name),
                    parts   : parts,
                    route   : parts.join('/'),
                    matches : null
               }

               // collect Dirs
               if (item.isDir && options.collectDirs === true) {
                    debug && console.log('  -sd()', 'by all ', item.type, item.name)
                    content.push(item)
               }
               else if (item.isDir && options.collectDirs instanceof RegExp) {
                    item.matches = item.name.match(options.collectDirs)
                    if (item.matches) {
                         debug && console.log('  -sd()', 'by RegExp', item.type, item.name, options.collectDirs, item.matches)
                         content.push(item)
                    }
               }
               // collect Files
               if (item.isFile && options.collectFiles === true) {
                    debug && console.log('  -sd()', 'by all ', item.type, item.name)
                    content.push(item)
               }
               else if (item.isFile && options.collectFiles instanceof RegExp) {
                    item.matches = item.name.match(options.collectFiles)
                    if (item.matches) {
                         debug && console.log('  -sd()', 'by RegExp', item.type, item.name, options.collectFiles, item.matches)
                         content.push(item)
                    }
               }

               // collect Recursive down or up
               if (options.recursive == true && item.isDir) {
                    let newOptions = {
                         ...options,
                         xr_depth: options.xr_depth + 1,
                         xr_parts: parts,
                         xr_path:  pathJoin,
                    };
                    if (options.maxDepth == -1 || newOptions.xr_depth <= options.maxDepth) {
                         const subItems = scandir(item.path, newOptions);
                         if (subItems)
                              content.push(...subItems);
                    }
               }
               else if (options.recursive == 'up' && item.isDir) {
                    debug && console.log('  -sd()', '> recursive up  ')
               }
               /***/
               
          }
          return content;
     } catch (error) {
          debug && console.log(error)
          return false
     } finally {
          debug && console.log('/scandir()')
     }
}

export function buildTree(items) {
     const debug = false
     debug && console.debug('#scandir_toTreeview()')
     const tree = [];
     try {
          // Map: group label => group object for easy access
          const groupsMap = new Map(); 

          for (const item of items) {
               const parts = item.parts || [];

               if (parts.length === 0) continue;

               const groupLabel = parts[0]; // first part is group (e.g., '(1_introduction)')

               // Get or create group
               let group = groupsMap.get(groupLabel);
               if (!group) {
                    group = {
                    group_label: groupLabel,
                    pages: [],
                    };
                    groupsMap.set(groupLabel, group);
                    tree.push(group);
               }

               // If there's a second part, it's a page under the group
               if (parts.length > 1) {
                    const pageLabel = parts[1];
                    // Avoid duplicate pages
                    if (!group.pages.find(p => p.label === pageLabel)) {
                    group.pages.push({ label: pageLabel });
                    }
               }
          }
     } catch (error) {
          console.error(error)
     }
     debug && console.debug('/scandir_toTreeview()')
     return tree;
}

export function scandir_toTreeview(items) {
     // Index for fast lookup
     const lookup = new Map();

     // Prepare items with children
     for (const item of items) {
          item.children = [];
          // Key: full parts path joined, so each path is unique
          lookup.set(item.parts.join('/'), item);
     }

     // Root tree
     const tree = [];

     for (const item of items) {
          if (item.parts.length > 1) {
               // Parent's key is all parts except last
               const parentKey = item.parts.slice(0, -1).join('/');
               const parent = lookup.get(parentKey);
               if (parent) {
                    parent.children.push(item);
               } else {
                    // No parent found, put in root just in case
                    tree.push(item);
               }
          } else {
               // Top-level items (root directories/files)
               tree.push(item);
          }
     }

     return tree;
}


     /*
          const newParts = [...(options.xr_parts ?? item.parts), item.name];
          const newPath  = [...(options.xr_path ?? []), item.name];
          const subPath  = path.join(dir, ...newPath);
          if (isDir(subPath)) {
               let newOptions = {
                    ...options,
                    xr_parts: newParts,
                    xr_path:  newPath,
                    xr_depth: (options.xr_depth || 0) + 1,
               };
               item.parts = newParts;
               item.route = newParts.join('/');
               debug && console.log('  -sd()', '> recursive down', newParts.join('/'), subPath, newOptions.xr_depth, newPath);
               const subFiles = await scandir(subPath, newOptions)
                    subFiles.forEach(subItem => {
                    content.push(subItem);
               });
          }
          /*
          const recOpts = { ...options}
          options.xr_depth  = options?.xr_depth || 0
          options.xr_depth += 1
          options.xr_parts  = item.parts
          options.xr_path   = options?.xr_path  || []
          options.xr_path.push( item.name )
          const subPath  = path.join(dir, ...options.xr_path)
          const isSubDir = isDir(subPath)
          if (isSubDir) {
               options.xr_parts.push(item.name)
               item.route = item.parts.join('/')
               const subRoute = options.xr_path.join('/')
               debug && console.log('  -sd()', '> recursive down', subRoute, subPath, options.xr_depth, options.xr_path)
               const subFiles = await scandir(subPath, options)
               //debug && console.log('      subFiles ', subFiles)
               subFiles.forEach(subItem => {
                    
                    content.push(subItem)
               })
          }
     */
     

