<?php

namespace App;

use App\Scrapper;


class Mangapark extends scrapper
{
    public $baseURL1;
    public $baseURL2;
    public $baseURL3;
    public $seriesInfo;
    public function __construct()
    {
        parent::__construct();
        $this->baseURL1 = "https://mangapark.net/manga/";
        $this->baseURL2 = "?st-mcl=3";
        $this->baseURL3 = "https://mangapark.net/search?genres=";
    }

    public function getSeriesInfo($seriesName, $options = [])
    {
        $seriesName = str_replace(" ", "-", trim($seriesName));
        $seriesInfo = [];
        $html = $this->wget($this->baseURL1 . $seriesName . $this->baseURL2);
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $seriesInfo["name"] = $seriesName;
        $imagenURL = $xpath->query("/html/body/section/div/div[2]/div[1]/div/img")->item(0)->getAttribute("src");
        if ($options["withImagen"]) {
            $imageInfo = base64_encode($this->wget("https:" . $imagenURL));
            $seriesInfo["imageInfo"] = $imageInfo;
        }
        $seriesInfo["imagenURL"] = "https:" . $imagenURL;
        $otherInfoContainers = $xpath->query("/html/body/section/div/div[2]/div[2]/table/tr[position() != last() and position() != 1]");
        foreach ($otherInfoContainers as $otherInfoContainer) {
            $toReplace = ["\n", "\t"];
            $title = str_replace($toReplace, "", $xpath->query("./th", $otherInfoContainer)->item(0)->nodeValue);
            $content = str_replace($toReplace, "", $xpath->query("./td", $otherInfoContainer)->item(0)->nodeValue);
            $seriesInfo["otherInfo"][$title] = $content;
        }
        $synopsis = $xpath->query("/html/body/section/div/p")->item(0)->nodeValue;
        $seriesInfo["synopsis"] = $synopsis;
        $versions = $xpath->query("//*[@id=\"list\"]/div");
        foreach ($versions as $version) {
            $versionHeaders = $xpath->query("./div[1]/div[1]//span", $version);
            $versionName = $versionHeaders->item(0)->nodeValue;
            $versionInfo = $versionHeaders->item(1)->nodeValue;
            $seriesInfo["versions"][$versionName]["versionInfo"] = $versionInfo;
            $versionChapters = $xpath->query("./div[position()!=1]", $version);
            foreach ($versionChapters as $versionChapter) {
                $versionChapterContainers = $xpath->query("./div/div/div/div/div/a", $versionChapter);
                foreach ($versionChapterContainers as $versionChapterContainer) {
                    $chapterURL =  $versionChapterContainer->getAttribute("href");
                    $chapterURLbits = explode("/", $chapterURL);
                    unset($chapterURLbits[count($chapterURLbits) - 1]);
                    $chapterURLtrimed = "https://mangapark.net";
                    foreach ($chapterURLbits as $chapterURLbit) $chapterURLtrimed .= "/" . $chapterURLbit;
                    $chapterName =  $versionChapterContainer->nodeValue;
                    $seriesInfo["versions"][$versionName]["chapters"][$chapterName] = $chapterURLtrimed;
                }
            }
        }
        $this->seriesInfo = $seriesInfo;
        return $seriesInfo;
    }

    public function getVersionChapters($chapters)
    {
        if (!file_exists("/var/www/datas/" . $this->seriesInfo["name"])) {
            mkdir("/var/www/datas/" . $this->seriesInfo["name"]);
        }
        foreach ($chapters as $number => $chapter) {
            $this->getChapter($chapter, $number);
        }
        $zip = new \ZipArchive;
        if ($zip->open('/var/www/datas/' . $this->seriesInfo["name"] . ".zip", \ZipArchive::CREATE) === TRUE) {
            if ($handle = opendir("/var/www/datas/" . $this->seriesInfo["name"])) {
                // Add all files inside the directory
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != ".." && !is_dir("/var/www/datas/" . $this->seriesInfo["name"] . "/" . $entry)) {
                        $zip->addFile("/var/www/datas/" . $this->seriesInfo["name"] . "/" . $entry);
                    }
                }
                closedir($handle);
            }
            $zip->close();
        }
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $this->seriesInfo["name"] . ".zip" . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize('/var/www/datas/' . $this->seriesInfo["name"] . ".zip"));
        ob_end_flush();
        @readfile('/var/www/datas/' . $this->seriesInfo["name"] . ".zip");
        unlink('/var/www/datas/' . $this->seriesInfo["name"] . ".zip");
        unlink('/var/www/datas/' . $this->seriesInfo["name"]);
    }

    public function getChapter($url, $number)
    {
        require_once('./resources/fpdf.php');
        $pdf = new \FPDF();
        $html = $this->wget($url);
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $images = $xpath->query("/html/body/script[2]");
        $resultado = explode('"u":', $images->item(0)->nodeValue);
        unset($resultado[0]);
        $final = [];
        foreach ($resultado as $trozo) {
            $trozos2 = explode('}', $trozo);
            $final[] = json_decode($trozos2[0]);
        }
        foreach ($final as $image) {
            $pdf->AddPage();
            $pdf->Image($image, 15, 15, 175, 275, 'JPG');
        }
        $pdf->Output("F", "/var/www/datas/" . $this->seriesInfo["name"] . "/" . $this->seriesInfo["name"] . "-" . $number . ".pdf");
    }

    public function getAllMangasByGenre($genresIncluded, $genresExcluded)
    {
        //prepare URL to search
        $urlFinal = $this->baseURL3;
        foreach ($genresIncluded as $genre) {
            $urlFinal .= $genre . ",";
        }
        substr($urlFinal, 0, -1);
        $urlFinal .= "&genres-exclude=";
        foreach ($genresExcluded as $genre) {
            $urlFinal .= $genre;
        }
        substr($urlFinal, 0, -1);
        $html = $this->wget($urlFinal);
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $pagesLength = $xpath->query("/html/body/section/div/div[3]/div[1]/ul/li[3]/select/option")->length;
        //iterar sobre las paginas que contienen los mangas
        $seriesInfoByPage = [];
        for ($pageNumber = 1; $pageNumber <= $pagesLength; $pageNumber++) {
            $seriesInfoByPage[$pageNumber] = $this->getAllMangasInPageByGenre($urlFinal . "&page=" . $pageNumber);
        }
        // ob_flush();
        // ob_start();
        // file_put_contents("dump.txt", ob_get_flush());
        return $seriesInfoByPage;
    }
    public function getAllMangasInPageByGenre($url)
    {
        $baseURL = "https://mangapark.net";
        $html = $this->wget($url);
        $doc = new \DOMDocument("1.0", "utf-8");
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $mangaLinks = $xpath->query("/html/body/section/div/div[3]/div[@class='item']//a[@class='cover']");
        $seriesInfoTotal = [];
        foreach ($mangaLinks as $mangaLink) {
            $link = $mangaLink->getAttribute("href");
            $seriesName = explode("/", $link);
            $seriesName = $seriesName[count($seriesName) - 1];
            $seriesInfoTotal[$seriesName] = $this->getSeriesInfo($seriesName, ["withImagen" => false]);
        }
        return $seriesInfoTotal;
    }
}
