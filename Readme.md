# WhatsApp Rotator Project

## Description

This project is a simple WhatsApp rotator that redirects users to different WhatsApp numbers to distribute traffic evenly. It includes a dashboard for managing numbers and viewing statistics.

## Features

*   **Redirection:** Evenly distributes users to different WhatsApp numbers.
*   **Dashboard:**
    *   Displays total redirects per phone number.
    *   Provides daily and monthly reports.
    *   Allows pagination for viewing all redirects.
    *   Enables adding and deleting WhatsApp numbers.
*   **Admin Authentication:** Simple login to protect the dashboard.
*   **Extensionless URLs:** Uses `.htaccess` to remove the `.php` extension from URLs.
*   **Modern Loading Screen:** A visually appealing loading screen before redirection.

## Requirements

*   PHP
*   MySQL
*   Apache with `mod_rewrite` enabled

## Installation

1.  **Clone the repository:**

    ```bash
    git clone [repository_url]
    ```

2.  **Set up the database:**

    *   Create a MySQL database named `rotator`.
    *   Import the provided SQL schema (if available) or let the `db.php` script create the necessary tables.
    *   Update the database credentials in `db.php`.

3.  **Configure Apache:**

    *   Ensure `mod_rewrite` is enabled.
    *   Place the `.htaccess` file in the `rotator` directory.
    *   Adjust `RewriteBase` in `.htaccess` if necessary.

4.  **Set up admin credentials:**

    *   Modify the admin username and password in `login.php`.

## Usage

1.  **Access the rotator:**

    *   Open the `index.php` file in your browser.

2.  **Access the dashboard:**

    *   Go to `dashboard` in your browser.
    *   Log in with the admin credentials.

## File Structure

*   `index.php`: Main file for redirection.
*   `dashboard.php`: Admin dashboard.
*   `login.php`: Admin login page.
*   `logout.php`: Admin logout page.
*   `db.php`: Database connection and table creation.
*   `.htaccess`: URL rewriting rules.
*   `counts.json`: Stores redirect counts for each number.
*   `readme.md`: Documentation.

## Security

*   **Important:** Change the default admin username and password in `login.php`.
*   Consider implementing more robust authentication and authorization mechanisms for production environments.

## Contributing

Feel free to contribute by submitting pull requests.

## License

[License]