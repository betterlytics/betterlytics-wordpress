# File Downloads

**File Download Tracking** automatically tracks when visitors download files from your website.

## How it works

Unlike 404 and search tracking, file download tracking is handled entirely on the client side:

1.  **Event Listener**: The plugin enqueues a JavaScript file (`betterlytics-events.js`) that attaches a `click` event listener to the `document` object.
2.  **Element Inspection**: When a user clicks a link (`<a>` tag), the script inspects the `href` attribute.
3.  **Extension Matching**: The script uses a regular expression to check if the file extension matches a list of supported download formats.
4.  **Event Firing**: If a match is found, the script calls `betterlytics.event('file-download', ...)` with the file URL and filename.

This approach ensures that all download links are tracked automatically, even those pointing to external servers or added dynamically via JavaScript.

## Supported Extensions

Betterlytics tracks the following file types by default:

*   **Documents**: `.pdf`, `.doc`, `.docx`, `.xls`, `.xlsx`, `.ppt`, `.pptx`
*   **Archives**: `.zip`, `.rar`, `.tar`, `.gz`, `.dmg`
*   **Executables**: `.exe`

## Data Captured

*   **Event Name**: `file-download`
*   **Properties**:
    *   `url`: The full source URL of the file.
    *   `filename`: The name of the file extracted from the URL path.

## Internal Binding

*   **JavaScript Hook**: `document.addEventListener('click', ...)`
*   **Regex Pattern**: `/\.(pdf|zip|doc|docx|xls|xlsx|ppt|pptx|exe|dmg|tar|gz|rar)$/i`
*   **Script**: `public/js/betterlytics-events.js`
