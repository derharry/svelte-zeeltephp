import { base } from "$app/paths";
import { page } from '$app/state';
import { dev }  from "$app/environment";
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";

/**
 * Parse Svelte/$app/page.
 * ZPPHP destructs this as ?/route/&?/action=value&query_params
 */
export function parse_routingFromSveltePage() {
     try {
          // set default current route 
          // use ZP_EventDetails to get the details :-)
          this.route  = page.url.pathname+'/';
          const ed = new ZP_EventDetails(page.url.searchParams);
          //this.route  = ed.route;
          this.action = ed.action;
          this.value  = ed.value;
          this.data   = page.params;
          this.fetch_query = page.url.search
          //console.log(page.url);
          //console.log('dataxxxx', this.data, JSON.stringify(this.data))
          this.message = 'parse_routingFromSveltePage'
          this.message = ed;
     } catch (error) {
          //this.message = error;
          console.log(error);
     }
}

/**
 * return the real used route by ZeeltePHP (spoc/icq)
 * @param {bool} returnRoutes be default false to return zp_route: string
 * @returns string or KeyValueoObjectList 
 */
export function zp_page_route(returnRoutes = false) {
     let zp_route = '';     
     if (true) {
          // main() v1.0.2
          // fast lane, use 
          //        page.url.pathname (returns the path (also mousehover/preloading) )
          //        page.route.id     (reads from current store of page (might be 1 step behind from actual routeing) )
          // Mr.Route should be found by now
          zp_route = page.url.pathname ||  page.route.id; // if this is going to be a 2-liner, then push it to EventDetails or ApiRouter
          
          
          if (zp_route instanceof String) {
               // !! tmpFix-001-v103-Routing-zp_route
               // Svelte should do this, but doesn't
               // PHP does now the counterpart.
               zp_route = zp_route.replace(base, '');  // remove BASE on builds 
          }
          // -- pitfalls
          //      +page.js         example.com   use load({url})
          //      +page.js         ApiRouter is 1-route behind - URL is not deparsed, preloading works with page.url.pathname)
          //      page.routing, page.url          one is always most current ! 
     } else {
          // keep backup to might re-start the search...
          // main() <=v1.0.1
          // the zp_route is now set by current page routing
          if (returnRoutes) {
               return zp_search_for_mrRoute(zp_route);
          }
          //    set the real used zp_route (we found Mr.Route)
          //    +page.js has to use load({url})
          //    // -- place in class -> the route still my be overruled at post-processing Events, Forms, or ZP_Router
          //
          if (false) {
               //debug
               const routes = zp_page_route(true);
               console.log(' !! zp_route ', zp_route);
               console.log(routes);
          } 
     }
     // return the zp_route
     return zp_route;
}


export function zp_search_for_mrRoute(zp_route = '') {
     /*
          ** the search to Mr.Route **
               in dev and build
               and in self or consumer project                
               // -- in dev   - page.route.id includes (sub)/sub/..
               // -- in build - page.url.pathname - base = /sub/.. 
               // -- const   zp_route = page.url.pathname + page.url.search;
          correct 2025-05-11  did we finally found Mr.Route?
               page.route.id - base             = BASE/(sub)/sub/.. (manually include / at route.id)
               page.route.id.replace(base, '')  = (sub)/sub/..
          route with no subpaths: let sv_route = page.url.pathname;  
          route with    subpaths: let sv_route = page.route.id;  
          // -- page?.route.id+'/'; // todo: page.url.pathname or page.route.id to be preferred!
     */
     const routes = {
          'zp_route':             zp_route,
          'page.route.id':        page.route.id,
          'page.route.id.RPL':    page.route.id.replace(base, ''),
          'page.url.pathname':    page.url.pathname,
          'page.url.pathnameRPL': page.url.pathname.replace(base, ''),
          'page.url.pathname':    page.url.pathname,
          'page.url.search':      page.url.search,
     };
     //console.log('ZP routes', routes);
     return routes;
}