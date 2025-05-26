/**
 * returns object of ZP_EventDetails()
 * @param {Event} event - The DOM event (e.g., form submit, button click)
 * @returns {ZP_EventDetails} Parsed event details
 */
export function get_event_action_details(event: Event): ZP_EventDetails;
/**
 * Extracts and normalizes routing, action, value, and data from various event types
 * (form submissions, buttons, keyboard/mouse events, URLSearchParams, etc.)
 * for use with ZeeltePHP's API router.
 */
export class ZP_EventDetails {
    /**
     * Constructs a ZP_EventDetails object from various event types.
     * @param {any}     event - Event, URLSearchParams, ZP_ApiRouter, SubmitEvent, PointerEvent, .. etc.
     * @param {boolean} debug - Enable debug logging
     * @returns {ZP_EventDetails|false}
     */
    constructor(event: any, debug?: boolean);
    /** @type {string} Current route (from page or event) */
    route: string;
    /** @type {Event} The original event */
    event: Event;
    /** @type {string} Name of the source element (e.g., button name) */
    name: string;
    /** @type {string} SvelteKit-Action (formaction ?/action) */
    action: string;
    /** @type {any} Value associated with the action */
    value: any;
    /** @type {any} Detected data payload from Event (formData, object, etc.) */
    data: any;
    /** @type {HTMLElement} Source element (generic) */
    srcElement: HTMLElement;
    /** @type {HTMLElement} Button element (if source is a button) */
    button: HTMLElement;
    /** @type {HTMLFormElement} Form element (if source is a form) */
    form: HTMLFormElement;
    /** @type {boolean|null} True if data is FormData */
    dataIsFormData: boolean | null;
    /** @type {string} detected Form encoding */
    encoding: string;
    /** @type {string} detected Form enctype */
    enctype: string;
    /** @type {string} last internal message */
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
     * @param {any} [subdata] - Optional sub-object to dump
     * @param {boolean} [sub] - Internal flag for recursion
     */
    dump(subdata?: any, sub?: boolean): void;
    /**
     * Parses a SubmitEvent (form submission).
     */
    parse_SubmitEvent(): void;
    /**
     * Parses a URL object.
     */
    parse_URL(): void;
    /**
     * Parses URLSearchParams for route, action, value, and data.
     * This is mostly used for +page.js to resolve the current route. +page.js does not have
     * $page to read from, which can lead to having the route from previous page (1 route behind).
     */
    parse_URLSearchParams(): void;
    /**
     * Parses a KeyboardEvent.
     */
    parse_KeyBoardEvent(): void;
    keyCode: any;
    keyName: any;
    /**
     * Parses a PointerEvent (e.g., mouse click).
     */
    parse_PointerEvent(): void;
    /**
     * Parses a form element for data and encoding information.
     * @param {HTMLFormElement} form
     */
    parse_form(form: HTMLFormElement): void;
    formAction: any;
    /**
     * Parses a button element for action, name, and value.
     * @param {HTMLButtonElement} button
     */
    parse_button(button: HTMLButtonElement): void;
    title: string;
}
