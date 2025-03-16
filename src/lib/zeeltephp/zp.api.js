
import { ZP_ApiRouter } from "./class.zp_apirouter";

export function zp_fetch_api(fetch, urlOrEvent = undefined, data = undefined, method = undefined) {
      const debug = false;
      try {
            const zpar = new ZP_ApiRouter(urlOrEvent, data, method);

            return new Promise((resolve, reject) => {

                  // we need the route - error
                  if (!zpar.route) {
                        reject(new Error("API route is undefined"));
                        return; // exit
                  }
                  if (debug) zpar.dump();

                  resolve(true)
            });
      } catch (error) {
            console.error(error)
      }
}