//package.json "postinstall": "bun run copy-files.js"
import { copy } from 'fs';
import path from 'path';

const sourceDir = './node_modules/';
const destDirs = ['./static/api', './static/'];

destDirs.forEach(destDir => {
  copy(sourceDir, destDir, { recursive: true }, (err) => {
    if (err) {
      console.error(`Error copying to ${destDir}:`, err);
    } else {
      console.log(`Files copied successfully to ${destDir}`);
    }
  });
});
