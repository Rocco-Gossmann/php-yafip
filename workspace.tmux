#!/bin/bash

tmux-workspace "php-YaFiP (Yet another Framework in PHP)" "editor" -c "nvim && zsh"\
    -w "lib"    -c "cd ./lib && nvim && zsh"\
    -w "server" -c "./run"
