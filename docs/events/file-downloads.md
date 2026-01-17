# File Downloads

**File Download Tracking** automatically tracks when visitors download files from your website.

## How it works

Betterlytics adds a click listener to your site. If a user clicks a link that points to a file with a supported extension, a download event is recorded.

## Supported Extensions

The following file types are tracked automatically:
*   Documents: `.pdf`, `.doc`, `.docx`, `.xls`, `.xlsx`, `.ppt`, `.pptx`
*   Archives: `.zip`, `.rar`, `.tar`, `.gz`, `.dmg`
*   Executables: `.exe`

## Data Captured

*   **Event Name**: `file-download`
*   **Properties**:
    *   `url`: The full URL of the file.
    *   `filename`: The name of the file (e.g., `brochure.pdf`).
