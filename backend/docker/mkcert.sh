#!/usr/bin/env bash

# Install mkcert (https://github.com/FiloSottile/mkcert)
mkcert=`which mkcert`
if [[ $mkcert == '' ]]; then
  uname_out="$(uname -s)"
  case "${uname_out}" in
      Linux*)     machine="linux";;
      Darwin*)    machine="darwin";;
      #CYGWIN*)    machine="windows";;
      #MINGW*)     machine="windows";;
      #MSYS_NT*)   machine="windows";;
      *)          machine="UNKNOWN"
  esac

  uname_out="$(uname -m)"
  case "${uname_out}" in
      x86_64*)    architecture="amd64";;
      aarch64*)   architecture="arm64";;
      arm64*)     architecture="arm64";;
      arm*)       architecture="arm";;
      *)          architecture="UNKNOWN"
  esac


  if [[ "$machine" == "UNKNOWN" || "$architecture" == "UNKNOWN" ]]; then
    echo "Install mkcert manually to run this script: https://github.com/FiloSottile/mkcert ";
    exit 1;
  else
    echo "Installing mkcert..."
    if [[ "$machine" == "linux" ]]; then
      wget -q -O mkcert https://dl.filippo.io/mkcert/latest?for=${machine}/${architecture}
    elif [[ "$machine" == "darwin" ]]; then
      curl -s -o mkcert https://dl.filippo.io/mkcert/latest?for=${machine}/${architecture}
    fi
    chmod +x mkcert
    if [[ "$UID" != 0 ]]; then
      group=$([ "$machine" == 'linux' ] && echo 'root'|| echo 'wheel')
      sudo chown root:${group} mkcert
      sudo mv mkcert /usr/local/bin
    else
      mv mkcert /usr/local/bin
    fi
  fi
fi

# Read the host name from .env
source .env
if [[ $HOST == '' ]]; then
  echo HOST in .env can not be empty
  exit 1
fi

# Install nss tools for the appropriate distro
apt=`which apt-get`
yum=`which yum`
macos=`which sw_vers`
arch=`which pacman`
if [[ $apt != "" ]]; then
  sudo apt -y install libnss3-tools
elif [[ $yum != "" ]]; then
  sudo yum install nss-tools
elif [[ $macos != "" ]]; then
  sudo brew install nss
elif [[ $arch != "" ]]; then
  sudo pacman -S nss
fi

# Install de CA
mkcert -install
# Clean previous certificates
rm -f nginx/certs/*
# Create the certificate for the host and its subdomains
mkcert -key-file ssl-cert.key -cert-file ssl-cert.pem $HOST *.$HOST
# Copy the files to Nginx
cp ssl-cert.* nginx/certs/
