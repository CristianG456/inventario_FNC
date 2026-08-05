# ==============================================================================
# Nginx para Inventario de Equipos (Imagen propia de producción)
# ==============================================================================
# Construir: docker build -t cristian123456/inventario-nginx:latest -f docker/nginx/Dockerfile .
# ==============================================================================

FROM nginx:alpine

# Eliminar configuración por defecto de Nginx
RUN rm -f /etc/nginx/conf.d/default.conf

# Copiar configuración personalizada
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
