<?php

class Springer {
    private static $URL = 'https://link.springer.com';

    public static function getUrl($page, $query, $content_type="", $language="") 
    {

        $url = "";
        if (!empty($content_type)) {
            $content_type = "&facet-content-type=\"$content_type\"";
        }
        if (!empty($language)) {
            $language = "&facet-language=\"$language\"";
        } 
        $url = self::$URL . "/search/page/$page?query=$query" . $content_type . $language;
        
        return $url;
    }

    public static function start($page, $query_string, $url, $file, $content_type, $language) {
        echo "Page: " . $page . BREAK_LINE;
        $url = self::getUrl($page, $query_string, $content_type, $language);
        self::progress($url, $file);
    }

    public static function progress($url, $file) {        
        $html = Util::loadURL($url, COOKIE, USER_AGENT);
        
        // Check Google Captcha
        if ( strpos($html, "gs_captcha_cb()") !== false || strpos($html, "sending automated queries") !== false ) {
            echo "Captha detected" . BREAK_LINE; exit;
        }

        $classname = "no-access";
        $htmlValues = Util::getHTMLFromClass($html, $classname, "li");        
        $bibtex_new = "";
        foreach($htmlValues as $htmlValue) {

            $data     = self::getTitleAndUrlAndDocFromHTML($htmlValue);
            
            Util::showMessage($data["title"]);

            if ( strpos($data["url_article"], "book") !== false ) {
                Util::showMessage("It was not possible download bibtex file from a Book.");
                continue;
            }
            
            $bibtex      = self::getBibtex($data["doc"]);
            
            if ( strpos($bibtex, "Internal Server Error") !== false || strpos($bibtex, "Page not found") !== false) {
                Util::showMessage("It was not possible download bibtex file: Internal Server Error or Page not found");
                sleep(rand(2,4)); // rand between 2 and 4 seconds
                continue;
            }

            if (!empty($data["url_article"])) {
                unset($data["title"]);
                unset($data["doc"]);
                $bibtex_new .= Util::add_fields_bibtex($bibtex, $data);
            } else {
                $bibtex_new .= $bibtex;
            }
            
            var_dump($file, $bibtex_new);
            file_put_contents($file, $bibtex_new, FILE_APPEND);
            exit;
            Util::showMessage("Download bibtex file OK.");
            Util::showMessage("");
            sleep(rand(2,4)); // rand between 2 and 4 seconds
        }

        if (!empty($bibtex_new)) {
            var_dump($file, $bibtex_new);
            file_put_contents($file, $bibtex_new, FILE_APPEND);
            Util::showMessage("File $file saved successfully.");
            Util::showMessage("");
        }
    }

    public static function getTitleAndUrlAndDocFromHTML($html) {
        $retorno    = array("url_article"=>"", "title"=> "", "doc"=>"");
        $classname  = "title";
        $values     = Util::getHTMLFromClass($html, $classname, "a");
        $url        = trim(Util::getURLFromHTML($values[0]));
        $title      = trim(strip_tags($values[0]));

        $docs = explode("/", $url);        
        $lengh = count($docs);
        $doc = "";
        for($i=2;$i<$lengh;$i++) {
            $doc .= $docs[$i] . "/"; 
        }
        $doc = rtrim($doc, "/");

        if (strpos($url, "http") === false) {
            $url = self::$URL . $url;
        }
        if (!empty($url)&& !empty($title)) {
            $retorno["url_article"] = $url;
        }
        if (!empty($title)) {
            $retorno["title"] = $title;
        }
        if (!empty($doc)) {
            $retorno["doc"] = $doc;
        }

        return $retorno;
    }
        
    public static function getBibtex($doc) {        
        $url = "https://citation-needed.springer.com/v2/references/$doc?format=bibtex&flavour=citation";
        $bibtex = Util::loadURL($url, COOKIE, USER_AGENT);
        $bibtex = strip_tags($bibtex); // remove html tags 
        return $bibtex;        
    }
    
    public static function getPDF($doc) {        
        $url = "https://citation-needed.springer.com/v2/references/$doc?format=bibtex&flavour=citation";
        $bibtex = Util::loadURL($url, COOKIE, USER_AGENT);
        $bibtex = strip_tags($bibtex); // remove html tags 
        return $bibtex;        
    }
}
    
?>
