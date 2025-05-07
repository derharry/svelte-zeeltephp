export function zp_get_eventDetails(event: any): ZP_EventDetails;
/**
 * Fetch from SvelteKit-Backend
 * (placeholder for new feature)
 */
export function fetch_api(fetch: any): void;
/**
 * Fetch from API-Backend
 * @param {*} fetch Svelte's fetch
 * @param { ZP_ApiRouter ZP_EventDetails } urlOrRouter
 * @param {*} data
 * @param {*} method
 * @param {*} headers
 * @returns
 */
export function zp_fetch_api(fetch: any, urlOrRouterOrEvent: any, data?: any, method?: any, headers?: any): Promise<any>;
import { ZP_EventDetails } from "./class.zp.eventdetails.js";
