<script>
      import VarDump from "$lib/zeeltephp/VarDump.svelte";
      export let data;

      //destruct data from +page.js
      const dataCSR = data['+page.js']
      const dataPHP = data['+page.server.php']
</script>



<h1>GET</h1>
<p>Debug Info for GET ZPHP</p>
<table class="tableCompact">
      <tbody>
            <tr>
                  <td class="td50" style="max-width:50%">
                        <VarDump title={dataCSR.message} vardump={dataCSR.csrAR} />
                  </td>
                  <td class="td50" style="max-width:50%">
                        <VarDump title={dataPHP.message} vardump={dataPHP.zpAR} />
                  </td>
            </tr>
            <tr>
                  <td class="td50" style="max-width:50%">
                        <VarDump title="+page.js/csrED" vardump={dataCSR.csrED} />
                  </td>
                  <td class="td50" style="max-width:50%">
                        <VarDump title="PHP/Requests" vardump={dataPHP.request} />
                  </td>
            </tr>
      </tbody>
</table>

<!--<pre style="overflow:auto">{JSON.stringify(data.csrAR, null, 2)}</pre>-->
<!--<pre style="overflow:auto">{JSON.stringify(data.phpAR, null, 2)}</pre>-->
<!--<pre style="overflow:auto">{JSON.stringify(data.csrED, null, 2)}</pre>-->
