<script>

      import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
      import { dev  } from "$app/environment"
      import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter.js";
      import { ZP_EventDetails } from "$lib/zeeltephp/class.zp.eventdetails.js";
      import VarDump from "$lib/VarDump.svelte";
      
      export let data;

      let zp_EventDetails = new ZP_EventDetails();
      let zp_ApiRouter    = new ZP_ApiRouter();

      const data_zp_basic = {
            'route is described above':   'in .routeBar (+layout.svelte)', 
            '.env.MODE':                  dev ? 'dev' : 'build',
            '.env.PUBLIC_ZEELTEPHP_BASE': PUBLIC_ZEELTEPHP_BASE,
      }
      
      const data_php_response = {
            '+page.js': data.res_load,
            '+page.server.php': data.res_php,
      }
      
      function handle_click(e) {
            console.clear()
            zp_EventDetails = new ZP_EventDetails(e);
      }

</script>


<h1>Welkom to ZeeltePHP</h1>

<VarDump title="basic info" vardump={data_zp_basic}     />
<VarDump title="data from +page.js and PHP" vardump={data_php_response} />
<VarDump title="ZP_ApiRouter" vardump={zp_ApiRouter} />
