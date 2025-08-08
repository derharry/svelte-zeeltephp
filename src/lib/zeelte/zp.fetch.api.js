/**
 * ZeeltePHP API Client
 * Provides Svelte/SvelteKit integration helpers for ZeeltePHP backend communication.
 */
import { json } from "@sveltejs/kit";
import { ZP_ApiRouter } from "./class.zp.apirouter.js" 
import { ZP_EventDetails } from "./class.zp.eventdetails.js"  

/**
 * Creates a ZP_EventDetails object from a browser event.
 * @param {*} event - The browser event (e.g., from a form or button)
 * @returns {ZP_EventDetails} Parsed event details
 */
export function zp_get_eventDetails(event) {
    return new ZP_EventDetails(event);
}

/**
 * Fetches data from the ZeeltePHP backend (PHP API) and resolves the response. 
 * For most use-cases you will just use 
 *      .svelte  : zp_fetch_api(fetch, event);
 *      +page.js : zp_fetch_api(fetch, url);
 * where param-router will be parsed by ZP_ApiRouter() and ZP_EventDetails().
 * Basic idea
 *      router can be anything to automatically support all kinds of 
 *      Events,Objects,etc to not repeat the same steps over and over.
 * 
 * Roadmap idea:
 *      Resolve the response directly into Svelte $page.data, $page.form, $page.error, etc...(Trigger SvelteKit's behaviour)
 *      until then this is equivalent to:
 *         SvelteKit:   fetch()
 *         SveltePHP:   data = zp_fetch()
 * 
 * Overloads:
 *   zp_fetch_api(fetch, Event [, ..])             AnyEvent - will be parsed by ZP_EventDetails
 *   zp_fetch_api(fetch, URL|URLParams   [, ..])   Will be parsed by ZP_EventDetails
 *   zp_fetch_api(fetch, ZP_ApiRouter    [, ..])   If you created ZP_ApiRouter earlier.
 *   zp_fetch_api(fetch, ZP_EventDetails [, ..])   If you created ZP_EventDetails earlier.
 *   ... more tbd
 * 
 *   *not yet* zp_fetch_api(fetch, string URL [, Data]) If your route or GET string (in near future: prefix + to fetch from +api.php).
 *   ... more tbd
 * 
 * @param {Function} fetch - SvelteKit's fetch function (include from +page.js or +page.svelte) (connot be imported seperatly)
 * @param {ZP_ApiRouter|ZP_EventDetails|Event|URL|URLParams|string} router - Router, event, or URL describing the request
 * @param {*}      [data] - Optional, force data to send with the request
 * @param {string} [method] - Optional, force used HTTP method (GET, POST, etc.)
 * @param {object} [headers] - Optional, additional headers for the request
 * @returns {Promise<any>} Resolves the backend response or the response object on error
 */
export function zp_fetch_api(fetch, router, data = undefined, method = undefined, headers = undefined, debug = true) {
    //const debug = debug;
     try {
        //-- forward to new zp_fetch
        //return zp_fetch(router, { data, method, headers, debug });
        //debug && console.clear()
        debug && console.log('#zp_fetch_api()');

        // Create the API router object (parse router/data/method)
        const zpar = new ZP_ApiRouter(router, data, method, debug);
        //debug && zpar.dump();

        return new Promise((resolve, reject) => {

            // Ensure a route is set
            if (!zpar.route) {
                reject(new Error("ZP: route is undefined"));
                return; 
            }

            // Optionally merge custom headers (currently not implemented)
            // if (zpar.fetch_options.headers && headers)
            //     zpar.fetch_options.headers = { ...zpar.fetch_options.headers, ...headers };
            //debug && console.log(zpar.fetch_url, zpar.method, zpar.route, zpar.action, zpar.data);

            // Perform the fetch to ZeeltePHP backend
            fetch(zpar.fetch_url, zpar.fetch_options)
                .then(response => response.json()) // Resolve the json-response
                .then(json => {
                    // -- idea: Push data to $page.data or $page.form or $page.error
                    // 1.0.4 - just return the JSON response
                    resolve(json);
                    /*
                        // previous <1.0.3
                        // If backend returned { ok: true, data: ... }, resolve with data
                        if (json !== null) {
                            // Received data response from +.php
                            // -- debug && console.log('ok', rawData.data);
                            resolve(rawData.data);
                        } 
                        // Otherwise, resolve the raw response (may be an Error-Notification, ..)
                        else {
                            resolve(json);
                            // -- debug && console.error(rawData);
                            // -- idea: Push data to $page.data or $page.form or $page.error
                        }
                    */
                })
                .catch(error => {
                    console.error('zp_fetch_api/promise', { error });
                    reject(new Error(error.message || 'Unknown error'));
                });
            //fetch()
        })
    } catch (error) {
        console.error({ error });
    }
}



/**
 * Fetches data from the ZeeltePHP backend (PHP API) and resolves the response. 
 * The route can be anything like Events, string. 
 * The options can be all options as normal `fetch` with some additional for ZeeltePHP.
 * 
 * For most use-cases you will just use in:
 *      Component:   zp_fetch_api(event, {options});
 *      +page.js :   zp_fetch_api(url  , {options});
 * 
 * This is equivalent to:
 *      SvelteKit:   nothing-default-behaviour or `fetch(route, options)`
 *      SveltePHP:   `data = zp_fetch()` / to directly put it into `export let data`;
 * 
 * Usage examples:  
 *      See repo:/src/routes/(examples)/
 *
 * Method overloads
 * @param {Event | ZP_ApiRouter | ZP_EventDetails | URL | URLParams | string | undefined
 * }                route       ... Router, event, or URL describing the request
 *                              if its a string then the +page
 * @param { }       options     any fetch-options and some additional
 * 
 * dd@param {object}          router        
 * @param {*}      [data] - Optional, force data to send with the request
 * @param {string} [method] - Optional, force used HTTP method (GET, POST, etc.)
 * @param {object} [headers] - Optional, additional headers for the request
 * @returns {Promise<any>} Resolves the backend response or the response object on error
 * 
 *  Context API
 * @param {String}          url      /api/demo/    to your /src/routes/api/demo/+server.php file.
 * @param {String}          method      GET, POST, PUT, PATCH, DELETE, HEAD   to use for API request. 
 * @param {*}               data        *optional*  data you want to send
 * @param {null|object}     headers     *optional*  additional headers for the request
 * @param {Boolean}         verbose     *optional*  to activate debug-output messages in browsers console. 
 * 
 */
export function zp_fetch(route, options = { 
    method:  'POST', 
    data:    undefined, 
    headers: {}, 
    debug:   true
}) {
    try {
        const defOptions = {
            method: 'POST',
            debug: true
        }
        const zpOptions = { ...defOptions, ...options  }
        const debug     = zpOptions.debug ?? false
        //zpOptions.fetch ? fetch = zpOptions.fetch : fetch

        //debug && console.clear()
        debug && console.log('# zp_fetch()')
        debug && console.log('    ', {zpOptions})
        debug && console.log('    ', {zpOptions})

        const zpar = new ZP_ApiRouter(route, zpOptions.data, zpOptions.method, debug, zpOptions);

        // -- moved inside promise 2025.08.02
        //if (!zpar.route) {
        //    reject(new Error("ZP: route is undefined"))
        //    return
        //}

        return new Promise((resolve, reject) => {
            debug && zpar.dump();

            if (!zpar.route) {
                reject(new Error("ZP: route is undefined"))
                return
            }

            // Perform the fetch to ZeeltePHP backend
            // -- idea: Resolve the response directly into Svelte $page.data, $page.form, $page.error, etc...(Trigger SvelteKit's behaviour)
            fetch(zpar.fetch_url, zpar.fetch_options) 
                .then(response => {
                    console.log(response.headers);
                    const contentLength = response.headers.get('Content-Length')
                    if (contentLength && Number(contentLength) > 0) 
                         return response.json()
                    else return response.headers // GET, HEAD
                })
                .then(jsonData =>  {
                        //let data = jsonData?.data
                        //let form = jsonData?.form
                        //({ data, form, promise } = zp_fetch_api());
                        resolve(jsonData) // directly return JSON response from 1.0.4
                }) 
                .catch(error => {
                    console.error({ error })
                    reject(new Error(error.message || 'Unknown error'))
                })
        })
    } catch (error) {
        console.error({ error })
    } finally {
        console.log('/ zp_fetch()')
    }
}

