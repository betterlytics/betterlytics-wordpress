# Site Search

**Site Search Tracking** gives you insights into what your visitors are searching for on your website.

## How it works

Site search tracking integrates directly with the WordPress search engine:

1.  **Backend Detection**: The plugin hooks into the WordPress `template_redirect` action. It uses `is_search()` to determine if the visitor is viewing a search results page.
2.  **Query Extraction**: If a search is active, the plugin retrieves the query string using the native `get_search_query()` function.
3.  **Event Queuing**: The search query is added to the global `$betterlytics_queued_events` PHP array.
4.  **Frontend Firing**: During the `wp_footer` action, the plugin outputs a JavaScript block that calls `betterlytics.event()` to transmit the search term to your dashboard.

This ensures that only actual WordPress searches are tracked, and the query is sanitized according to your WordPress settings.

## Data Captured

*   **Event Name**: `search`
*   **Properties**:
    *   `query`: The keyword or phrase the visitor searched for.

## Internal Binding

*   **Action**: `template_redirect`
*   **WordPress Checks**: `is_search()`, `get_search_query()`
*   **Method**: `Betterlytics_Hooks::track_page_events()`
