<?php

\Illuminate\Support\Facades\Schedule::command('backup:run --only-db')->hourly();
\Illuminate\Support\Facades\Schedule::command('backup:run')->daily()->at('00:00');
\Illuminate\Support\Facades\Schedule::command('backup:clean')->daily()->at('02:00');
