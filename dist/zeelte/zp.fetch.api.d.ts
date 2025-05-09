/**
 * alias for new ZP_EventDetails(event)
 * @param {*} event
 * @returns ZP_EventDetails
 */
export function zp_get_eventDetails(event: any): ZP_EventDetails;
/**
 * Use SvelteKit-native fetch and backend behaviour - resolve as @zeelte:fetch_api
 * (placeholder for new feature - port zeelte:fetch_api)
 */
export function fetch_api(fetch: any, dataOrEvent: any): void;
/**
 * Fetch PHP-backend - resolve as @zeelte:fetch_api.
 * Overloading
 * zp_fetch_api(fetch, Event [, Data])   -> parses Details eg Action, value, formData, Data ...
 * zp_fetch_api(fetch, Event, anyData)   -> get all from Event but data is overruled
 * zp_fetch_api(fetch, URLParams, anyEvent) -> tbd
 * zp_fetch_api(fetch, ZP_EventDetails, anyData)
 * zp_fetch_api(fetch, ZP_ApiRouter, anyData) -> hook into ZP_ApiRouter
 * @param {*} fetch Svelte's fetch
 * @param { ZP_ApiRouter ZP_EventDetails string event URL } router
 * @param {*} dataOrEvent
 * @param {string} method  override method used GET, POST, JSON
 * @param {object} headers { ...fetchOptions.headers }
 * @returns
 */
export function zp_fetch_api(fetch: any, router: any, dataOrEvent?: any, method?: string, headers?: object): Promise<any>;
import { ZP_EventDetails } from "./class.zp.eventdetails.js";
