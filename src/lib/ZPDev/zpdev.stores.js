//zpdev.stores.js
import { writable, derived, get   } from "svelte/store";
import { persistentStore   } from "zeelte/js";
import { data, form, error, statuscode } from "$lib/js/zp.fetch.js" ; // hook zp_fetch() Stores
import { ZP_ApiRouter      } from "$lib/js/class.zp.apirouter.js";
import { ZP_EventDetails   } from "zeelte";

const debug = false

// ------------------------------
// Persistent UI state
// ------------------------------
export const showApp       = persistentStore('ZPDev_showApp', 'PAGE.SERVER.PHP')
export const showDumpPanel = persistentStore('ZPDev_showDumpPanel', 'data')
export const dataDumpPanel = writable()

// ------------------------------
// Local API/Dev state stores
// ------------------------------
export const promise_fetch = writable()
export const zpED_js       = writable()
export const zpED_svelte   = writable()
export const zpAR_js       = writable()
export const zpAR_svelte   = writable()
export const zpAR_pageJS   = writable()
export const zpAR_php      = writable()
export const zpDB_php      = writable()
export const zpENV_php     = writable()

// ------------------------------
// Navigation AppTabs and DumpTabs
// ------------------------------
export const appTabs = [
     { key: "PAGE.SERVER.PHP", label: "+page.server.php" },
     { key: "SERVER.PHP",      label: "+server.php" },
     { key: "DB",              label: "DB" }
];

export const dumpTabs = [
    { key: "data",         store: data,         label: "data"   },
    { key: "form",         store: form,         label: "form"   },
    { key: "error",        store: error,        label: "error"  },
    { key: "_zpED_js",     store: zpED_js,      label: "ED js"  },
    { key: "_zpED_svelte", store: zpED_svelte,  label: "ED sv"  },
    { key: "_zpAR_js",     store: zpAR_js,      label: "AR js"  },
    { key: "_zpAR_svelte", store: zpAR_svelte,  label: "AR sv"  },
    { key: "_zpAR",        store: zpAR_php,     label: "AR php" },
    { key: "_zpDB",        store: zpDB_php,     label: "DB"     },
    { key: "_zpENV",       store: zpENV_php,    label: "ENV"    }
];
console.log('stores in zpdev.stores.js:', { data, form, error, statuscode });


dumpTabs.forEach(({key, store}) => {
  console.log(`${key}: valid store?`, store && typeof store.subscribe === 'function');
});

console.log('showDumpPanel:', showDumpPanel && typeof showDumpPanel.subscribe === 'function');


derived([data, form, error], ([$data, $form, $error]) => {
     const sources = [
          { value: $data,  store: data },
          { value: $form,  store: form },
          { value: $error, store: error }
     ];

     sources.forEach(({ value, store }) => {
          if (!value || typeof value !== "object") return;

          let rest = { ...value };
          let changed = false;

          dumpTabs.forEach(tab => {
               if (rest.hasOwnProperty(tab.key)) {
               const vv = rest[tab.key];
               tab.store.set(vv);
               delete rest[tab.key];
               changed = true;
               }
          });

          // Always set the cleaned object back
          if (changed) {
               store.set(isEmpty(rest) ? undefined : rest);
          }
     });
}).subscribe(() => {});


// ------------------------------
// Derived: dumpTabsHasData
// ------------------------------
export const dumpTabsHasData = derived(
  dumpTabs.map(t => t.store),
  (values) =>
    dumpTabs.map((t, i) => ({
      key: t.key,
      value: values[i],
      hasData: !isEmpty(values[i]),
    }))
);

// Derived: current dump panel’s content
export const currentDumpPanelValue = derived(
    [showDumpPanel,   ...dumpTabs.map(t => t.store)],
    ([$showDumpPanel, ...values]) => {
        const tabIndex = dumpTabs.findIndex(t => t.key === $showDumpPanel);
        if (tabIndex === -1) return '-'; // Not found
        return values[tabIndex];
    }
);

// Keep `dataDumpPanel` in sync automatically
currentDumpPanelValue.subscribe(val => dataDumpPanel.set(val));

/**
 * Initializes dashboard and API state before a fetch.
 * @param e Optional event object
 */
export function init_ZPDev(event = undefined) {
     //debug &&
     console.clear()
     debug && console.log('# init_ZPDev()')
     event.preventDefault()
     resetLocalStores()
     // show the manually the pre steps of zp_fetch(event) ..
     zpED_svelte.set(new ZP_EventDetails(event))
     zpAR_svelte.set(new ZP_ApiRouter(zpED_svelte))
     // show correct DumpPanel
     if (get(zpED_svelte).action) {
          debug && console.log('# setDumpPanel()')
          showDumpPanel.set('form')
     }
     else 
          showDumpPanel.set('data')
     debug && console.log('/ init_ZPDev()')
}

/**
 * Resets all API state variables to initial values.
 */
export function resetLocalStores() {
     debug && console.log(" #/ resetLocalStores()");
     [    data, form, error, statuscode,
          zpED_js, zpED_svelte,
          zpAR_js, zpAR_svelte, zpAR_pageJS,
          zpAR_php, zpDB_php, zpENV_php
     ].forEach(s => s && typeof s.set === 'function' && s.set(undefined));
}

function isEmpty(val) {
     if (val == undefined) return true;
     if (val == null)      return true;
     if (typeof val === "string" && val.trim().length === 0) return true;
     if (typeof val === "number" && val === 0) return true;
     if (Array.isArray(val) && val.length === 0) return true;
     if (typeof val === "object" && !Array.isArray(val)) {
          const meaningfulKeys = Object.keys(val).filter(k => !isEmpty(val[k]));
          return meaningfulKeys.length === 0;
     }
     return false;
}



