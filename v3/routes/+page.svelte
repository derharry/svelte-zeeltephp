<script>
      
      import { dev  } from "$app/environment"
      import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
      import VarDump from "$lib/VarDump.svelte";

      export let data;
      //$: console.log('home data', data);

      const data_zp_basic = {
            'route is described above':   'in .routeBar (+layout.svelte)', 
            '.env.MODE':                  dev ? 'dev' : 'build',
            '.env.PUBLIC_ZEELTEPHP_BASE': PUBLIC_ZEELTEPHP_BASE,
      }
      const data_php_response = {
            '+page.js': data.res_load,
            '+page.server.php': data.res_php,
      }
</script>


<h1>Welkom to ZeeltePHP</h1>
<VarDump title="basic info" table_data={data_zp_basic}     />
<VarDump title="data +page.js and PHP" table_data={data_php_response} />
<VarDump />