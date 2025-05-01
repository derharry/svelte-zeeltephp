/**
 * Original file within the Zeelte-Lib-Project /lib/api/zeeltephp.api.js
 */
import { browser } from "$app/environment";
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
import { base } from "$app/paths";
import { ZP_ApiRouter } from "./class.zp.apirouter";
import { ZP_EventDetails } from "./class.zp.eventdetails";


export function zp_get_eventDetails(event) {
        return new ZP_EventDetails(event);
}


/**
 * 
 * @param {*} fetch Svelte's fetch
 * @param { ZP_ApiRouter ZP_EventDetails } urlOrRouter 
 * @param {*} data 
 * @param {*} method 
 * @param {*} headers 
 * @returns 
 */
export function zp_fetch_api(fetch, urlOrRouterOrEvent, data = undefined, method = undefined, headers = undefined) {
    const debug = false;
    try {
        const zpar = new ZP_ApiRouter(urlOrRouterOrEvent, data, method);
        if (debug) zpar.dump();

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
            if (debug) console.log(zpar.fetch_url, zpar.method, zpar.route, zpar.action, zpar.data);

            let fullResponse = null
    
            fetch(zpar.fetch_url, zpar.fetch_options)
                // send the fetch by ZP_ApiRouter
                .then(response => response.json())
                .then(data => {
                    console.log('data zp_fetch_api()', data.data, data.error, data)
                    //data is the ZeeltePHP/jsonResponse()
                    //data.data is the response of any +page.server.php
                    fullResponse = data;
                    if (data?.data) {
                        resolve(data.data || '') // if data.data == null -> set '' as empty fallback
                    }
                    else
                        resolve(data);
                })
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

