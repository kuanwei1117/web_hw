#!/bin/bash
# @Author: gunjianpan
# A docker of build thinkphp env, support dev(support hot update) & production
# :paprams <deploy_env> <frontend_port> <tag> <container_name>

set -e

if [ -n "$(which docker | sed -n "/not/p")" ]; then
    echo "can't find docker, please install docker first!!!"
    exit 1
fi
chmod 765 .

# default paprams
deploy_env='dev'
frontend_port=8848
tag="gunjianpan/thinkphp_${deploy_env}:v0.0.2"
container_name="thinkphp_${deploy_env}"
current_dir=$(pwd)
ip=$(/sbin/ifconfig -a | grep inet | grep -v 127.0.0.1 | grep -v inet6 | awk '{print $2}' | tr -d "addr:")

if [ ! -z "${5}" ]; then
    echo 'The maximum of params is 4!!! please be check!!!'
    echo "Usage: ${0} <deploy_env> <frontend_port> <tag> <container_name>"
fi

if [ ! -z "${1}" ]; then deploy_env=${1}; fi
if [ ! -z "${2}" ]; then frontend_port=${2}; fi
if [ ! -z "${3}" ]; then tag=${3}; fi
if [ ! -z "${4}" ]; then container_name=${4}; fi

tag_nov=$(cut -d':' -f1 <<<${tag})

if [ ! -n "$(echo ${frontend_port} | sed -n "/^[0-9]*$/p")" ]; then
    echo '<frontend_port> must be num!!! please check'
    exit 2
fi

case ${deploy_env} in
dev)
    echo '>>>>>>>Running in [dev] env, support hot supdate>>>>>>>>'
    sed -i- '/^C/d' Dockerfile && rm Dockerfile-
    ;;
product)
    sed -i- '/^C/d' Dockerfile && rm Dockerfile-
    echo 'COPY . /var/www/html' >>Dockerfile
    echo '<<<<<<<Running in [product] env<<<<<<<'
    ;;
*)
    echo '<deploy_env> only be dev or product!!!'
    exit 3
    ;;
esac

if [ ! -n "$(docker images ${tag_nov} | sed -n '2'p)" ]; then
    echo '----------BEGIN BUILD DOCKER IMAGE----------'
    docker build -t ${tag} .
fi

if [ -n "$(docker ps --filter name=${container_name} | sed -n '2'p)" ]; then
    echo '----------STOP RUNNING DOCKER CONTAINER----------'
    docker stop ${container_name}
fi

if [ -n "$(docker ps -a --filter name=${container_name} | sed -n '2'p)" ]; then
    echo '----------CLEAN EXISTENCE DOCKER CONTAINER----------'
    docker rm ${container_name}
fi

port_used() {
    echo "Port ${1} already in use"
    exit 4
}
open_ports=$(netstat -an -ptcp | grep LISTEN | awk '{print $4}' | sed 's/^.*\.//')
grep -q -x ${frontend_port} <<<"${open_ports}" && port_used ${frontend_port}

case ${deploy_env} in
dev)
    docker run -it -d --name ${container_name} -v $(pwd):/var/www/html -p ${frontend_port}:80 ${tag}
    ;;
product)
    docker run -it -d --name ${container_name} -p ${frontend_port}:80 ${tag}
    ;;
esac

echo "~~~~~~~~NOW YOU CAN USE thinkPHP IN http:://${ip}:${frontend_port} ~~~~~~~~"
