<script>            
     import '@picocss/pico'
     import '../app.css'
     import { base } from "$app/paths";
     import { zp_page_route } from "$lib/zeeltephp/zp.tools.js";
</script>

<div class="layoutApp">
     <nav>
           <ul>
                 <li><a href="{base}/">Home</a></li>
                 <li><a href="{base}/zp_fetch_API">Usage Examples</a></li>
                 <li><a href="{base}/zpdev">ZPDev</a></li>
                 <li><a href="{base}/apidemo">API-Demo</a></li>
                 <!--<li><a href="{base}/documentation">Documentation</a></li>-->
           </ul>
     </nav>
     <div class="routeBar">{zp_page_route()}</div>
     <slot />
</div>