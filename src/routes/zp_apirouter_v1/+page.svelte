<script>

      import { onMount } from "svelte";
      //import { zp_fetch_api } from "$lib/zeeltephp/zeeltephp.api";
      import { zp_fetch_api } from "$lib/zeeltephp/zp.fetch.api";
      import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";
      import VarDump from "$lib/VarDump.svelte";


      let zpAR_Svelte = new ZP_ApiRouter();
      let zpAR_zpPHP  = {};

      onMount(async () => {
            zpAR_zpPHP = await zp_fetch_api(fetch, zpAR_Svelte);
            zpAR_zpPHP = zpAR_zpPHP?.data || zpAR_zpPHP;
      });

</script>

<table>
      <tbody>
            <tr>
                  <td class="zp50 top"><VarDump title="ZP_ApiRouter (Svelte)" table_data={zpAR_Svelte} /></td>
                  <td class="zp50 top"><VarDump title="ZP_ApiRouter (PHP)"    table_data={zpAR_zpPHP} /></td>
            </tr>
      </tbody>
</table>



