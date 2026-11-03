<?php

return [

    /*
    | Quote requests are limited per IP address. The enquiry notification
    | email is queued, so a queue worker must be running in production.
    |
    */

    'quote_rate_limit' => (int) env('QUOTE_RATE_LIMIT', 5),

    'quote_rate_decay_seconds' => (int) env('QUOTE_RATE_DECAY_SECONDS', 3600),

];
