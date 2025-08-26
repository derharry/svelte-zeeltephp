<script>
      import '@picocss/pico'
      import '../app.css'
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

<main>
      <Navigation data_nav={menu} />
      <article>
            {@render children()}
      </article>
</main>

<style>

      main {
            background-color: none;
            height: 100vh;
            overflow: hidden;
            display: grid;
            grid-template-rows: auto 1fr;
      }

      article {
            overflow: auto;
      }

</style>


