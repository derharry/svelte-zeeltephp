<script>
// ZPDevDDS.svelte
          
     import { data, form, error } from "$lib/zeeltephp/zp.fetch.js"
     import { 
          showApp, showDumpPanel,
          dataDumpPanel,
          appTabs, dumpTabs,
          zpED_js,
          zpED_svelte,
          zpAR_js,
          zpAR_svelte,
          zpAR_pageJS,
          zpAR_php,
          zpDB_php,
          zpENV_php,
          dumpTabsHasData
     } from "./zpdev.stores.js";

     const debug = true


     let {
          promise_fetch,
     } = $props();

     $effect.pre(() => {
          const sources = [
               { value: $data,  store: data },
               { value: $form,  store: form },
               { value: $error, store: error }
          ];
          sources.forEach(({ value, store }) => {
               let matched = false;
               dumpTabs.forEach(tab => {
                    if (value?.[tab.key] !== undefined) {
                    matched = true;
                    const vv = value[tab.key];
                    console.log(`match: ${tab.key}`, vv);
                    tab.store.set(vv);                  // push value to target store
                    store.set({ ...value, [tab.key]: undefined }); // remove from source
               }
               });
               if (!matched && value && Object.keys(value).length === 0) {
                    store.set(undefined);
                    console.log('  match#//& ', value, dataDumpPanel)
               }
          });
     });


     $effect.pre(() => {
          let panelValue;
          switch ($showDumpPanel) {
               case 'data':   panelValue = $data; break;
               case 'form':   panelValue = $form; break;
               case 'error':  panelValue = $error; break;
               case '_zpAR':  panelValue = $zpAR_php; break;
               case '_zpDB':  panelValue = $zpDB_php; break;
               case '_zpENV': panelValue = $zpENV_php; break;
               default:       panelValue = {};
          }
          dataDumpPanel.set(panelValue);
     });
</script>