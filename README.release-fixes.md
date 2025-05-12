

* tmpFix-001-v103-Routing-zp_route
- problem - Svelte-ApiRouter (CSR) should send the zp_route without .env.BASE in url, but does.
- ok   - run dev
- bug  - run build zp_route.replace(BASE, '') is not happening. The project name is still in url?.
         or did i forget doing the counterpart at PHP using .env.PUBLIC_BASE?
- fix  - class.zp.apirouter.php/check_route() does do counterpart (replace BASE/PUBLIC_BASE)
- open - why its not replacing at CSR? or, at least for now, is php-counterpart required?