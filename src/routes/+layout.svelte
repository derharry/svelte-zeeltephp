<script>
      //import '@picocss/pico'
      import '../app.css'
      import { Navigation } from "zeelte";

      let { children } = $props();

      const menu = [
            { label: 'Documentation',  url: '/docs/introduction' },
            { label: 'Lib',            url: '/docs_lib'            },
            { label: 'ZPDev',          url: '/zpdev'               },
            //{ label: 'Usage Examples', url: '/zp_fetch_PAGE'  },
            //{ label: 'What Else',      url: '/what_else'      }
      ];

</script>

<main>
      <Navigation data_nav={menu}>
            <div slot="logo"><a href="/">ZeeltePHP</a></div>
            <div slot="right"><input type="search"></div>
      </Navigation>
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


