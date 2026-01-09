
// Polyfill for http-deceiver used by webpack-dev-server -> spdy
// Fixes "Error: No such module: http_parser" in Node.js 12+ due to removal of process.binding('http_parser')

const originalBinding = process.binding;
process.binding = function (name) {
  if (name === 'http_parser') {
    return {
      HTTPParser: {
        methods: [
          'DELETE', 'GET', 'HEAD', 'POST', 'PUT', 'CONNECT', 'OPTIONS', 'TRACE', 
          'COPY', 'LOCK', 'MKCOL', 'MOVE', 'PROPFIND', 'PROPPATCH', 'SEARCH', 
          'UNLOCK', 'BIND', 'REBIND', 'UNBIND', 'ACL', 'REPORT', 'MKACTIVITY', 
          'CHECKOUT', 'MERGE', 'M-SEARCH', 'NOTIFY', 'SUBSCRIBE', 'UNSUBSCRIBE', 
          'PATCH', 'PURGE', 'MKCALENDAR', 'LINK', 'UNLINK'
        ],
        kOnHeaders: 0,
        kOnHeadersComplete: 1,
        kOnBody: 2,
        kOnMessageComplete: 3
      }
    };
  }
  return originalBinding.apply(process, arguments);
};

require('../node_modules/webpack-dev-server/bin/webpack-dev-server.js');
