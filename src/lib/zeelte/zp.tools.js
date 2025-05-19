import { base } from "$app/paths";
import { page } from '$app/state';


/**
 * Gets the current normalized route for ZeeltePHP
 * @param {boolean} [returnRoutes=false] - Return detailed route info object
 * @returns {string|object} Route string or debug information
 */
export function zp_page_route(returnRoutes = false) {
     let route = '';     

     // Set current route from from Svelte page (default)
     route = page.url.pathname || page.route.id;
     
     // !! tmpFix-001-v103-Routing-zp_route
     // Svelte should do this, but doesn't. PHP does the counterpart now.
     route = route.replace(base, '');  // remove BASE on builds 

     return route;
}