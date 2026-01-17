# 404 Error Pages

**404 Error Tracking** allows you to see when visitors land on pages that do not exist on your site. This is helpful for identifying broken links or incorrect URLs.

## How it works

Betterlytics listens for the standard WordPress 404 event (`is_404()`). When a 404 page is loaded, an event is automatically sent to your dashboard.

## Data Captured

*   **Event Name**: `404`
*   **Property**: `path` (The full URL path that caused the error, e.g., `https://example.com/broken-link`)
