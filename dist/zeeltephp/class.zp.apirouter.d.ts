export class ZP_ApiRouter {
    /**
     *
     * @param {*} routeUrl = string | event | submitter?form-send/
     * @param {*} data     = any
     * @param {*} method   = string:GET, POST, PUT, ...
     */
    constructor(routeUrl?: any, data?: any, method?: any);
    route: any;
    action: any;
    value: any;
    params: any;
    data: any;
    environment: string;
    method: string;
    base_url: any;
    fetch_url: any;
    fetch_query: any;
    fetch_options: any;
    fetch_enctype: string;
    dataIsFormData: boolean;
    message: any;
    dump(): void;
    /**
     * Parse Svelte/$app/page.
     * ZPPHP destructs this as ?/route/&?/action=value&query_params
     */
    parse_routingFromSveltePage(): void;
    /**
     * Destruct the given URL:string to be reused
     * @param {*} urlString
     */
    parse_url_string(urlString: any): void;
    set_data(data: any): void;
    set_best_method(data?: any, method?: any): void;
    /**
     * Prepare everything to exec fetch(ZeeltePHP)
     */
    prepare(): void;
}
