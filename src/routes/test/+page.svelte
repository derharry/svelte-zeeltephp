<script>

      import { base } from "$app/paths";

      export let data;

      $: console.log(data);

</script>


<h1>SubPage to ZeeltePHP</h1>
<ul>
      <li><a href="{base}/">Route home</a></li>
      <li>.env BASE = {base}</li>
      <li>data.res_page = {data.res_page}</li>
      <li>data.res_php &nbsp;= {data.res_php}</li>
</ul>
