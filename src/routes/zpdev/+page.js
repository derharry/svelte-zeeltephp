import { ZP_ApiRouter, zp_fetch } from 'zeeltephp';

export async function load({ params, fetch, url }) {
      try {
            // show zpAR
            return;
            const zpAR_pageJS = new ZP_ApiRouter(url, undefined, undefined, true);
            console.log(zpAR_pageJS);
            const res_pageServer  = await zp_fetch(zpAR_pageJS);
            return {
                  '+page.js': 'ZPDdev load( )',
                  'zpAR_pageJS': zpAR_pageJS,
                  // return results from php 
                  ...res_pageServer
            }
      } 
      catch (error) {
            console.error(error);
            return {
                  '+page.js': 'error'+error.message
            }
      }
}

