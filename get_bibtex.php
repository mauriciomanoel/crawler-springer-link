<?php
    set_time_limit(0);

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });
    
    $break_line         = "<br>";
    $query_string       = urlencode(trim(@$_GET['query']));
    $file_name          = trim(@$_GET['query']);
    $page               = (int) @$_GET['page'];
    $pages              = (int) @$_GET['pages'];
    $content_type       = trim(@$_GET['content_type']);
    $language           = strtolower(trim(@$_GET['language']));
    
    define('BREAK_LINE', $break_line);

    try {
        if (empty($query_string)) {
            throw new Exception("Query String not found");
        } 
        if ( (!empty($page) && !empty($pages) ) || ( empty($page) && empty($pages) )) {
            throw new Exception("Only one parameter: page or pages");
        }
        if (!empty($content_type)) {
            $contentTypes = array("Chapter", "ConferencePaper", "Article", "ReferenceWorkEntry", "Book", "ConferenceProceedings", "Protocol");
            $key = array_search($content_type, $contentTypes);
            if ($key === false) {
                throw new Exception("Content Type Invalid");
            }
        }
        if (!empty($language)) {
            $languages = array("pt", "en");
            $key = array_search($language, $languages);
            if ($key === false) {
                throw new Exception("Language Invalid");
            }
            $language = ucfirst($language);
        } else {
            $language = "En";
        }
        
        $file           = Util::slug(trim($file_name)) . ".bib";
        $url            = Springer::getUrl(1, $query_string);
        $cookie         = Util::getCookie($url);
        $user_agent     = (!empty($_SERVER["HTTP_USER_AGENT"])) ? $_SERVER["HTTP_USER_AGENT"] : "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0";
        define('USER_AGENT', $user_agent);   
        define('COOKIE', @$cookie);
        define('FILE', $file);

        if (!empty($page)) {            
            Springer::start($page, $query_string, $url, $file, $content_type, $language);
        }  else if (!empty($pages)) {

            for($page=1; $page<=$pages; $page++) {
                Springer::start($page, $query_string, $url, $file, $content_type, $language);
                $sleep = rand(2,5);
                if ($page != $pages) {
                    Util::showMessage("Wait for " . $sleep . " seconds before executing next page");
                    Util::showMessage("");
                    sleep($sleep);
                }
            }
        }

    } catch(Exception $e) {
        echo $e->getMessage() . BREAK_LINE;
    }
?>