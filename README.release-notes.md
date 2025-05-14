# v1.0.2 (rc1)   2025-05-14
* New Installation steps !
*        previous installation should remain working.
*        if not follow "reinstall" steps to reinstall ZeeltePHP.
* README         - meet v1.0.2 (rc1). 
* v102 core      - code cleanup, code documentation, improvements, removed code repeating (e.g. spoc), 
*                  cleaner cli-log output, internal variable renamings, vite-plugin, ZP_ApiRouter, ZP_EventDetails, zp_fetch_api(), and PHP-@/api/core
* ZP_Demo        - can be used as template, skeleton and debugger for your project.
*                   - copy&paste /zpdemo into your /routes, run npm run dev, open in browser.
*                   * Green lights ? yeah, minimal installation is done and ZeeltePHP environment is running (finds current route and .page.server.php).
*                   * Other lights ? well... minimal installation is correct? .. Well, check your svelte,php code. Is there a +.php file? ;-) 
* ZP_Dev         - works directly on ZP_Demo.
*                - debug your route /route/**/+page.svelte by placing <ZPDev />
*                - moved /src/routes/send-data and basics into ZPDev + ZP_Demo
*                - ZPDev is a full live demo of use-cases how to work with ZeeltePHP.
*                           or it can be used as initial helper/debugger to check if all setup is ok or to find the root-cause.
*                           ZP_Dev works directly on ZP_Demo.
*                - parts of /src/routes/ as documentation and live-demo moved into ZPDev as 'debugger-feature'
*                - action name,value editor to use ZPDev into any of your routes and test your php-code.
* ZP_ApiRouter-PHP    - fix to support grouped-routes /routes/(any)/..
* zeeltephp_loadEnv() - changed auto db-provider generation .. to .. and set as default (instead mysql).
*                       .. 'wordpress://../wordpress/' to 'wordpress://../../../wordpress/wp-load.php'


# v1.0.1 (rc1)   2025-05-07
* README         - re-written to meet v1.0.0 and v1.0.1 requirements, examples, installation-steps, code-cleanup, etc. 
*                - added new README.relaease-notes, -.project.project.env (renamed), -.project.pack-exposing
* ZPDev          - A Component helping debugging on +page.server.php files. 
* trustedDependencies and postinstall - is not required anymore, postinstall is now done via zeeltephp_loadEnv() at npm run;
* -- library self only -- 
* post-build      - added 'sameLine' for console.log
* zeelte-init.php - moved code to zp-bootstrap.php and zp-paths for cleanup and readability.
* import from 'zeeltephp' - project minimal-config as by README.project.pack-exposing.


# v1.0.0 (rc1)   2025-05-03 
* Initial v1.0.0 
* Focussing on documentation, stabilizing, and on roadmap-ideas if required.
* Based on un-released v0.


# Roadmap / ideas
* more documentation and examples 
* support other +.php files, +server,api,hooks
* support more event types at ZP_EventDetails
* support installation via tgz or Github-Releases or NPM-package
* load response data from zp_fetch_api() directly into $page.data or $page.form to use export let data and form;
* (done 25-04-01) Support for DB-MySQL
* (done 25-04-15) added post-build for builds
* (done 25-05-01) Install via bun add github
*                 Run as Vite/Svelte Plugin for development, static at /BUILD
*                 Postinstall to create +layout.js, /static/api/index.php
*                 Autoload and print .env variables
* (done 25-05-02) Install with demo/example 
*                 Zero config install with trustedDependencies, vite.config.js and svelte.config.js.
* (done 25-05-06) ZP_DEMO should be /src/routes/** as complete example, documentation and Live-app (Demo.svelte) within consumer project
*                 Live Demo and Debugger - use Demo.svelte to load Documentation and Debugger in consumer project.
*                 Integrate PostInstallation to zeeltephp_loadEnd() and so Postinstall and package.json/trustedDependencies is not required anymore.
* (done 25-05-07) add a debug-Component to use in lib and consumer project to see if all is up and running. -> ZPDev.svelte
* (done 25-05-10) move PHP error logging default to /static/api/log 
