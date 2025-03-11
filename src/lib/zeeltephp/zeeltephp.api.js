/**
 * Original file within the Zeelte-Lib-Project /lib/api/zeeltephp.api.js
 */
import { browser } from "$app/environment";
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
import { base } from "$app/paths";
//import { page } from "$app/state";
import { ZP_ApiRouter } from "./class.zp.apirouter";
import { ZP_EventActionDetails } from "./event.helper";


export function zp_getApiRoute(url) {
    try {
        
    } catch (error) {
        console.error({error});
    }
    finally {
        return urlnew;
    }
}


/**
 * 
 * @param {*} fetch 
 * @param { string | ZP_ApiRouter | ZP_EventActionDetails } urlOrRouter
 * @param {*} data 
 * @param {*} method 
 * @param {*} headers 
 * @returns 
 */
export function zp_fetch_api(fetch, urlOrRouter, data = undefined, method = undefined, headers = undefined) {
    try {

        // urlOrRouter can be already ZP_ApiRouter else create the ZP_ApiRouter
        const zpar = urlOrRouter instanceof ZP_ApiRouter ? urlOrRouter : 
                        new ZP_ApiRouter(urlOrRouter, data, method);


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
            zpar.dump();

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

    const zped = new ZP_EventActionDetails(event);
    const zpar = new ZP_ApiRouter(url, data)
    
}

  