/**
 * Original file within the Zeelte-Lib-Project /lib/api/zeeltephp.api.js
 */
import { browser } from "$app/environment";
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
import { base } from "$app/paths";
//import { page } from "$app/state";


export function zp_getApiRoute(url) {
      if (!browser) return;
      // set default path "/$base/api.php?route&"
      let urlnew = PUBLIC_ZEELTEPHP_BASE + '?%&$';
      const res = {
        url:    PUBLIC_ZEELTEPHP_BASE,
        route:  undefined,
        action: undefined,
        value:  undefined,
        params: '',
        method: 'GET'
      }
      try {
            const debug = false;
            if (debug) console.log(' zp_getApiRoute() ');
            //if (debug) console.log('  ', 'route1', page.route);          // page is current route - we need new route
            //if (debug) console.log('  ', 'route2', page.url.pathname);   // page is current route - we need new route
            if (debug) console.log('  ', 'base  ', base);        // passed param url is new route
            if (debug) console.log('  ', 'route3', url.pathname);        // passed param url is new route
            if (debug) console.log('  ', 'route4', window.location.pathname); // passed param url is new route
            if (debug) console.log('  ', 'route5', url);                 // passed param url is string and new route
            if (debug) console.log('  ', 'params5', url.searchParams.toString());                 // passed param url is string and new route
            if (debug) console.log('  ', 'params5', url.search);                 // passed param url is string and new route

            if (typeof url === 'string')
                res.url = url
            else if (url?.pathname) {
                res.route  = url.pathname.replace(base, '',)
                res.params = url.searchParams.toString()
            }

            //urlnew = PUBLIC_ZEELTEPHP_BASE + '?'+ route +'&';
            urlnew = urlnew.replace('%', res.route);
            urlnew = urlnew.replace('$', res.params);
            if (debug) console.log('  new url', urlnew);
      } catch (error) {
            console.error({error});
      }
      finally {
            return urlnew;
      }
}


export function zp_fetch_api(fetch, url, params = undefined) {
    //console.log(' zp_getApiRoute/fetch_api() ', url);
    const url_fetch = zp_getApiRoute(url);
    return new Promise((resolve, reject) => {
        if (!url_fetch) {
            reject(new Error("API route is undefined"));
            return;
        }
        fetch(url_fetch)
            .then(response => response.json())
            .then(data => resolve(data))
            .catch(error => {
                console.error('zeeltephp.api/fetch_api()', {error});
                reject(new Error(error.message || 'Unknown error'));
            });
    });
}
  