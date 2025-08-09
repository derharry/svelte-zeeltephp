// zpdev.store.js
import { writable, derived } from "svelte/store";
import { persistentStore_KeyValue } from "$lib/zeelte/persistentStore";

/** hook zp_fetch() Stores  */
import { data, form, error } from "$lib/zeeltephp/zp.fetch.js"

/** persistent Stores  */
export const showApp       = persistentStore_KeyValue('ZPDev_showApp', 'PAGE.SERVER.PHP')
export const showDumpPanel = persistentStore_KeyValue('ZPDev_showDumpPanel', 'data')
export const dataDumpPanel = writable();

/** local Stores */
export const zpED_js     = writable()
export const zpED_svelte = writable()
export const zpAR_js     = writable()
export const zpAR_svelte = writable()
export const zpAR_pageJS = writable()
export const zpAR_php    = writable()
export const zpDB_php    = writable()
export const zpENV_php   = writable()

// Navigation: "App" panel buttons
export const appTabs = [
     { key: "PAGE.SERVER.PHP", label: "+page.server.php" },
     { key: "SERVER.PHP",      label: "+server.php" },
     { key: "DB",              label: "DB" }
];

// Navigation: DumpPanel buttons
export const dumpTabs = [
     { key: "data",     label: "data",   store: data },
     { key: "form",     label: "form",   store: form },
     { key: "error",    label: "error",  store: error },
     { key: "_zpAR_js", label: "AR js",  store: zpAR_svelte },
     { key: "_zpED_js", label: "ED js",  store: zpAR_svelte },
     { key: "_zpAR",    label: "AR php", store: zpAR_php },
     { key: "_zpDB",    label: "DB",     store: zpDB_php  },
     { key: "_zpENV",   label: "ENV",    store: zpENV_php }
];

function isEmpty(val) {
  if (val == undefined) return true;
  if (val == null) return true;
  if (typeof val === "string" && val.trim().length === 0) return true;
  if (typeof val === "number" && val === 0) return true;
  if (Array.isArray(val) && val.length === 0) return true;
  // Only treat plain objects as empty if not null and not array
  if (typeof val === "object" && !Array.isArray(val)) {
    return Object.keys(val).length === 0;
  }
  return false;
}

export const dumpTabsHasData = derived(
  dumpTabs.map(t => t.store),
  (values) =>
    dumpTabs.map((t, i) => {
      const val = values[i];
      return { key: t.key, hasData: !isEmpty(val), value: val };
    })
);

/**
 * Resets all API state variables to initial values.
 */
export function resetLocalStores () {
     console.log(" #/ resetLocalStores()")
     data.set(undefined);
     form.set(undefined);
     error.set(undefined);
     zpED_js.set(undefined)
     zpED_svelte.set(undefined)
     zpAR_js.set(undefined)
     zpAR_svelte.set(undefined)
     zpAR_pageJS.set(undefined)
     zpAR_php   .set(undefined)
     zpENV_php  .set(undefined)
}