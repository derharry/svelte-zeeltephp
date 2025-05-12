export function get_event_action_details(event: any): ZP_EventDetails;
/**
 * Destructs details of most EventTypes to have all in 1 Zeelte structure :-)/**
 */
export class ZP_EventDetails {
    /**
     *
     * @param { app-page | URLSearchParams | SubmitEvent | PointerEvent | ZP_ApiRouter} event
     * @param { bool } debug
     * @returns
     */
    constructor(event: any, debug?: bool);
    message: string;
    route: string | {
        zp_route: string;
        'page.route.id': any;
        'page.route.id.RPL': any;
        'page.url.pathname': any;
        'page.url.pathnameRPL': any;
        'page.url.search': any;
    };
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
    runtimeID: string;
    debug: boolean;
    debug_msgs: any[];
    log(msg: any, value?: string): void;
    dump(subdata?: any, sub?: boolean): void;
    parse_SubmitEvent(): void;
    parse_URL(): void;
    /**
     * mostly used for +page.js to resolve the current route!! ;-)
     * otherwise a bahavoiur like 1 route behind occurs
     */
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
