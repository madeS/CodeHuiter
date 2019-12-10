#!/usr/bin/env bash

git fetch --all
git reset --hard origin/master

composer install --no-dev
