FROM nginx:alpine
WORKDIR '/application'


RUN echo "$(cd /application && ls -l)"

# Make translations directory writtable from web
RUN chmod 0777 translations/ -R