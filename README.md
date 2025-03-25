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

    ```bash
    git clone https://github.com/timsmith89/kelsius_tech_test.git
    cd kelsius_tech_test
    ```

2. **Build the Docker Image**

    Use Docker Compose to build the app's image:

    ```bash
    docker compose build
    ```

    This will pull the necessary base images and set up the environment for the app.

3. **Start the App**

    After building the image, run the app using Docker Compose:

    ```bash
    docker compose up -d
    ```
    -d will detach Docker from the terminal. Remove the -d from the commnd if you need to debug the application.

    By default, the app uses port 8080 & 8081. If either of these ports are already in use, you can change them in the docker-compose.yml file.

4. **Fake Data**

    When the app is initialised, you will need to go into the Docker container to install composer and then run the script which uses the Faker library.

    There is a command in the Dockerfile for installing Composer, but for whatever reason, it doesn't appear to be creating the vendor folder, so the alternative option is to manually run the following:

    ```bash
    docker compose exec php bash
    composer install && php populate_db.php 3
    ```

    If you don't provide a number in the command, it will default to creating 10 fake users.

    After running these commands, proceed to the next step.

5. **View the Database**

    The Docker container includes phpmyadmin which will allow you to view any data stored in the database

    **http://localhost:8081**

    User Name: **root**
    Password: **root**

    The database should have been populated with some fake data (including users, posts & comments).

    Each fake user created will have the password: **password**

6. **Access the App**

    The app should now be running. Open your browser and navigate to the following address:

    **http://localhost:8080**

    You should be able to see the app in action.

7. **Other points to mention**

    Each of the fake users will be assigned with either the admin or user role.

    Any users that you create manually on the register page can be assigned with either role.

    To demonstrate the user of user roles, users assigned with the user role will not be able to delete posts or edit/delete comments.