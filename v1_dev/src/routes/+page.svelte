<script>
      
      import { dev  } from "$app/environment"
      import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
      import TableShowData from "$lib/TableShowData.svelte";

      export let data;
      //$: console.log('home data', data);

      const data_zp_basic = {
            'route is described above':   'in .routeBar (+layout.svelte)', 
            '.env.MODE':                  dev ? 'dev' : 'build',
            '.env.PUBLIC_ZEELTEPHP_BASE': PUBLIC_ZEELTEPHP_BASE,
      }
      const data_php_response = {
            '+page.server.php': data.res_php,
      }
</script>


<h1>Welkom to ZeeltePHP</h1>
<TableShowData title="data +page.js and PHP" table_data={data_php_response} />
<TableShowData title="ZP_ApiRouter" table_data={data_zp_basic}     />
