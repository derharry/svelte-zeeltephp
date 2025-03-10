import { base } from "$app/paths";

// function get_form_event_action_details(event) {
export function get_event_action_details(event) {
    try {
        // event is typeof HTMLElementForm | FormSubmit?
        return {
            target:     event.target,
            button:     event.submitter,
            action:     event.submitter.getAttribute('formaction'),
            name:       event.submitter.name,
            value:      event.submitter.value,
            formAction: event.target.action,
            form:       event.target,
            formData:   new FormData(event.target),
            keyId:      event.keyId,
            keyName:    event.keyName,
            encoding:   'application/x-www-form-urlencoded',
            enctype:    'application/x-www-form-urlencoded',
            route:      window.location.pathname.replace(base, '')
        }
    } catch (error) {
        console.error(error);
    }
}

export class ZP_EventActionDetails {
    constructor(event) {
        //this = {... get_event_action_details(event) }
    }
}