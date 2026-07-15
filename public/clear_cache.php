<?php
header('Content-Type: text/plain');
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared successfully!\n";
} else {
    echo "OPcache is not enabled or opcache_reset is not available.\n";
}
