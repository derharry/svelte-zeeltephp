//zpfetch.js

/**
 * ZeeltePHP API Client
 * Provides Svelte/SvelteKit integration helpers for ZeeltePHP backend communication.
 */
import { writable        } from "svelte/store";
import { ZP_ApiRouter    } from "$lib/class.zp.apirouter.js" 
import { EventDetails    } from "zeelte";


export const statuscode = writable(0)
export const data  = writable({})
export const form  = writable({})
export const error = writable({})

console.log('Initializing zeeltephp-fetch stores:', { data, form, error, statuscode });
/**
 * returns EventDetails  from a browser event.
 * @param   {*} event - any Dom Event
 * @returns {EventDetails} Parsed event details
 */
export function zp_get_eventDetails(event) {
    return new EventDetails(event);
}

/**
 * *deprecated*  zp_fetch() is the new method to prefer. 
 * For backwards compability zp_fetch_api() forwards to zp_fetch() and will remain at least for next 2 updates.
 * 
 * Overloads:
 *   zp_fetch_api(fetch, Event [, ..])             AnyEvent - will be parsed by EventDetails
 *   zp_fetch_api(fetch, URL|URLParams   [, ..])   Will be parsed by EventDetails
 *   zp_fetch_api(fetch, ZP_ApiRouter    [, ..])   If you created ZP_ApiRouter earlier.
 *   zp_fetch_api(fetch, EventDetails [, ..])   If you created EventDetails earlier.
 * 
 * @param {Function} fetch - SvelteKit's fetch function (include from +page.js or +page.svelte) (connot be imported seperatly)
 * @param {ZP_ApiRouter|EventDetails|Event|URL|URLParams|string} router - Router, event, or URL describing the request
 * @param {*}      [data] - Optional, force data to send with the request
 * @param {string} [method] - Optional, force used HTTP method (GET, POST, etc.)
 * @param {object} [headers] - Optional, additional headers for the request
 * @returns {Promise<any>} Resolves the backend response or the response object on error
 */
export function zp_fetch_api(fetch, router, data = undefined, method = undefined, headers = undefined, debug = true) {
    console.debug(' ! zp_fetch_api is * deprecated ')
    console.debug(' ! change to zp_fetch() ')
    return zp_fetch(router, { data, method, headers, debug, fetch });
}


/**
 * Fetches data from ZeeltePHP, resolves the response and forwards the data.
 * The response will be destructed into Stores $data, $form, $error.
 * 
 * For most use-cases you will just use:
 *   In a Component:           zp_fetch(event, {options})
 *   In +page.js/load({url}):  zp_fetch(url  , {options})
 * 
 * This is equivalent to:
 *      SvelteKit:   nothing as SvelteKit reacts on Events or `fetch(input, options)`
 *      SveltePHP:   `promise = zp_fetch()`     `promise = fetch()`
 *                   `data = await zp_fetch()`  `data = await fetch()`
 * 
 * Usage examples:  
 *      See repo:/src/routes/(usage_examples)/
 * 
 * Basic idea
 *      router can be anything to automatically support all kinds of request-Types.
 * 
 * Roadmap idea:
 *      Resolve the response directly into Svelte $page.data, $page.form, $page.error, etc...(Trigger SvelteKit's behaviour)
 *      until then this is equivalent to:
 *         SvelteKit:   fetch()
 *         SveltePHP:   data = zp_fetch()
 * 
 * @param {string | Event | EventDetails | ZP_ApiRouter | URL | URLParams | undefined} route
 *  if `string`           this is the path +server.php, or, if starting with https:// its a normal fetch to the endpoint URL.
 *  if `Event`            any DOM-events, parsed and destructured by `EventDetails`.
 *  if `EventDetails`  instance of, if you want to change attributes.
 *  if `ZP_ApiRouter`     instance of, if you want to change attributes.
 *  if `URL`              instance of. Like load({url}) in +page.js.
 *  if `URLParams`    instance of Svelte/URLParams
 *  if `undefined`    the request is the current route and will execute load() in +layout|page.server.php in route.
 * @param {Object}   [options                 ]  - Optional fetch configuration.
 * @param {string}   [options.method  = 'POST']  - force HTTP method to use (e.g., 'GET', 'POST', 'PUT'). Note: default GET when route starts with http(s)://.
 * @param {*}        [options.data            ]  - override data-payload, e.g. instead of the data-payload by EventDetails.
 * @param {Object}   [options.headers = {}    ]  - Additional request headers.
 * @param {boolean}  [options.debug   = false ]  - If true, logs debug information.
 * @returns {Promise<any>} A promise resolving to Stores `$data`, `$form`, and `$error`.
 */
export function zp_fetch(route, options = { 
    method:  'POST', 
    data:    undefined, 
    headers: {}, 
    debug:   false
}) {
    let debug = false
    try {
        const defaultOptions = {
            method: 'POST',
            debug: false
        }
        const zp_fetch_options = { ...defaultOptions, ...options  }
        debug = zp_fetch_options.debug ?? false

        debug && console.clear()
        debug && console.log('# zp_fetch()', debug)

        return new Promise((resolve, reject) => {

            /*
            if (/^https?:\/\//.test(route)) {
                if (zp_fetch_options?.fetch)
                    return zp_fetch_options.fetch(route, options)
                else
                    return fetch(route, options)
            }
            */

            debug && console.log('    route:           :', route)
            debug && console.log('    zp_fetch_options :', {zp_fetch_options})
            const zpar = new ZP_ApiRouter(route, zp_fetch_options.data, zp_fetch_options.method, debug, zp_fetch_options);
            debug && zpar.dump();

            if (!zpar.route) {
                reject(new Error("route is undefined"))
                return
            }

            let fetchFn = zp_fetch_options.fetch ?? fetch;

            fetchFn(zpar.fetch_url, zpar.fetch_options) 
                .then(response => {
                    // -- if (contentLength && Number(contentLength) > 0) 
                    // -- else return response.headers // GET, HEAD
                    statuscode.set(response.status);
                    return response.json()
                })
                .then(jsonData =>  {
                    if (jsonData?.zpxc && jsonData?.zpxc == 'page') {
                        // deparse Context-Layer +layout|page.server.php
                        data.set (jsonData.data  ?? null);
                        form.set (jsonData.form  ?? null);
                        error.set(jsonData.error ?? null);
                        jsonData = jsonData.data
                    }
                    else {
                        data.set(jsonData);
                    }
                    resolve(jsonData)
                })
                .catch(error => {
                    console.error({ error })
                    reject(new Error(error.message || 'Unknown error'))
                })
        })
    } catch (error) {
        console.error({ error })
    } finally {
        debug && console.log('/ zp_fetch()')
    }
}