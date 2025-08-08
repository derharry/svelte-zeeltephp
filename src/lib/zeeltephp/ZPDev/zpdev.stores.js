import { writable } from "svelte/store";
import { persistentStore_KeyValue } from "$lib/zeelte/persistentStore";

/** persistent Stores  */
export const showApp       = persistentStore_KeyValue('ZPDev_showApp', 'PAGE.SERVER.PHP')
export const showDumpPanel = persistentStore_KeyValue('ZPDev_showDumpPanel', 'data')

/** local Stores */
export const zpED_svelte   = writable()
export const zpAR_pageJS   = writable()
export const zpAR_svelte   = writable()
export const zpAR_php      = writable()
export const zpDB_php      = writable()
