import './zpdev.css';
interface $$__sveltets_2_IsomorphicComponent<Props extends Record<string, any> = any, Events extends Record<string, any> = any, Slots extends Record<string, any> = any, Exports = {}, Bindings = string> {
    new (options: import('svelte').ComponentConstructorOptions<Props>): import('svelte').SvelteComponent<Props, Events, Slots> & {
        $$bindings?: Bindings;
    } & Exports;
    (internal: unknown, props: Props & {
        $$events?: Events;
        $$slots?: Slots;
    }): Exports & {
        $set?: any;
        $on?: any;
    };
    z_$$bindings?: Bindings;
}
declare const ZpDev: $$__sveltets_2_IsomorphicComponent<{
    data: any;
    form: any;
}, {
    [evt: string]: CustomEvent<any>;
}, {}, {}, string>;
type ZpDev = InstanceType<typeof ZpDev>;
export default ZpDev;
