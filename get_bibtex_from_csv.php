<?php
    set_time_limit(0);

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });
    
    $break_line = "<br>";
    define('BREAK_LINE', $break_line);
    $user_agent     = (!empty($_SERVER["HTTP_USER_AGENT"])) ? $_SERVER["HTTP_USER_AGENT"] : "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0";


    $path = "csv/";
    $files = array_diff(scandir($path), array('.', '..', ".DS_Store"));

    foreach($files as $file) {
        Util::showMessage("$path$file");
        $lines = file($path.$file);
        Util::showMessage("Total files: " . count($lines));
        Util::showMessage("");

        foreach($lines as $key => $line) {

            Util::showMessage("Article: " . ($key + 1) . "/" . count($lines));
            $new_data = array();
            $new_bibtex = "";

            $data = explode('","', $line);
            $url = $data[8];
            $id = str_replace(array("http://link.springer.com/article/","http://link.springer.com/chapter/"), "", $url);
            $urlBibtex = "https://citation-needed.springer.com/v2/references/$id?format=bibtex&flavour=citation";
            $bibtex = Util::loadURL($urlBibtex, null, $user_agent);

            if (empty($bibtex)) {
                Util::showMessage("File bibtex not found: $url");
                Util::showMessage("");
                continue;
            }

            $htmlArticle = Util::loadURL($url, null, $user_agent);
            $classname = "Keyword";
            $arrKeywords = Util::getHTMLFromClass($htmlArticle, $classname, "span");

            $classname = "test-metric-count c-button-circle gtm-citations-count";
            $arrCitations = Util::getHTMLFromClass($htmlArticle, $classname, "span");

            $classname = "article-metrics__views";
            $arrDownloads = Util::getHTMLFromClass($htmlArticle, $classname, "span");

            $keywords = "";
            if (!empty($arrKeywords)) {
                $keywords = strip_tags(implode(",", $arrKeywords));
            }

            $citations = "";
            if (!empty($arrCitations) && !empty($arrCitations[0])) {
                $citations = strip_tags($arrCitations[0]);
            }

            $downloads = "";
            if (!empty($arrDownloads) && !empty($arrDownloads[0])) {
                $downloads = strip_tags($arrDownloads[0]);
                $downloads = (substr($downloads,-1) == "k") ? substr($downloads,0,-1) * 1000 : substr($downloads,-1);
            }
            $new_data["keywords"] = $keywords;
            $new_data["citations"] = $citations;
            $new_data["downloads"] = $downloads;
            $new_bibtex = Util::add_fields_bibtex($bibtex, $new_data);

            file_put_contents("bib/" . substr($file, 0, -4) . ".bib", $new_bibtex, FILE_APPEND);
            $sleep = rand(2,5);
            Util::showMessage("Wait for " . $sleep . " seconds before executing next page");
            Util::showMessage("");
            sleep($sleep);
        }   
    }
?>