# NOTEHUB - Notes Sharing Website
> A simple, clean, and beginner-friendly BCA college mini project built using Core Procedural PHP (MySQLi), MySQL, HTML5, CSS3, and basic JavaScript.

---

## 📌 Project Overview
**NOTEHUB** is an ordinary, neat, and simple notes-sharing platform developed for college students. Students can create an account, upload their class notes in PDF, DOC, or DOCX formats, search notes by title or subject, download study materials, and delete their own notes.

The user interface uses clean, natural styling with a white background, Arial typography, thin grey borders, and simple blue buttons, designed specifically as a first-year college practical project.

---

## 📁 File Structure

```text
NOTEHUB/
├── db.php           # Database connection using procedural MySQLi
├── index.php        # Homepage (small intro, search, action buttons, available notes list)
├── browse_notes.php # Browse notes page with search, subject filter, and HTML table
├── login.php        # Student login
├── register.php     # Student registration
├── upload.php       # Upload notes (PDF, DOC, DOCX up to 5 MB)
├── download.php     # Secure download handler & download counter increment
├── about.php        # Simple about page (one short paragraph)
├── contact.php      # Simple working contact form
├── logout.php       # End user session
├── style.css        # Clean, simple styling (Arial, white background, blue buttons)
├── script.js        # Basic JavaScript (mobile nav toggle, file size check, delete confirmation)
├── database.sql     # Database schema (starts completely empty with 2 tables: users & notes)
├── uploads/         # Folder where uploaded note files are stored
└── README.md        # Project setup guide
```

---

## 🛠️ How to Set Up and Run in XAMPP

### Step 1: Copy Project Folder
Copy the `NOTEHUB` folder to your XAMPP `htdocs` directory:
```text
C:\xampp\htdocs\NOTEHUB
```

### Step 2: Start Apache and MySQL
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.

### Step 3: Import database.sql
1. Open your browser and navigate to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click **New** on the left menu.
3. Enter database name: `notehub` and click **Create**.
4. Click the **Import** tab at the top.
5. Click **Choose File** and select `database.sql` from `C:\xampp\htdocs\NOTEHUB\database.sql`.
6. Click **Import** (or **Go**).
7. The database is now ready with two empty tables (`users` and `notes`).

### Step 4: Open NOTEHUB in Your Browser
Visit:
```text
http://localhost/NOTEHUB/
```

---

## 📋 Pages & Clean Features

1. **Homepage (`index.php`):**
   - Clean navigation bar: `NOTEHUB`, `Home`, `Browse Notes`, and `Login` / `Register` (or `Logout`).
   - Small heading: *"Notes Sharing Website"*.
   - Simple search input.
   - "Browse Notes" button and "Upload Notes" button.
   - Heading: *"Available Notes"*.
   - Shows actual notes fetched from MySQL, or if empty: *"No notes available."*
   - Small footer: `"NOTEHUB | Notes Sharing Website"`.

2. **Browse Notes (`browse_notes.php`):**
   - Heading: *"Browse Notes"*.
   - Search input and subject filter.
   - Displays uploaded notes in a clean HTML table.
   - If no notes exist: *"No notes found."*

3. **Login (`login.php`):**
   - Heading: *"Login"*.
   - Email and Password inputs.
   - "Login" button and link to register.

4. **Register (`register.php`):**
   - Heading: *"Registration"*.
   - Name, Email, Password, and Confirm Password inputs.
   - "Register" button and link to login.

5. **Upload Notes (`upload.php`):**
   - Heading: *"Upload Notes"*.
   - Fields: Note title, Subject, Course, Semester, Description (optional), File.
   - "Upload" button with validation.

6. **About (`about.php`):**
   - One short paragraph explaining that NOTEHUB allows students to share study notes.

7. **Contact (`contact.php`):**
   - Simple working contact form with Name, Email, Message, and submit button.

8. **Footer (all pages):**
   - Small footer: `"NOTEHUB | Notes Sharing Website"`.

---

## 🔒 Security & Code Standards

- **MySQLi Prepared Statements:** Used in all queries to prevent SQL Injection.
- **Password Hashing:** Passwords are encrypted with `password_hash()` and verified with `password_verify()`.
- **Session Authentication:** Ensures only logged-in students can upload or delete notes.
- **Ownership Verification:** A student can only delete notes where `notes.user_id` matches their own session ID.
- **Output Escaping:** `htmlspecialchars()` is applied to user-generated output to prevent XSS.
- **File Validation:** Allowed extensions are `.pdf`, `.doc`, `.docx` with a 5 MB maximum file size and safe randomized filenames.
