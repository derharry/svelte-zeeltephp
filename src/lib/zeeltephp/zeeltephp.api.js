/**
 * Original file within the Zeelte-Lib-Project /lib/api/zeeltephp.api.js
 */
import { browser } from "$app/environment";
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
import { base } from "$app/paths";
//import { page } from "$app/state";
import { ZP_ApiRouter } from "./class.zp.apirouter";
import { ZP_EventDetails } from "./class.zp.eventdetails";


/**
 * 
 * @param {*} fetch 
 * @param { string | ZP_ApiRouter | ZP_EventDetails } urlOrRouter
 * @param {*} data 
 * @param {*} method 
 * @param {*} headers 
 * @returns 
 */
export function zp_fetch_api(fetch, urlOrRouterOrEvent, data = undefined, method = undefined, headers = undefined) {
    const debug = false;
    try {

        const zped = new ZP_EventDetails(urlOrRouterOrEvent);
        const zpar = new ZP_ApiRouter(urlOrRouterOrEvent, data, method);

        zpar.dump();

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
                reject(new Error("API route is undefined"));
                return; // exit
            }

            // add custom headers to ZP_ApiRouter if not undefined
            //if (zpar.fetch_options.headers) 
            //    zpar.fetch_options.headers = { ...zpar.fetch_options.headers, ...headers  }

            // debug
            if (debug) zpar.dump();

            //console.log(zpar.fetch_url, zpar.method, zpar.route, zpar.action, zpar.data);

            fetch(zpar.fetch_url, {...zpar.fetch_options})
                // send the fetch by ZP_ApiRouter
                .then(response => response.json())
                .then(data => resolve(data))
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



export function zp_fetch_api_action(fetch, url, action, data) {
    // idea, use zp_fetch_api_action directly with the action - 
    // so the action does not need to be read from url - 
    // like adding action to page.search = zpar.route at handle_form(e) 
}

export function zp_fetch_api_event_action(fetch, url, event, data = undefined) {
    // idea, use function at handle_form like handle_form(e) => zp_fetch_api_event(fetch, url, e);

    const zped = new ZP_EventDetails(event);
    const zpar = new ZP_ApiRouter(url, data)
    
}

  