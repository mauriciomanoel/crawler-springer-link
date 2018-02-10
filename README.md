# crawler-springer-link

An easy way to save your articles in bibtex format from the "Springer Link" repository see [here](https://link.springer.com).

## Installing

### Prerequisite

`Web Server with PHP` is required, see [here](https://www.apachefriends.org/download.html).

## Quick Start

## Downloading and Installing

Clone the code repositories
```
 $ mkdir crawler-springer-link
 
 $ cd crawler-springer-link
 
 $ git clone https://github.com/mauriciomanoel/crawler-springer-link.git
 ```

## Web Server
```
http://my-server/crawler-springer-link/get_bibtex.php?page=NUMBER_PAGE&query=QUERY_STRING

E.g. http://my-server/crawler-springer-link/get_bibtex.php?page=1&query="Internet of medical things"
E.g. http://my-server/crawler-springer-link/get_bibtex.php?page=1&query=("healthcare IoT" OR "health IoT" OR "healthIoT")
```

I hope I've helped