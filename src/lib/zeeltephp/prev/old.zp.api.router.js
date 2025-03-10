import { browser } from "$app/environment";
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
import { base } from "$app/paths";
import { ZP_ApiRouter } from "../class.zp.apirouter";


export function zp_get_api_router(event, url) {}


export function zp_fetch_api_action(fetch, url, action, data) {
    // idea, use zp_fetch_api_action directly with the action - 
    // so the action does not need to be read from url - 
    // like adding action to page.search = zpar.route at handle_form(e) 
}

export function zp_fetch_api_event(fetch, url, event) {
    // idea, use function at handle_form like handle_form(e) => zp_fetch_api_event(fetch, url, e);
}



export function zp_fetch_api(fetch, urlOrRouter, data = undefined, method = undefined, headers = undefined) {
    try {

        // urlOrRouter can be already ZP_ApiRouter else create the ZP_ApiRouter
        const zpar = urlOrRouter instanceof ZP_ApiRouter ? url :
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
            if (zpar.headers) 
                zpar.headers = { ...zpar.headers, ...headers  }

            // debug
            console.log(zpar.fetch_url, zpar.method, zpar.route, zpar.action, zpar.data);

            fetch(zpar.fetch_url, zpar.fetchOptions)
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
