<script>
     import { DocToc } from 'zeelte';

     let {
          data,
          children
     } = $props()

</script>

<DocToc toc_data={data.mainToc || []}>
     {@render children()}
</DocToc>