
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
export class ZP_ApiRouter 
{
}
