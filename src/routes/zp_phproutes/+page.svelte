<script>
    import TableShowData from '$lib/VarDump.svelte';

      
      export let data;
      //$: console.log(data);

      const data_php_routes = {
            '+page.js':             data.res_page,
            '+page.server.php':     data.res_pageserver,
            './lib/lib.static.php': data.res_phplib,
            './lib/sub/lib.sub.static.php': data.res_phplibsub,
            '$lib/zplib/zplib.php': data.res_phpzplib,
      }

</script>

<TableShowData title="PHP responses from routes" table_data={data_php_routes} />
<!--
<h2>Basic PHP response</h2>
<ul>
      <li>{PUBLIC_ZEELTEPHP_BASE}</li>
      <li>BASE = {base}</li>
      <li>{data.res_page}</li>
      <li>{data.res_php1}</li>
      <li>{data.res_php2}</li>
      <li>{data.res_php3}</li>
</ul>
-->
