<?php

require_once __DIR__ . '/includes/upgrade.php';
require_once __DIR__ . '/includes/urls.php';
require_once __DIR__ . '/includes/templates.php';
require_once __DIR__ . '/includes/routes/route.php';
require_once __DIR__ . '/includes/routes/attendee/list.php';
require_once __DIR__ . '/includes/routes/event/create.php';
require_once __DIR__ . '/includes/routes/event/details.php';
require_once __DIR__ . '/includes/routes/event/edit.php';
require_once __DIR__ . '/includes/routes/event/trash.php';
require_once __DIR__ . '/includes/routes/event/delete.php';
require_once __DIR__ . '/includes/routes/event/list.php';
require_once __DIR__ . '/includes/routes/event/image.php';
require_once __DIR__ . '/includes/routes/event/list-trashed.php';
require_once __DIR__ . '/includes/routes/event/translations.php';
require_once __DIR__ . '/includes/routes/user/attend-event.php';
require_once __DIR__ . '/includes/routes/user/host-event.php';
require_once __DIR__ . '/includes/routes/user/my-events.php';
require_once __DIR__ . '/includes/attendee/attendee.php';
require_once __DIR__ . '/includes/attendee/attendee-repository.php';
require_once __DIR__ . '/includes/attendee/attendee-adder.php';
require_once __DIR__ . '/includes/event/event-date.php';
require_once __DIR__ . '/includes/event/event.php';
require_once __DIR__ . '/includes/event/event-repository-interface.php';
require_once __DIR__ . '/includes/event/event-repository.php';
require_once __DIR__ . '/includes/event/event-repository-cached.php';
require_once __DIR__ . '/includes/event/event-form-handler.php';
require_once __DIR__ . '/includes/notifications/notifications-schedule.php';
require_once __DIR__ . '/includes/notifications/notifications-send.php';
require_once __DIR__ . '/includes/event/event-capabilities.php';
require_once __DIR__ . '/includes/stats/stats-calculator.php';
require_once __DIR__ . '/includes/stats/stats-listener.php';
require_once __DIR__ . '/includes/project/project-repository.php';
require_once __DIR__ . '/includes/event-text-snippet.php';
require_once __DIR__ . '/includes/routes/attendee/list.php';
require_once __DIR__ . '/includes/routes/attendee/remove.php';
