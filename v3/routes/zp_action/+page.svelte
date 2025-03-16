<script>

      import { onMount } from "svelte";
      import { zp_fetch_api } from "$lib/zeeltephp/zeeltephp.api";
      import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";
      import VarDump from "$lib/VarDump.svelte";
      import { page } from "$app/state";

      const zpAR = new ZP_ApiRouter();
      let data   = undefined

      onMount(async () => {
            //data = await zp_fetch_api(fetch, page.url);
            //console.log(page.url.search)


      })

</script>

<VarDump title="ZP_ApiRouter" table_data={zpAR} />
