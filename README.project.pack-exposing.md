# Package Using Exposing 

### Use-Case-Story
As developer I have a SvelteKit-Library project 
<br>where I want to use 
<br>    import { any } from 'zeeltephp';
<br>    /src/lib/**     
<br>    /src/routes/** 
<br>in both self-Library and cp-Consumer-project context
<br>so that self:/src/routes/** acts as documentation and for developing.


### Minimal Library configuration 

``` $lib/index.js
export { libFunction } from './path/to/inc.tools.js';
export { default as libComponent } from './path/to/Component.svelte';
```

``` vite.config.js
instead of could alsy be done via json.package/scripts:"ZP_SELF=true && bun run build"
export default defineConfig(({ mode }) => {

	// ZeeltePHP - set flag ZP_SELF - to distinguish between context lib(src) or consumer-project(dist)
	process.env.ZP_SELF = true;

	return {
           // ZeeltePHP - set server/fs/allow list
		 // The request url "...\zeeltephp\dist\index.js" is outside of Vite serving allow list.
		 server: {
			  fs: {
			    // Allow serving files from one level up to the project root
			    allow: ['..', '../node_modules/zeeltephp'],
			  },
		 }
	};

});
```

``` svelte.config.js
const config = { 
     kit: { 
	     // ZeeltePHP - set alias library-project to simulate '@zeeltephp'
		alias: {
			'zeeltephp': process.env.ZP_SELF ? path.resolve('./src/lib/index.js') : ''
		}
     }
	// package: {} - config.package is no longer supported.
};
```

``` package.json
{
     "main": "./dist/index.js",
	"svelte": "./dist/index.js",
	"types": "./dist/index.d.ts",
	"type": "module",
	"exports": {
		".": {
			"types": "./dist/index.d.ts",
			"svelte": "./dist/index.js",
     		"import": "./dist/index.js"
		}
	}
}
```


## testing
### A   Library project
1.) add import { libFunction } from 'zeeltephp' and Component in /src/routes/+page.svelte 
2.) `bun run dev`
     <br>test in browser http://localhost:5143
3.) `bun run build`
     <br>test in browser http://localhost/zeeltephp/build
4.) `bun link`
     <br>to test dist in Consumer-project

### B  Consumer project 
1.) `bun link zeeltephp` to add local-zeeltephp/dist to consumer-project
2.) add import { libFunction } from 'zeeltephp' and Component in /src/routes/+page.svelte 
2.) `bun run dev `
     <br>test in browser http://localhost:5143
3.) `bun run build`
     <br>test in browser http://localhost/consumer-project/build

<p>If all works without errors - it's a sunny day, right ;-)</p>



## Additional
exclude ts file generation by svelte-package
```jsconfig.json
{	
	"exclude": [
	  "src/lib/templates/example.*",
	  "src/lib/zeeltephp/**",
	  "src/lib/zplib/**",
	  "node_modules/**"
	]
}
```