FROM nginx:alpine

COPY nginx.conf /etc/nginx/nginx.conf
COPY default.conf /etc/nginx/http.d/default.conf
COPY index.html app.js /usr/share/nginx/html/

EXPOSE 80
