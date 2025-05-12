/**
 * Api/Router - Svelte <-> ZeeltePHP
 * See constructor for overloading
 */
export class ZP_ApiRouter {
    /**
     * Create the router with default settings and auto-detection.
     * Overloading:
     * ZP_ApiRouter()
     *    ready-to-fetch, most suitable to use in +page.svelte
     * ZP_ApiRouter(router)
     *    Any               Support auto-detect most use-cases.
     *    ZP_EventDetails   As ZP_EventDetails supports auto-detection.
     *    ZP_ApiRouter      me-self
     *    String            future +server.php, +api.php
     * @param {*} router Any, ZP_ApiRouter, String, ZP_EventDetails
     * @param {*} data   force data to send. overrules auto detected from ZP_EventDetails
     * @param {string} method force using method GET, POST, PUT, ...
     * @param {*} debug  active and show debug info
     * @returns ZP_ApiRouter
     */
    constructor(router?: any, data?: any, method?: string, debug?: any);
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
    message: string;
    runtimeID: string;
    debug: boolean;
    debug_msgs: any[];
    log(msg: any, value?: string): void;
    dump(): void;
    /**
     * Parse Svelte/$app/page.
     * ZPPHP destructs this as ?/route/&?/action=value&query_params
     */
    parse_routingFromSveltePage(): void;
    parse_routingFromZPeventDetails(event: any): false | ZP_EventDetails;
    set_data(data: any): void;
    /**
     *
     * @param {string} forceMethod force / lock method beeing used
     */
    set_best_method(forceMethod?: string): void;
    /**
     * ready-to-fetch state.
     */
    prepare(): void;
    prepare_GET(): void;
    prepare_POST(): void;
}
import { ZP_EventDetails } from "./class.zp.eventdetails.js";
