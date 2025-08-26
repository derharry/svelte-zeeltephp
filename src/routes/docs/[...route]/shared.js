import { scandir } from "zeelte";

export function findRouteMDFile(routeName) {
     const escapedRouteName = routeName.toString().replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // 
     const regexPattern = escapedRouteName + '\\.MD$'; // escape dot before MD
     const files = scandir('./src/routes/docs/', { 
          recursive:     true,
          maxDepth:     -1,
          collectDirs:  false,
          collectFiles: new RegExp(regexPattern, 'i')
     })
     if (files.length > 0) {
          return files[0]
     }
     return []
}

export function findRouteTocJsonFile(routeName) {
     const escapedRouteName = routeName.toString().replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // 
     const regexPattern = escapedRouteName + '\\.toc.json$'; // escape dot before MD
     const files = scandir('./src/routes/docs/', { 
          recursive:     true,
          maxDepth:     -1,
          collectDirs:  false,
          collectFiles: new RegExp(regexPattern, 'i')
     })
     if (files.length > 0) {
          return files[0]
     }
     return []
}