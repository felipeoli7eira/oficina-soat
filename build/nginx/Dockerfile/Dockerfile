FROM nginx:1.28

WORKDIR /var/www/html

COPY app .

COPY ./build/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
