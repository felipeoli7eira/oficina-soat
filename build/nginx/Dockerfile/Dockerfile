FROM nginx:1.28

WORKDIR /var/www/html

COPY code .

# Copia configurações customizadas do nginx (descomente se necessário)
# COPY ./docker/nginx/http.d/default.conf /etc/nginx/conf.d/default.conf
# COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

COPY ./build/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

COPY ./build/nginx/entrypoint/docker-entrypoint.sh /usr/local/bin
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Define o entrypoint customizado
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

CMD ["nginx", "-g", "daemon off;"]
