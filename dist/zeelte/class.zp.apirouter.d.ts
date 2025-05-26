/**
 * Api for communication between Svelte <-> ZeeltePHP.
 * Handles route/action/value/data extraction, fetch URL and options preparation,
 * and supports multiple construction patterns for CSR/SSR and event-driven requests.
 *
 * Usage:
 *   new ZP_ApiRouter();                    // detects routing from Svelte page
 *   new ZP_ApiRouter(event);               // detects routing, formAction, data to send, etc from event details via ZP_EventDetails
 *   new ZP_ApiRouter(ZP_EventDetails);     // uses the settings from ZP_EventDetails
 */
export class ZP_ApiRouter {
    /**
     * Constructor: Overloaded for multiple use-cases. See zp_fetch_api() for details.
     * @param {*} router  Can be ZP_ApiRouter, string, event, or undefined
     * @param {*} data    Optional, force data payload (overrides auto-detected)
     * @param {string} method Optional, force HTTP method (GET, POST, etc.)
     * @param {boolean} debug Enable debug mode
     */
    constructor(router?: any, data?: any, method?: string, debug?: boolean);
    /** @type {string|null} Route path (e.g. /foo/bar/) */
    route: string | null;
    /** @type {string|null} Action name (e.g. ?/ACTION) */
    action: string | null;
    /** @type {any} Action value */
    value: any;
    /** @type {any} Query params */
    params: any;
    /** @type {any} Data payload (formData, JSON, etc.) */
    data: any;
    /** @type {string} Environment flag ('dev' or 'prod') */
    environment: string;
    /** @type {string} HTTP method ('GET', 'POST', etc.) */
    method: string;
    /** @type {string} Base API URL */
    base_url: string;
    /** @type {string|null} Final fetch URL */
    fetch_url: string | null;
    /** @type {string|null} Query string for fetch */
    fetch_query: string | null;
    /** @type {object|null} Fetch options (method, headers, body, etc.) */
    fetch_options: object | null;
    /** @type {string} Default content type */
    fetch_enctype: string;
    /** @type {boolean} True if data is FormData */
    dataIsFormData: boolean;
    /** @type {string} Debugging last_message */
    last_message: string;
    debug: boolean;
    debug_msgs: any[];
    /**
     * Add a debug message (if debug mode is enabled).
     * @param {string} msg
     * @param {any} value
     */
    log(msg: string, value?: any): void;
    /**
     * Dumps the current state to the console (for debugging).
     */
    dump(): void;
    /**
     * Parse/decode the string for the routing details.
     * @param {string} router
     */
    parse_routerFromString(router: string): void;
    /**
     * Parse routing/action/value/data from a ZP_EventDetails object or event.
     * @param {*} event
     * @returns {ZP_EventDetails|false}
     */
    parse_routerFromZPeventDetails(event: any): ZP_EventDetails | false;
    /**
     * Sets the data payload and detects if it's FormData.
     * @param {*} data
     */
    set_data(data: any): void;
    /**
     * Determines the best HTTP method based on the action/data, or uses the forced method.
     * @param {string|undefined} forceMethod
     */
    set_best_method(forceMethod?: string | undefined): void;
    /**
     * Prepares the fetch_url and fetch_options for the current state.
     */
    prepare(): void;
    /**
     * Prepares a GET request (URL with query string).
     */
    prepare_GET(): void;
    /**
     * Prepares a POST request (JSON or FormData).
     */
    prepare_POST(): void;
    /**
     * *deprecated*
     * Decodes a URL-string to get the route/action/value/params components.
     * Can be used as url checker.
     * @param {string} urlString
     */
    decode_url_string(urlString: string): void;
    message: any;
}
import { ZP_EventDetails } from "./class.zp.eventdetails.js";
