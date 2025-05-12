/**
 * Parse Svelte/$app/page.
 * ZPPHP destructs this as ?/route/&?/action=value&query_params
 */
export function parse_routingFromSveltePage(): void;
export class parse_routingFromSveltePage {
    route: string;
    action: any;
    value: any;
    data: any;
    fetch_query: any;
    message: any;
}
/**
 * return the real used route by ZeeltePHP (spoc/icq)
 * @param {bool} returnRoutes be default false to return zp_route: string
 * @returns string or KeyValueoObjectList
 */
export function zp_page_route(returnRoutes?: bool): string | {
    zp_route: string;
    'page.route.id': any;
    'page.route.id.RPL': any;
    'page.url.pathname': any;
    'page.url.pathnameRPL': any;
    'page.url.search': any;
};
export function zp_search_for_mrRoute(zp_route?: string): {
    zp_route: string;
    'page.route.id': any;
    'page.route.id.RPL': any;
    'page.url.pathname': any;
    'page.url.pathnameRPL': any;
    'page.url.search': any;
};
