//import { base } from "$app/paths";
import { page } from '$app/state';
import { ZP_ApiRouter } from './class.zp.apirouter';
import { is_typeof, get_typeof, get_instanceof } from "./dev.types";

// function get_form_event_action_details(event) {
export function get_event_action_details(event) {
    return new ZP_EventDetails(event);
}

/**
 * Destructs details of most Event Types to have all in 1 Zeelte structure :-)
 */
export class ZP_EventDetails {

    message
    route
    name
    action
    value
    data

    keyId
    keyName

    formAction
    button
    form
    dataIsFormData
    encoding
    enctype
    event
    search 


    /**
     * 
     * @param { app-page | URLSearchParams | SubmitEvent | PointerEvent | ZP_ApiRouter} event 
     * @returns 
     */
    constructor(event) {
        try {
            //# exit() reasons
            if (!event) { this.message = 'no-event'; return; }  //  event is_nullish
            if (event instanceof ZP_EventDetails) return event; //  event is self - return self

            //# main()
            this.event = event;  //  set src
            this.route = page.route.id +'/';  // set current routing as minimum requirement
                                              // window.location.pathname.replace(base, '') // todo: page.url.pathname or page.route.id to be preferred!
            this.message = 'init'
            
            if (event instanceof PointerEvent) {
                this.parse_PointerEvent()
            }
            else if (event instanceof URLSearchParams) {
                this.message = 'event of URLSearchParams'
                this.parse_URLSearchParams()                
            }
            else if (event instanceof ZP_ApiRouter) {
                this.message = 'instance of ZP_ApiRouter. nothing todo.'
            }
            else {
                // leave message unknown event type
                //this.message = 'unknown event type to parse:' + get_typeof(event)
            }
            //this.message = get_instanceof(event);
            /*
            this.parse_keyboard(event)
            this.parse_mouse(event)
            //  parse known event types
            if (event === page && event?.url) {
                this.message = 'event of URLSearchParams(of page)'
                this.event = event.url.searchParams
                this.parse_URLSearchParams()
            }
            else if (event instanceof SubmitEvent) {
                this.message = 'event of SubmitEvent'
                this.parse_SubmitEvent()                
            }
            */
            console.log(this.message)
        } catch (error) {
            this.message = 'error; ' +  this.message;
            this.event   = error;
            console.error({ error })
        }
        return false
    }

    dump() {
        console.log('---DUMP ZP_EventDetails-----------------------------');
        Object.entries(this).forEach(([variable, value]) => {
            if (value !== undefined && value !== null) 
                console.log(variable, value);
        });
        console.log('---END DUMP ZP_EventDetails-----------------------------');
    }


    parse_URLSearchParams() {
        this.message = 'URLSearchParams'
        //this.search  = this.event.search
        if (this.event.size > 0) {
              this.data = [];
              for (const [key, value] of this.event) {
                    if (key.startsWith('?/')) {
                        this.action = key;
                        this.value  = value;
                    }
                    else {
                        this.data[key] = value;
                    }
              }
        }
    }

    parse_SubmitEvent(e) {
        // event is typeof HTMLElementForm | FormSubmit?
        this.message = 'SubmitEvent'
        this.parse_form(e.target)
        this.parse_button(e.submitter)
    }

    parse_PointerEvent() {
        this.message = 'PointerEvent'
        this.button  = this.event.srcElement
        this.parse_button();
    }


    parse_keyboard(e) {
        this.keyId   = e?.keyId   || undefined
        this.keyName = e?.keyName || undefined
    }
    parse_mouse(e) {

    }

    parse_form(form) {
        this.form       = form
        this.formAction = form.action
        this.formData   = new FormData(form)
        this.encoding   = form.encoding // 'application/x-www-form-urlencoded'
        this.enctype    = form.enctype  //'application/x-www-form-urlencoded'

    }

    parse_button() {
        this.name       = this.button.name
        this.value      = this.button.value
        this.action     = this.button.getAttribute('formaction')
    }

}