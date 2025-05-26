import { ZP_ApiRouter } from "./class.zp.apirouter.js"
import { zp_page_route } from "../zeeltephp/zp.tools.js";

/**
 * returns object of ZP_EventDetails()
 * @param {Event} event - The DOM event (e.g., form submit, button click)
 * @returns {ZP_EventDetails} Parsed event details
 */
export function get_event_action_details(event) {
    return new ZP_EventDetails(event);
}

/**
 * Extracts and normalizes routing, action, value, and data from various event types
 * (form submissions, buttons, keyboard/mouse events, URLSearchParams, etc.)
 * for use with ZeeltePHP's API router. 
 */
export class ZP_EventDetails {

      /** @type {string} Current route (from page or event) */
      route;
      /** @type {Event} The original event */
      event;

      /** @type {string} Name of the source element (e.g., button name) */
      name;
      /** @type {string} SvelteKit-Action (formaction ?/action) */
      action;
      /** @type {any} Value associated with the action */
      value;
      /** @type {any} Detected data payload from Event (formData, object, etc.) */
      data;

      // Event source references

      //** @type {any} Event target (for future use) */
      // -- target;
      
      /** @type {HTMLElement} Source element (generic) */
      srcElement;
      /** @type {HTMLElement} Button element (if source is a button) */
      button;
      /** @type {HTMLFormElement} Form element (if source is a form) */
      form;
      /** @type {boolean|null} True if data is FormData */
      dataIsFormData = null;
      /** @type {string} detected Form encoding */
      encoding;
      /** @type {string} detected Form enctype */
      enctype;

      /** @type {string} last internal message */
      last_message = "";

      // --- Internal/debug ---
      debug      = false;
      debug_msgs = [];

      /**
       * Add a debug message (if debug mode is enabled).
       * @param {string} msg
       * @param {any} value
       */
      log(msg, value = '') {
            this.last_message = msg;
            if (this.debug) {
                  this.debug_msgs.push({msg, value});
                  console.log('  ', msg, value);
            }
      }

      /**
       * Constructs a ZP_EventDetails object from various event types.
       * @param {any}     event - Event, URLSearchParams, ZP_ApiRouter, SubmitEvent, PointerEvent, .. etc.
       * @param {boolean} debug - Enable debug logging
       * @returns {ZP_EventDetails|false}
       */
      constructor(event, debug = false) {
            try {
                  if (!event) return false
                  if (event instanceof ZP_EventDetails) return event
                  this.debug = debug
                  this.log('-- ZP EventDetails ')

                  // Set current route from from Svelte page (default)
                  this.route = zp_page_route();
                  this.log('route', this.route)
                  
                  this.event = event;
                  this.log('state', 'init');

                  // parse anyway?
                  // -- this.parse_keyboard();
                  // -- this.parse_mouse();

                  // Parse event based on type
                  if (event instanceof SubmitEvent) 
                        this.parse_SubmitEvent()
                  else if (event instanceof KeyboardEvent) 
                        this.parse_KeyBoardEvent()
                  else if (event instanceof PointerEvent) 
                        this.parse_PointerEvent()
                  else if (event instanceof URLSearchParams)
                        this.parse_URLSearchParams()
                  else if (event instanceof URL)
                        this.parse_URL()
                  else if (event instanceof ZP_ApiRouter)
                        this.log('event is type of ZP_ApiRouter. nothing to do.')
                  else if (event?.detail) {
                        this.log('event.detail');
                        this.action = event.detail.formaction;
                        this.name   = event.detail.name;
                        this.value  = event.detail.value;
                  }
                  else
                        this.log('unknown event-type', typeof event, event);

                  // ready
                  //this.log(this);
                  this.log('// ZP EventDetails ');
            } catch (error) {
                  this.log(' ! ERROR ', error);
            }
      }

      /**
       * Dumps the current state to the console (for debugging).
       * @param {any} [subdata] - Optional sub-object to dump
       * @param {boolean} [sub] - Internal flag for recursion
       */
      dump(subdata = null, sub = false) {
            if (!sub) console.log('---DUMP ZP_EventDetails-----------------------------');
            Object.entries(this).forEach(([variable, value]) => {
                  if (value !== undefined && value !== null) {
                  console.log(variable, value);
                  }
            });
            if (!sub) console.log('---END DUMP ZP_EventDetails-----------------------------');
      }

      /**
       * Parses a SubmitEvent (form submission).
       */
      parse_SubmitEvent() {
            this.log('parse_SubmitEvent');
            this.parse_form(this.event.target);
            this.parse_button(this.event.submitter);
      }

      /**
       * Parses a URL object.
       */
      parse_URL() {
            this.log('parse_URL !');
            this.parse_URLSearchParams();
      }

      /**
       * Parses URLSearchParams for route, action, value, and data.
       * This is mostly used for +page.js to resolve the current route. +page.js does not have
       * $page to read from, which can lead to having the route from previous page (1 route behind). 
       */
      parse_URLSearchParams() {
            this.log('parse_URLSearchParams');
            const url    = this.event;
            this.route   = url.pathname;
            const params = url.searchParams;
            if (params && params.size > 0) {
                  for (const [key, value] of params) {
                  if (key.startsWith('?/')) {
                        this.action = key;
                        this.value = value;
                        params.delete(key);
                        break;
                  }
                  }
                  this.data = Object.fromEntries(params);
            }
      }

      /**
       * Parses a KeyboardEvent.
       */
      parse_KeyBoardEvent() {
            this.log('parse_KeyBoardEvent');
            this.keyCode = this.event.keyCode || this.keyCode; // || this.event.submitter?.keyCode
            this.keyName = this.event.key     || this.keyName; // || this.event.submitter?.keyName
      }

      /**
       * Parses a PointerEvent (e.g., mouse click).
       */
      parse_PointerEvent() {
            this.log('parse_PointerEvent');
            // this.form = this.event;
            // this.parse_button(this.event.srcElement);
      }

      parse_PointerEvent() {
            this.log('parse_PointerEvent');
            //console.log(' @@ event      ', this.event)
            //console.log(' @@ target     ', typeof this.event.target)
            //console.log(' @@ src Element', typeof this.event.srcElement)
            //this.parse_form(this.event?.target);
            this.parse_button(this.event.srcElement);
            this.form    = this.event
      }

      /**
       * Parses a form element for data and encoding information.
       * @param {HTMLFormElement} form
       */
      parse_form(form) {
            this.log('parse_form');
            this.form       = form
            this.formAction = form?.formAction
            this.data       = new FormData(form)
            this.dataIsFormData = true
            this.encoding   = form?.encoding || this.encoding // 'application/x-www-form-urlencoded'
            this.enctype    = form?.enctype  || this.enctype  //'application/x-www-form-urlencoded'
      }

      /**
       * Parses a button element for action, name, and value.
       * @param {HTMLButtonElement} button
       */
      parse_button(button) {
            this.log('parse_button');
            this.button   = button
            this.title    = button?.title
            this.name     = button?.name
            this.value    = button?.value
            this.action   = button?.formAction && button.hasAttribute('formaction') ? button.getAttribute('formaction') : undefined
            this.encoding = button?.encoding || this.encoding
            this.enctype  = button?.enctype  || this.enctype
            //console.log('BUTTON', button)
      }
}