<script>
      import { base } from "$app/paths";
      import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";

      export let data;
      $: console.log(data);
</script>


<h2>Home</h2>
<ul>
      <li>{PUBLIC_ZEELTEPHP_BASE}</li>
      <li>BASE = {base}</li>
      <li>{data.res_page}</li>
      <li>{data.res_php1}</li>
      <li>{data.res_php2}</li>
</ul>
