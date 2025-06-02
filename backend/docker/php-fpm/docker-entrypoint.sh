#!/usr/bin/env sh
set -e

echo "Service 'All': Status"
sudo rc-status -a

echo "Service 'SSHd': Stoping ..."
sudo rc-service sshd stop

echo "Service 'SSHd': Starting ..."
sudo rc-service sshd start

if [ "$1" = 'php-fpm8' ]; then
  echo "Command: '$@'"

  echo "Service '$1': Launching ..."
fi

exec $@
