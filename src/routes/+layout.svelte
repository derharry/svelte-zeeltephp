<script>

     import { Navigation } from "zeelte";

     let { children } = $props();

      const menu = [
            { label: 'ZeeltePHP',      url: '/'               },
            { label: 'Documentation',  url: '/docs'           },
            //{ label: 'Zeelte',         url: '/docs_zeelte'    },
            //{ label: 'Usage Examples', url: '/zp_fetch_PAGE'  },
            { label: 'ZPDev',          url: '/zpdev'          },
            //{ label: 'What Else',      url: '/what_else'      }
      ];

</script>

<Navigation data_nav={menu} />

{@render children()}
