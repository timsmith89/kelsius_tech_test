# Kelsius Tech Test

Welcome to my **Kelsius Tech Test**! Follow the steps below to set up the project on your local machine or server.

## Prerequisites

Before you begin, ensure you have the following installed on your machine:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

Installation methods will depend on your operating system.

## Installation

1. **Clone the Repository**

   Start by cloning this repository to your local machine:

   **git clone https://github.com/timsmith89/kelsius_tech_test.git**

   **cd kelsius_tech_test**

2. **Build the Docker Image**

    Use Docker Compose to build the app's image:

    **docker compose build**

    This will pull the necessary base images and set up the environment for the app.

3. **Start the App**

    After building the image, run the app using Docker Compose:

    **docker compose up -d** (-d will detach Docker from the terminal)

4. **Access the App**

    The app should now be running. Open your browser and navigate to the following address:

    http://localhost:8080

    You should be able to see the app in action.

5. **View the Database**

    The Docker container includes phpmyadmin which will allow you to view any data stored in the database

    http://localhost:8081

    User Name: **root**
    Password: **root**

    The database should have been populated with some fake data (including users, posts & comments).

6. **Changing Number of Fake Users Created**

    Open docker-compose.yml and look for this line:

    **sh -c "/usr/local/bin/wait-for-it mysql:3306 --timeout=30 -- php /var/www/html/docker/populate_db.php && apache2-foreground"**

    To change the number of fake users generated, simply add a number after populate_db.php:

    Example: **sh -c "/usr/local/bin/wait-for-it mysql:3306 --timeout=30 -- php /var/www/html/docker/populate_db.php 5 && apache2-foreground"**

    After making this change, run the following commands:

    **docker compose down -v**

    **docker compose build**

    **docker compose up -d**