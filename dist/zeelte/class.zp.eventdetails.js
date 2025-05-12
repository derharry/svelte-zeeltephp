import { ZP_ApiRouter } from "./class.zp.apirouter.js" // -> import { ZP_ApiRouter } from "../../../zeeltephp"
import { zp_page_route } from "./inc.zp.tools.js";
import { tinyid } from './tiny.id.js';

// function get_form_event_action_details(event) {
export function get_event_action_details(event) {
      return new ZP_EventDetails(event);
}

/**
 * Destructs details of most EventTypes to have all in 1 Zeelte structure :-)/**
 */
export class ZP_EventDetails 
{
      message     = ""; // deprecated to use log and debug_msgs // state/debug
      route       // current route of page
      event       // source of event

      name        // name of source e.g. button
      action      // formaction
      value       // value of formaction
      data        // data - formData, null, 

      // event src is Button
      button
      srcElement

      // event src is HTMLForm
      form
      dataIsFormData = null
      encoding
      enctype
      

      //wherefor used?
      target

      // internal
      runtimeID  = '';
      debug      = false;
      debug_msgs = [];
      log(msg, value = '') {
            if (this.debug) {
                  this.debug_msgs.push({msg, value});
                  console.log('  ', msg, value);
            }
      }

    /**
     * 
     * @param { app-page | URLSearchParams | SubmitEvent | PointerEvent | ZP_ApiRouter} event 
     * @param { bool } debug 
     * @returns 
     */
      constructor(event, debug = false) {
            try {
                  // exit first
                  //   if is_nullish( event ) - exit false
                  //   if typeof( event) == myself - return myself
                  if (!event || event == null || event == undefined) return false;
                  if (event instanceof ZP_EventDetails) return event;

                  // debugging?
                  this.debug = debug; // for now because @debug is no param
                  if (debug) console.log('-- ZP EventDetails ');

                  // main()
                  if (debug) this.runtimeID = tinyid(); // avoid overhead, only dev mode
                  if (debug) this.log('rtid', this.runtimeID);

                  // add current page.routing to event details (also for zp_route)
                  this.route = zp_page_route();
                  this.log('route', this.route);
                  
                  //# set defaults
                  this.event = event  // set src
                  //this.target = event?.target || undefined 
                  //this.srcElement = event.srcElement || undefined

                  //# ready
                  this.log('state', 'init')

                  // parse anyway?
                  // -- this.parse_keyboard();
                  // -- this.parse_mouse();

                  // parse EventTypes
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

                  // all ok by now
                  // but to we have all data?
                  // this.log('<<-->> finally')

                  this.log(this);
                  if (debug) console.log('// ZP EventDetails ');
            } catch (error) {
                  this.log(' ! ERROR ', error);
            } finally {
            }
      }

      dump(subdata = null, sub = false) {
            if (!sub) console.log('---DUMP ZP_EventDetails-----------------------------');
            Object.entries(this).forEach(([variable, value]) => {
            if (value !== undefined && value !== null) 
                  // if (typeof value == 'object' && Object.entries(value).length > 0)
                  //      this.dump(value, true)
                  //else 
                        console.log(variable, value);
            });
            if (!sub) console.log('---END DUMP ZP_EventDetails-----------------------------');
      }

      parse_SubmitEvent() {
            // event is typeof HTMLElementForm | FormSubmit?
            this.log('parse_SubmitEvent');
            this.parse_form(this.event.target)
            // Display the key/value pairs
            // for (var pair of this.data.entries()) { console.log(pair[0]+ ', ' + pair[1]); }
            this.parse_button(this.event.submitter)
      }

      parse_URL() {
            this.log('parse_URL !');
            this.parse_URLSearchParams();
            //const url  = this.event
            //this.route = url?.pathname
            //this.parse_URLSearchParams(url.searchParams)
      }

      /**
       * mostly used for +page.js to resolve the current route!! ;-)
       * otherwise a bahavoiur like 1 route behind occurs
       */
      parse_URLSearchParams() {
            this.log('parse_URLSearchParams');
            const url  = this.event            
            //console.log(url)
            this.route = url.pathname;
            //this.search  = this.event.search
            const params = url.searchParams
            if (params && params.size > 0) {
                  //parse for ?/action, the rest is data
                  for (const [key, value] of params) {
                        if (key.startsWith('?/')) {
                        this.action = key;
                        this.value  = value;
                        params.delete(key); // remove from params
                        break;
                  }}
                  this.data = Object.fromEntries(params)
            }
      }


      parse_KeyBoardEvent() {
            this.log('parse_KeyBoardEvent');
            const keyEvent = this.event;
            this.keyId = keyEvent.keyCode;
            //this.parse_keyboard()
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

      
      parse_keyboard() {
            this.log('parse_keyboard');
            this.keyId   = this.event?.keyId   || this.event?.submitter?.keyId || undefined
            this.keyName = this.event?.keyName || this.event?.submitter?.keyId || undefined
      }
      parse_mouse(e) {
            this.log('parse_mouse');

      }

      parse_form(form) {
            this.log('parse_form');
            this.form       = form
            this.formAction = form?.formAction
            this.data       = new FormData(form)
            this.dataIsFormData = true
            this.encoding   = form?.encoding || this.encoding // 'application/x-www-form-urlencoded'
            this.enctype    = form?.enctype  || this.enctype  //'application/x-www-form-urlencoded'
            //console.log('FORM', form)
      }

      parse_button(button) {
            this.log('parse_button');
            this.button = button
            this.title      = button?.title
            this.name       = button?.name
            this.value      = button?.value
            this.action     = button?.formAction && button.hasAttribute('formaction') ? button.getAttribute('formaction') : undefined
            this.encoding   = button?.encoding || this.encoding
            this.enctype    = button?.enctype  || this.enctype
            //console.log('BUTTON', button)
      }
}