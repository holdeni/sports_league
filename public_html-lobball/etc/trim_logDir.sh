#!/bin/sh

# $Id: trim_logDir.sh 166 2011-06-08 20:02:31Z Henry $
# Last Change: $Date: 2011-06-08 16:02:31 -0400 (Wed, 08 Jun 2011) $

# ... move into the log directory and delete files that were modified over 7 days ago
cd public_html/application/logs
find . -type f -mtime +7 -name "log-*.php" -print -delete