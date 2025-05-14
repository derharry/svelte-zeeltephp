/**
 * Original file within the Zeelte-Lib-Project /lib/api/zeeltephp.api.js
 */
import { page } from "$app/state";
import { ZP_ApiRouter } from "./class.zp.apirouter.js" 
import { ZP_EventDetails } from "./class.zp.eventdetails.js"  


/**
 * alias for new ZP_EventDetails(event)
 * @param {*} event 
 * @returns ZP_EventDetails
 */
export function zp_get_eventDetails(event) {
    return new ZP_EventDetails(event);
}


/**
 * Use SvelteKit-native fetch and backend behaviour - resolve as @zeelte:fetch_api
 * (placeholder for new feature - port zeelte:fetch_api)
 */
export function fetch_api(fetch, dataOrEvent) {
    // to be used when switching to SvelteKit-native
}


/**
 * Fetch PHP-backend - resolve as @zeelte:fetch_api.
 * Overloading 
 * zp_fetch_api(fetch, Event [, Data])   -> parses Details eg Action, value, formData, Data ...
 * zp_fetch_api(fetch, Event, anyData)   -> get all from Event but data is overruled
 * zp_fetch_api(fetch, URLParams, anyEvent) -> tbd
 * zp_fetch_api(fetch, ZP_EventDetails, anyData)
 * zp_fetch_api(fetch, ZP_ApiRouter, anyData) -> hook into ZP_ApiRouter 
 * @param {*} fetch Svelte's fetch
 * @param { ZP_ApiRouter ZP_EventDetails string event URL } router
 * @param {*} dataOrEvent 
 * @param {string} method  override method used GET, POST, JSON
 * @param {object} headers { ...fetchOptions.headers }
 * @returns 
 */
// urlOrRouterOrEvent 
export function zp_fetch_api(fetch, router, dataOrEvent = undefined, method = undefined, headers = undefined) {
    const debug = false;
    try {
        // create the ApiRouter to defaults or take hooked ApiRouter
        const zpar = new ZP_ApiRouter(router, dataOrEvent, method);
        //if (debug) zpar.dump();

        // lets fetch 
        //   create and return the Promise that 
        //      handles the fetch by using ZP_ApiRouter
        return new Promise((resolve, reject) => {
            // we now always can use Promise  
            // for Svelte   {#await :then :catch}
            //     Button   promise={Promise}  e.g. ?/action, loading, saving, ..
            //              instead isLoading={ true | false }

            // we need the route - error
            if (!zpar.route) {
                reject(new Error("ZP: route is undefined"));
                return; // exit
            }

            // add custom headers to ZP_ApiRouter if not undefined
            //if (zpar.fetch_options.headers) 
            //    zpar.fetch_options.headers = { ...zpar.fetch_options.headers, ...headers  }

            // debug
            if (debug) zpar.dump();
            if (debug) console.log(zpar.fetch_url, zpar.method, zpar.route, zpar.action, zpar.data);

            let fullResponse = null

            // send the fetch to ZeeltePHP defined as from ZP_ApiRouter.
            //    then resolve the raw-json-response from PHP
            //    then resolve the json-response to @zeelte:api  and return to project
            //    catch error  and forward to project
            fetch(zpar.fetch_url, zpar.fetch_options)
                // resolve the json-response
                .then(response => response.json())
                // resolve the data to project
                .then(rawData => {
                    // received raw JSON response from ZeeltePHP as @zeelte:fetch_api
                    // push data to $page.data or $page.form or $page.error
                    if (rawData.ok && 'data' in rawData) {
                        // all ok - received 200 from php
                        //          received data response from +.php files
                        console.log('ok', rawData.data)
                        resolve(rawData.data);
                    }
                    else {
                        // thats a pitty - lets resolve an the raw response
                        //    might be a javascript failure or custom response
                        console.error(rawData);
                        resolve(rawData);
                    }
                    /*if (false) {
                        // roadmap feature $page
                        // resolve into $page.data or $page.form to imitate SvelteKit's behaviour (trigger) 
                        // if (debug) console.log('data zp_fetch_api()', data_api.data, data.error, data)
                        // # deparse zpAR, zpDB, zpARjs
                        /*
                        const zp_debug = {
                            zpAR_php : rawData?.zpAR,
                            zpAR_js  : rawData?.zpAR_js,
                            zpED_js  : rawData?.zpED_js,
                            zpDB     : rawData?.zpDB
                        }
                        delete rawData.zpAR;
                        delete rawData.zpAR_js;
                        delete rawData.zpED_js;
                        delete rawData.zpDB;
                    }*/
                })
                // catch errors
                .catch(error => {
                    console.error('zp_fetch_api/promise', { error });
                    reject(new Error(error.message || 'Unknown error'));
                });
            //end fetch
        })
    } catch (error) {
        console.error({ error });
    }
}
