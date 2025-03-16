<script>

      import VarDump from '$lib/VarDump.svelte';

      export let data;
      //$: console.log(data);

</script>

<VarDump title="PHP responses from routes" vardump={data} />
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
