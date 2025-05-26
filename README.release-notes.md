**v1.0.2 (rc1)**   2025-05-14
- New Installation steps
  - previous installation should remain working. If not follow "reinstall" steps to reinstall ZeeltePHP.
- README, meets v1.0.0 - 1.0.2 (rc1). 
- v102 core
  - code cleanup, code documentation, improvements, removed code repeating (e.g. spoc), 
  - cleaner cli-log output, internal variable renamings, vite-plugin, ZP_ApiRouter, ZP_EventDetails, zp_fetch_api(), and PHP-@/api/core
- ZP_Demo        
  - can be used as template, skeleton and debugger for your project.
  - copy&paste /zpdemo into your /routes, run npm run dev, open in browser.
  - Green lights ? yeah, minimal installation is done and ZeeltePHP environment is running (finds current route and .page.server.php).
  - Other lights ? well... minimal installation is correct? .. Well, check your svelte,php code. Is there a +.php file? ;-) 
- ZP_Dev         
  - works directly on ZP_Demo.
  - debug your route /route/--/+page.svelte by placing <ZPDev />
  - moved /src/routes/send-data and basics into ZPDev + ZP_Demo
  - ZPDev can be used as a full live demo or as initial helper/debugger to find the root-cause.
  - Works directly on ZP_Demo.
  - Added action name,value editor to use ZPDev in any route to test php-code.
- /src/routes/
   - parts of /src/routes/ as documentation moved into ZPDev as 'debugger-feature'.
- ZP_ApiRouter-PHP    
  - added support of Grouped-Routes /routes/(any)/..
- zeeltephp_loadEnv()
  - changed auto loaded db-provider to wordpess (instead mysql) and the path: 'wordpress://../wordpress/' to 'wordpress://../../../wordpress/wp-load.php'

**v1.0.1 (rc1)** 2025-05-07
- README
  - re-written to meet v1.0.0 and v1.0.1 requirements, examples, installation-steps, code-cleanup, etc. 
  - added new README.relaease-notes, -.project.project.env (renamed), -.project.pack-exposing
- ZPDev         
  - A Component helping debugging on +page.server.php files. 
- Installation
  - trustedDependencies and postinstall is not required anymore. Postinstall is now done via zeeltephp_loadEnv() at npm run;
- Internal 
  - post-build; added 'sameLine' for console.log
  - zeelte-init.php; moved code to zp-bootstrap.php and zp-paths for cleanup and readability.
  - import from 'zeeltephp';

**v1.0.1 (rc1)** 2025-05-03
- Initial v1.0.0, based on un-released v0
- upcoming: documentation, stabilizing, and on roadmap-ideas if required.
