<script>            
     import { base } from "$app/paths";
     import { zp_page_route } from "$lib/zeeltephp/zp.tools.js";
     let { children } = $props();
</script>

<div>

<nav style="margin-top: -2em; margin-bottom: -1em;">
      <ul>
            <li>
                  <ul>
                        <li><a href="{base}/zp_fetch_PAGE">PAGE</a></li>
                        <li><a href="{base}/zp_fetch_API">API</a></li>
                  </ul>
            </li>
            <li><a href="{base}/PHP">PHP</a></li>
      </ul>
</nav>
</div>

{@render children()}