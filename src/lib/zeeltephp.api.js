/**
 * Original file within the Zeelte-Lib-Project /lib/api/zeeltephp.api.js
 */
import { browser } from "$app/environment";
import { base } from "$app/paths";
import { page } from "$app/state";
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";


export function zp_getApiRoute() {
      if (!browser) return;
      let url = ''
      try {
            // get the current route
            let route1 = page
            route1     = route1.replace(base, '');
            console.log('zp_getApiRoute/route1 :', route1, page);

            // bkp   
            let route2 = window.location.pathname;
            route2     = route2.replace(base, '');
            console.log('zp_getApiRoute/route2 :', route2);

            // set default path "/$base/api.php?route&"
            url = PUBLIC_ZEELTEPHP_BASE + '?'+ route2 +'&';
            console.log('zp_getApiRoute/url    :', url);
            
      } catch (error) {
            console.error({error});
      }
      return url;
}


export function fetch_api(fetch, action) {
      return new Promise((resolve, reject) => {
          const url = zp_getApiRoute();
          if (!url) {
              reject(new Error("API route is undefined"));
              return;
          }
          fetch(url)
              .then(response => response.json())
              .then(data => resolve(data))
              .catch(error => {
                  console.error('zeeltephp.api/fetch_api()', {error});
                  reject(new Error(error.message || 'Unknown error'));
              });
      });
  }
  