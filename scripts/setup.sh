#!/bin/sh

NC="\033[0m"
RED="\033[0;31m";
GREEN="\033[0;32m";
BLUE="\033[0;34m";

if ! $(wp core is-installed); then
  echo "";
  echo "${RED}No installed WordPress site found.${NC}";
  echo "${GREEN}?${NC} Should we install one for you? [y/n]";
  old_stty_cfg=$(stty -g)
  stty raw -echo ; answer=$(head -c 1) ; stty $old_stty_cfg
  if echo "$answer" | grep -iq "^y" ;then
      echo "Yes, let’s create a site:";
      echo "";
      printf "  ${GREEN}TITLE:${NC} " && read TITLE;
      printf "  ${GREEN}ADMIN USER:${NC} " && read ADMIN_USER;
      printf "  ${GREEN}ADMIN EMAIL:${NC} " && read ADMIN_EMAIL;
      echo "";
      wp core install --url=$WP_HOME --title=$TITLE --admin_user=$ADMIN_USER --admin_email=$ADMIN_EMAIL
  else
      echo "No"
  fi
else
  echo "";
  echo "${BLUE}A WordPress site is already installed.${NC}";
fi
