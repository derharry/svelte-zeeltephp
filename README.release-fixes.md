
**tmpFix-001-v103-Routing-zp_route**
- **Problem:**
  - ApiRouter (CSR) should send the `zp_route` without `.env.BASE` in the URL, but currently does so.
  - When running the build, `zp_route.replace(BASE, '')` is not happening. The project name is still present in the URL.
  - Or did I forget to implement the counterpart in PHP using `.env.PUBLIC_BASE`?
- **OK:** Works as expected when running in dev mode.
- **Fix:** `class.zp.apirouter.php` / `check_route()` does the counterpart (replaces BASE/PUBLIC_BASE).
- **Open:** Why is the replacement not happening in CSR? Or, at least for now, is the PHP counterpart required?
