<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config("app.name") }} API Documentation</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Source+Code+Pro:300,600|Titillium+Web:400,600,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.1.0/swagger-ui.min.css" >

    <link rel="icon" type="image/png" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@4.1.0/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@4.1.0/favicon-16x16.png" sizes="16x16" />
</head>

<body>
<div id="swagger-ui"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.1.0/swagger-ui-bundle.min.js"> </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.1.0/swagger-ui-standalone-preset.min.js"> </script>
<script>
    window.onload = function() {
        // Build a system
      window.ui = SwaggerUIBundle({
          dom_id: '#swagger-ui',

          url: "/api/api-docs",
          operationsSorter: null,
          configUrl: null,
          validatorUrl: null,
          oauth2RedirectUrl: "",

          requestInterceptor: function (request) {
            const x = document.cookie.split("; ").map(x => x.split("=")).find(x => x[0] === "XSRF-TOKEN");

            if (x) {
              request.headers['X-XSRF-TOKEN'] = decodeURIComponent(x[1]);
            }
            return request;
          },

          presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
          ],

          plugins: [
            SwaggerUIBundle.plugins.DownloadUrl
          ],

          layout: "StandaloneLayout"
        })
    }
</script>
</body>

</html>
