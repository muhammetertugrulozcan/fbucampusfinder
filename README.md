# FbuCampus Finder 🎓 - Lost & Found

A dynamic web application developed to easily report and find lost items within the Fenerbahçe University campus. 

*Note: This project was developed as a requirement for the **Web Application Development** course. It was previously hosted and live at `fbucampusfinder.gt.tc`, but is currently showcased here as an open-source repository.*

## 🚀 Tech Stack
As per the course curriculum requirements for server-side programming, this project was built using a traditional stack:
* **Backend:** PHP
* **Database:** MySQL
* **Frontend:** HTML, CSS

## 💻 Local Setup (Localhost)
If you want to run and test this project on your local machine, follow these steps:

1. Clone or download this repository.
2. Move the extracted folder into the `htdocs` directory of your XAMPP installation (e.g., `C:\xampp\htdocs\fbucampusfinder`).
3. Open the XAMPP Control Panel and start the **Apache** and **MySQL** services.
4. Navigate to `http://localhost/phpmyadmin` and create a new database for the project.
5. Import the provided `.sql` file (if available) into your new database to set up the tables.
6. Ensure your database credentials (username, password, db name) are correctly configured in the `db.php` file.
7. Open your browser and go to `http://localhost/fbucampusfinder` to view the application.

## 📂 Project Structure
* `index.php` - Main page displaying recently reported lost/found items
* `add_item.php` - Form to report a new lost or found item
* `detail.php` - Detailed view of a specific item
* `login.php` / `register.php` / `auth.php` - User authentication 
* `db.php` - Database connection configuration
* `admin.php` - Admin dashboard for managing reports
* `style.css` - Stylesheet for the application

---
*Developer:* Muhammet Ertugrul Ozcan , Mert Ali Mirzanli
