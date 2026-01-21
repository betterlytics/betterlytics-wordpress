# 404 Error Pages

**404 Error Tracking** allows you to see when visitors land on pages that do not exist on your site. This is helpful for identifying broken links or incorrect URLs.

## How it works

404 tracking uses a hybrid approach to ensure reliable capture of error pages:

1.  **Backend Detection**: The plugin hooks into the WordPress `template_redirect` action. It uses the native `is_404()` function to detect if the current request is resulting in a "Page Not Found" state.
2.  **Event Queuing**: If a 404 is detected, the `path` (requested URL) is added to a global PHP queue (`$betterlytics_queued_events`).
3.  **Frontend Firing**: During the `wp_footer` action, the plugin outputs a small JavaScript snippet that processes the queue and calls the Betterlytics script's `betterlytics.event()` function.

This approach is more reliable than pure client-side detection because it hooks directly into the WordPress routing logic.

## Data Captured

*   **Event Name**: `404`
*   **Properties**:
    *   `path`: The full URL path that caused the error (e.g., `/non-existent-page`).

## Internal Binding

*   **Action**: `template_redirect`
*   **WordPress Check**: `is_404()`
*   **Method**: `Betterlytics_Hooks::track_page_events()`
