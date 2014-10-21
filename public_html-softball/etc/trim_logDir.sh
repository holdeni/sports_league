#!/bin/sh

# ... move into the log directory and delete files that were modified over 7 days ago
cd $HOME/public_html/application/logs
find . -type f -mtime +7 -name "log-*.php" -print -delete