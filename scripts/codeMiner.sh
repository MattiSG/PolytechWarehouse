#!/bin/bash

find . -type f -name "*.php" -exec grep -n -H "$1" {} \;