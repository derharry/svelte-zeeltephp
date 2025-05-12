import { ZP_ApiRouter, zp_fetch_api } from 'zeeltephp';

export async function load({ params, fetch, url }) {
      try {
            // show zpAR            
            const zpAR_pageJS        = new ZP_ApiRouter(url, undefined, undefined, true);
            console.log(zpAR_pageJS);
            const res_pageServerPHP  = await zp_fetch_api(fetch, zpAR_pageJS);
            return {
                  '+page.js': '/load( zpdemo )',
                  // return results from php 
                  //'+page.server.php': res_php,
                  // for ZPDev we need zpAR at least exposed to the root.
                  'zpAR_pageJS': zpAR_pageJS,
                  'zpAR_php':    res_pageServerPHP.zpAR
            }
      } 
      catch (error) {
            console.error(error);
            return {
                  '+page.js': 'error'+error.message
            }
      }
}

