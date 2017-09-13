#! /bin/bash
hulk ./app/modules/*/widgets/*.mustache ./app/views/widgets/*.mustache > ./public/assets/js/widget_templates.js
minify ./public/assets/js/widgets.js ./public/assets/js/widget_templates.js  > ./public/assets/js/widgets.min.js
