//zp-static-api.js
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';

const __dirname = dirname(fileURLToPath(import.meta.url));

export const PATHS = {
  INTERNAL: {
    API: join(__dirname, 'static/api'),
    ZPLIB: join(__dirname, 'src/lib/zplib'),
    ROUTES: join(__dirname, 'src/routes')
  },
  CONSUMER: {
    API: null,
    ZPLIB: null // Set by plugin
  }
};
