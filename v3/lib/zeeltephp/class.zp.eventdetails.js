//import { base } from "$app/paths";
import { page } from '$app/state';
import { ZP_ApiRouter } from './class.zp.apirouter';

// function get_form_event_action_details(event) {
export function get_event_action_details(event) {
    return new ZP_EventDetails(event);
}

export class ZP_EventDetails {

    route
    event
    message

    target
    button
    name
    action
    value
    form
    formAction
    formData
    encoding
    enctype

    keyId
    keyName


    constructor(event) {
        try {
            // exit if event is nullish
            if (!event) {
                this.message = event
                return;
            }
            // just return self
            if (event instanceof ZP_EventDetails) return event;

            // main()
            // set current routing
            this.route   = page.route.id +'/';  // window.location.pathname.replace(base, '') // todo: page.url.pathname or page.route.id to be preferred!
            // process event.defaultParmas
            // parse event
            this.event   = event            
            this.target  = event?.target || event
            this.parse_keyboard(event)
            this.parse_mouse(event)
            if (event instanceof SubmitEvent) 
                return this.fetch_SubmitEvent(event)
            else if (event instanceof PointerEvent) 
                return this.fetch_PointerEvent(event)
            else if (event instanceof URLSearchParams)
                return this.fetch_URLSearchParams(event)
            else if (event instanceof ZP_ApiRouter)
                    null //this.message = 'nothing todo :-) '
            else
                this.message = 'Unknown event '+event
            //console.log(this.message, typeof event, {event})
        } catch (error) {
            this.message = error;
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

    fetch_SubmitEvent(e) {
        // event is typeof HTMLElementForm | FormSubmit?
        this.parse_form(e.target)
        this.parse_button(e.submitter)
    }

    fetch_PointerEvent(e) {
        this.parse_button(e.srcElement);
    }

    fetch_URLSearchParams(searchParams) {
        if (searchParams.size > 0) {
              this.params = [];
              for (const [key, value] of searchParams) {
                    if (key.startsWith('?/')) {
                        this.action = key;
                        this.value  = value;
                    }
                    else {
                        this.params[key] = value;
                    }
                    
              }
        }
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

    parse_button(button) {
        this.button     = button
        this.action     = button.getAttribute('formaction')
        this.name       = button.name
        this.value      = button.value
    }

}