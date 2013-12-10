HTMLIG (HTML Image Getter)
======

A very simple PHP script to collect all the images URLs from an HTML page

I wrote this PHP script because I needed to take all the images URLs contained into the "src" attributes of "img" HTML tags. This script is very simple and may be extended... so, feel free to fork it and enjoy yourself! :-)

It doesn't depend from other libraries. It's standalone.

======

Some invocation examples through web server:

http://localhost/htmlig/?url=https://www.example.com/
http://localhost/htmlig/?url=https://www.example.com/&debug (in debug mode)

The examples above suppose that you installed the code on your local HTTPD server, under the htmlig folder.