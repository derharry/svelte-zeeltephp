<script>

      import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
      import { base } from "$app/paths";

      export let data;
      $: console.log(data);

</script>


<h1>Welcome to ZeeltePHP</h1>
<ul>
      <li><a href="{base}/test/">Route subpage test</a></li>
      <li>.env BASE = {base}</li>
      <li>data.res_page = {data.res_page}</li>
      <li>data.res_php1 = {data.res_php1}</li>
      <li>{PUBLIC_ZEELTEPHP_BASE}</li>
</ul>
