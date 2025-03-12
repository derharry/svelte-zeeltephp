<script>

      import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";
      import TableShowData from "$lib/TableShowData.svelte";

      const zpAR = new ZP_ApiRouter();

</script>

<TableShowData title="ZP_ApiRouter" table_data={zpAR} />
