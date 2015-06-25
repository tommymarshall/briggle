# Briggle Blogging Platform

## General

* version 1.1
* date June 2, 2011

![Screenshot](https://s3.amazonaws.com/vigesharing-is-vigecaring/tmarshall/Home_2015-06-25_11-27-39.jpg)

## Introduction

Briggle is a group-blogging web application that focuses on ease of setup, maintenance, controlling access to, and posting content or images to.

The system is developed using PHP and MySQL, and uses a custom framework which resembles an MVC programming design, but has taken much influence from the ease of customization offered by way of Wordpress. As such, it is very easy to update, customize, and build upon.


## Requirements

A web host with enough access to upload files (FTP) and ability to create a database, user, and assign privileges to that user on the given database.


## Installation

1. Create a database and assign a user to that database with full access.
2. Unzip the Briggle archive and upload all files to the installation directory.
3. Navigate to the main directory of your home page where you will be forwarded to the /install folder. 
4. Complete the installation steps.
5. If successful, delete the /install folder from your server.


## Urls And Htaccess
By default, Briggle wants to use pretty URLS (http://domain.com/view/5 as opposed to http://domain.com/index.php/view/5).

If your server does not have mod_rewrite enabled, you must modify the configuration.php file as well as the .htaccess file. First, you must add "index.php/" (note trailing slash) to BRIGGLE_DIR, BRIGGLE_THEMES, BRIGGLE_ASSETS, and BRIGGLE_INC after the installation directory of Briggle. For example, BRIGGLE_THEMES would become: http://domain.com/blog/index.php/bc-content/themes/

Additionally, you should remove completely or simply comment out all the lines in the .htaccess file.


## Using Briggle

1. GUEST ACCESS If you have the system set to private, then users must log in or sign in as a guest using the Guest Password that is set on the Settings page. If no password is set, then the guest system is disabled by default. Guests cannot write posts or comment on articles, they have a read-only access.
2. WRITING POSTS To write a post, simply click the "Write Post" link at the top right of every page. Your post must contain a title, but need not contain content or an image. To upload an image, simply click the "Choose file" button, select an image, and another "Choose file" button will appear. Posts support unlimited images. If your post does not contain text but does contain an image, the image will be displayed in full view on the home page. However, if you do have content as well, then a thumbnail will be used.

## Roles

### Authors

Have access to view other authors, write posts, and add comments. They do not have access to create other authors or edit other authors' posts or comments. 

### Editors

Can view authors, write posts, add comments, and edit or delete anyone's post or comment. They do not have access to the Settings page.

### Administrators

Have full access to the website and can edit Settings.

## Copyright Information

All source code is copyright Tommy Marshall unless otherwise stated. You may not claim it as your own and sell it as such. You simply receive a license to use the software, not claim the code as your own.
