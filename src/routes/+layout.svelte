<script>            
      import '@picocss/pico'
      import '../app.css'

      //
      import { base } from "$app/paths";
      import { page } from '$app/state';
      
</script>


<div class="layoutApp">
      <nav>
            <ul>
                  <li><a href="{base}/">Home</a></li>
                  <li><a href="{base}/01_basics">Basics</a></li>
                  <li><a href="{base}/02_apirouter">ApiRouter</a></li>
                  <li><a href="{base}/03_database">Database</a></li>
                  <li><a href="{base}/04_send-data">Send-Data</a></li>
                  <li><a href="{base}/05_GET">GET</a></li>
                  <!--
                  <li><a href="{base}/01_zp_phproutes">ZP_Routes</a></li>
                  <li><a href="{base}/02_db_wordpress">ZP_DB</a></li>
                  <li><a href="{base}/04_send-data">Send-Data</a></li>
                  <li><a href="{base}/zp_apirouter_v1/??/example1=69&foo=bar">ZP_ApiRouter</a></li>
                  <li><a href="{base}/zp_apirouter??/example1=69&foo=bar">ZP_ApiRouter</a></li>
                  <li><a href="{base}/send-data">Send-Data</a></li>
                  -->
            </ul>
      </nav>
      <div class="routeBar">
            <!--{base}{page.route.id}{page.url.search}
            <br>-->
            {page.url.pathname}{page.url.search}
      </div>

      <slot />
</div>
