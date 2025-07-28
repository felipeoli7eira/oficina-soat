FROM nginx:1.28

WORKDIR /var/www/html

COPY app .

COPY ./build/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

COPY ./build/nginx/entrypoint/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

CMD ["nginx", "-g", "daemon off;"]
