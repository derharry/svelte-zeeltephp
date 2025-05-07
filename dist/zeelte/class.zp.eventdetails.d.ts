export function get_event_action_details(event: any): ZP_EventDetails;
/**
 * Destructs details of most Event Types to have all in 1 Zeelte structure :-)
 */
export class ZP_EventDetails {
    /**
     *
     * @param { app-page | URLSearchParams | SubmitEvent | PointerEvent | ZP_ApiRouter} event
     * @returns
     */
    constructor(event: any);
    message: any;
    route: any;
    event: any;
    name: any;
    action: any;
    value: any;
    data: any;
    button: any;
    srcElement: any;
    form: any;
    dataIsFormData: any;
    encoding: any;
    enctype: any;
    target: any;
    dump(subdata?: any, sub?: boolean): void;
    parse_SubmitEvent(): void;
    parse_URL(): void;
    parse_URLSearchParams(): void;
    parse_KeyBoardEvent(): void;
    keyId: any;
    parse_PointerEvent(): void;
    parse_keyboard(): void;
    keyName: any;
    parse_mouse(e: any): void;
    parse_form(form: any): void;
    formAction: any;
    parse_button(button: any): void;
    title: any;
}
