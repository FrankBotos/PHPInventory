# PHPInventory
A PHP Inventory system, making use of a web server and databases. It also implements user accounts, logging in and out, and protection against SQL Injections and XSS attacks. This was created for educational purposes, with the aesthetic design taking a back seat to the backend/server-side programming. However, the end result did turn out to be rather useable, providing a rich set of features that could help a business efficiently keep track of inventory.

#Portable Server
Because of the nature of Web Servers and remote databases, and the fact that hosting a server for a coding side-project would be impractical, I have created this repository by integrating my source code with a piece of software called "Uniform Server," (downloaded from http://www.uniformserver.com) which creates a portable, local web server on any Windows PC that does not require any sort of install, and allows for the local testing of PHP, Databases, and other such server-side features without the need for a constant remote server that all people who test this would need to connect to through the Internet. Most of the files and directories in this repository are a part of that portable server program. **To find the source code, simply go to www->PHPInventory.** There you will find all of the PHP source code that makes up this project. One of the benefits of Uniform Server is that the end user of this software can simply download the repository, and immediately test the program on a local server that already has Apache, MySQL, and PHP installed.

# Running on Windows with the Included Portable Server
Simply download the repository, and run UniController.exe. Allow firewall access if asked. Once the program is open, click Start Apache and wait for the green light. Then click Start MySQL and wait for the green light. 

Then go to your web browser of choice, and go to this link: **localhost/PHPInventory/login.php**

**username:** user@user.com

**password:** user

From there on out you can experiment with the software without the need for a remote server. If you wish to use this software in a more practical way, it is also possible to configure Uniform Server to be a real web server, allowing for connections from the Internet (there are tutorials for doing this throughout the Internet).

#Some Notes
This can run on Linux, however getting it to work with a new or already exsiting web server would require a complex series of steps ranging from setting up a server, to installing PHP, to making exact copies of the databases needed, to updating all the access details that the source code uses to connect to the database. Therefore, it is recommended that this code is either used "as is" with the included Portable Server, or simply used as a reference for your own design, and to see the code required to make such a system work and function correctly.

#Issues
Currently a number of cumbersome steps need to be taken in order to add a new user account to the database. I hope to implement a new page that makes the addition of user accounts a much simpler, streamlined process.
